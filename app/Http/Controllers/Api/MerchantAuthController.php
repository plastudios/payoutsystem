<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\MerchantBalance;

class MerchantAuthController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/merchant/token",
     *     summary="Merchant Login - Get Bearer Token",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email", "password"},
     *             @OA\Property(
     *                 property="email",
     *                 type="string",
     *                 format="email",
     *                 example="merchant@example.com"
     *             ),
     *             @OA\Property(
     *                 property="password",
     *                 type="string",
     *                 format="password",
     *                 example="123456"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful login and token issued",
     *         @OA\JsonContent(
     *             @OA\Property(property="code", type="integer", example=200),
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="token", type="string", example="your-bearer-token"),
     *             @OA\Property(property="expires_at", type="string", format="date-time", example="2026-01-16T03:09:09+00:00"),
     *             @OA\Property(property="merchant_id", type="string", example="merchant123"),
     *             @OA\Property(
     *                 property="user",
     *                 type="object",
     *                 @OA\Property(property="name", type="string", example="Demo Merchant"),
     *                 @OA\Property(property="email", type="string", example="merchant@example.com"),
     *                 @OA\Property(property="merchant_id", type="string", example="merchant123")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized - invalid credentials",
     *         @OA\JsonContent(
     *             @OA\Property(property="code", type="integer", example=401),
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Invalid credentials")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="code", type="integer", example=422),
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Validation failed")
     *         )
     *     )
     * )
     */


    public function token(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)
            ->where('role', 'merchant')
            ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            // return response()->json(['message' => 'Invalid credentials'], 401);

            return response()->json([
                'code' => 401,
                'status' => 'error',
                'message' => 'Invalid credentials'
            ]);
        }

        // Create token with 1-day expiration
        $token = $user->createToken('merchant-api-token', ['*'], now()->addDay());

        return response()->json([
            'code' => 200,
            'status' => 'success',
            'token' => $token->plainTextToken,
            'expires_at' => $token->accessToken->expires_at->toIso8601String(),
            'merchant_id' => $user->merchant_id,
            'user' => [
                'name' => $user->name,
                'email' => $user->email,
                'merchant_id' => $user->merchant_id,
            ],
        ]);
    }


    public function fetchBalance(Request $request)
    {
        $request->validate([
            'merchant_id' => 'required|string',
        ]);

        if ($request->user()->merchant_id !== $request->merchant_id) {
            return response()->json([
                'code' => 403,
                'status' => 'error',
                'message' => 'Merchant ID mismatch',
            ], 403);
        }

        $merchantId = $request->merchant_id;

        $credit = MerchantBalance::where('merchant_id', $merchantId)
            ->where('type', 'credit')
            ->sum('amount');

        $debit = MerchantBalance::where('merchant_id', $merchantId)
            ->where('type', 'debit')
            ->sum('amount');

        $availableBalance = $credit - $debit;

        return response()->json([
            'code' => 200,
            'status' => 'success',
            'balance' => $availableBalance,
        ]);
    }




}
