<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Plugins\Payment\CashUpCash\CashUpCashService;

class CashUpCashController extends Controller
{
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
            'payment_intent_id' => 'required|string|max:255',
            'sender_identifier' => 'required|string|max:100|regex:/^[a-zA-Z0-9\s\x{0600}-\x{06FF}]+$/u',
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

        $service = app(CashUpCashService::class);
        $result = $service->validatePayment(
            $request->payment_intent_id,
            trim($request->sender_identifier)
        );

        if (!$result['success']) {
            return response()->json($result, 400);
        }

        $amountPaid = $result['amount_paid'] ?? $pending['amount'];

        session([
            'cashup_verified_payment' => [
                'payment_intent_id' => $request->payment_intent_id,
                'amount' => $amountPaid,
                'sender_identifier' => trim($request->sender_identifier),
                'validated_at' => now()->toIso8601String(),
            ],
        ]);
        session()->forget('cashup_pending_payment');

        return response()->json(array_merge($result, [
            'message' => $result['message'] ?? 'تم التحقق من الدفع بنجاح. يمكنك تأكيد الطلب الآن.',
        ]));
    }
}
