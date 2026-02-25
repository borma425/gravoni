<?php

namespace Plugins\Payment\CashUpCash;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * CashUp Cash Payment Plugin
 * Integrates with CashUp API for payment verification before order confirmation.
 * Based on the flow from cashup project demo.
 */
class CashUpCashService
{
    protected string $baseUrl;

    protected string $apiKey;

    protected string $appId;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('plugins.cashup_cash.base_url', ''), '/');
        $this->apiKey = config('plugins.cashup_cash.api_key', '');
        $this->appId = config('plugins.cashup_cash.app_id', '');
    }

    public function isConfigured(): bool
    {
        return !empty($this->baseUrl)
            && !empty($this->apiKey)
            && !empty($this->appId)
            && str_starts_with($this->appId, 'app_');
    }

    /**
     * Create a payment intent for the given amount.
     * Returns payment_intent_id and receiver_number for the customer to pay.
     */
    public function createPaymentIntent(float $amount, string $orderReference = ''): array
    {
        if (!$this->isConfigured()) {
            return ['success' => false, 'message' => 'CashUp Cash غير مُعد بشكل صحيح'];
        }

        if ($amount <= 0) {
            return ['success' => false, 'message' => 'المبلغ يجب أن يكون أكبر من صفر'];
        }

        $orderId = $orderReference ?: 'order_' . substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 12);

        $payload = [
            'product_name' => 'طلب من جرافوني - ' . $orderId,
            'amount' => $amount,
            'order_id' => $orderId,
        ];

        try {
            Log::info('CashUp: Creating payment intent', [
                'amount' => $amount,
                'order_id' => $orderId,
            ]);

            $response = Http::timeout(30)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => 'application/json',
                ])
                ->post($this->baseUrl . '/api/v1/transactions/' . $this->appId . '/payment_intents', $payload);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'success' => true,
                    'payment_intent_id' => $data['payment_intent_id'] ?? null,
                    'receiver_number' => $data['receiverNumber'] ?? $data['receiver_number'] ?? null,
                    'order_id' => $orderId,
                ];
            }

            $errorData = $response->json();
            $message = $errorData['message'] ?? $errorData['error'] ?? 'فشل في إنشاء نية الدفع';

            if ($response->status() === 401) {
                $message = 'مفتاح API غير صحيح';
            } elseif ($response->status() === 404) {
                $message = 'معرف التطبيق غير موجود';
            }

            Log::error('CashUp: createPaymentIntent failed', [
                'status' => $response->status(),
                'response' => $response->body(),
            ]);

            return ['success' => false, 'message' => $message];
        } catch (\Exception $e) {
            Log::error('CashUp: createPaymentIntent exception', ['error' => $e->getMessage()]);
            return ['success' => false, 'message' => 'حدث خطأ في الاتصال بخدمة الدفع'];
        }
    }

    /**
     * Validate that the customer has paid (by sender phone or InstaPay name).
     */
    public function validatePayment(string $paymentIntentId, string $senderIdentifier): array
    {
        if (!$this->isConfigured()) {
            return ['success' => false, 'message' => 'CashUp Cash غير مُعد بشكل صحيح'];
        }

        $paymentIntentId = htmlspecialchars(trim($paymentIntentId), ENT_QUOTES, 'UTF-8');
        $senderIdentifier = trim(htmlspecialchars($senderIdentifier, ENT_QUOTES, 'UTF-8'));

        if (empty($paymentIntentId) || empty($senderIdentifier)) {
            return ['success' => false, 'message' => 'بيانات التحقق غير مكتملة'];
        }

        try {
            Log::info('CashUp: Validating payment', [
                'payment_intent_id' => substr($paymentIntentId, 0, 20) . '...',
            ]);

            $response = Http::timeout(30)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => 'application/json',
                ])
                ->post($this->baseUrl . '/api/v1/transactions/payment_intents/' . $paymentIntentId . '/validate', [
                    'sender_identifier' => $senderIdentifier,
                ]);

            $data = $response->json();

            if ($response->status() === 200 && ($data['status'] === 'PAYMENT_SUCCESS' || ($data['status'] ?? '') === 'succeeded')) {
                return [
                    'success' => true,
                    'status' => 'PAYMENT_SUCCESS',
                    'message' => $data['message'] ?? 'تم التحقق من الدفع بنجاح',
                    'amount_paid' => $data['amount_paid'] ?? null,
                ];
            }

            if ($response->status() === 400 && isset($data['amount_paid']) && (float) ($data['amount_paid'] ?? 0) > 0) {
                return [
                    'success' => true,
                    'status' => 'partial_payment',
                    'message' => $data['message'] ?? 'تم التحقق من الدفع',
                    'amount_paid' => (float) $data['amount_paid'],
                ];
            }

            return [
                'success' => false,
                'message' => $data['message'] ?? 'فشل التحقق من الدفع. تأكد من الرقم أو الاسم وحاول مرة أخرى',
            ];
        } catch (\Exception $e) {
            Log::error('CashUp: validatePayment exception', ['error' => $e->getMessage()]);
            return ['success' => false, 'message' => 'حدث خطأ في التحقق من الدفع'];
        }
    }
}
