<?php

use App\Http\Controllers\Api\ProductApiController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// All API routes require API Key authentication
Route::middleware('api.key')->group(function () {
    // Products API
    Route::get('/products', [ProductApiController::class, 'index']);
    Route::get('/products/{id}', [ProductApiController::class, 'show']);

    // Orders API
    Route::get('/orders/{id}', [\App\Http\Controllers\Api\OrderApiController::class, 'show']);
    Route::post('/orders', [\App\Http\Controllers\Api\OrderApiController::class, 'store']);
    Route::put('/orders/{id}', [\App\Http\Controllers\Api\OrderApiController::class, 'update']);

    // Governorates API
    Route::get('/governorates', [\App\Http\Controllers\Api\GovernorateApiController::class, 'index']);
});

