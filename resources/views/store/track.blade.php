@extends('layouts.store')

@section('title', 'تتبع الطلب ' . $order->tracking_id . ' - جرافوني')

@section('content')
<div class="min-h-screen bg-gray-50 py-12 px-4">
    <div class="max-w-2xl mx-auto">
        <h1 class="text-2xl font-bold text-slate-900 mb-8">تتبع طلبك</h1>

        <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
            <!-- Order Header -->
            <div class="bg-slate-900 text-white p-6">
                <p class="text-slate-300 text-sm">رقم التتبع</p>
                <p class="text-2xl font-bold">{{ $order->tracking_id }}</p>
                <p class="text-slate-300 text-sm mt-2">تاريخ الطلب: {{ $order->created_at->format('Y-m-d H:i') }}</p>
            </div>

            <!-- Status -->
            <div class="p-6 border-b border-gray-100">
                <h2 class="font-bold text-slate-900 mb-4">حالة الطلب</h2>
                <div class="flex items-center gap-3">
                    <span class="px-4 py-2 rounded-xl font-semibold
                        @if($order->status === 'pending') bg-amber-100 text-amber-800
                        @elseif($order->status === 'accepted') bg-emerald-100 text-emerald-800
                        @elseif($order->status === 'delivery_fees_paid') bg-blue-100 text-blue-800
                        @elseif($order->status === 'shipped') bg-green-100 text-green-800
                        @elseif($order->status === 'cancelled') bg-red-100 text-red-800
                        @else bg-gray-100 text-gray-800
                        @endif">
                        {{ $order->status_label }}
                    </span>
                </div>
                <div class="mt-4 flex flex-col gap-2">
                    @if($order->status !== 'cancelled')
                    <div class="flex items-center gap-3">
                        <span class="w-3 h-3 rounded-full {{ in_array($order->status, ['accepted','delivery_fees_paid','shipped']) ? 'bg-green-500' : 'bg-gray-300' }}"></span>
                        <span class="{{ in_array($order->status, ['accepted','delivery_fees_paid','shipped']) ? 'text-slate-900' : 'text-slate-500' }}">تم استلام الطلب</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="w-3 h-3 rounded-full {{ in_array($order->status, ['delivery_fees_paid','shipped']) ? 'bg-green-500' : 'bg-gray-300' }}"></span>
                        <span class="{{ in_array($order->status, ['delivery_fees_paid','shipped']) ? 'text-slate-900' : 'text-slate-500' }}">جاري التجهيز</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="w-3 h-3 rounded-full {{ $order->status === 'shipped' ? 'bg-green-500' : 'bg-gray-300' }}"></span>
                        <span class="{{ $order->status === 'shipped' ? 'text-slate-900' : 'text-slate-500' }}">تم الشحن</span>
                    </div>
                    @else
                    <p class="text-slate-600">تم إلغاء هذا الطلب.</p>
                    @endif
                </div>
            </div>

            <!-- Order Details -->
            <div class="p-6">
                <h2 class="font-bold text-slate-900 mb-4">تفاصيل الطلب</h2>
                <dl class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <dt class="text-slate-500">العميل</dt>
                        <dd class="text-slate-900 font-medium">{{ $order->customer_name }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-slate-500">العنوان</dt>
                        <dd class="text-slate-900">{{ $order->customer_address }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-slate-500">الهاتف</dt>
                        <dd class="text-slate-900">{{ $order->customer_numbers[0] ?? '-' }}</dd>
                    </div>
                </dl>

                <h2 class="font-bold text-slate-900 mt-6 mb-4">المنتجات</h2>
                <ul class="space-y-2">
                    @foreach($order->items ?? [] as $item)
                    <li class="flex justify-between py-2 border-b border-gray-50 last:border-0">
                        <span>{{ $item['product_name'] ?? '' }} × {{ $item['quantity'] ?? 1 }}</span>
                        <span>{{ number_format(($item['price'] ?? 0) * ($item['quantity'] ?? 1), 0) }} ج.م</span>
                    </li>
                    @endforeach
                </ul>

                <div class="mt-4 pt-4 border-t border-gray-100 flex justify-between font-bold">
                    <span>الإجمالي</span>
                    <span>{{ number_format($order->total_amount, 0) }} ج.م</span>
                </div>
            </div>
        </div>

        <div class="mt-6 text-center">
            <a href="{{ route('store.index') }}" class="text-slate-600 hover:text-slate-900 font-medium">← العودة للمتجر</a>
        </div>
    </div>
</div>
@endsection
