<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

use App\Models\MFSPayout;
use App\Models\Merchant;
use App\Models\MerchantBalance;
use App\Services\MFSPayoutWebhookService;
use App\Mail\PayoutBatchCreated;


class MFSPayoutApiController extends Controller
{
    
    /**
     * @OA\Post(
     *     path="/api/merchant/mfs-payouts",
     *     tags={"MFS Payouts"},
     *     security={{"bearerAuth":{}}},
     *     summary="Create a new MFS payout batch",
     *
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="merchant_id", type="string", example="faastpay"),
     *             @OA\Property(property="batch_id", type="string", example="MFS-ABC123XYZ"),
     *             @OA\Property(
     *                 property="payouts",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="reference_key", type="string", example="MFS-REF001"),
     *                     @OA\Property(property="wallet_number", type="string", example="01712345678"),
     *                     @OA\Property(property="amount", type="number", format="float", example=100.00),
     *                     @OA\Property(property="method", type="string", enum={"bKash", "Nagad"})
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(response=200, description="Batch created successfully"),
     *     @OA\Response(response=400, description="Insufficient balance"),
     *     @OA\Response(response=422, description="Validation errors")
     * )
     */
    public function bulkCreate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'merchant_id' => 'required|exists:merchants,merchant_id',
            'batch_id' => 'required|unique:mfs_payouts,batch_id',
            'payouts' => 'required|array|min:1',
            'payouts.*.amount' => 'required|numeric|min:0.01',
            'payouts.*.wallet_number' => 'required|string',
            'payouts.*.method' => 'required|in:bKash,Nagad',
            'payouts.*.reference_key' => 'required|string|unique:mfs_payouts,reference_key',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'code' => 422,
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        $merchantId = $request->merchant_id;
        $batchId = $request->batch_id;
        $totalAmount = collect($request->payouts)->sum('amount');

        $credited = MerchantBalance::where('merchant_id', $merchantId)->where('type', 'credit')->sum('amount');
        $debited = MerchantBalance::where('merchant_id', $merchantId)->where('type', 'debit')->sum('amount');
        $availableBalance = $credited - $debited;

        if ($totalAmount > $availableBalance) {
            return response()->json([
                'code' => 400,
                'status' => 'error',
                'message' => "Insufficient balance. Payout request: $totalAmount, Available: $availableBalance"
            ], 400);
        }

        foreach ($request->payouts as $row) {
            MFSPayout::create([
                'batch_id' => $batchId,
                'reference_key' => $row['reference_key'],
                'amount' => $row['amount'],
                'wallet_number' => $row['wallet_number'],
                'method' => strtolower($row['method']),
                'merchant_id' => $merchantId,
                'status' => 'Pending',
            ]);
        }

        MFSPayoutWebhookService::notify($batchId, $merchantId);

        $payouts = MFSPayout::where('batch_id', $batchId)->get();
        $totalAmount = $payouts->sum('amount');
        $totalCount = $payouts->count();

        Mail::to(['imtiazakil@gmail.com', 'imtiaz@aamarpay.com'])
            ->queue(new PayoutBatchCreated($batchId, $merchantId, $totalAmount, $totalCount));

        return response()->json([
            'code' => 200,
            'status' => 'success',
            'message' => "MFS Payouts created for batch: $batchId"
        ]);
    }
    /**
 * @OA\Get(
 *     path="/api/merchant/mfs-payouts/batch/{batchId}",
 *     tags={"MFS Payouts"},
 *     summary="Fetch MFS payout batch details by Batch ID",
 *     description="Returns list of MFS payouts for the given batch ID",
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="batchId",
 *         in="path",
 *         required=true,
 *         description="The batch ID of the MFS payouts to fetch",
 *         @OA\Schema(type="string", example="MFS-ABC123XYZ")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Batch details fetched successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="code", type="integer", example=200),
 *             @OA\Property(property="status", type="string", example="success"),
 *             @OA\Property(property="batch_id", type="string", example="MFS-ABC123XYZ"),
 *             @OA\Property(property="total_count", type="integer", example=3),
 *             @OA\Property(property="total_amount", type="number", format="float", example=300.00),
 *             @OA\Property(
 *                 property="data",
 *                 type="array",
 *                 @OA\Items(
 *                     @OA\Property(property="reference_key", type="string", example="MFS-REF001"),
 *                     @OA\Property(property="wallet_number", type="string", example="01712345678"),
 *                     @OA\Property(property="amount", type="number", format="float", example=100),
 *                     @OA\Property(property="method", type="string", example="bkash"),
 *                     @OA\Property(property="status", type="string", example="Pending")
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Batch not found",
 *         @OA\JsonContent(
 *             @OA\Property(property="code", type="integer", example=404),
 *             @OA\Property(property="status", type="string", example="error"),
 *             @OA\Property(property="message", type="string", example="No data found for this batch ID.")
 *         )
 *     )
 * )
 */

    public function fetchBatch(Request $request, $batchId)
    {
        $merchantId = $request->user()->merchant_id;

        $payouts = MFSPayout::where('batch_id', $batchId)
            ->where('merchant_id', $merchantId)
            ->get();

        if ($payouts->isEmpty()) {
            return response()->json([
                'code' => 404,
                'status' => 'error',
                'message' => 'No data found for this batch ID.'
            ], 404);
        }

        $filtered = $payouts->map(function ($p) {
            return collect($p)->except(['id']);
        });

        return response()->json([
            'code' => 200,
            'status' => 'success',
            'batch_id' => $batchId,
            'total_count' => $payouts->count(),
            'total_amount' => $payouts->sum('amount'),
            'data' => $filtered
        ]);
    }

   /**
 * @OA\Get(
 *     path="/api/merchant/mfs-payouts/reference/{referenceKey}",
 *     tags={"MFS Payouts"},
 *     summary="Fetch single payout by reference key",
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="referenceKey",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="string")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Payout found",
 *         @OA\JsonContent(
 *             @OA\Property(property="code", type="integer", example=200),
 *             @OA\Property(property="status", type="string", example="success"),
 *             @OA\Property(
 *                 property="data",
 *                 type="object",
 *                 @OA\Property(property="reference_key", type="string", example="MFS-REF001"),
 *                 @OA\Property(property="wallet_number", type="string", example="01712345678"),
 *                 @OA\Property(property="amount", type="number", example=100),
 *                 @OA\Property(property="method", type="string", example="bkash"),
 *                 @OA\Property(property="status", type="string", example="Pending")
 *             )
 *         )
 *     ),
 *     @OA\Response(response=404, description="Payout not found")
 * )
 */


    public function getByReferenceKey($referenceKey)
    {
        $payout = MFSPayout::where('reference_key', $referenceKey)->first();

        if (!$payout) {
            return response()->json([
                'code' => 404,
                'status' => 'error',
                'message' => 'Payout not found'
            ], 404);
        }

        return response()->json([
            'code' => 200,
            'status' => 'success',
            'data' => collect($payout)->except(['id'])
        ]);
    }
}
