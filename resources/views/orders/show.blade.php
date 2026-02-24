@extends('layouts.app')

@section('title', 'تفاصيل الطلب')

@section('content')
<div class="mb-8">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">تفاصيل الطلب</h1>
            <p class="mt-2 text-sm text-gray-600">معلومات الطلب والمنتجات</p>
        </div>
        <div class="flex items-center space-x-reverse space-x-3">
            <a href="{{ route('orders.edit', $order) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-slate-500 transition-colors">
                <svg class="ml-2 -mr-1 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                تعديل
            </a>
            <a href="{{ route('orders.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-slate-500 transition-colors">
                <svg class="ml-2 -mr-1 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                العودة
            </a>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 gap-6">
    <!-- Customer Information -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">معلومات العميل</h3>
            <dl class="space-y-4">
                <div>
                    <dt class="text-sm font-medium text-gray-500">اسم العميل</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $order->customer_name }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">العنوان</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $order->customer_address }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">المحافظة</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $order->governorate?->name ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">أرقام الهاتف</dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        @foreach($order->customer_numbers as $number)
                            <div>{{ $number }}</div>
                        @endforeach
                    </dd>
                </div>
            </dl>
        </div>
    </div>

    <!-- Order Items -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">المنتجات</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">المنتج</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الكمية</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">السعر</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الحجم</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">اللون</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">المجموع</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($order->items as $item)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $item['product_name'] }}</div>
                                <div class="text-sm text-gray-500">ID: {{ $item['product_id'] }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $item['quantity'] }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ number_format($item['price'], 2) }} ج.م</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $item['size'] ?? '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $item['color'] ?? '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ number_format($item['price'] * $item['quantity'], 2) }} ج.م
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Order Summary -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">ملخص الطلب</h3>
            <dl class="space-y-4">
                <div class="flex justify-between">
                    <dt class="text-sm font-medium text-gray-500">عدد المنتجات</dt>
                    <dd class="text-sm text-gray-900">{{ count($order->items) }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-sm font-medium text-gray-500">رسوم التوصيل</dt>
                    <dd class="text-sm text-gray-900">{{ number_format($order->delivery_fees, 2) }} ج.م</dd>
                </div>
                <div class="flex justify-between border-t border-gray-200 pt-4">
                    <dt class="text-base font-medium text-gray-900">المبلغ الإجمالي</dt>
                    <dd class="text-base font-bold text-gray-900">{{ number_format($order->total_amount, 2) }} ج.م</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">الحالة</dt>
                    <dd class="mt-1">
                        @php
                            $statusColors = [
                                'pending' => 'bg-yellow-100 text-yellow-800',
                                'accepted' => 'bg-emerald-100 text-emerald-800',
                                'delivery_fees_paid' => 'bg-blue-100 text-blue-800',
                                'shipped' => 'bg-green-100 text-green-800',
                                'cancelled' => 'bg-red-100 text-red-800',
                            ];
                        @endphp
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$order->status] ?? 'bg-gray-100 text-gray-800' }}">
                            {{ $order->status_label }}
                        </span>
                    </dd>
                </div>
                @if($order->payment_method)
                <div>
                    <dt class="text-sm font-medium text-gray-500">طريقة الدفع</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $order->payment_method_label }}</dd>
                </div>
                @endif
                <div>
                    <dt class="text-sm font-medium text-gray-500">تاريخ الإنشاء</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $order->created_at->format('Y-m-d H:i') }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">آخر تحديث</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $order->updated_at->format('Y-m-d H:i') }}</dd>
                </div>
            </dl>
        </div>
    </div>
</div>
@endsection

