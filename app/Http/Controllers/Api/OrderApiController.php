<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrderRequest;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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
            'delivery_fees' => 'required|numeric|min:0',
            'items' => 'required|array|min:1',
            'items.*.product_name' => 'required|string|max:255',
            'items.*.product_id' => 'required|string',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.size' => 'nullable|string|max:50',
            'items.*.color' => 'nullable|string|max:50',
            'total_amount' => 'required|numeric|min:0',
            'status' => 'required|in:pending,delivery_fees_paid,shipped',
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
            $order = Order::create($validator->validated());

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
                'message' => 'تم إنشاء الطلب بنجاح',
                'data' => $formattedOrder
            ], 201, [], JSON_UNESCAPED_UNICODE);
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
            'delivery_fees' => 'required|numeric|min:0',
            'items' => 'required|array|min:1',
            'items.*.product_name' => 'required|string|max:255',
            'items.*.product_id' => 'required|string',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.size' => 'nullable|string|max:50',
            'items.*.color' => 'nullable|string|max:50',
            'total_amount' => 'required|numeric|min:0',
            'status' => 'required|in:pending,delivery_fees_paid,shipped',
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
            $order->update($validator->validated());

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
                'message' => 'تم تحديث الطلب بنجاح',
                'data' => $formattedOrder
            ], 200, [], JSON_UNESCAPED_UNICODE);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تحديث الطلب',
                'error' => $e->getMessage()
            ], 500, [], JSON_UNESCAPED_UNICODE);
        }
    }
}
