<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\MerchantAuthController;
use App\Http\Controllers\Api\PayoutApiController;
use App\Http\Controllers\Api\MFSPayoutApiController;




Route::post('/merchant/token', [MerchantAuthController::class, 'token']);


Route::middleware('auth:sanctum')->group(function () {
    
    Route::post('/merchant/balance', [MerchantAuthController::class, 'fetchBalance']);

    Route::post('/merchant/payouts', [PayoutApiController::class, 'bulkCreate']);
    Route::get('/merchant/payouts/batch/{batch_id}', [PayoutApiController::class, 'fetchBatch']);
    Route::get('/merchant/payouts/reference/{referenceKey}', [PayoutApiController::class, 'getByReferenceKey']);

    Route::post('/merchant/mfs-payouts', [MFSPayoutApiController::class, 'bulkCreate']);
    Route::get('/merchant/mfs-payouts/batch/{batch_id}', [MFSPayoutApiController::class, 'fetchBatch']);
    Route::get('/merchant/mfs-payouts/reference/{referenceKey}', [MFSPayoutApiController::class, 'getByReferenceKey']);
});





