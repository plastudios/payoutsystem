<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Merchant;
use App\Models\MerchantBalance;
use App\Models\BalanceHistory;
use App\Models\Payout;
use Carbon\Carbon;


class BalanceController extends Controller
{
    // public function index()
    // {
    //     $merchants = Merchant::all();
    //     $histories = MerchantBalance::latest()->get();

    //     // Compute current balance per merchant
    //     $merchantBalances = [];

    //     foreach ($merchants as $merchant) {
    //         $creditDebit = MerchantBalance::where('merchant_id', $merchant->merchant_id)->get()
    //             ->sum(function ($entry) {
    //                 return $entry->type === 'credit' ? $entry->amount : -$entry->amount;
    //             });

    //         $payoutUsed = \App\Models\Payout::where('merchant_id', $merchant->merchant_id)
    //             ->where('status', 'Success')
    //             ->sum('amount');

    //         $merchantBalances[$merchant->merchant_id] = $creditDebit - $payoutUsed;
    //     }

    //     return view('balance.index', compact('merchants', 'histories', 'merchantBalances'));
    // }
    public function showAll(Request $request)
    {
        $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'merchant_id' => 'nullable|string',
        ]);

        $user = auth()->user();
        $q = MerchantBalance::query();
        $merchantIds = $user->getMerchantIds();

        // Merchant/agent filter - scope to their merchant(s)
        if (!empty($merchantIds)) {
            $q->whereIn('merchant_id', $merchantIds);
            $merchantId = count($merchantIds) === 1 ? $merchantIds[0] : ($request->input('merchant_id') ?: 'all');
        } else {
            // For admin/other roles, allow filtering
            $merchantId = $request->input('merchant_id');
            if ($merchantId && $merchantId !== 'all') {
                $q->where('merchant_id', $merchantId);
            }
        }
        if (empty($merchantIds) && !isset($merchantId)) {
            $merchantId = $request->input('merchant_id');
        }

        // Date range (inclusive)
        $start = $request->filled('start_date') ? Carbon::parse($request->start_date)->startOfDay() : null;
        $end = $request->filled('end_date') ? Carbon::parse($request->end_date)->endOfDay() : null;

        if ($start && $end) {
            // if user swapped dates, fix it
            if ($end->lt($start))
                [$start, $end] = [$end, $start];
            $q->whereBetween('created_at', [$start, $end]);
        } elseif ($start) {
            $q->where('created_at', '>=', $start);
        } elseif ($end) {
            $q->where('created_at', '<=', $end);
        }

        // Always newest first by id
        $balances = $q->orderBy('id', 'desc')->get();

        // Merchant list for dropdown
        // If you have a merchants table, prefer that:
        // $merchants = Merchant::orderBy('merchant_id')->pluck('merchant_id');
        // Otherwise, build from balance history:
        $merchants = MerchantBalance::select('merchant_id')->distinct()->orderBy('merchant_id')->pluck('merchant_id');

        return view('admin.balance_history', [
            'balances' => $balances,
            'merchants' => $merchants,
            'merchantId' => $merchantId,
            'start' => $request->start_date,
            'end' => $request->end_date,
            'isMerchant' => in_array($user->role, ['merchant', 'agent']), // Pass this to the view
        ]);
    }
    public function index()
    {
        $merchants = Merchant::all();

        // Get total credits and debits per merchant from merchant_balances table
        $credits = MerchantBalance::where('type', 'credit')
            ->selectRaw('merchant_id, SUM(amount) as total_credit')
            ->groupBy('merchant_id')
            ->pluck('total_credit', 'merchant_id');

        $debits = MerchantBalance::where('type', 'debit')
            ->selectRaw('merchant_id, SUM(amount) as total_debit')
            ->groupBy('merchant_id')
            ->pluck('total_debit', 'merchant_id');

        // Prepare summary from only merchant_balances table
        $summary = [];

        foreach ($merchants as $merchant) {
            $id = $merchant->merchant_id;

            $credit = $credits[$id] ?? 0;
            $debit = $debits[$id] ?? 0;
            $available = $credit - $debit;

            $summary[] = [
                'merchant_id' => $id,
                'company_name' => $merchant->company_name,
                'credit' => $credit,
                'debit' => $debit,
                'allocated' => $credit,
                'available' => $available,
            ];
        }

        return view('balance.index', compact('summary', 'merchants'));
    }

    public function BalanceDetails(Request $request)
    {
        $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
        ]);

        $user = auth()->user();
        $merchantIds = $user->getMerchantIds();

        // Build query based on user role
        if (!empty($merchantIds)) {
            $q = MerchantBalance::whereIn('merchant_id', $merchantIds);
        } else {
            // Admins or others can see all data
            $q = MerchantBalance::query();
        }

        // Date range filtering (inclusive)
        $start = $request->filled('start_date') ? Carbon::parse($request->start_date)->startOfDay() : null;
        $end = $request->filled('end_date') ? Carbon::parse($request->end_date)->endOfDay() : null;

        if ($start && $end) {
            // if user swapped dates, fix it
            if ($end->lt($start))
                [$start, $end] = [$end, $start];
            $q->whereBetween('created_at', [$start, $end]);
        } elseif ($start) {
            $q->where('created_at', '>=', $start);
        } elseif ($end) {
            $q->where('created_at', '<=', $end);
        }

        $balances = $q->latest()->get();

        return view('merchants.balance_details', [
            'balances' => $balances,
            'start' => $request->start_date,
            'end' => $request->end_date,
        ]);
    }







    public function storeBalance(Request $request)
    {
        $request->validate([
            'merchant_id' => 'required|exists:merchants,merchant_id',
            'type' => 'required|in:credit,debit',
            'amount' => 'required|numeric|min:0.01',
            'remarks' => 'nullable|string',
            'payout_charge' => 'nullable|numeric|min:0|max:100',
        ]);

        $merchantId = $request->merchant_id;
        $type = $request->type;
        $amount = $request->amount;
        $payoutCharge = $request->payout_charge ?? 0;

        if ($type == 'debit') {
            $entries = MerchantBalance::where('merchant_id', $merchantId)->get();

            $totalBalance = $entries->sum(function ($entry) {
                return $entry->type === 'credit' ? $entry->amount : -$entry->amount;
            });

            if ($amount > $totalBalance) {
                return back()->with('error', "Insufficient balance. Available: " . number_format($totalBalance, 2));
            }
        }

        // Create the main credit/debit entry
        MerchantBalance::create([
            'merchant_id' => $merchantId,
            'type' => $type,
            'amount' => $amount,
            'remarks' => $request->remarks,
        ]);

        // If credit with payout charge, create a debit entry for the charge
        if ($type === 'credit' && $payoutCharge > 0) {
            $chargeAmount = round($amount * ($payoutCharge / 100), 2);

            MerchantBalance::create([
                'merchant_id' => $merchantId,
                'type' => 'debit',
                'amount' => $chargeAmount,
                'remarks' => "Payout Charge ({$payoutCharge}%)",
            ]);

            return back()->with('success', "Balance credited: " . number_format($amount, 2) . ". Payout charge deducted: " . number_format($chargeAmount, 2));
        }

        return back()->with('success', 'Balance updated successfully.');
    }

}
