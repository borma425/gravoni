<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * التحويل ورصيد الحساب (يعتمد على نفس API مشروع cashup).
 * يستخدم مفتاح API من الإعدادات (CASHUP_API_KEY أو CASHUP_TRANSFER_API_KEY).
 */
class TransferMoneyController extends Controller
{
    protected function getBaseUrl(): string
    {
        return rtrim(config('plugins.cashup_cash.transfer_base_url', ''), '/');
    }

    protected function getApiKey(): string
    {
        return config('plugins.cashup_cash.transfer_api_key', '');
    }

    protected function isConfigured(): bool
    {
        return !empty($this->getBaseUrl()) && !empty($this->getApiKey());
    }

    /**
     * جلب أرقام الحساب من API.
     */
    public function getPhoneNumbers(): array
    {
        $phoneNumbers = [];
        if (!$this->isConfigured()) {
            return $phoneNumbers;
        }
        try {
            $response = Http::timeout(30)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->getApiKey(),
                    'Content-Type' => 'application/json',
                ])
                ->get($this->getBaseUrl() . '/api/v1/accounts/numbers');

            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['numbers']) && is_array($data['numbers'])) {
                    foreach ($data['numbers'] as $number) {
                        $phoneNumber = null;
                        if (is_string($number)) {
                            $phoneNumber = $number;
                        } elseif (is_array($number)) {
                            $phoneNumber = $number['phone_number'] ?? $number['number'] ?? null;
                        } elseif (is_numeric($number)) {
                            $phoneNumber = (string) $number;
                        }
                        if (!empty($phoneNumber) && $phoneNumber !== 'غير محدد') {
                            $phoneNumbers[] = $phoneNumber;
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            Log::warning('TransferMoney: getPhoneNumbers exception', ['error' => $e->getMessage()]);
        }
        return $phoneNumbers;
    }

    /**
     * جلب الرصيد الإجمالي لجميع أرقام الحساب.
     */
    public function getAllBalance(): array
    {
        $numbers = $this->getPhoneNumbers();
        if (empty($numbers)) {
            return ['success' => true, 'balance' => '0.00', 'message' => 'لا توجد أرقام متاحة', 'numbers' => []];
        }
        try {
            $response = Http::timeout(30)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->getApiKey(),
                    'Content-Type' => 'application/json',
                ])
                ->post($this->getBaseUrl() . '/api/v1/accounts/numbers/balance', [
                    'numbers' => $numbers,
                ]);

            if ($response->successful()) {
                $data = $response->json();
                $message = $data['message'] ?? 'تم الاستعلام عن الرصيد بنجاح';
                if (strpos($message, 'Couldn\'t retrieve balance') !== false) {
                    $message = 'لا يمكن الوصول إلى الرصيد حالياً';
                } elseif (strpos($message, 'Balance retrieved successfully') !== false) {
                    $message = 'تم الاستعلام عن الرصيد بنجاح';
                }
                return [
                    'success' => true,
                    'balance' => $data['balance'] ?? '0.00',
                    'message' => $message,
                    'numbers' => $numbers,
                ];
            }
            $errorData = $response->json();
            $errorMessage = $errorData['message'] ?? 'حدث خطأ في الاستعلام عن الرصيد';
            if (strpos($errorMessage, 'Couldn\'t retrieve balance') !== false) {
                $errorMessage = 'لا يمكن الوصول إلى الرصيد حالياً';
            }
            return [
                'success' => false,
                'balance' => $errorData['balance'] ?? '0.00',
                'message' => $errorMessage,
                'numbers' => $numbers,
            ];
        } catch (\Exception $e) {
            Log::error('TransferMoney: getAllBalance exception', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'balance' => '0.00',
                'message' => 'حدث خطأ في الاتصال بالخدمة',
                'numbers' => $numbers,
            ];
        }
    }

    /**
     * AJAX GET: الرصيد الإجمالي (لزر تحديث الرصيد).
     */
    public function getAccountBalance(): JsonResponse
    {
        $result = $this->getAllBalance();
        return response()->json($result);
    }

    /**
     * AJAX: استعلام الرصيد لأرقام محددة.
     */
    public function getBalance(Request $request): JsonResponse
    {
        $request->validate([
            'numbers' => 'required|array',
            'numbers.*' => 'required|string|min:10|max:15',
        ]);
        if (!$this->isConfigured()) {
            return response()->json([
                'success' => false,
                'message' => 'مفتاح API أو عنوان الخدمة غير مُعد',
                'balance' => '0.00',
            ], 422);
        }
        $userNumbers = $this->getPhoneNumbers();
        $requested = $request->input('numbers');
        $invalid = array_diff($requested, $userNumbers);
        if (!empty($invalid)) {
            return response()->json([
                'success' => false,
                'message' => 'بعض الأرقام غير صحيحة أو لا تنتمي للحساب',
                'balance' => '0.00',
            ], 422);
        }
        try {
            $response = Http::timeout(30)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->getApiKey(),
                    'Content-Type' => 'application/json',
                ])
                ->post($this->getBaseUrl() . '/api/v1/accounts/numbers/balance', [
                    'numbers' => $requested,
                ]);

            if ($response->successful()) {
                $data = $response->json();
                $message = $data['message'] ?? 'تم الاستعلام عن الرصيد بنجاح';
                if (strpos($message, 'Couldn\'t retrieve balance') !== false) {
                    $message = 'لا يمكن الوصول إلى الرصيد حالياً';
                }
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'balance' => $data['balance'] ?? '0.00',
                    'numbers' => $requested,
                ]);
            }
            $errorData = $response->json();
            $errorMessage = $errorData['message'] ?? 'حدث خطأ في الاستعلام عن الرصيد';
            if (strpos($errorMessage, 'Couldn\'t retrieve balance') !== false) {
                $errorMessage = 'لا يمكن الوصول إلى الرصيد حالياً';
            }
            return response()->json([
                'success' => false,
                'message' => $errorMessage,
                'balance' => $errorData['balance'] ?? '0.00',
            ], $response->status());
        } catch (\Exception $e) {
            Log::error('TransferMoney: getBalance exception', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ في الاتصال بالخدمة',
                'balance' => '0.00',
            ], 500);
        }
    }

    /**
     * AJAX: تحويل مبلغ من رقم مرسل إلى رقم مستقبل.
     */
    public function transferMoney(Request $request): JsonResponse
    {
        $request->validate([
            'sender_number' => 'required|string|min:10|max:15',
            'receiver_number' => 'required|string|min:10|max:15',
            'amount' => 'required|string|regex:/^\d+(\.\d{1,2})?$/',
        ]);
        if (!$this->isConfigured()) {
            return response()->json([
                'success' => false,
                'message' => 'مفتاح API أو عنوان الخدمة غير مُعد',
            ], 422);
        }
        $userNumbers = $this->getPhoneNumbers();
        $senderNumber = $request->input('sender_number');
        if (!in_array($senderNumber, $userNumbers)) {
            return response()->json([
                'success' => false,
                'message' => 'الرقم المرسل لا ينتمي لحسابك',
            ], 422);
        }
        $receiverNumber = $request->input('receiver_number');
        $amount = $request->input('amount');
        $baseUrl = $this->getBaseUrl();
        $apiKey = $this->getApiKey();

        try {
            $balanceResponse = Http::timeout(30)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $apiKey,
                    'Content-Type' => 'application/json',
                ])
                ->post($baseUrl . '/api/v1/accounts/numbers/balance', [
                    'numbers' => [$senderNumber],
                ]);

            if ($balanceResponse->successful()) {
                $balanceData = $balanceResponse->json();
                $balanceMessage = $balanceData['message'] ?? '';
                if (strpos($balanceMessage, 'Couldn\'t retrieve balance') !== false) {
                    return response()->json([
                        'success' => false,
                        'message' => 'لا يمكن الوصول إلى الرصيد حالياً. يرجى المحاولة لاحقاً',
                    ], 422);
                }
                $currentBalance = (float) ($balanceData['balance'] ?? 0);
                $transferAmount = (float) $amount;
                if ($currentBalance < $transferAmount) {
                    return response()->json([
                        'success' => false,
                        'message' => 'رصيد غير كافٍ. الرصيد الحالي: ' . number_format($currentBalance, 2) . ' ج.م، المطلوب: ' . number_format($transferAmount, 2) . ' ج.م',
                    ], 422);
                }
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'لا يمكن التحقق من الرصيد. يرجى المحاولة لاحقاً',
                ], 422);
            }

            $response = Http::timeout(30)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $apiKey,
                    'Content-Type' => 'application/json',
                ])
                ->post($baseUrl . '/api/v1/accounts/transfer', [
                    'sender_number' => $senderNumber,
                    'receiver_number' => $receiverNumber,
                    'amount' => $amount,
                ]);

            if ($response->successful()) {
                $data = $response->json();
                return response()->json([
                    'success' => true,
                    'message' => $data['message'] ?? 'تم تحويل الأموال بنجاح',
                    'status' => $data['status'] ?? 'SUCCESS',
                ]);
            }
            $errorData = $response->json();
            $errorMessage = $errorData['message'] ?? 'حدث خطأ في تحويل الأموال';
            if (isset($errorData['remainingBalance']) && $errorData['remainingBalance'] == 0) {
                $errorMessage = 'رصيد غير كافٍ للتحويل. تأكد من الرصيد على الرقم المرسل.';
            } elseif (!empty($errorData['remainingBalance'])) {
                $errorMessage .= ' (الرصيد المتبقي: ' . $errorData['remainingBalance'] . ' ج.م)';
            }
            return response()->json([
                'success' => false,
                'message' => $errorMessage,
                'status' => $errorData['status'] ?? 'FAILURE',
            ], $response->status());
        } catch (\Exception $e) {
            Log::error('TransferMoney: transferMoney exception', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ في الاتصال بالخدمة',
            ], 500);
        }
    }
}
