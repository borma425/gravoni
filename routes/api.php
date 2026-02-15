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

