<?php
/**
 * ملف اختبار مؤقت للتحقق من أن Webhook يعمل
 * يمكنك حذف هذا الملف بعد التأكد من أن كل شيء يعمل
 * 
 * استخدمه بالطريقة التالية:
 * https://gravoni.com/test-webhook.php?hub_mode=subscribe&hub_verify_token=grav_key_444&hub_challenge=test123
 */

require __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$request = Illuminate\Http\Request::create(
    '/api/webhook/messenger',
    'GET',
    [
        'hub_mode' => $_GET['hub_mode'] ?? 'subscribe',
        'hub_verify_token' => $_GET['hub_verify_token'] ?? 'grav_key_444',
        'hub_challenge' => $_GET['hub_challenge'] ?? 'test_challenge_123',
    ]
);

$response = $kernel->handle($request);
$response->send();

$kernel->terminate($request, $response);

