<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderRequest;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Order::query();

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($qry) use ($q) {
                $qry->where('customer_name', 'like', "%{$q}%")
                    ->orWhere('customer_address', 'like', "%{$q}%")
                    ->orWhere('tracking_id', 'like', "%{$q}%")
                    ->orWhere('customer_numbers', 'like', "%{$q}%");
            });
        }

        $orders = $query->with('governorate')->latest()->paginate(20)->withQueryString();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'html' => view('orders.partials.table', compact('orders'))->render(),
            ]);
        }

        return view('orders.index', compact('orders'));
    }

    public function reject(Order $order)
    {
        if ($order->status === 'cancelled') {
            return redirect()->route('orders.index')->with('error', 'الطلب مرفوض بالفعل');
        }

        $barcode = $order->shipping_data['barcode'] ?? null;
        if ($barcode && config('plugins.mylerz.enabled', false)) {
            try {
                $mylerz = app(\Plugins\Shipping\Mylerz\MylerzService::class);
                if ($mylerz->isConfigured()) {
                    $mylerz->cancelShipment($barcode);
                }
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::error('Mylerz cancel failed', [
                    'order_id' => $order->id,
                    'barcode' => $barcode,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $order->update(['status' => 'cancelled']);
        return redirect()->route('orders.index')->with('success', $barcode ? 'تم رفض الطلب وإلغاؤه في Mylerz' : 'تم رفض الطلب');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $products = Product::all();
        $governorates = \App\Models\Governorate::orderBy('name')->get();
        return view('orders.create', compact('products', 'governorates'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreOrderRequest $request)
    {
        $data = $request->validated();
        if (empty($data['tracking_id'])) {
            $data['tracking_id'] = Order::generateTrackingId();
        }
        $order = Order::create($data);

        $mylerzError = null;
        if (config('plugins.mylerz.enabled', false)) {
            try {
                $mylerz = app(\Plugins\Shipping\Mylerz\MylerzService::class);
                if ($mylerz->isConfigured()) {
                    \Illuminate\Support\Facades\Log::info('Mylerz: Sending manual order to Mylerz', ['order_id' => $order->id, 'tracking_id' => $order->tracking_id]);
                    $result = $mylerz->createShipment($order);
                    if ($result['success'] && !empty($result['shipping_data'])) {
                        $order->update(['shipping_data' => $result['shipping_data']]);
                        \Illuminate\Support\Facades\Log::info('Mylerz: Manual order sent successfully', ['order_id' => $order->id, 'barcode' => $result['barcode'] ?? '']);
                        $this->sendWhatsAppOrderConfirmation($order);
                    } else {
                        $mylerzError = $result['error'] ?? 'فشل إرسال الطلب لـ Mylerz';
                        \Illuminate\Support\Facades\Log::error('Mylerz: createShipment failed for manual order', ['order_id' => $order->id, 'result' => $result]);
                    }
                } else {
                    $mylerzError = 'Mylerz غير مُعد بشكل صحيح';
                }
            } catch (\Throwable $e) {
                $mylerzError = $e->getMessage();
                \Illuminate\Support\Facades\Log::error('Mylerz shipment exception (manual order)', [
                    'order_id' => $order->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $message = 'تم إضافة الطلب بنجاح';
        if ($mylerzError) {
            $message .= '. تنبيه: ' . $mylerzError;
        } elseif (config('plugins.mylerz.enabled', false)) {
            $message .= ' وتم إرساله إلى Mylerz بنجاح.';
        }

        return redirect()->route('orders.index')
            ->with('success', $message);
    }

    /**
     * Display the specified resource.
     */
    public function show(Order $order)
    {
        return view('orders.show', compact('order'));
    }

    /**
     * Download/print Mylerz shipping label (AWB) PDF.
     */
    public function mylerzLabel(Order $order)
    {
        $barcode = $order->shipping_data['barcode'] ?? null;
        if (!$barcode) {
            return redirect()->route('orders.show', $order)
                ->with('error', 'لا يوجد باركود Mylerz لهذا الطلب.');
        }

        if (!config('plugins.mylerz.enabled', false)) {
            return redirect()->route('orders.show', $order)
                ->with('error', 'تكامل Mylerz غير مفعّل.');
        }

        $mylerz = app(\Plugins\Shipping\Mylerz\MylerzService::class);
        if (!$mylerz->isConfigured()) {
            return redirect()->route('orders.show', $order)
                ->with('error', 'Mylerz غير مُعد بشكل صحيح.');
        }

        $pdf = $mylerz->getShippingLabelPdf($order);
        if (!$pdf) {
            return redirect()->route('orders.show', $order)
                ->with('error', 'فشل تحميل ملصق الشحن من Mylerz.');
        }

        $filename = 'mylerz-label-' . $order->tracking_id . '.pdf';

        return response($pdf, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $filename . '"',
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Order $order)
    {
        $products = Product::all();
        return view('orders.edit', compact('order', 'products'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreOrderRequest $request, Order $order)
    {
        $order->update($request->validated());

        return redirect()->route('orders.index')
            ->with('success', 'تم تحديث الطلب بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Order $order)
    {
        $order->delete();

        return redirect()->route('orders.index')
            ->with('success', 'تم حذف الطلب بنجاح');
    }
}
