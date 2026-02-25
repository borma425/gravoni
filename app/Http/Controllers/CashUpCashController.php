<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Plugins\Payment\CashUpCash\CashUpCashService;

class CashUpCashController extends Controller
{
    /**
     * Upload transfer receipt image (for InstaPay).
     */
    public function uploadTransferImage(Request $request): JsonResponse
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,jpg,png,gif,webp|max:5120',
        ]);

        if (!config('plugins.cashup_cash.enabled', false)) {
            return response()->json(['success' => false, 'message' => 'غير مفعّل'], 400);
        }

        $file = $request->file('image');
        $path = $file->store('transfer-receipts', 'public');
        $url = asset('storage/' . $path);

        return response()->json(['success' => true, 'url' => $url]);
    }
    /**
     * Create payment intent for checkout amount.
     */
    public function createPaymentIntent(Request $request): JsonResponse
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01|max:100000',
        ]);

        if (!config('plugins.cashup_cash.enabled', false)) {
            return response()->json(['success' => false, 'message' => 'طريقة الدفع غير مفعّلة'], 400);
        }

        $service = app(CashUpCashService::class);
        $amount = (float) $request->amount;

        $result = $service->createPaymentIntent($amount);

        if (!$result['success']) {
            return response()->json($result, 400);
        }

        session([
            'cashup_pending_payment' => [
                'payment_intent_id' => $result['payment_intent_id'],
                'amount' => $amount,
                'receiver_number' => $result['receiver_number'] ?? null,
                'order_id' => $result['order_id'] ?? null,
            ],
        ]);

        return response()->json($result);
    }

    /**
     * Validate payment and store for order confirmation.
     */
    public function validatePayment(Request $request): JsonResponse
    {
        $request->validate([
            'payment_intent_id' => 'required|string|max:500',
            'sender_identifier' => 'required|string|max:500',
        ]);

        if (!config('plugins.cashup_cash.enabled', false)) {
            return response()->json(['success' => false, 'message' => 'طريقة الدفع غير مفعّلة'], 400);
        }

        $pending = session('cashup_pending_payment');
        if (!$pending || ($pending['payment_intent_id'] ?? '') !== $request->payment_intent_id) {
            return response()->json([
                'success' => false,
                'message' => 'نية الدفع غير صالحة أو منتهية. يرجى البدء من جديد',
            ], 400);
        }

        $senderIdentifier = trim($request->sender_identifier);
        $transferImageUrl = $request->input('transfer_image_url');

        $service = app(CashUpCashService::class);
        $result = $service->validatePayment($request->payment_intent_id, $senderIdentifier);

        if (!$result['success']) {
            return response()->json($result, 400);
        }

        $amountPaid = $result['amount_paid'] ?? $pending['amount'];

        $verifiedData = [
            'payment_intent_id' => $request->payment_intent_id,
            'amount' => $amountPaid,
            'sender_identifier' => $senderIdentifier,
            'validated_at' => now()->toIso8601String(),
        ];
        if (!empty($transferImageUrl)) {
            $verifiedData['transfer_image_url'] = $transferImageUrl;
        }
        session(['cashup_verified_payment' => $verifiedData]);
        session()->forget('cashup_pending_payment');

        return response()->json(array_merge($result, [
            'message' => $result['message'] ?? 'تم التحقق من الدفع بنجاح. يمكنك تأكيد الطلب الآن.',
        ]));
    }
}
