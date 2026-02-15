<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MessengerWebhookController extends Controller
{
    /**
     * التحقق من Webhook (GET request)
     * يستخدم هذا من قبل Facebook للتحقق من صحة الـ webhook
     */
    public function verify(Request $request)
    {
        $verifyToken = config('services.messenger.verify_token');

        $mode = $request->query('hub_mode');
        $token = $request->query('hub_verify_token');
        $challenge = $request->query('hub_challenge');

        // تسجيل معلومات الطلب للمساعدة في التشخيص
        Log::info('Messenger Webhook verification attempt', [
            'mode' => $mode,
            'token_received' => $token,
            'token_expected' => $verifyToken,
            'challenge' => $challenge,
            'token_match' => $token === $verifyToken,
        ]);

        // Facebook يتطلب أن يكون الـ mode = 'subscribe' والـ token متطابق
        if ($mode === 'subscribe' && $token === $verifyToken) {
            Log::info('Messenger Webhook verified successfully');
            // إرجاع الـ challenge كنص عادي (plain text)
            return response($challenge, 200)
                ->header('Content-Type', 'text/plain');
        }

        Log::warning('Messenger Webhook verification failed', [
            'mode' => $mode,
            'token_match' => $token === $verifyToken,
            'expected_token' => $verifyToken,
            'received_token' => $token,
        ]);

        return response('Forbidden', 403);
    }

    /**
     * معالجة الرسائل الواردة (POST request)
     */
    public function handle(Request $request)
    {
        $payload = $request->all();

        Log::info('Messenger Webhook received', ['payload' => $payload]);

        // التحقق من أن الطلب من صفحة
        if (isset($payload['object']) && $payload['object'] === 'page') {
            foreach ($payload['entry'] as $entry) {
                // معالجة كل حدث
                if (isset($entry['messaging'])) {
                    foreach ($entry['messaging'] as $event) {
                        $this->processEvent($event);
                    }
                }
            }

            return response('EVENT_RECEIVED', 200);
        }

        return response('Not Found', 404);
    }

    /**
     * معالجة الحدث الفردي
     */
    protected function processEvent(array $event)
    {
        $senderId = $event['sender']['id'] ?? null;

        // معالجة الرسالة النصية
        if (isset($event['message']['text'])) {
            $messageText = $event['message']['text'];
            Log::info('Received message', [
                'sender_id' => $senderId,
                'text' => $messageText,
            ]);

            // يمكنك إضافة منطق الرد هنا
            // $this->sendReply($senderId, "تم استلام رسالتك: $messageText");
        }

        // معالجة الـ Postback (الأزرار)
        if (isset($event['postback'])) {
            $postbackPayload = $event['postback']['payload'];
            Log::info('Received postback', [
                'sender_id' => $senderId,
                'payload' => $postbackPayload,
            ]);
        }
    }

    /**
     * إرسال رد للمستخدم (اختياري)
     */
    protected function sendReply(string $recipientId, string $messageText)
    {
        $accessToken = config('services.messenger.page_access_token');

        $response = \Illuminate\Support\Facades\Http::post(
            'https://graph.facebook.com/v18.0/me/messages',
            [
                'recipient' => ['id' => $recipientId],
                'message' => ['text' => $messageText],
                'access_token' => $accessToken,
            ]
        );

        if ($response->failed()) {
            Log::error('Failed to send message', [
                'recipient' => $recipientId,
                'response' => $response->json(),
            ]);
        }

        return $response;
    }
}

