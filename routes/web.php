<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// مسار اختبار مؤقت للتحقق من إعدادات Messenger
Route::get('/test-messenger-config', function () {
    return [
        'verify_token' => config('services.messenger.verify_token'),
        'has_token' => !empty(config('services.messenger.verify_token')),
    ];
});

// سياسة الخصوصية
Route::get('/privacy-policy', function () {
    return view('privacy-policy');
})->name('privacy-policy');

// شروط الخدمة
Route::get('/terms-of-service', function () {
    return view('terms-of-service');
})->name('terms-of-service');
