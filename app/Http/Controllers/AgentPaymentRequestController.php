<?php

namespace App\Http\Controllers;

use App\Models\MFSPayout;
use App\Services\MFSPayoutWebhookService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AgentPaymentRequestController extends Controller
{
    public function index(Request $request)
    {
        $merchantIds = auth()->user()->getMerchantIds();
        $filter = [
            'start_date' => $request->get('start_date', ''),
            'end_date'   => $request->get('end_date', ''),
            'status'     => $request->get('status', 'all'),
        ];

        if (empty($merchantIds)) {
            $payouts = collect();
        } else {
            $query = MFSPayout::whereIn('merchant_id', $merchantIds);

            if (! empty($filter['start_date'])) {
                $query->whereDate('created_at', '>=', $filter['start_date']);
            }
            if (! empty($filter['end_date'])) {
                $query->whereDate('created_at', '<=', $filter['end_date']);
            }
            if (! empty($filter['status']) && $filter['status'] !== 'all') {
                $query->where('status', $filter['status']);
            }

            $payouts = $query->orderBy('created_at', 'desc')->get();
        }

        return view('agent.payment_requests.index', compact('payouts', 'filter'));
    }

    public function markSuccess(Request $request, $id)
    {
        $request->validate([
            'mfs_transaction_id' => [
                'required',
                'string',
                'max:255',
                Rule::unique('mfs_payouts', 'mfs_transaction_id')->ignore($id, 'id'),
            ],
        ], [
            'mfs_transaction_id.unique' => 'This MFS Transaction ID is already used by another payout.',
        ]);

        $merchantIds = auth()->user()->getMerchantIds();
        $payout = MFSPayout::whereIn('merchant_id', $merchantIds)->findOrFail($id);

        if ($payout->status !== 'Pending') {
            return back()->with('error', 'Only Pending payouts can be marked as Success.');
        }

        $oldStatus = $payout->status;

        $payout->update([
            'status' => 'Success',
            'completed_at' => now(),
            'mfs_transaction_id' => $request->mfs_transaction_id,
            'agent_id' => auth()->id(),
        ]);

        app(MFSPayoutController::class)->applyBalanceForStatus($payout, 'Success', $oldStatus);
        MFSPayoutWebhookService::notify($payout->batch_id, $payout->merchant_id);

        return back()->with('success', 'Payout marked as Success.');
    }

    public function markFailed(Request $request, $id)
    {
        $request->validate([
            'remark' => 'required|string|max:1000',
        ]);

        $merchantIds = auth()->user()->getMerchantIds();
        $payout = MFSPayout::whereIn('merchant_id', $merchantIds)->findOrFail($id);

        if ($payout->status !== 'Pending') {
            return back()->with('error', 'Only Pending payouts can be marked as Failed.');
        }

        $oldStatus = $payout->status;

        $payout->update([
            'status' => 'Failed',
            'completed_at' => now(),
            'agent_id' => auth()->id(),
            'remarks' => $request->remark,
        ]);

        app(MFSPayoutController::class)->applyBalanceForStatus($payout, 'Failed', $oldStatus);
        MFSPayoutWebhookService::notify($payout->batch_id, $payout->merchant_id);

        return back()->with('success', 'Payout marked as Failed.');
    }
}
