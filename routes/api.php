<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BillController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\DailyEntryController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\ReportController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
| Token-based (Sanctum) API for the mobile / delivery-boy app and any
| third-party integrations. Every route below (except login) requires a
| Bearer token and is additionally gated by the same Spatie permission
| names used on the web side, checked inline in each controller.
*/

Route::prefix('v1')->group(function () {
    Route::post('login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('me', [AuthController::class, 'me']);

        Route::apiResource('customers', CustomerController::class)->only(['index', 'show']);

        Route::get('daily-entries', [DailyEntryController::class, 'index']);
        Route::post('daily-entries/bulk-save', [DailyEntryController::class, 'bulkSave']);

        Route::apiResource('bills', BillController::class)->only(['index', 'show']);
        Route::post('bills/generate', [BillController::class, 'generate']);

        Route::apiResource('payments', PaymentController::class)->only(['index', 'show', 'store']);

        Route::prefix('reports')->group(function () {
            Route::get('outstanding', [ReportController::class, 'outstanding']);
            Route::get('profit-loss', [ReportController::class, 'profitLoss']);
            Route::get('milk-summary', [ReportController::class, 'milkSummary']);
        });
    });
});
