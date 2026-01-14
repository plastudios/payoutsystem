<?php

namespace App\Services;

use App\Models\MFSPayout;
use App\Models\Merchant;
use App\Models\WebhookLog;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MFSPayoutWebhookService
{
    public static function notify($batchId, $merchantId)
    {
        $merchant = Merchant::where('merchant_id', $merchantId)->first();

        if (!$merchant || !$merchant->webhook_url) {
            Log::info("No webhook URL for merchant ID: $merchantId, skipping webhook.");
            return;
        }

        $payouts = MFSPayout::where('batch_id', $batchId)->get();

        if ($payouts->isEmpty()) {
            Log::warning("No MFS payouts found for batch ID: $batchId");
        }

        $payload = [
            'batch_id' => $batchId,
            'payouts' => $payouts->map(function ($payout) {
                return [
                    'referenceKey'   => $payout->reference_key,
                    'amount'         => $payout->amount,
                    'wallet_number'  => (string) $payout->wallet_number,
                    'method'         => ucfirst($payout->method),
                    'status'         => $payout->status,
                ];
            })->toArray(),
        ];

        try {
            $response = Http::post($merchant->webhook_url, $payload);

            WebhookLog::create([
                'batch_id'         => $batchId,
                'merchant_id'      => $merchantId,
                'url'              => $merchant->webhook_url,
                'request_payload'  => json_encode($payload),
                'response_payload' => $response->body(),
                'status_code'      => $response->status(),
            ]);
        } catch (\Exception $e) {
            Log::error("MFS Webhook failed: " . $e->getMessage());

            WebhookLog::create([
                'batch_id'         => $batchId,
                'merchant_id'      => $merchantId,
                'url'              => $merchant->webhook_url,
                'request_payload'  => json_encode($payload),
                'response_payload' => $e->getMessage(),
                'status_code'      => null,
            ]);
        }
    }
}
