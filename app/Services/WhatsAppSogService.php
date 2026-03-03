<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppSogService
{
    protected string $baseUrl = 'https://whatsapp.sog.eg';

    public function isConfigured(): bool
    {
        return !empty(config('plugins.whatsapp_sog.appkey'))
            && !empty(config('plugins.whatsapp_sog.authkey'))
            && config('plugins.whatsapp_sog.enabled', false);
    }

    /**
     * Send a WhatsApp message via SOG API.
     * @param string|null $fileUrl Optional URL of file to attach (legacy - prefers file content)
     * @param string|null $fileContent Optional raw PDF/file binary content (preferred - matches SOG panel which uses binary upload)
     * @param string $fileName Optional filename when using fileContent (e.g. mylerz-label.pdf)
     */
    public function sendMessage(string $to, string $message, ?string $fileUrl = null, ?string $fileContent = null, string $fileName = 'label.pdf'): array
    {
        if (!$this->isConfigured()) {
            return ['success' => false, 'error' => 'WhatsApp SOG غير مُعد'];
        }

        $phone = $this->normalizePhone($to);
        if (!$phone) {
            return ['success' => false, 'error' => 'رقم الهاتف غير صالح'];
        }

        try {
            $hasFile = !empty($fileContent) || !empty($fileUrl);
            Log::info('WhatsApp SOG: Sending message', ['to' => $phone, 'with_file' => $hasFile, 'file_type' => $fileContent ? 'binary' : ($fileUrl ? 'url' : 'none')]);
            $result = $this->sendViaCurl($phone, $message, $fileUrl, $fileContent, $fileName);
            if ($result['success']) {
                Log::info('WhatsApp SOG: Message sent successfully', ['to' => $phone]);
            }
            return $result;
        } catch (\Throwable $e) {
            Log::error('WhatsApp SOG exception', [
                'to' => $phone,
                'error' => $e->getMessage(),
            ]);
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Send via cURL - multipart/form-data.
     * SOG panel uses binary file upload - try file content first, fallback to URL.
     */
    protected function sendViaCurl(string $phone, string $message, ?string $fileUrl = null, ?string $fileContent = null, string $fileName = 'label.pdf'): array
    {
        $apiUrl = $this->baseUrl . '/api/create-message';
        $appkey = config('plugins.whatsapp_sog.appkey');
        $authkey = config('plugins.whatsapp_sog.authkey');

        $postFields = [
            'appkey' => $appkey,
            'authkey' => $authkey,
            'to' => $phone,
            'message' => $message,
        ];

        $tmpFile = null;
        if (!empty($fileUrl)) {
            $postFields['file'] = $fileUrl;
        } elseif (!empty($fileContent)) {
            $tmpFile = tempnam(sys_get_temp_dir(), 'sog_');
            file_put_contents($tmpFile, $fileContent);
            $postFields['file'] = new \CURLFile($tmpFile, 'application/pdf', $fileName);
        }

        $ch = curl_init($apiUrl);
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $postFields,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 60,
            CURLOPT_SSL_VERIFYPEER => true,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        if ($tmpFile !== null && file_exists($tmpFile)) {
            @unlink($tmpFile);
        }
        curl_close($ch);

        if ($error) {
            Log::warning('WhatsApp SOG cURL error', ['error' => $error]);
            return ['success' => false, 'error' => $error];
        }

        $body = json_decode($response, true) ?? [];
        Log::info('WhatsApp SOG API response', ['http_code' => $httpCode, 'body' => $body]);

        if ($httpCode < 200 || $httpCode >= 300) {
            $errMsg = $body['message'] ?? $body['error'] ?? $this->sanitizeErrorResponse($response, $httpCode);
            return ['success' => false, 'error' => $errMsg];
        }

        $messageStatus = $body['message_status'] ?? $body['status'] ?? null;
        if ($messageStatus !== 'Success' && $messageStatus !== 'success') {
            $errMsg = $body['message'] ?? $body['error'] ?? ($body['data']['message'] ?? 'فشل الإرسال حسب استجابة الـ API');
            return ['success' => false, 'error' => $errMsg];
        }

        return ['success' => true];
    }

    /**
     * Extract a short error summary from HTML/raw response to avoid logging huge payloads.
     */
    protected function sanitizeErrorResponse(string $response, int $httpCode): string
    {
        if (strlen($response) <= 500) {
            return $response;
        }
        if (stripos($response, '<html') !== false || stripos($response, '<!DOCTYPE') !== false) {
            if (preg_match('/<title>(.+?)<\/title>/is', $response, $m)) {
                return 'HTTP ' . $httpCode . ': ' . trim(strip_tags($m[1]));
            }
            if (preg_match('/ConnectionException: (.+?)(?:\s+in\s+file|$)/s', $response, $m)) {
                return 'HTTP ' . $httpCode . ': ' . trim($m[1]);
            }
            return 'HTTP ' . $httpCode . ': خطأ من الخادم (استجابة HTML)';
        }
        return 'HTTP ' . $httpCode . ': ' . substr($response, 0, 300) . '...';
    }

    /**
     * Build multipart/form-data body like curl --form.
     */
    protected function buildMultipartBody(string $boundary, array $fields): string
    {
        $lines = [];
        foreach ($fields as $name => $value) {
            $lines[] = '--' . $boundary;
            $lines[] = 'Content-Disposition: form-data; name="' . $name . '"';
            $lines[] = '';
            $lines[] = $value;
        }
        $lines[] = '--' . $boundary . '--';
        $lines[] = '';

        return implode("\r\n", $lines);
    }

    /**
     * Format order details as a WhatsApp message.
     */
    public function formatOrderMessage(Order $order): string
    {
        $trackUrl = $order->tracking_id
            ? url(route('store.track', $order->tracking_id))
            : '';

        $lines = [
            'مرحباً ' . $order->customer_name . ' 👋',
            '',
            'تم تأكيد طلبك بنجاح مع شركة الشحن Mylerz.',
            '',
            '📋 تفاصيل الطلب:',
            '• رقم التتبع: ' . ($order->tracking_id ?? '—'),
            '• العنوان: ' . $order->customer_address,
            '• المحافظة: ' . ($order->governorate?->name ?? '—'),
            '',
            '🛍 المنتجات:',
        ];

        foreach ($order->items ?? [] as $item) {
            $name = $item['product_name'] ?? 'منتج';
            $qty = $item['quantity'] ?? 1;
            $price = number_format((float) ($item['price'] ?? 0), 2);
            $size = trim($item['size'] ?? '');
            $color = trim($item['color'] ?? '');
            $details = array_filter([$size ? "مقاس {$size}" : null, $color ? "لون {$color}" : null]);
            $suffix = count($details) > 0 ? ' (' . implode('، ', $details) . ')' : '';
            $lines[] = "  • {$name}{$suffix} × {$qty} — {$price} ج.م";
        }

        $itemsTotal = (float) ($order->items_revenue ?? 0);
        $deliveryFees = (float) ($order->delivery_fees ?? 0);
        $isCashup = ($order->payment_method ?? '') === 'cashup';

        $lines[] = '';
        $lines[] = '💰 مبلغ المنتجات: ' . number_format($itemsTotal, 2) . ' ج.م';
        if ($deliveryFees > 0) {
            $lines[] = '🚚 رسوم التوصيل: ' . number_format($deliveryFees, 2) . ' ج.م' . ($isCashup ? ' (مدفوعة)' : '');
        }
        if ($isCashup && $deliveryFees > 0) {
            $lines[] = '💵 المبلغ عند الاستلام: ' . number_format($itemsTotal, 2) . ' ج.م';
        } else {
            $lines[] = '💵 المجموع: ' . number_format((float) $order->total_amount, 2) . ' ج.م';
        }
        $lines[] = '💳 الدفع: ' . ($order->payment_method_label ?? '—');

        if (!empty($order->shipping_data['barcode'])) {
            $lines[] = '';
            $lines[] = '📦 باركود Mylerz: ' . $order->shipping_data['barcode'];
        }

        if ($trackUrl) {
            $lines[] = '';
            $lines[] = '🔗 تتبع شحنتك: ' . $trackUrl;
        }

        return implode("\n", $lines);
    }

    /**
     * Send order confirmation to customer after Mylerz success.
     */
    public function sendOrderConfirmation(Order $order): array
    {
        $phone = $order->customer_numbers[0] ?? null;
        if (!$phone) {
            return ['success' => false, 'error' => 'لا يوجد رقم هاتف للعميل'];
        }

        $message = $this->formatOrderMessage($order);
        return $this->sendMessage($phone, $message);
    }

    /**
     * Format admin notification message (for internal tracking).
     */
    public function formatAdminOrderMessage(Order $order): string
    {
        $trackUrl = $order->tracking_id
            ? url(route('store.track', $order->tracking_id))
            : '';

        $lines = [
            '🆕 طلب جديد — Mylerz',
            '',
            '📋 ' . ($order->tracking_id ?? '—') . ' | ' . $order->customer_name,
            '📍 ' . $order->customer_address,
            '📦 باركود: ' . ($order->shipping_data['barcode'] ?? '—'),
        ];

        foreach ($order->items ?? [] as $item) {
            $name = $item['product_name'] ?? 'منتج';
            $qty = $item['quantity'] ?? 1;
            $size = trim($item['size'] ?? '');
            $color = trim($item['color'] ?? '');
            $details = array_filter([$size ? "مقاس {$size}" : null, $color ? "لون {$color}" : null]);
            $suffix = count($details) > 0 ? ' (' . implode('، ', $details) . ')' : '';
            $lines[] = "  • {$name}{$suffix} × {$qty}";
        }

        $lines[] = '';
        $lines[] = '💰 ' . number_format((float) $order->total_amount, 0) . ' ج.م';
        if ($trackUrl) {
            $lines[] = '🔗 ' . $trackUrl;
        }

        return implode("\n", $lines);
    }

    /**
     * Send admin notification with order details and PDF attachment.
     * API requires file as URL (not binary) - URL must be publicly accessible (not localhost).
     */
    public function sendAdminOrderNotification(Order $order, ?string $pdfContent = null, ?string $pdfUrl = null): array
    {
        $adminNumber = config('plugins.whatsapp_sog.admin_number', '');
        if (empty($adminNumber)) {
            return ['success' => false, 'error' => 'رقم الإدارة غير مُعد'];
        }

        $message = $this->formatAdminOrderMessage($order);
        $fileName = 'mylerz-' . ($order->tracking_id ?? $order->id) . '.pdf';
        return $this->sendMessage($adminNumber, $message, $pdfUrl, $pdfContent, $fileName);
    }

    /**
     * Normalize phone to SOG format (e.g. 201147544303).
     */
    protected function normalizePhone(string $input): ?string
    {
        $digits = preg_replace('/[^0-9]/', '', $input);
        $digits = ltrim($digits, '0');
        if (!str_starts_with($digits, '20')) {
            $digits = '20' . (strlen($digits) === 10 ? $digits : substr($digits, -10));
        }
        return strlen($digits) >= 12 ? $digits : null;
    }
}
