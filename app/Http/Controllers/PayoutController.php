<?php

namespace App\Http\Controllers;

use App\Services\PayoutWebhookService;

use App\Mail\PayoutBatchCreated;
use Illuminate\Support\Facades\Mail;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Payout;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Http;
use App\Models\Merchant;
use App\Models\MerchantBalance;
use App\Models\BalanceHistory;

class PayoutController extends Controller
{
    public function uploadForm()
    {
        $merchants = Merchant::all(); 
        return view('payout_upload', compact('merchants'));
    }
    
    public function dashboard()
    {
        $successCount = Payout::where('status', 'Success')->count();
        $failedCount = Payout::where('status', 'Failed')->count();

        $successAmount = Payout::where('status', 'Success')->sum('amount');
        $failedAmount = Payout::where('status', 'Failed')->sum('amount');

        //Get merchant-wise total amounts
        $merchantData = Payout::select('merchant_id')
            ->selectRaw('SUM(amount) as total_amount')
            ->groupBy('merchant_id')
            ->orderBy('total_amount', 'desc')
            ->get();
        // New data
        $totalMerchants = Merchant::count();

        $totalAllocated = MerchantBalance::where('type', 'credit')->sum('amount');

        return view('dashboard', compact(
            'successCount',
            'failedCount',
            'successAmount',
            'failedAmount',
            'merchantData',
            'totalMerchants',
            'totalAllocated'
        ));

        // return view('dashboard', compact('successCount', 'failedCount', 'successAmount', 'failedAmount'));
    }

    private function getAuthToken()
    {
        $authPayload = [
            "instituteCode" => "AAPAY",
            "token" => "acdc103c96e7de7849e4c2d40254b0ec4299ee36fb9d48c52983a0cb2dd9845c"
        ];

        $authResponse = Http::post('https://sandbox.aamarpay.com/mghnabank/authenticate.php', $authPayload);

        $json = $authResponse->json();

        if (isset($json['auth_token'])) {
            return $json['auth_token'];
        }

        return null;
    }
    public function processExcel(Request $request)
    {
        $request->validate([
            'payout_file' => 'required|mimes:xlsx,xls',
            'merchant_id' => 'required|exists:merchants,merchant_id',
        ]);
    
        $merchantId = auth()->user()->role === 'merchant'
        ? auth()->user()->merchant_id
        : $request->merchant_id;

        $batchId = 'BATCH-' . strtoupper(Str::random(10));
        $data = Excel::toArray([], $request->file('payout_file'));
    
        // Calculate total payout request amount
        $totalRequestAmount = 0;
        foreach ($data[0] as $index => $row) {
            if ($index == 0) continue;
            $totalRequestAmount += floatval($row[0]);
        }
    
        $credited = MerchantBalance::where('merchant_id', $merchantId)->where('type', 'credit')->sum('amount');
        $debited = MerchantBalance::where('merchant_id', $merchantId)->where('type', 'debit')->sum('amount');
        $availableBalance = $credited - $debited;
    
        if ($totalRequestAmount > $availableBalance) {
            return back()->with('error', "Insufficient balance. Payout request amount: " . number_format($totalRequestAmount, 2) . ", available balance: " . number_format($availableBalance, 2));
        }
    
        foreach ($data[0] as $index => $row) {
            if ($index == 0) continue;
    
            Payout::create([
                'batch_id' => $batchId,
                'referenceKey' => 'AP-' . strtoupper(Str::random(15)),
                'amount' => floatval($row[0]),
                'currency' => $row[1],
                'remarks' => $row[2],
                'bankCode' => $row[3],
                'bankShortCode' => $row[4],
                'benType' => $row[5],
                'txnChannel' => $row[6],
                'beneficiaryAcc' => $row[7],
                'beneficiaryName' => $row[8],
                'beneficiaryEmail' => $row[9],
                'routingNumber' => $row[10],
                'txnChannelCode' => $row[11],
                'merchant_id' => $merchantId,
                'status' => 'Pending',
                'approval_status' => 'pending',
            ]);
        }
        // 🔔 Send webhook to merchant with batch info
        PayoutWebhookService::notify($batchId, $merchantId);

                // get payouts for the batch
        $payouts = Payout::where('batch_id', $batchId)->get();
        $totalAmount = $payouts->sum('amount');
        $totalCount = $payouts->count();

        // send email
        // queue instead of send
        Mail::to(['imtiazakil@gmail.com', 'imtiaz@aamarpay.com'])
        ->queue(new PayoutBatchCreated($batchId, $merchantId, $totalAmount, $totalCount));

        return back()->with('success', 'Excel uploaded for approval with Batch ID: ' . $batchId);
    }

    public function details()
    {
        $user = auth()->user();

        if ($user->role === 'merchant') {
            // Show only payouts belonging to the logged-in merchant
            $payouts = Payout::where('merchant_id', $user->merchant_id)
                            ->latest()
                            ->get();
        } else {
            // Admins, checkers, authors, etc. can see all payouts
            $payouts = Payout::latest()->get();
        }

        return view('payout_details', compact('payouts'));
    }

    public function report()
    {
        return view('payout_report');
    }

    // public function reportSearch(Request $request)
    // {
    //     $request->validate([
    //         'from_date' => 'required|date',
    //         'to_date' => 'required|date|after_or_equal:from_date',
    //     ]);

    //     $from = $request->from_date . ' 00:00:00';
    //     $to = $request->to_date . ' 23:59:59';

    //     $payouts = Payout::whereBetween('created_at', [$from, $to])
    //         ->orderBy('created_at', 'desc')
    //         ->get();

    //     return view('payout_report', [
    //         'payouts' => $payouts,
    //         'from' => $request->from_date,
    //         'to' => $request->to_date
    //     ]);
    // }

    public function reportSearch(Request $request)
    {
        $request->validate([
            'from_date' => 'required|date',
            'to_date' => 'required|date|after_or_equal:from_date',
        ]);

        $from = $request->from_date . ' 00:00:00';
        $to = $request->to_date . ' 23:59:59';
        $user = auth()->user();

        $query = Payout::whereBetween('created_at', [$from, $to]);

        // ✅ Filter for merchant
        if ($user->role === 'merchant') {
            $query->where('merchant_id', $user->merchant_id);
        }

        $payouts = $query->orderBy('created_at', 'desc')->get();

        return view('payout_report', [
            'payouts' => $payouts,
            'from' => $request->from_date,
            'to' => $request->to_date
        ]);
    }

    public function showForChecker()
    {
        $payouts = Payout::select('batch_id', 'merchant_id')
            ->where('approval_status', 'pending')
            ->selectRaw('SUM(amount) as amount, COUNT(*) as total_rows')
            ->groupBy('batch_id', 'merchant_id')
            ->get();

        return view('payouts.to_check', compact('payouts'));
    }

    public function markChecked($batchId)
    {
        Payout::where('batch_id', $batchId)
            ->where('approval_status', 'pending')
            ->update(['approval_status' => 'checked']);
    
        return back()->with('success', "Batch $batchId approved by checker.");
    }


    public function showForMaker()
    {
        $batchGroups = Payout::where('approval_status', 'checked')
            ->where('status', 'Pending')
            ->select('batch_id', 'merchant_id')
            ->groupBy('batch_id', 'merchant_id')
            ->get();
    
        $payouts = $batchGroups->map(function ($group) {
            $group->amount = Payout::where('batch_id', $group->batch_id)
                ->where('status', 'Pending')
                ->sum('amount');
    
            $group->count = Payout::where('batch_id', $group->batch_id)
                ->where('status', 'Pending')
                ->count();
    
            $group->status = 'checked';
            return $group;
        });
    
        return view('payouts.to_approve', compact('payouts'));
    }
    


    public function approveAndProcess($batchId)
    {
    $token = $this->getAuthToken();
    if (!$token) {
        return back()->with('error', 'Failed to fetch auth token.');
    }

    $payouts = Payout::where('batch_id', $batchId)->where('approval_status', 'checked')->get();

    foreach ($payouts as $payout) {
        $payload = [
            "token" => $token,
            "referenceKey" => $payout->referenceKey,
            "userName" => "AAPAY2MEGHNA",
            "companyCode" => "93",
            "sourceAccount" => "110111100000393",
            "clientPreference" => "CORPORATE",
            "sourceAccountId" => "505",
            "amount" => $payout->amount,
            "currency" => $payout->currency,
            "remarks" => $payout->remarks,
            "bankCode" => $payout->bankCode,
            "bankShortCode" => $payout->bankShortCode,
            "benType" => $payout->benType,
            "txnChannel" => $payout->txnChannel,
            "beneficiaryAcc" => $payout->beneficiaryAcc,
            "beneficiaryName" => $payout->beneficiaryName,
            "beneficiaryEmail" => $payout->beneficiaryEmail,
            "routingNumber" => $payout->routingNumber,
            "txnChannelCode" => $payout->txnChannelCode,
        ];

        $response = Http::post('https://sandbox.aamarpay.com/mghnabank/transfer_transaction.php', $payload);
        $json = $response->json();
        $status = $json['responseCode'] === '000' ? 'Success' : 'Failed';

        $payout->update([
            'status' => $status,
            'api_response' => json_encode($json),
            'approval_status' => $status === 'Success' ? 'approved' : 'failed',
        ]);

        if ($status === 'Success') {
            MerchantBalance::create([
                'merchant_id' => $payout->merchant_id,
                'type' => 'debit',
                'amount' => $payout->amount,
                'remarks' => "Bank Payout Deduction | Batch: {$batchId}, Ref: {$payout->referenceKey}",
            ]);
        }
    }
        // 🔔 Send webhook to merchant with batch info
       PayoutWebhookService::notify($batchId, $payout->merchant_id);

    return back()->with('success', 'Batch #' . $batchId . ' processed and updated.');
    }

    public function rejectBatch($batchId)
    {
        $payouts = Payout::where('batch_id', $batchId)
        ->whereIn('approval_status', ['pending', 'checked'])
        ->get();

        if ($payouts->isEmpty()) {
            return back()->with('error', "No pending/checked payouts found for batch $batchId.");
         }

        // Get merchant ID from one of the payouts
        $merchantId = $payouts->first()->merchant_id;
            Payout::where('batch_id', $batchId)
                ->whereIn('approval_status', ['pending', 'checked']) // allow both
                ->update([
                    'status' => 'Failed',
                    'approval_status' => 'rejected',
                ]);
                // 🔔 Send webhook after update
        PayoutWebhookService::notify($batchId, $merchantId);

        return back()->with('error', "Batch $batchId has been rejected.");
    }

    public function batchList()
    {
        $user = auth()->user();

        $query = Payout::query();

        if ($user->role === 'merchant') {
            $query->where('merchant_id', $user->merchant_id);
        }

        $batches = $query->select('merchant_id', 'batch_id')
            ->selectRaw('SUM(amount) as total_amount')
            ->selectRaw('COUNT(*) as total_count')
            ->selectRaw("SUM(CASE WHEN status = 'Pending' THEN amount ELSE 0 END) as pending_amount")
            ->selectRaw("SUM(CASE WHEN status = 'Success' THEN amount ELSE 0 END) as success_amount")
            ->selectRaw("SUM(CASE WHEN status = 'Failed' THEN amount ELSE 0 END) as failed_amount")
            ->groupBy('merchant_id', 'batch_id')
            ->orderByDesc('batch_id')
            ->get();

        return view('payouts.batch_list', compact('batches'));
    }

    public function batchDetails($batchId)
    {
        $user = auth()->user();

        $query = Payout::where('batch_id', $batchId);

        if ($user->role === 'merchant') {
            $query->where('merchant_id', $user->merchant_id);
        }

        $payouts = $query->get();

        // Retrieve merchant_id from the first payout
        $merchantId = $payouts->first()->merchant_id ?? null;

        return view('payouts.batch_details', compact('payouts', 'batchId', 'merchantId'));
    }


}
