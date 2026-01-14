<?php

namespace App\Services;

use App\Models\MFSPayout;

use App\Models\Merchant;
use App\Models\Payout;
use App\Models\WebhookLog;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PayoutWebhookService
{
    public static function notify($batchId, $merchantId)
    {
        $merchant = Merchant::where('merchant_id', $merchantId)->first();

        if (!$merchant || !$merchant->webhook_url) {
            Log::info("No webhook URL for merchant ID: $merchantId, skipping webhook.");
            return;
        }

        $payouts = Payout::where('batch_id', $batchId)->get()->map(function ($payout) {
            return [
                'referenceKey'      => $payout->referenceKey,
                'amount'            => $payout->amount,
                'currency'          => $payout->currency,
                'remarks'           => $payout->remarks,
                'bankCode'          => $payout->bankCode,
                'bankShortCode'     => $payout->bankShortCode,
                'benType'           => $payout->benType,
                'txnChannel'        => $payout->txnChannel,
                'beneficiaryAcc'    => $payout->beneficiaryAcc,
                'beneficiaryName'   => $payout->beneficiaryName,
                'beneficiaryEmail'  => $payout->beneficiaryEmail,
                'routingNumber'     => $payout->routingNumber,
                'txnChannelCode'    => $payout->txnChannelCode,
                'status'            => $payout->status,
                'approval_status'   => $payout->approval_status,
            ];
        })->toArray();

        $payload = [
            'batch_id' => $batchId,
            'payouts' => $payouts,
        ];

        try {
            $response = Http::post($merchant->webhook_url, $payload);

            WebhookLog::create([
                'batch_id'        => $batchId,
                'merchant_id'     => $merchantId,
                'url'             => $merchant->webhook_url,
                'request_payload' => json_encode($payload),
                'response_payload'=> $response->body(),
                'status_code'     => $response->status(),
            ]);
        } catch (\Exception $e) {
            Log::error("Webhook failed: " . $e->getMessage());

            WebhookLog::create([
                'batch_id'        => $batchId,
                'merchant_id'     => $merchantId,
                'url'             => $merchant->webhook_url,
                'request_payload' => json_encode($payload),
                'response_payload'=> $e->getMessage(),
                'status_code'     => null,
            ]);
        }
    }

    
}
