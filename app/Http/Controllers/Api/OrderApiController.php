<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrderRequest;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class OrderApiController extends Controller
{
    /**
     * Display the specified order.
     */
    public function show(string $id): JsonResponse
    {
        $order = Order::find($id);

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'الطلب غير موجود'
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }

        $formattedOrder = [
            'id' => (string) $order->id,
            'customer_name' => $order->customer_name,
            'customer_address' => $order->customer_address,
            'customer_numbers' => $order->customer_numbers ?? [],
            'delivery_fees' => (float) $order->delivery_fees,
            'governorate' => $order->governorate?->name ?? '',
            'items' => $order->items ?? [],
            'total_amount' => (float) $order->total_amount,
            'status' => $order->status,
            'payment_method' => $order->payment_method,
            'created_at' => $order->created_at->toISOString(),
            'updated_at' => $order->updated_at->toISOString(),
        ];

        return response()->json([
            'success' => true,
            'data' => $formattedOrder
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }

    /**
     * Store a newly created order.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'customer_name' => 'required|string|max:255',
            'customer_address' => 'required|string',
            'customer_numbers' => 'required|array|min:1',
            'customer_numbers.*' => 'string|max:20',
            'governorate_id' => 'nullable|exists:governorates,id',
            'delivery_fees' => 'required|numeric|min:0',
            'items' => 'required|array|min:1',
            'items.*.product_name' => 'required|string|max:255',
            'items.*.product_id' => 'required|string',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.size' => 'nullable|string|max:50',
            'items.*.color' => 'nullable|string|max:50',
            'total_amount' => 'required|numeric|min:0',
            'status' => 'required|in:pending,accepted,delivery_fees_paid,shipped',
            'payment_method' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'خطأ في التحقق من البيانات',
                'errors' => $validator->errors()
            ], 422, [], JSON_UNESCAPED_UNICODE);
        }

        try {
            $validated = $validator->validated();
            if (empty($validated['tracking_id'])) {
                $validated['tracking_id'] = Order::generateTrackingId();
            }
            $order = Order::create($validated);

            $mylerzResult = $this->sendToMylerzIfNeeded($order);

            $formattedOrder = [
                'id' => (string) $order->id,
                'customer_name' => $order->customer_name,
                'customer_address' => $order->customer_address,
                'customer_numbers' => $order->customer_numbers ?? [],
                'delivery_fees' => (float) $order->delivery_fees,
                'governorate' => $order->governorate?->name ?? '',
                'items' => $order->items ?? [],
                'total_amount' => (float) $order->total_amount,
                'status' => $order->status,
                'payment_method' => $order->payment_method,
                'created_at' => $order->created_at->toISOString(),
                'updated_at' => $order->updated_at->toISOString(),
                'shipping_data' => $order->shipping_data,
            ];

            $response = [
                'success' => true,
                'message' => 'تم إنشاء الطلب بنجاح',
                'data' => $formattedOrder
            ];

            if (!empty($mylerzResult['error'])) {
                $response['mylerz_warning'] = $mylerzResult['error'];
            } elseif (!empty($mylerzResult['success'])) {
                $response['mylerz_status'] = 'تم إرسال الطلب لـ Mylerz بنجاح';
            }

            return response()->json($response, 201, [], JSON_UNESCAPED_UNICODE);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء إنشاء الطلب',
                'error' => $e->getMessage()
            ], 500, [], JSON_UNESCAPED_UNICODE);
        }
    }

    /**
     * Update the specified order.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $order = Order::find($id);

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'الطلب غير موجود'
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }

        $validator = Validator::make($request->all(), [
            'customer_name' => 'required|string|max:255',
            'customer_address' => 'required|string',
            'customer_numbers' => 'required|array|min:1',
            'customer_numbers.*' => 'string|max:20',
            'governorate_id' => 'nullable|exists:governorates,id',
            'delivery_fees' => 'required|numeric|min:0',
            'items' => 'required|array|min:1',
            'items.*.product_name' => 'required|string|max:255',
            'items.*.product_id' => 'required|string',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.size' => 'nullable|string|max:50',
            'items.*.color' => 'nullable|string|max:50',
            'total_amount' => 'required|numeric|min:0',
            'status' => 'required|in:pending,accepted,delivery_fees_paid,shipped',
            'payment_method' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'خطأ في التحقق من البيانات',
                'errors' => $validator->errors()
            ], 422, [], JSON_UNESCAPED_UNICODE);
        }

        try {
            $oldStatus = $order->status;
            $order->update($validator->validated());

            $mylerzResult = $this->sendToMylerzIfNeeded($order, $oldStatus);

            $formattedOrder = [
                'id' => (string) $order->id,
                'customer_name' => $order->customer_name,
                'customer_address' => $order->customer_address,
                'customer_numbers' => $order->customer_numbers ?? [],
                'delivery_fees' => (float) $order->delivery_fees,
                'governorate' => $order->governorate?->name ?? '',
                'items' => $order->items ?? [],
                'total_amount' => (float) $order->total_amount,
                'status' => $order->status,
                'payment_method' => $order->payment_method,
                'created_at' => $order->created_at->toISOString(),
                'updated_at' => $order->updated_at->toISOString(),
                'shipping_data' => $order->shipping_data,
            ];

            $response = [
                'success' => true,
                'message' => 'تم تحديث الطلب بنجاح',
                'data' => $formattedOrder
            ];

            if (!empty($mylerzResult['error'])) {
                $response['mylerz_warning'] = $mylerzResult['error'];
            } elseif (!empty($mylerzResult['success'])) {
                $response['mylerz_status'] = 'تم إرسال الطلب لـ Mylerz بنجاح';
            }

            return response()->json($response, 200, [], JSON_UNESCAPED_UNICODE);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تحديث الطلب',
                'error' => $e->getMessage()
            ], 500, [], JSON_UNESCAPED_UNICODE);
        }
    }

    /**
     * Send order to Mylerz if status is accepted or delivery_fees_paid and not already sent.
     */
    protected function sendToMylerzIfNeeded(Order $order, ?string $oldStatus = null): array
    {
        $acceptedStatuses = ['accepted', 'delivery_fees_paid'];
        
        if (!in_array($order->status, $acceptedStatuses)) {
            return [];
        }

        if (!empty($order->shipping_data['barcode'])) {
            return [];
        }

        if ($oldStatus !== null && in_array($oldStatus, $acceptedStatuses)) {
            return [];
        }

        if (!config('plugins.mylerz.enabled', false)) {
            Log::info('Mylerz: disabled, skipping API order', ['order_id' => $order->id]);
            return [];
        }

        try {
            $mylerz = app(\Plugins\Shipping\Mylerz\MylerzService::class);
            if (!$mylerz->isConfigured()) {
                Log::warning('Mylerz: not configured for API order', ['order_id' => $order->id]);
                return ['error' => 'Mylerz غير مُعد بشكل صحيح'];
            }

            Log::info('Mylerz: Sending API order to Mylerz', [
                'order_id' => $order->id,
                'tracking_id' => $order->tracking_id,
                'status' => $order->status,
            ]);

            $result = $mylerz->createShipment($order);

            if ($result['success'] && !empty($result['shipping_data'])) {
                $order->update(['shipping_data' => $result['shipping_data']]);
                Log::info('Mylerz: API order sent successfully', [
                    'order_id' => $order->id,
                    'barcode' => $result['barcode'] ?? '',
                ]);
                $this->sendWhatsAppOrderConfirmation($order);
                return ['success' => true, 'barcode' => $result['barcode'] ?? ''];
            }

            $error = $result['error'] ?? 'فشل إرسال الطلب لـ Mylerz';
            Log::error('Mylerz: createShipment failed for API order', [
                'order_id' => $order->id,
                'result' => $result,
            ]);
            return ['error' => $error];

        } catch (\Throwable $e) {
            Log::error('Mylerz shipment exception (API order)', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return ['error' => $e->getMessage()];
        }
    }
}
