<?php

namespace App\Http\Controllers;

use App\Http\Requests\CheckoutRequest;
use App\Models\Governorate;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;

class StoreController extends Controller
{
    public function index()
    {
        $products = Product::latest()->paginate(12);
        return view('store.index', compact('products'));
    }

    public function show(Product $product)
    {
        return view('store.show', compact('product'));
    }

    public function checkout()
    {
        $cart = session('cart', []);
        if (empty($cart)) {
            return redirect()->route('store.index')->with('info', 'السلة فارغة');
        }

        $items = [];
        $subtotal = 0;
        foreach ($cart as $key => $item) {
            $product = Product::find($item['product_id']);
            if (!$product) continue;
            $price = $item['price'];
            $qty = (int)($item['quantity'] ?? 1);
            $rowTotal = $price * $qty;
            $subtotal += $rowTotal;
            $items[] = [
                'key' => $key,
                'product' => $product,
                'quantity' => $qty,
                'size' => $item['size'] ?? '',
                'color' => $item['color'] ?? '',
                'price' => $price,
                'row_total' => $rowTotal,
            ];
        }

        $governorates = Governorate::orderBy('name')->get();
        return view('store.checkout', compact('items', 'subtotal', 'governorates'));
    }

    public function placeOrder(CheckoutRequest $request)
    {
        $cart = session('cart', []);
        if (empty($cart)) {
            return redirect()->route('store.index')->with('error', 'السلة فارغة');
        }

        $items = [];
        $subtotal = 0;
        foreach ($cart as $item) {
            $product = Product::find($item['product_id']);
            if (!$product) continue;
            $price = $item['price'];
            $qty = (int)($item['quantity'] ?? 1);
            $items[] = [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'quantity' => $qty,
                'size' => $item['size'] ?? '',
                'color' => $item['color'] ?? '',
                'price' => $price,
            ];
            $subtotal += $price * $qty;
        }

        $governorate = $request->governorate_id
            ? \App\Models\Governorate::find($request->governorate_id)
            : null;
        $deliveryFees = $governorate ? (float) $governorate->shipping_fee : 0;
        $totalAmount = $subtotal + $deliveryFees;

        $order = Order::create([
            'tracking_id' => Order::generateTrackingId(),
            'customer_name' => $request->customer_name,
            'customer_address' => $request->customer_address,
            'customer_numbers' => [$request->customer_phone],
            'governorate_id' => $governorate?->id,
            'delivery_fees' => $deliveryFees,
            'items' => $items,
            'total_amount' => $totalAmount,
            'status' => 'accepted',
            'payment_method' => 'cod',
        ]);

        $mylerzError = null;
        if (config('plugins.mylerz.enabled', false)) {
            try {
                $mylerz = app(\Plugins\Shipping\Mylerz\MylerzService::class);
                if ($mylerz->isConfigured()) {
                    \Illuminate\Support\Facades\Log::info('Mylerz: Sending order to Mylerz', ['order_id' => $order->id, 'tracking_id' => $order->tracking_id]);
                    $result = $mylerz->createShipment($order);
                    if ($result['success'] && !empty($result['shipping_data'])) {
                        $order->update(['shipping_data' => $result['shipping_data']]);
                        \Illuminate\Support\Facades\Log::info('Mylerz: Order sent successfully', ['order_id' => $order->id, 'barcode' => $result['barcode'] ?? '']);
                    } else {
                        $mylerzError = $result['error'] ?? 'فشل إرسال الطلب لـ Mylerz';
                        \Illuminate\Support\Facades\Log::error('Mylerz: createShipment failed', ['order_id' => $order->id, 'result' => $result]);
                    }
                } else {
                    $mylerzError = 'Mylerz غير مُعد بشكل صحيح';
                    \Illuminate\Support\Facades\Log::warning('Mylerz: not configured', ['order_id' => $order->id]);
                }
            } catch (\Throwable $e) {
                $mylerzError = $e->getMessage();
                \Illuminate\Support\Facades\Log::error('Mylerz shipment exception', [
                    'order_id' => $order->id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
            }
        } else {
            \Illuminate\Support\Facades\Log::info('Mylerz: disabled, skipping', ['order_id' => $order->id]);
        }

        session()->forget('cart');

        return redirect()->route('store.order.success', $order)
            ->with('order', $order)
            ->with('mylerz_error', $mylerzError);
    }

    public function orderSuccess(Order $order)
    {
        return view('store.order-success', compact('order'));
    }

    public function track(string $trackingId)
    {
        $order = Order::where('tracking_id', $trackingId)->first();
        if (!$order) {
            abort(404, 'الطلب غير موجود');
        }
        return view('store.track', compact('order'));
    }
}
