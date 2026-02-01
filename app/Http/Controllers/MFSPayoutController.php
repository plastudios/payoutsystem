<?php

namespace App\Http\Controllers;

use App\Models\MFSPayout;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ArrayExport;
use App\Models\Merchant;
use App\Services\MFSPayoutWebhookService;

use App\Models\MerchantBalance;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\PayoutBatchCreated;
use App\Services\PayoutWebhookService;
// new namespace
use Illuminate\Support\Facades\DB; 

class MFSPayoutController extends Controller
{
    public function uploadForm()
    {
        $merchants = Merchant::all();
        return view('mfs.upload', compact('merchants'));
    }

    public function submitExport(Request $request)
    {
        $request->validate([
            'batch_id' => 'required|string'
        ]);

        return redirect()->route('mfs.export', ['batchId' => $request->batch_id]);
    }

    public function processUpload(Request $request)
    {
        $request->validate([
            'payout_file' => 'required|mimes:xlsx,xls',
            'merchant_id' => 'required|exists:merchants,merchant_id',
        ]);

        $user = auth()->user();
        $merchantIds = $user->getMerchantIds();
        if (!empty($merchantIds)) {
            $merchantId = count($merchantIds) === 1 ? $merchantIds[0] : $request->merchant_id;
            if ($user->role === 'agent' && (! $merchantId || ! in_array($merchantId, $merchantIds))) {
                return back()->with('error', 'Invalid or unauthorized merchant.');
            }
        } else {
            $merchantId = $request->merchant_id;
        }

        $batchId = 'MFS-' . strtoupper(Str::random(10));
        $data = Excel::toArray([], $request->file('payout_file'));

        $totalRequestAmount = 0;
        foreach ($data[0] as $index => $row) {
            if ($index === 0) continue;
            $totalRequestAmount += floatval($row[0]);
        }

        $credited = MerchantBalance::where('merchant_id', $merchantId)->where('type', 'credit')->sum('amount');
        $debited = MerchantBalance::where('merchant_id', $merchantId)->where('type', 'debit')->sum('amount');
        $availableBalance = $credited - $debited;

        if ($totalRequestAmount > $availableBalance) {
            return back()->with('error', "Insufficient balance. Payout request amount: " . number_format($totalRequestAmount, 2) . ", available balance: " . number_format($availableBalance, 2));
        }

        foreach ($data[0] as $index => $row) {
            if ($index === 0) continue;

            MFSPayout::create([
                'batch_id' => $batchId,
                'reference_key' => 'MFS-' . strtoupper(Str::random(12)),
                'amount' => floatval($row[0]),
                'wallet_number' => $row[1],
                'method' => strtolower($row[2]),
                'merchant_id' => $merchantId,
                'status' => 'Pending',
            ]);
        }

        // Webhook Notify
        MFSPayoutWebhookService::notify($batchId, $merchantId);

        // Email Notify
        $payouts = MFSPayout::where('batch_id', $batchId)->get();
        $totalAmount = $payouts->sum('amount');
        $totalCount = $payouts->count();

        Mail::to(['imtiazakil@gmail.com', 'imtiaz@aamarpay.com'])
            ->queue(new PayoutBatchCreated($batchId, $merchantId, $totalAmount, $totalCount));

        return back()->with('success', 'MFS Excel uploaded for approval with Batch ID: ' . $batchId);
    }

    public function export($batchId)
    {
        $payouts = MFSPayout::where('batch_id', $batchId)->get();

        $data = $payouts->map(function ($item) {
            return [
                'reference_key'   => $item->reference_key,
                'wallet_number'   => $item->wallet_number, // prevent Excel from converting to scientific format
                'amount'          => $item->amount,
                'method'          => ucfirst($item->method),
                'status'          => '', // Admin will fill this manually
            ];
        })->toArray();

        // 🔧 Add header row
        array_unshift($data, ['Reference Key', 'Wallet Number', 'Amount', 'Method', 'Status']);

        return Excel::download(new ArrayExport($data), "mfs_batch_{$batchId}.xlsx");
    }

    public function updateStatusFromExcel(Request $request)
    {
        $request->validate([
            'status_file' => 'required|mimes:xlsx,xls',
        ]);
    
        $data = Excel::toArray([], $request->file('status_file'));
    
        $batchId = null;
        $merchantId = null;
    
        foreach ($data[0] as $index => $row) {
            if ($index === 0) continue;
    
            $reference = trim($row[0]); // reference_key
            $status = ucfirst(strtolower(trim($row[4]))); // Success / Failed
    
            if (!in_array($status, ['Success', 'Failed'])) continue;
    
            $payout = MFSPayout::where('reference_key', $reference)->first();
            if (!$payout || $payout->status !== 'Pending') continue;
    
            // Save batch and merchant once
            if (!$batchId) {
                $batchId = $payout->batch_id;
                $merchantId = $payout->merchant_id;
            }
    
            $payout->update(['status' => $status]);
    
            if ($status === 'Success') {
                MerchantBalance::create([
                    'merchant_id' => $payout->merchant_id,
                    'type' => 'debit',
                    'amount' => $payout->amount,
                    'remarks' => "MFS Payout Deduction | Batch: {$payout->batch_id}, Ref: {$payout->reference_key}",
                ]);
            }
        }
        // Save batch info
        $batchId = $payout->batch_id;
        $merchantId = $payout->merchant_id;

        $updatedReferences[] = $reference;
       
        if ($batchId && $merchantId && count($updatedReferences)) {
            MFSPayoutWebhookService::notify($batchId, $merchantId);
        }
    
        return back()->with('success', 'Statuses updated successfully.');
    }

    public function showBatchSummary()
    {
        $query = MFSPayout::select(
            'batch_id',
            'merchant_id',
            \DB::raw('SUM(amount) as total_amount'),
            \DB::raw('COUNT(*) as count'),
            \DB::raw('SUM(CASE WHEN status = "Pending" THEN amount ELSE 0 END) as pending'),
            \DB::raw('SUM(CASE WHEN status = "Success" THEN amount ELSE 0 END) as success'),
            \DB::raw('SUM(CASE WHEN status = "Failed" THEN amount ELSE 0 END) as failed')
        )
            ->groupBy('batch_id', 'merchant_id');

        $merchantIds = auth()->user()->getMerchantIds();
        if (!empty($merchantIds)) {
            $query->whereIn('merchant_id', $merchantIds);
        }

        $batches = $query->get();

        return view('mfs.batches', compact('batches'));
    }

    public function showExportBatches()
    {
        $batches = MFSPayout::select(
            'batch_id',
            'merchant_id',
            \DB::raw('SUM(amount) as total_amount'),
            \DB::raw('COUNT(*) as count'),
            \DB::raw('SUM(CASE WHEN status = "Pending" THEN amount ELSE 0 END) as pending'),
            \DB::raw('SUM(CASE WHEN status = "Success" THEN amount ELSE 0 END) as success'),
            \DB::raw('SUM(CASE WHEN status = "Failed" THEN amount ELSE 0 END) as failed')
        )
        ->groupBy('batch_id', 'merchant_id')
        ->having('pending', '>', 0)
        ->get();

        return view('mfs.export', compact('batches'));
    }

    

    // public function batchDetails($batchId)
    // {
    //     $query = MFSPayout::where('batch_id', $batchId);

    //     // Restrict for merchant users
    //     if (auth()->user()->role === 'merchant') {
    //         $query->where('merchant_id', auth()->user()->merchant_id);
    //     }

    //     $payouts = $query->get();

    //     return view('mfs.batch_details', compact('payouts', 'batchId'));
    // }
     public function batchDetails($batchId)
    {
        $query = MFSPayout::where('batch_id', $batchId);
        $merchantIds = auth()->user()->getMerchantIds();
        if (!empty($merchantIds)) {
            $query->whereIn('merchant_id', $merchantIds);
        }

        $payouts = $query->get();

        // If it's an AJAX request (from the modal), return JSON
        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'batchId' => $batchId,
                'payouts' => $payouts
            ]);
        }

        // Otherwise, return the regular view (your existing functionality)
        return view('mfs.batch_details', compact('payouts', 'batchId'));
    }
    public function showStatusUploadForm()
    {
        return view('mfs.status_upload');
    }
    // public function showAll()
    // {
    //     if (auth()->user()->role === 'merchant') {
    //         // If merchant, fetch only their payouts
    //         $payouts = MFSPayout::where('merchant_id', auth()->user()->merchant_id)
    //                             ->latest()
    //                             ->get();
    //     } else {
    //         // Admin can see all payouts
    //         $payouts = MFSPayout::latest()->get();
    //     }
    //     return view('mfs.all', compact('payouts'));
    // }
    public function showAll(Request $request)
    {
        $request->validate([
            'start_date'  => 'nullable|date',
            'end_date'    => 'nullable|date',
            'status'      => 'nullable|in:all,Pending,Success,Failed',
            'merchant_id' => 'nullable|string',
        ]);

        $q = MFSPayout::query();
        $merchantIds = auth()->user()->getMerchantIds();

        // Role-based merchant scoping (merchant or agent)
        if (!empty($merchantIds)) {
            $q->whereIn('merchant_id', $merchantIds);
            if ($request->filled('merchant_id') && $request->merchant_id !== 'all' && in_array($request->merchant_id, $merchantIds)) {
                $q->where('merchant_id', $request->merchant_id);
                $merchantFilter = $request->merchant_id;
            } else {
                $merchantFilter = 'all';
            }
            $merchants = collect($merchantIds);
        } else {
            // Admins can filter by merchant or see all
            if ($request->filled('merchant_id') && $request->merchant_id !== 'all') {
                $q->where('merchant_id', $request->merchant_id);
                $merchantFilter = $request->merchant_id;
            } else {
                $merchantFilter = 'all';
            }
            $merchants = MFSPayout::select('merchant_id')->distinct()->orderBy('merchant_id')->pluck('merchant_id');
        }

        // Status filter (All by default)
        if ($request->filled('status') && $request->status !== 'all') {
            $q->where('status', $request->status);
        }

        // Date range (inclusive)
        if ($request->filled('start_date') || $request->filled('end_date')) {
            $start = $request->filled('start_date')
                ? Carbon::parse($request->start_date)->startOfDay()
                : Carbon::create(1970, 1, 1)->startOfDay();

            $end = $request->filled('end_date')
                ? Carbon::parse($request->end_date)->endOfDay()
                : Carbon::now()->endOfDay();

            if ($end->lt($start)) { [$start, $end] = [$end, $start]; } // swap if needed
            $q->whereBetween('created_at', [$start, $end]);
        }

        // Newest first
        $payouts = $q->orderBy('created_at', 'desc')->get();

        // Pass filters to the view
        return view('mfs.all', [
            'payouts'     => $payouts,
            'merchants'   => $merchants,
            'filter'      => [
                'start_date'  => $request->start_date,
                'end_date'    => $request->end_date,
                'merchant_id' => $merchantFilter ?? ($request->merchant_id ?? 'all'),
                'status'      => $request->status ?? 'all',
            ],
        ]);
    }
    
    // new function
    
    // ---------- Helpers ----------
    protected function debitRemark(MFSPayout $p)
    {
        return "MFS Payout Deduction | Batch: {$p->batch_id}, Ref: {$p->reference_key}";
    }
    protected function creditRemark(MFSPayout $p)
    {
        return "Reversal of MFS Payout Deduction | Batch: {$p->batch_id}, Ref: {$p->reference_key}";
    }
    protected function ensureDebitIfMissing(MFSPayout $p)
    {
        $exists = MerchantBalance::where('merchant_id', $p->merchant_id)
            ->where('type', 'debit')
            ->where('remarks', $this->debitRemark($p))
            ->exists();

        if (!$exists) {
            MerchantBalance::create([
                'merchant_id' => $p->merchant_id,
                'type'        => 'debit',
                'amount'      => $p->amount,
                'remarks'     => $this->debitRemark($p),
            ]);
        }
    }
    protected function ensureCreditIfMissing(MFSPayout $p)
    {
        // Only needed if a debit happened earlier
        $hadDebit = MerchantBalance::where('merchant_id', $p->merchant_id)
            ->where('type', 'debit')
            ->where('remarks', $this->debitRemark($p))
            ->exists();

        if ($hadDebit) {
            $alreadyCredited = MerchantBalance::where('merchant_id', $p->merchant_id)
                ->where('type', 'credit')
                ->where('remarks', $this->creditRemark($p))
                ->exists();

            if (!$alreadyCredited) {
                MerchantBalance::create([
                    'merchant_id' => $p->merchant_id,
                    'type'        => 'credit',
                    'amount'      => $p->amount,
                    'remarks'     => $this->creditRemark($p),
                ]);
            }
        }
    }
    protected function applyStatusWithAccounting(MFSPayout $payout, string $newStatus)
    {
        $old = $payout->status;

        if ($newStatus === 'Success') {
            // charge (idempotent)
            $this->ensureDebitIfMissing($payout);
        } elseif ($newStatus === 'Failed') {
            // if previously Success, reverse with a credit (idempotent)
            if ($old === 'Success') {
                $this->ensureCreditIfMissing($payout);
            }
        }
        // Save new status
        $payout->status = $newStatus;
        $payout->save();
    }

    /**
     * Apply only balance (debit/credit) logic for a status change. Used when payout
     * has already been updated (e.g. by AgentPaymentRequestController) with status,
     * completed_at, agent_id, etc. Does not modify the payout model.
     */
    public function applyBalanceForStatus(MFSPayout $payout, string $newStatus, string $oldStatus): void
    {
        if ($newStatus === 'Success') {
            $this->ensureDebitIfMissing($payout);
        } elseif ($newStatus === 'Failed' && $oldStatus === 'Success') {
            $this->ensureCreditIfMissing($payout);
        }
    }

    // ---------- Single update ----------
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:Success,Failed'
        ]);

        $p = MFSPayout::findOrFail($id);

        DB::transaction(function () use ($p, $request) {
            $this->applyStatusWithAccounting($p, $request->status);
        });

        // notify per payout (or group later if you prefer)
        MFSPayoutWebhookService::notify($p->batch_id, $p->merchant_id);

        if ($request->expectsJson()) {
            return response()->json(['message' => "Updated to {$request->status}"]);
        }
        return back()->with('success', "Payout {$p->reference_key} marked as {$request->status}.");
    }

    // ---------- Bulk update ----------
    public function bulkUpdateStatus(Request $request)
    {
        $request->validate([
            'ids'    => 'required|array|min:1',
            'ids.*'  => 'integer|exists:mfs_payouts,id',
            'status' => 'required|in:Success,Failed',
        ]);

        $payouts = MFSPayout::whereIn('id', $request->ids)->get();

        DB::transaction(function () use ($payouts, $request) {
            foreach ($payouts as $p) {
                $this->applyStatusWithAccounting($p, $request->status);
            }
        });

        // Group webhooks per (batch_id, merchant_id)
        $pairs = $payouts->map(fn($p) => $p->batch_id.'|'.$p->merchant_id)->unique();
        foreach ($pairs as $pair) {
            [$batch, $merchant] = explode('|', $pair);
            MFSPayoutWebhookService::notify($batch, $merchant);
        }

        return response()->json([
            'message' => "Updated {$payouts->count()} payout(s) to {$request->status}."
        ]);
    }

    // ---------- Delete single ----------
    public function destroy(Request $request, $id)
    {
        $p = MFSPayout::findOrFail($id);

        DB::transaction(function () use ($p) {
            // If it was Success, add a credit reversal (idempotent)
            if ($p->status === 'Success') {
                $this->ensureCreditIfMissing($p);
            }
            $p->delete();
        });

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Deleted']);
        }
        return back()->with('success', 'Payout deleted');
    }

    // ---------- Bulk delete ----------
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids'   => 'required|array|min:1',
            'ids.*' => 'integer|exists:mfs_payouts,id',
        ]);

        $payouts = MFSPayout::whereIn('id', $request->ids)->get();

        DB::transaction(function () use ($payouts) {
            foreach ($payouts as $p) {
                if ($p->status === 'Success') {
                    $this->ensureCreditIfMissing($p);
                }
                $p->delete();
            }
        });

        return response()->json(['message' => "Deleted {$payouts->count()} payout(s)."]);
    }
    
    //payout report controller

    public function payoutSummary(Request $request)
    {
        $request->validate([
            'merchant_id' => 'nullable|string',   // 'all' or specific merchant_id
            'start_date'  => 'nullable|date',
            'end_date'    => 'nullable|date',
        ]);

        // Merchant list for dropdown
        // Prefer merchants table if you have it, otherwise derive from balances/payouts
        // $merchants = Merchant::orderBy('merchant_id')->pluck('merchant_id');
        $merchants = MerchantBalance::select('merchant_id')->distinct()->orderBy('merchant_id')->pluck('merchant_id');

        // Role-based scoping (merchant or agent)
        $merchantIds = auth()->user()->getMerchantIds();
        if (!empty($merchantIds)) {
            $merchantId = $request->input('merchant_id', count($merchantIds) === 1 ? $merchantIds[0] : 'all');
            if ($merchantId !== 'all' && !in_array($merchantId, $merchantIds)) {
                $merchantId = count($merchantIds) === 1 ? $merchantIds[0] : 'all';
            }
            $merchants = collect($merchantIds);
        } else {
            $merchantId = $request->input('merchant_id', 'all');
        }

        // Date range (inclusive)
        $start = $request->filled('start_date') ? Carbon::parse($request->start_date)->startOfDay() : null;
        $end   = $request->filled('end_date')   ? Carbon::parse($request->end_date)->endOfDay()   : null;
        if ($start && $end && $end->lt($start)) {
            [$start, $end] = [$end, $start];
        }

        // --- Base scopes
        $balScope = MerchantBalance::query();
        $payScope = MFSPayout::query();

        if (!empty($merchantIds)) {
            if ($merchantId !== 'all') {
                $balScope->where('merchant_id', $merchantId);
                $payScope->where('merchant_id', $merchantId);
            } else {
                $balScope->whereIn('merchant_id', $merchantIds);
                $payScope->whereIn('merchant_id', $merchantIds);
            }
        } elseif ($merchantId !== 'all') {
            $balScope->where('merchant_id', $merchantId);
            $payScope->where('merchant_id', $merchantId);
        }

        if ($start) {
            $balScope->where('created_at', '>=', $start);
            $payScope->where('created_at', '>=', $start);
        }
        if ($end) {
            $balScope->where('created_at', '<=', $end);
            $payScope->where('created_at', '<=', $end);
        }

        // --- Totals in range
        $totalCredit = (clone $balScope)->where('type', 'credit')->sum('amount');
        $totalDebit  = (clone $balScope)->where('type', 'debit')->sum('amount');

        $successAmt  = (clone $payScope)->where('status', 'Success')->sum('amount');
        $failedAmt   = (clone $payScope)->where('status', 'Failed')->sum('amount');
        $pendingAmt  = (clone $payScope)->where('status', 'Pending')->sum('amount');

        // --- Available balance (all-time, not limited by date range)
        $availScope = MerchantBalance::query();
        if (!empty($merchantIds)) {
            if ($merchantId !== 'all') {
                $availScope->where('merchant_id', $merchantId);
            } else {
                $availScope->whereIn('merchant_id', $merchantIds);
            }
        } elseif ($merchantId !== 'all') {
            $availScope->where('merchant_id', $merchantId);
        }
        $creditedAll = (clone $availScope)->where('type', 'credit')->sum('amount');
        $debitedAll  = (clone $availScope)->where('type', 'debit')->sum('amount');
        $availableBalance = $creditedAll - $debitedAll;

        // --- Payout details table (in range)
        $payouts = $payScope->latest('created_at')->get();

        return view('mfs.payout_summary', [
            'merchants' => $merchants,
            'filters' => [
                'merchant_id' => $merchantId,
                'start_date'  => $request->input('start_date', ''),
                'end_date'    => $request->input('end_date', ''),
            ],
            'totals' => [
                'credit'      => $totalCredit,
                'debit'       => $totalDebit,
                'success'     => $successAmt,
                'failed'      => $failedAmt,
                'pending'     => $pendingAmt,
                'available'   => $availableBalance, // all-time
            ],
            'payouts' => $payouts,
        ]);
    }
    

}

