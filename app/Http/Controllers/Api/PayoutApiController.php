<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

use App\Models\Payout;
use App\Models\Merchant;
use App\Models\MerchantBalance;

use App\Services\PayoutWebhookService;
use App\Mail\PayoutBatchCreated;

class PayoutApiController extends Controller
{
    /**
 * @OA\Post(
 *     path="/api/merchant/payouts",
 *     summary="Create a new Bank Payout Batch",
 *     tags={"Bank Payouts"},
 *     security={{"bearerAuth":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"merchant_id", "batch_id", "payouts"},
 *             @OA\Property(property="merchant_id", type="string", example="faastpay"),
 *             @OA\Property(property="batch_id", type="string", example="BANK-ABC123XYZ"),
 *             @OA\Property(
 *                 property="payouts",
 *                 type="array",
 *                 @OA\Items(
 *                     @OA\Property(property="referenceKey", type="string", example="BANK-REF001"),
 *                     @OA\Property(property="amount", type="number", example=500),
 *                     @OA\Property(property="currency", type="string", example="BDT"),
 *                     @OA\Property(property="remarks", type="string", example="Vendor payment"),
 *                     @OA\Property(property="bankCode", type="string", example="011"),
 *                     @OA\Property(property="bankShortCode", type="string", example="DBBL"),
 *                     @OA\Property(property="benType", type="string", example="individual"),
 *                     @OA\Property(property="txnChannel", type="string", example="BEFTN"),
 *                     @OA\Property(property="beneficiaryAcc", type="string", example="123456789"),
 *                     @OA\Property(property="beneficiaryName", type="string", example="John Doe"),
 *                     @OA\Property(property="beneficiaryEmail", type="string", example="john@example.com"),
 *                     @OA\Property(property="routingNumber", type="string", example="123456789"),
 *                     @OA\Property(property="txnChannelCode", type="string", example="beftn")
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Payouts created successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="code", type="integer", example=200),
 *             @OA\Property(property="status", type="string", example="success"),
 *             @OA\Property(property="message", type="string", example="Payouts created for batch: BANK-ABC123XYZ")
 *         )
 *     ),
 *     @OA\Response(response=400, description="Insufficient balance"),
 *     @OA\Response(response=422, description="Validation error")
 * )
 */

    public function bulkCreate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'merchant_id' => 'required|exists:merchants,merchant_id',
            'batch_id' => 'required|unique:payouts,batch_id',
            'payouts' => 'required|array|min:1',
            'payouts.*.amount' => 'required|numeric|min:0.01',
            'payouts.*.referenceKey' => 'required|string|unique:payouts,referenceKey',
            'payouts.*.currency' => 'required|string',
            'payouts.*.remarks' => 'nullable|string',
            'payouts.*.bankCode' => 'required|string',
            'payouts.*.bankShortCode' => 'required|string',
            'payouts.*.benType' => 'required|string',
            'payouts.*.txnChannel' => 'required|string',
            'payouts.*.beneficiaryAcc' => 'required|string',
            'payouts.*.beneficiaryName' => 'required|string',
            'payouts.*.beneficiaryEmail' => 'nullable|email',
            'payouts.*.routingNumber' => 'nullable|string',
            'payouts.*.txnChannelCode' => 'required|string',
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
            Payout::create([
                'batch_id' => $batchId,
                'referenceKey' => $row['referenceKey'],
                'amount' => $row['amount'],
                'currency' => $row['currency'],
                'remarks' => $row['remarks'] ?? '',
                'bankCode' => $row['bankCode'],
                'bankShortCode' => $row['bankShortCode'],
                'benType' => $row['benType'],
                'txnChannel' => $row['txnChannel'],
                'beneficiaryAcc' => $row['beneficiaryAcc'],
                'beneficiaryName' => $row['beneficiaryName'],
                'beneficiaryEmail' => $row['beneficiaryEmail'] ?? null,
                'routingNumber' => $row['routingNumber'] ?? null,
                'txnChannelCode' => $row['txnChannelCode'],
                'merchant_id' => $merchantId,
                'status' => 'Pending',
                'approval_status' => 'pending',
            ]);
        }
    
        // 🔔 Notify merchant via webhook
        PayoutWebhookService::notify($batchId, $merchantId);
    
        // 📧 Send Email Notification
        $payouts = Payout::where('batch_id', $batchId)->get();
        $totalAmount = $payouts->sum('amount');
        $totalCount = $payouts->count();
    
        Mail::to(['imtiazakil@gmail.com', 'imtiaz@aamarpay.com'])
            ->queue(new PayoutBatchCreated($batchId, $merchantId, $totalAmount, $totalCount));
    
        return response()->json([
            'code' => 200,
            'status' => 'success',
            'message' => "Payouts created for batch: $batchId"
        ]);
    }

    /**
 * @OA\Get(
 *     path="/api/merchant/payouts/batch/{batch_id}",
 *     summary="Fetch all payouts under a given batch ID",
 *     tags={"Bank Payouts"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="batch_id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="string")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Batch details fetched successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="code", type="integer", example=200),
 *             @OA\Property(property="status", type="string", example="success"),
 *             @OA\Property(property="batch_id", type="string", example="BANK-ABC123XYZ"),
 *             @OA\Property(property="total_count", type="integer", example=2),
 *             @OA\Property(property="total_amount", type="number", example=1000),
 *             @OA\Property(property="data", type="array", @OA\Items(type="object"))
 *         )
 *     ),
 *     @OA\Response(response=404, description="Batch not found")
 * )
 */

    public function fetchBatch(Request $request, $batch_id)
    {
        $user = $request->user();

        // Optional: Ensure the authenticated user owns this merchant_id
        $merchantId = $user->merchant_id;

        $payouts = Payout::where('batch_id', $batch_id)
            ->where('merchant_id', $merchantId)
            ->get();
        $filtered = $payouts->map(function ($payout) {
            return collect($payout)->except([
                'id',
                'approval_status',
                'author_id',
                'checker_id',
                'maker_id'
            ]);
        });
        if ($payouts->isEmpty()) {
            return response()->json([
                'code' => 404,
                'status' => 'error',
                'message' => 'No data found for this batch ID.'
            ], 404);
        }

        return response()->json([
            'code' => 200,
            'status' => 'success',
            'batch_id' => $batch_id,
            'total_count' => $payouts->count(),
            'total_amount' => $payouts->sum('amount'),
            'data' => $filtered
        ]);
    }

/**
 * @OA\Get(
 *     path="/api/merchant/payouts/reference/{referenceKey}",
 *     summary="Fetch a single payout using the reference key",
 *     tags={"Bank Payouts"},
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
 *             @OA\Property(property="data", type="object",
 *                 @OA\Property(property="referenceKey", type="string", example="BANK-REF001"),
 *                 @OA\Property(property="amount", type="number", example=500),
 *                 @OA\Property(property="currency", type="string", example="BDT"),
 *                 @OA\Property(property="status", type="string", example="Pending")
 *             )
 *         )
 *     ),
 *     @OA\Response(response=404, description="Payout not found")
 * )
 */

    public function getByReferenceKey($referenceKey)
    {
        $payout = Payout::where('referenceKey', $referenceKey)->first();

        if (!$payout) {
            return response()->json([
                'code' => 404,
                'status' => 'error',
                'message' => 'Payout not found'
            ], 404);
        }

        $filteredData = collect($payout)->except([
            'id', 'approval_status', 'author_id', 'checker_id', 'maker_id'
        ]);

        return response()->json([
            'code' => 200,
            'status' => 'success',
            'data' => $filteredData,
        ]);
    }
}
