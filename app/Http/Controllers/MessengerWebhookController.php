<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class MessengerWebhookController extends Controller
{
    /**
     * الحد الأقصى للردود في الدقيقة (للحماية من الحظر)
     */
    const MAX_REPLIES_PER_MINUTE = 20;
    
    /**
     * التأخير بين الردود (بالثواني)
     */
    const REPLY_DELAY_SECONDS = 2;

    /**
     * التحقق من Webhook (GET request)
     * يستخدم هذا من قبل Facebook للتحقق من صحة الـ webhook
     */
    public function verify(Request $request)
    {
        $verifyToken = config('services.messenger.verify_token', 'grav_key_444');

        $mode = $request->query('hub_mode');
        $token = $request->query('hub_verify_token');
        $challenge = $request->query('hub_challenge');

        Log::info('Messenger Webhook verification attempt', [
            'mode' => $mode,
            'token_received' => $token,
            'token_expected' => $verifyToken,
            'challenge' => $challenge,
            'token_match' => $token === $verifyToken,
        ]);

        if (empty($mode) || empty($token) || empty($challenge)) {
            return response('Bad Request', 400);
        }

        if ($mode === 'subscribe' && $token === $verifyToken) {
            Log::info('Messenger Webhook verified successfully');
            return response($challenge, 200, ['Content-Type' => 'text/plain']);
        }

        return response('Forbidden', 403);
    }

    /**
     * معالجة الرسائل الواردة (POST request)
     */
    public function handle(Request $request)
    {
        $payload = $request->all();

        Log::info('Messenger Webhook received', ['payload' => $payload]);

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
                    $pageId = $entry['id'] ?? null;
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
     * التحقق من معدل الردود (Rate Limiting)
     */
    protected function canSendReply(): bool
    {
        $key = 'facebook_reply_count_' . now()->format('Y-m-d-H-i');
        $count = Cache::get($key, 0);
        
        if ($count >= self::MAX_REPLIES_PER_MINUTE) {
            Log::warning('Rate limit reached - skipping reply to prevent ban', [
                'current_count' => $count,
                'max_allowed' => self::MAX_REPLIES_PER_MINUTE,
            ]);
            return false;
        }
        
        Cache::put($key, $count + 1, 120); // تنتهي بعد دقيقتين
        return true;
    }

    /**
     * تأخير ذكي قبل الرد
     */
    protected function smartDelay(): void
    {
        // تأخير عشوائي بين 1 و 3 ثواني لتجنب اكتشاف البوت
        $delay = rand(1, 3);
        sleep($delay);
    }

    /**
     * معالجة الحدث الفردي (رسائل Messenger)
     */
    protected function processEvent(array $event)
    {
        $senderId = $event['sender']['id'] ?? null;

        if (isset($event['message']['text'])) {
            $messageText = $event['message']['text'];
            Log::info('Received message', [
                'sender_id' => $senderId,
                'text' => $messageText,
            ]);

            $this->handleAutoReply($senderId, $messageText);
        }

        if (isset($event['postback'])) {
            $postbackPayload = $event['postback']['payload'];
            Log::info('Received postback', [
                'sender_id' => $senderId,
                'payload' => $postbackPayload,
            ]);

            $this->handlePostback($senderId, $postbackPayload);
        }
    }

    /**
     * معالجة الرد التلقائي على رسائل Messenger
     */
    protected function handleAutoReply(string $senderId, string $messageText)
    {
        if (!config('services.messenger.auto_reply_enabled', true)) {
            Log::info('Auto-reply is disabled', ['sender_id' => $senderId]);
            return;
        }

        // التحقق من معدل الردود
        if (!$this->canSendReply()) {
            return;
        }

        // تأخير ذكي
        $this->smartDelay();

        $lowerText = mb_strtolower($messageText);

        if (str_contains($lowerText, 'مرحبا') || str_contains($lowerText, 'هلا') || str_contains($lowerText, 'السلام')) {
            $reply = "مرحباً بك! 👋\nكيف يمكنني مساعدتك اليوم؟";
        } elseif (str_contains($lowerText, 'سعر') || str_contains($lowerText, 'اسعار') || str_contains($lowerText, 'كم')) {
            $reply = "للاستفسار عن الأسعار، يرجى زيارة موقعنا أو التواصل مع فريق المبيعات.\n📞 سنتواصل معك قريباً!";
        } elseif (str_contains($lowerText, 'شكر')) {
            $reply = "شكراً لتواصلك معنا! 🙏\nنحن سعداء بخدمتك.";
        } elseif (str_contains($lowerText, 'مساعد') || str_contains($lowerText, 'help')) {
            $reply = "بالتأكيد! 😊\nيمكنك:\n• الاستفسار عن الخدمات\n• طلب معلومات\n• التحدث مع فريق الدعم\n\nكيف يمكنني مساعدتك؟";
        } else {
            $reply = "شكراً لرسالتك! 📩\nتم استلام رسالتك وسيتم الرد عليك في أقرب وقت.\n\nللاستفسارات العاجلة، يرجى الاتصال بنا مباشرة.";
        }

        $this->sendReply($senderId, $reply);
    }

    /**
     * معالجة الـ Postback (الأزرار)
     */
    protected function handlePostback(string $senderId, string $payload)
    {
        // التحقق من معدل الردود
        if (!$this->canSendReply()) {
            return;
        }

        // تأخير ذكي
        $this->smartDelay();

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
     * إرسال رد للمستخدم عبر Messenger
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
        } else {
            Log::info('Message sent successfully', [
                'recipient' => $recipientId,
            ]);
        }

        return $response;
    }

    /**
     * معالجة أحداث التعليقات
     */
    protected function processCommentEvent(array $value, ?string $pageId = null)
    {
        if (!isset($value['item']) || $value['item'] !== 'comment') {
            return;
        }

        $commentId = $value['comment_id'] ?? null;
        $postId = $value['post_id'] ?? null;
        $message = $value['message'] ?? '';
        $verb = $value['verb'] ?? 'add';
        
        // تجاهل غير التعليقات الجديدة
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

        // تجاهل تعليقات الصفحة نفسها
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
            Log::info('Ignoring empty comment', ['comment_id' => $commentId]);
            return;
        }

        Log::info('New comment received', [
            'comment_id' => $commentId,
            'post_id' => $postId,
            'sender_id' => $senderId,
            'sender_name' => $senderName,
            'message' => $message,
        ]);

        // الرد التلقائي على التعليقات
        if (config('services.messenger.auto_reply_comments_enabled', false)) {
            $this->handleCommentAutoReply($commentId, $message, $senderId, $senderName);
        }
    }

    /**
     * معالجة الرد التلقائي على التعليقات
     * 
     * ملاحظة مهمة:
     * - الرد على التعليق (علني) يعمل دائماً
     * - إرسال رسائل خاصة عبر Messenger غير ممكن للمستخدمين الذين لم يبدأوا محادثة
     * - Private Replies تحتاج مراجعة خاصة من Facebook وغير متاحة تلقائياً
     */
    protected function handleCommentAutoReply(string $commentId, string $commentText, ?string $senderId, ?string $senderName)
    {
        // التحقق من معدل الردود
        if (!$this->canSendReply()) {
            return;
        }

        // تأخير ذكي (مهم لتجنب الحظر)
        $this->smartDelay();

        $lowerText = mb_strtolower($commentText);

        // ردود تلقائية بناءً على الكلمات المفتاحية
        // مع إضافة دعوة للتواصل عبر Messenger في كل رد
        if (str_contains($lowerText, 'مرحبا') || str_contains($lowerText, 'هلا') || str_contains($lowerText, 'السلام')) {
            $reply = "مرحباً بك يا " . ($senderName ?: 'صديقنا') . "! 👋\nكيف يمكنني مساعدتك اليوم؟\n\n💬 للتواصل المباشر، راسلنا عبر Messenger";
        } elseif (str_contains($lowerText, 'سعر') || str_contains($lowerText, 'اسعار') || str_contains($lowerText, 'كم')) {
            $reply = "للاستفسار عن الأسعار، يرجى التواصل مع فريق المبيعات.\n📞 سنتواصل معك قريباً!\n\n💬 أو راسلنا مباشرة عبر Messenger للرد السريع";
        } elseif (str_contains($lowerText, 'شكر')) {
            $reply = "شكراً لك يا " . ($senderName ?: 'صديقنا') . "! 🙏\nنحن سعداء بخدمتك.";
        } elseif (str_contains($lowerText, 'مساعد') || str_contains($lowerText, 'help')) {
            $reply = "بالتأكيد! 😊\nيمكنك:\n• الاستفسار عن الخدمات\n• طلب معلومات\n• التحدث مع فريق الدعم\n\n💬 راسلنا عبر Messenger للرد الفوري";
        } else {
            // رد افتراضي مع دعوة للتواصل عبر Messenger
            $reply = "شكراً لتعليقك يا " . ($senderName ?: 'صديقنا') . "! 📩\nتم استلام تعليقك وسيتم الرد عليك.\n\n💬 للتواصل المباشر والسريع، راسلنا عبر Messenger";
        }

        // الرد على التعليق (علني فقط)
        $this->replyToComment($commentId, $reply);
        
        // لا نحاول إرسال رسائل خاصة لأن:
        // 1. Facebook يمنع إرسال رسائل لمستخدمين لم يبدأوا محادثة
        // 2. Private Replies تحتاج مراجعة خاصة من Facebook
        // 3. بدلاً من ذلك، نطلب من المستخدم التواصل عبر Messenger في الرد العلني
    }

    /**
     * الرد على تعليق في Facebook (علني)
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
}
