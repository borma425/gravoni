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
        // قراءة القيمة من config مع قيمة افتراضية
        $verifyToken = config('services.messenger.verify_token', 'grav_key_444');

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
            'all_params' => $request->all(),
        ]);

        // التحقق من وجود جميع المعاملات المطلوبة
        if (empty($mode) || empty($token) || empty($challenge)) {
            Log::warning('Messenger Webhook verification failed - missing parameters', [
                'mode' => $mode,
                'token' => $token,
                'challenge' => $challenge,
            ]);
            return response('Bad Request', 400);
        }

        // Facebook يتطلب أن يكون الـ mode = 'subscribe' والـ token متطابق تماماً
        if ($mode === 'subscribe' && $token === $verifyToken) {
            Log::info('Messenger Webhook verified successfully', [
                'challenge' => $challenge,
            ]);
            
            // إرجاع الـ challenge كنص خام (Facebook يتطلب هذا التنسيق بالضبط)
            return response($challenge, 200, [
                'Content-Type' => 'text/plain',
            ]);
        }

        Log::warning('Messenger Webhook verification failed', [
            'mode' => $mode,
            'mode_match' => $mode === 'subscribe',
            'token_match' => $token === $verifyToken,
            'expected_token' => $verifyToken,
            'received_token' => $token,
            'token_length_expected' => strlen($verifyToken ?? ''),
            'token_length_received' => strlen($token ?? ''),
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
                // معالجة رسائل Messenger
                if (isset($entry['messaging'])) {
                    foreach ($entry['messaging'] as $event) {
                        $this->processEvent($event);
                    }
                }
                
                // معالجة التعليقات على المنشورات
                if (isset($entry['changes'])) {
                    $pageId = $entry['id'] ?? null; // Page ID من entry
                    foreach ($entry['changes'] as $change) {
                        if ($change['field'] === 'feed' && isset($change['value'])) {
                            $this->processCommentEvent($change['value'], $pageId);
                        }
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

            // الرد التلقائي على الرسائل
            $this->handleAutoReply($senderId, $messageText);
        }

        // معالجة الـ Postback (الأزرار)
        if (isset($event['postback'])) {
            $postbackPayload = $event['postback']['payload'];
            Log::info('Received postback', [
                'sender_id' => $senderId,
                'payload' => $postbackPayload,
            ]);

            // الرد على الأزرار
            $this->handlePostback($senderId, $postbackPayload);
        }
    }

    /**
     * معالجة الرد التلقائي
     */
    protected function handleAutoReply(string $senderId, string $messageText)
    {
        // التحقق من تفعيل الرد التلقائي
        if (!config('services.messenger.auto_reply_enabled', true)) {
            Log::info('Auto-reply is disabled, skipping reply', [
                'sender_id' => $senderId,
                'message' => $messageText,
            ]);
            return;
        }

        // تحويل النص إلى حروف صغيرة للمقارنة
        $lowerText = mb_strtolower($messageText);

        // ردود تلقائية بناءً على الكلمات المفتاحية
        if (str_contains($lowerText, 'مرحبا') || str_contains($lowerText, 'هلا') || str_contains($lowerText, 'السلام')) {
            $reply = "مرحباً بك! 👋\nكيف يمكنني مساعدتك اليوم؟";
        } elseif (str_contains($lowerText, 'سعر') || str_contains($lowerText, 'اسعار') || str_contains($lowerText, 'كم')) {
            $reply = "للاستفسار عن الأسعار، يرجى زيارة موقعنا أو التواصل مع فريق المبيعات.\n📞 سنتواصل معك قريباً!";
        } elseif (str_contains($lowerText, 'شكر')) {
            $reply = "شكراً لتواصلك معنا! 🙏\nنحن سعداء بخدمتك.";
        } elseif (str_contains($lowerText, 'مساعد') || str_contains($lowerText, 'help')) {
            $reply = "بالتأكيد! 😊\nيمكنك:\n• الاستفسار عن الخدمات\n• طلب معلومات\n• التحدث مع فريق الدعم\n\nكيف يمكنني مساعدتك؟";
        } else {
            // رد افتراضي
            $reply = "شكراً لرسالتك! 📩\nتم استلام رسالتك وسيتم الرد عليك في أقرب وقت.\n\nللاستفسارات العاجلة، يرجى الاتصال بنا مباشرة.";
        }

        $this->sendReply($senderId, $reply);
    }

    /**
     * معالجة الـ Postback (الأزرار)
     */
    protected function handlePostback(string $senderId, string $payload)
    {
        switch ($payload) {
            case 'GET_STARTED':
                $reply = "أهلاً وسهلاً بك! 🎉\nمرحباً بك في صفحتنا.\nكيف يمكننا مساعدتك اليوم؟";
                break;
            case 'CONTACT_US':
                $reply = "للتواصل معنا:\n📧 البريد: info@gravoni.com\n🌐 الموقع: gravoni.com";
                break;
            default:
                $reply = "شكراً لتفاعلك! كيف يمكنني مساعدتك؟";
        }

        $this->sendReply($senderId, $reply);
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

    /**
     * معالجة أحداث التعليقات
     */
    protected function processCommentEvent(array $value, ?string $pageId = null)
    {
        // Facebook يرسل أحداث التعليقات ببنيات مختلفة
        // البنية 1: عندما يكون item = 'comment'
        if (isset($value['item']) && $value['item'] === 'comment') {
            $commentId = $value['comment_id'] ?? null;
            $postId = $value['post_id'] ?? null;
            $message = $value['message'] ?? '';
            $verb = $value['verb'] ?? 'add'; // 'add' للتعليق الجديد
            
            // التحقق من أن الحدث هو تعليق جديد وليس حذف أو تعديل
            if ($verb !== 'add') {
                Log::info('Comment event is not a new comment', [
                    'verb' => $verb,
                    'comment_id' => $commentId,
                ]);
                return;
            }
            
            $from = $value['from'] ?? null;
            $senderId = $from['id'] ?? null;
            $senderName = $from['name'] ?? null;

            // تجاهل التعليقات التي يرسلها النظام نفسه (من الصفحة)
            if (!empty($pageId) && $senderId === $pageId) {
                Log::info('Ignoring comment from page itself', [
                    'comment_id' => $commentId,
                    'sender_id' => $senderId,
                    'page_id' => $pageId,
                ]);
                return;
            }

            // تجاهل التعليقات الفارغة
            if (empty($message)) {
                Log::info('Ignoring empty comment', [
                    'comment_id' => $commentId,
                    'sender_id' => $senderId,
                ]);
                return;
            }

            Log::info('New comment received', [
                'comment_id' => $commentId,
                'post_id' => $postId,
                'sender_id' => $senderId,
                'sender_name' => $senderName,
                'message' => $message,
                'verb' => $verb,
            ]);

            // التحقق من تفعيل الرد التلقائي على التعليقات
            if (config('services.messenger.auto_reply_comments_enabled', false)) {
                $this->handleCommentAutoReply($commentId, $message, $senderId, $senderName, $pageId);
            }
        }
    }

    /**
     * معالجة الرد التلقائي على التعليقات
     */
    protected function handleCommentAutoReply(string $commentId, string $commentText, ?string $senderId, ?string $senderName, ?string $pageId = null)
    {
        // تجاهل إذا كان المرسل هو الصفحة نفسها
        if (!empty($pageId) && $senderId === $pageId) {
            Log::info('Skipping auto-reply - comment from page itself', [
                'comment_id' => $commentId,
                'sender_id' => $senderId,
            ]);
            return;
        }

        // تحويل النص إلى حروف صغيرة للمقارنة
        $lowerText = mb_strtolower($commentText);

        // ردود تلقائية بناءً على الكلمات المفتاحية
        if (str_contains($lowerText, 'مرحبا') || str_contains($lowerText, 'هلا') || str_contains($lowerText, 'السلام')) {
            $reply = "مرحباً بك! 👋\nكيف يمكنني مساعدتك اليوم؟";
        } elseif (str_contains($lowerText, 'سعر') || str_contains($lowerText, 'اسعار') || str_contains($lowerText, 'كم')) {
            $reply = "للاستفسار عن الأسعار، يرجى زيارة موقعنا أو التواصل مع فريق المبيعات.\n📞 سنتواصل معك قريباً!";
        } elseif (str_contains($lowerText, 'شكر')) {
            $reply = "شكراً لتواصلك معنا! 🙏\nنحن سعداء بخدمتك.";
        } elseif (str_contains($lowerText, 'مساعد') || str_contains($lowerText, 'help')) {
            $reply = "بالتأكيد! 😊\nيمكنك:\n• الاستفسار عن الخدمات\n• طلب معلومات\n• التحدث مع فريق الدعم\n\nكيف يمكنني مساعدتك؟";
        } else {
            // رد افتراضي
            $reply = "شكراً لتعليقك! 📩\nتم استلام تعليقك وسيتم الرد عليك في أقرب وقت.\n\nللاستفسارات العاجلة، يرجى التواصل معنا عبر Messenger.";
        }

        // الرد على التعليق (علني)
        $this->replyToComment($commentId, $reply);

        // إرسال رسالة خاصة للمستخدم عبر Messenger (Private Reply)
        // فقط إذا كان المستخدم ليس الصفحة نفسها
        // Private Reply يعمل حتى لو لم يبدأ المستخدم محادثة من قبل
        if (config('services.messenger.send_private_message_on_comment', true) 
            && !empty($commentId) 
            && (empty($pageId) || $senderId !== $pageId)) {
            $this->sendPrivateMessageToCommenter($commentId, $reply, $senderName);
        }
    }

    /**
     * الرد على تعليق في Facebook
     */
    protected function replyToComment(string $commentId, string $messageText)
    {
        $accessToken = config('services.messenger.page_access_token');

        if (empty($accessToken)) {
            Log::error('Page access token is missing for comment reply');
            return null;
        }

        $response = \Illuminate\Support\Facades\Http::post(
            "https://graph.facebook.com/v18.0/{$commentId}/comments",
            [
                'message' => $messageText,
                'access_token' => $accessToken,
            ]
        );

        if ($response->failed()) {
            Log::error('Failed to reply to comment', [
                'comment_id' => $commentId,
                'response' => $response->json(),
                'status' => $response->status(),
            ]);
        } else {
            Log::info('Comment reply sent successfully', [
                'comment_id' => $commentId,
                'reply_id' => $response->json()['id'] ?? null,
            ]);
        }

        return $response;
    }

    /**
     * إرسال رسالة خاصة للمستخدم عبر Messenger بعد التعليق (Private Reply)
     * 
     * ملاحظة: Private Replies تسمح بإرسال رسالة خاصة كرد على تعليق
     * يمكن استخدامها حتى لو لم يبدأ المستخدم محادثة من قبل
     * لكن يجب أن يكون التعليق حديث (خلال 7 أيام)
     */
    protected function sendPrivateMessageToCommenter(string $commentId, string $messageText, ?string $senderName)
    {
        $accessToken = config('services.messenger.page_access_token');

        if (empty($accessToken)) {
            Log::error('Page access token is missing for private reply');
            return null;
        }

        // استخدام Private Replies API - يسمح بإرسال رسالة خاصة كرد على تعليق
        // هذا يعمل حتى لو لم يبدأ المستخدم محادثة من قبل
        $privateMessage = "مرحباً " . ($senderName ? $senderName : '') . "! 👋\n\n" . $messageText . "\n\n💬 يمكنك التواصل معنا مباشرة عبر Messenger في أي وقت.";

        $response = \Illuminate\Support\Facades\Http::post(
            "https://graph.facebook.com/v18.0/{$commentId}/private_replies",
            [
                'message' => $privateMessage,
                'access_token' => $accessToken,
            ]
        );

        if ($response->failed()) {
            $errorData = $response->json();
            $errorCode = $errorData['error']['code'] ?? null;
            $errorSubcode = $errorData['error']['error_subcode'] ?? null;
            $errorMessage = $errorData['error']['message'] ?? 'Unknown error';

            // Facebook قد يرفض Private Reply لعدة أسباب:
            // 1. التعليق قديم جداً (أكثر من 7 أيام)
            // 2. الصفحة لا تملك صلاحية private_replies
            // 3. المستخدم حظر الصفحة
            
            if ($errorCode == 10 || $errorCode == 100 || 
                str_contains($errorMessage, 'not allowed') || 
                str_contains($errorMessage, 'permission') ||
                str_contains($errorMessage, 'لم يتم العثور على مستخدم') ||
                str_contains($errorMessage, 'time') ||
                $errorSubcode == 2018001) {
                // هذه أخطاء متوقعة
                Log::info('Private reply not sent - expected limitation', [
                    'comment_id' => $commentId,
                    'sender_name' => $senderName,
                    'error_code' => $errorCode,
                    'error_subcode' => $errorSubcode,
                    'error_message' => $errorMessage,
                ]);
            } else {
                // أخطاء غير متوقعة
                Log::error('Failed to send private reply to commenter', [
                    'comment_id' => $commentId,
                    'sender_name' => $senderName,
                    'response' => $errorData,
                    'status' => $response->status(),
                ]);
            }
        } else {
            Log::info('Private reply sent successfully to commenter', [
                'comment_id' => $commentId,
                'sender_name' => $senderName,
                'message_id' => $response->json()['id'] ?? null,
            ]);
        }

        return $response;
    }
}

