<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\FIController;
use App\Http\Controllers\PayoutController;
use App\Http\Controllers\MerchantController;
use App\Http\Controllers\BalanceController;
use App\Http\Controllers\Api\PayoutApiController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MerchantSettingsController;
use App\Http\Controllers\MFSPayoutController;


use App\Models\Payout;
use App\Models\Merchant;
use App\Models\MerchantBalance;
use App\Models\WebhookLog;
use App\Models\MFSPayout;

Route::get('/', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);


Route::middleware(['auth', 'nocache'])->group(function () {

    Route::get('/dashboard', [AuthController::class, 'dashboard'])->middleware('auth');

    Route::post('/logout', function () {
        Auth::logout();
        return redirect('/');
    });
    // Route::get('/dashboard', function () {
//     $user = Auth::user();
//     if ($user->role === 'merchant') {
//         $successCount = Payout::where('merchant_id', $user->merchant_id)->where('status', 'Success')->count();
//         $failedCount = Payout::where('merchant_id', $user->merchant_id)->where('status', 'Failed')->count();
//         $successAmount = Payout::where('merchant_id', $user->merchant_id)->where('status', 'Success')->sum('amount');
//         $failedAmount = Payout::where('merchant_id', $user->merchant_id)->where('status', 'Failed')->sum('amount');
//         // 🟢 Calculate Available Balance
//         $credit = MerchantBalance::where('merchant_id', $user->merchant_id)
//                     ->where('type', 'credit')->sum('amount');
//         $debit = MerchantBalance::where('merchant_id', $user->merchant_id)
//                     ->where('type', 'debit')->sum('amount');
//         $availableBalance = $credit - $debit;

    //         return view('dashboard', compact('successCount', 'failedCount', 'successAmount', 'failedAmount','availableBalance','credit','debit'));
//     }
//     $successCount = Payout::where('status', 'Success')->count();
//     $failedCount = Payout::where('status', 'Failed')->count();
//     $successAmount = Payout::where('status', 'Success')->sum('amount');
//     $failedAmount = Payout::where('status', 'Failed')->sum('amount');
//     $totalMerchants = Merchant::count();
//     $totalAllocated = MerchantBalance::where('type', 'credit')->sum('amount');

    //     return view('dashboard', compact('successCount', 'failedCount', 'successAmount', 'failedAmount','totalMerchants','totalAllocated'));
// })->middleware('auth');

    Route::get('/dashboard', function () {
        $user = Auth::user();

        if ($user->role === 'merchant') {
            $merchantId = $user->merchant_id;

            $successCount = Payout::where('merchant_id', $merchantId)->where('status', 'Success')->count();
            $failedCount = Payout::where('merchant_id', $merchantId)->where('status', 'Failed')->count();
            $successAmount = Payout::where('merchant_id', $merchantId)->where('status', 'Success')->sum('amount');
            $failedAmount = Payout::where('merchant_id', $merchantId)->where('status', 'Failed')->sum('amount');

            $credit = MerchantBalance::where('merchant_id', $merchantId)->where('type', 'credit')->sum('amount');
            $debit = MerchantBalance::where('merchant_id', $merchantId)->where('type', 'debit')->sum('amount');
            $availableBalance = $credit - $debit;
            $MFSsuccessAmount = MFSPayout::where('status', 'Success')->sum('amount');
            $successAmount = $successAmount + $MFSsuccessAmount;
            $mfsSuccessCount = MFSPayout::where('merchant_id', $merchantId)->where('status', 'Success')->count();
            $mfsFailedCount = MFSPayout::where('merchant_id', $merchantId)->where('status', 'Failed')->count();

            return view('dashboard', compact(
                'successCount',
                'failedCount',
                'successAmount',
                'failedAmount',
                'availableBalance',
                'credit',
                'debit',
                'mfsSuccessCount',
                'mfsFailedCount'
            ));
        }

        // Admin dashboard data
        $successCount = Payout::where('status', 'Success')->count();
        $failedCount = Payout::where('status', 'Failed')->count();
        $successAmount = Payout::where('status', 'Success')->sum('amount');
        $failedAmount = Payout::where('status', 'Failed')->sum('amount');
        $totalMerchants = Merchant::count();
        $totalAllocated = MerchantBalance::where('type', 'credit')->sum('amount');

        $MFSsuccessAmount = MFSPayout::where('status', 'Success')->sum('amount');
        $successAmount = $successAmount + $MFSsuccessAmount;
        $mfsSuccessCount = MFSPayout::where('status', 'Success')->count();
        $mfsFailedCount = MFSPayout::where('status', 'Failed')->count();

        return view('dashboard', compact(
            'successCount',
            'failedCount',
            'successAmount',
            'failedAmount',
            'totalMerchants',
            'totalAllocated',
            'mfsSuccessCount',
            'mfsFailedCount'
        ));
    })->middleware('auth');


    Route::middleware(['auth', 'role:admin'])->get('/admin/webhook-logs', function () {
        $logs = WebhookLog::latest()->get(); // No pagination since you're using DataTables
        return view('admin.webhook_logs', compact('logs'));
    })->name('admin.webhook.logs');




    Route::middleware(['auth', 'role:checker,admin'])->group(function () {
        Route::get('/payouts/check', [PayoutController::class, 'showForChecker'])->name('payouts.to_check');
        Route::post('/payouts/check/{batch_id}', [PayoutController::class, 'markChecked'])->name('payouts.mark_checked');
    });

    Route::middleware(['auth', 'role:maker,admin'])->group(function () {
        Route::get('/payouts/approve', [PayoutController::class, 'showForMaker'])->name('payouts.to_approve');
        Route::post('/payouts/approve/{batch_id}', [PayoutController::class, 'approveAndProcess'])->name('payouts.approve_process');
    });

    Route::middleware('auth:api')->group(function () {
        Route::post('/merchant/payouts', [PayoutApiController::class, 'bulkCreate']);
    });

    Route::middleware(['auth'])->group(function () {
        Route::get('/admin/create-user', [UserController::class, 'create'])->name('admin.user.create');
        Route::post('/admin/store-user', [UserController::class, 'store'])->name('admin.user.store');
    });
    Route::get('/admin/users', [UserController::class, 'index'])->name('admin.users')->middleware('auth');

    Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {

        // User list
        Route::get('/users', [UserController::class, 'user_index'])->name('users.index');

        // Create user
        Route::get('/users/create', [UserController::class, 'user_create'])->name('users.create');
        Route::post('/users/store', [UserController::class, 'user_store'])->name('users.store');

        // Edit user
        Route::get('/users/{id}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('/users/{id}', [UserController::class, 'update'])->name('users.update');

        // Delete user
        Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('users.destroy');

        // Update password
        Route::get('/users/{id}/change-password', [UserController::class, 'showChangePasswordForm'])->name('users.change_password');
        Route::put('/users/{id}/update-password', [UserController::class, 'updatePassword'])->name('users.update_password');
    });

    //route group

    Route::middleware(['auth', 'role:admin,author,checker,maker'])->group(function () {
        Route::get('/fi/list', [FIController::class, 'showFiList']);
        Route::get('/fi/fetch', [FIController::class, 'fetchAndStoreFi']);

        Route::get('/payout/create', [PayoutController::class, 'uploadForm']);
        Route::post('/payout/upload', [PayoutController::class, 'processExcel']);


        Route::get('/merchants', [MerchantController::class, 'index']);
        Route::get('/merchants/create', [MerchantController::class, 'create']);
        Route::post('/merchants', [MerchantController::class, 'store']);
        Route::get('/merchant/balance', [BalanceController::class, 'index'])->name('merchant.balance');
        Route::post('/merchant/balance/store', [BalanceController::class, 'storeBalance'])->name('merchant.balance.store');
        Route::post('/payout/reject/{batchId}', [PayoutController::class, 'rejectBatch'])->name('payout.reject');
        Route::get('/merchant-balances', [BalanceController::class, 'showAll'])->name('merchant.balances');
        //mfs route


        Route::get('/mfs-payout/export-submit', [MFSPayoutController::class, 'submitExport'])->name('mfs.export.submit');
        Route::get('/mfs-payout/export/{batchId}', [MFSPayoutController::class, 'export'])->name('mfs.export');
        Route::post('/mfs-payout/status-upload', [MFSPayoutController::class, 'updateStatusFromExcel'])->name('mfs.status.update');

        Route::get('/mfs-payout/ExportBatches', [MFSPayoutController::class, 'showExportBatches'])->name('mfs.export.batches');

        Route::get('/mfs-payout/status-upload', [MFSPayoutController::class, 'showStatusUploadForm'])->name('mfs.status.upload.form');

        //new route
        // Single status update
        Route::post('/mfs-payout/{id}/update-status', [MFSPayoutController::class, 'updateStatus'])
            ->name('mfs.update.status');

        // Bulk status update
        Route::post('/mfs-payout/bulk-update-status', [MFSPayoutController::class, 'bulkUpdateStatus'])
            ->name('mfs.bulk.update.status');

        // Single delete
        Route::delete('/mfs-payout/{id}', [MFSPayoutController::class, 'destroy'])
            ->name('mfs.delete');

        // Bulk delete
        Route::post('/mfs-payout/bulk-delete', [MFSPayoutController::class, 'bulkDelete'])
            ->name('mfs.bulk.delete');
        Route::get('/mfs-payout/payout-summary', [MFSPayoutController::class, 'payoutSummary'])
            ->name('mfs.payout.summary');
        //new route end
    });

    Route::middleware(['auth', 'role:admin,author,checker,maker,merchant'])->group(function () {
        // mfs route
        Route::get('/mfs-payout/batch/{batchId}', [MFSPayoutController::class, 'batchDetails'])->name('mfs.batch.details');
        Route::get('/mfs-payout/batches', [MFSPayoutController::class, 'showBatchSummary'])->name('mfs.batches');
        Route::get('/mfs-payout/all', [MFSPayoutController::class, 'showAll'])->name('mfs.all');
        Route::post('/mfs-payout/upload', [MFSPayoutController::class, 'processUpload'])->name('mfs.upload.process');
        Route::get('/mfs-payout/upload', [MFSPayoutController::class, 'uploadForm'])->name('mfs.upload.form');
        Route::get('/payout/batches', [PayoutController::class, 'batchList'])->name('payout.batches');
        Route::get('/payout/batch/{batchId}', [PayoutController::class, 'batchDetails'])->name('payout.batch.details');
        Route::get('/payout/details', [PayoutController::class, 'details']);
        Route::get('/payout/report', [PayoutController::class, 'report']);
        Route::post('/payout/report', [PayoutController::class, 'reportSearch']);
        Route::get('/payout/create', [PayoutController::class, 'uploadForm']);
        Route::post('/payout/upload', [PayoutController::class, 'processExcel']);
        Route::get('/merchant/balance/details', [BalanceController::class, 'BalanceDetails']);
        Route::get('/webhook', [MerchantSettingsController::class, 'editWebhook'])->name('merchant.webhook.edit');
        Route::post('/webhook', [MerchantSettingsController::class, 'updateWebhook'])->name('merchant.webhook.update');
        Route::get('/webhook-logs', [MerchantSettingsController::class, 'viewWebhookLogs'])->name('merchant.webhook.logs');
    });

    Route::middleware(['auth', 'role:merchant'])->group(function () {


    });


});
// Route::post('/merchant/login', [MerchantAuthController::class, 'login']);


