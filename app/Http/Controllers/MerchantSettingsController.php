<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Merchant;
use App\Models\WebhookLog;

class MerchantSettingsController extends Controller
{
    public function editWebhook()
    {
        $merchant = auth()->user()->merchant;
        return view('merchants.settings.webhook', compact('merchant'));
    }

    public function updateWebhook(Request $request)
    {
        $request->validate([
            'webhook_url' => 'nullable|url'
        ]);

        $merchant = auth()->user()->merchant;
        $merchant->webhook_url = $request->webhook_url;
        $merchant->save();

        return back()->with('success', 'Webhook URL updated successfully!');
    }

    public function viewWebhookLogs()
    {
        $merchant = auth()->user()->merchant;
        $logs = WebhookLog::where('merchant_id', $merchant->merchant_id)->latest()->get();

        return view('merchants.settings.webhook_logs', compact('logs'));
    }
}
