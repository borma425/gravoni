<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MessengerWebhookController;

/*
|--------------------------------------------------------------------------
| Messenger Webhook Routes
|--------------------------------------------------------------------------
|
| هذه المسارات مخصصة لـ Facebook Messenger Webhook
| URL: https://yourdomain.com/api/webhook/messenger
|
*/

Route::prefix('webhook')->group(function () {
    // التحقق من الـ Webhook (Facebook يرسل GET request)
    Route::get('/messenger', [MessengerWebhookController::class, 'verify']);

    // استقبال الرسائل (Facebook يرسل POST request)
    Route::post('/messenger', [MessengerWebhookController::class, 'handle']);
});

// مسار اختبار للتحقق من الإعدادات (يمكن حذفه لاحقاً)
Route::get('/webhook/test-config', function () {
    return response()->json([
        'verify_token' => config('services.messenger.verify_token'),
        'has_token' => !empty(config('services.messenger.verify_token')),
        'token_length' => strlen(config('services.messenger.verify_token') ?? ''),
    ]);
});

