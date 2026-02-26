@extends('layouts.app')

@section('title', 'المبيعات')

@section('content')
<div class="mb-6 sm:mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div class="min-w-0">
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">المبيعات</h1>
        <p class="mt-1 sm:mt-2 text-sm text-gray-600">الأوردرات المقبولة والمبيعات اليدوية</p>
    </div>
    <a href="{{ route('sales.create') }}" class="inline-flex items-center justify-center px-4 py-2.5 rounded-xl bg-slate-800 text-white text-sm font-medium hover:bg-slate-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-slate-500 transition-colors flex-shrink-0">
        <svg class="w-5 h-5 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
        </svg>
        تسجيل بيع يدوي
    </a>
</div>

@if(session('success'))
    <div class="mb-4 rounded-lg bg-emerald-50 border border-emerald-200 px-4 py-3 text-sm text-emerald-800">
        {{ session('success') }}
    </div>
@endif
@if(session('error'))
    <div class="mb-4 rounded-lg bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-800">
        {{ session('error') }}
    </div>
@endif
@if($errors->any())
    <div class="mb-4 rounded-lg bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-800">
        {{ $errors->first() }}
    </div>
@endif

{{-- إحصائيات --}}
<div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="px-5 py-4 sm:p-5">
            <div class="flex items-center gap-3">
                <div class="flex-shrink-0 w-12 h-12 rounded-xl bg-emerald-100 flex items-center justify-center">
                    <svg class="w-6 h-6 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-slate-500">عدد المبيعات</p>
                    <p class="text-2xl font-bold text-slate-900">{{ number_format($totalSalesCount) }}</p>
                </div>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="px-5 py-4 sm:p-5">
            <div class="flex items-center gap-3">
                <div class="flex-shrink-0 w-12 h-12 rounded-xl bg-amber-100 flex items-center justify-center">
                    <svg class="w-6 h-6 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-slate-500">إجمالي الإيرادات</p>
                    <p class="text-2xl font-bold text-slate-900">{{ number_format($totalRevenue, 2) }} <span class="text-base font-normal text-slate-500">ج.م</span></p>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- بحث موحد في كل المبيعات --}}
<div class="mb-6">
    <form method="GET" action="{{ route('sales.index') }}" class="relative max-w-2xl">
        <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none text-gray-400">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
        </div>
        <input type="text" name="q" id="sales-search" value="{{ $q ?? '' }}"
               placeholder="ابحث باسم المنتج، اسم العميل، رقم التتبع، العنوان، المحافظة..."
               class="w-full py-3.5 pl-4 pr-12 rounded-2xl border border-gray-200 bg-white shadow-sm
                      focus:ring-2 focus:ring-slate-400 focus:border-slate-400 focus:outline-none
                      placeholder-gray-400 text-gray-900 transition-all duration-200">
        @if(!empty($q))
        <a href="{{ route('sales.index') }}" class="absolute inset-y-0 left-4 flex items-center text-slate-500 hover:text-slate-700 transition-colors" title="مسح البحث">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </a>
        @endif
    </form>
    @if(!empty($q))
    <p class="mt-2 text-sm text-slate-600">عرض {{ $totalSalesCount }} نتيجة لـ "<strong>{{ $q }}</strong>"</p>
    @endif
</div>

{{-- قائمة المبيعات --}}
<h2 class="text-lg font-semibold text-slate-900 mb-4">الأوردرات المقبولة</h2>
<div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-200">
            <thead class="bg-slate-50/80">
                <tr>
                    <th scope="col" class="px-4 sm:px-6 py-3.5 text-right text-xs font-semibold text-slate-600 uppercase tracking-wider">رقم التتبع</th>
                    <th scope="col" class="px-4 sm:px-6 py-3.5 text-right text-xs font-semibold text-slate-600 uppercase tracking-wider">العميل</th>
                    <th scope="col" class="px-4 sm:px-6 py-3.5 text-right text-xs font-semibold text-slate-600 uppercase tracking-wider">المحافظة</th>
                    <th scope="col" class="px-4 sm:px-6 py-3.5 text-right text-xs font-semibold text-slate-600 uppercase tracking-wider">المنتج</th>
                    <th scope="col" class="px-4 sm:px-6 py-3.5 text-right text-xs font-semibold text-slate-600 uppercase tracking-wider">الكمية</th>
                    <th scope="col" class="px-4 sm:px-6 py-3.5 text-right text-xs font-semibold text-slate-600 uppercase tracking-wider">سعر البيع</th>
                    <th scope="col" class="px-4 sm:px-6 py-3.5 text-right text-xs font-semibold text-slate-600 uppercase tracking-wider">الربح</th>
                    <th scope="col" class="px-4 sm:px-6 py-3.5 text-right text-xs font-semibold text-slate-600 uppercase tracking-wider">الحالة</th>
                    <th scope="col" class="px-4 sm:px-6 py-3.5 text-right text-xs font-semibold text-slate-600 uppercase tracking-wider">التاريخ</th>
                    <th scope="col" class="px-4 sm:px-6 py-3.5 text-right text-xs font-semibold text-slate-600 uppercase tracking-wider">حذف</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-slate-100">
                @php
                    $statusColors = [
                        'accepted' => 'bg-emerald-100 text-emerald-800',
                        'delivery_fees_paid' => 'bg-blue-100 text-blue-800',
                        'shipped' => 'bg-green-100 text-green-800',
                    ];
                @endphp
                @forelse($orders as $order)
                @php
                    $items = $order->items ?? [];
                    $productNames = collect($items)->pluck('product_name')->filter()->implode('، ');
                    $totalQty = collect($items)->sum('quantity');
                    $subtotal = collect($items)->sum(fn($i) => ($i['price'] ?? 0) * ($i['quantity'] ?? 0));
                    $orderProfit = 0;
                    foreach ($items as $item) {
                        $pid = $item['product_id'] ?? null;
                        $prod = $pid ? ($products[$pid] ?? null) : null;
                        $cost = $prod ? ($prod->average_cost_price ?? 0) : 0;
                        $orderProfit += (($item['price'] ?? 0) - $cost) * ($item['quantity'] ?? 0);
                    }
                @endphp
                <tr class="hover:bg-slate-50/50 transition-colors">
                    <td class="px-4 sm:px-6 py-4">
                        @if($order->tracking_id)
                            <span class="font-mono text-sm font-semibold text-slate-900">{{ $order->tracking_id }}</span>
                            <a href="{{ url(route('store.track', $order->tracking_id)) }}" target="_blank" class="block mt-1 text-xs text-slate-500 hover:text-slate-700">فتح التتبع</a>
                        @else
                            <span class="text-slate-400">—</span>
                        @endif
                    </td>
                    <td class="px-4 sm:px-6 py-4">
                        <div class="text-sm font-medium text-slate-900">{{ $order->customer_name }}</div>
                        <div class="text-xs text-slate-500 max-w-[180px] truncate">{{ $order->customer_address }}</div>
                    </td>
                    <td class="px-4 sm:px-6 py-4 text-sm text-slate-600">
                        {{ $order->governorate?->name ?? '—' }}
                    </td>
                    <td class="px-4 sm:px-6 py-4 text-sm text-slate-900 max-w-[200px]">
                        {{ $productNames ?: '—' }}
                    </td>
                    <td class="px-4 sm:px-6 py-4 text-sm text-slate-900">
                        {{ $totalQty }}
                    </td>
                    <td class="px-4 sm:px-6 py-4">
                        <span class="text-sm font-medium text-slate-900">{{ number_format($subtotal, 2) }} ج.م</span>
                    </td>
                    <td class="px-4 sm:px-6 py-4">
                        <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium {{ $orderProfit >= 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ number_format($orderProfit, 2) }} ج.م
                        </span>
                    </td>
                    <td class="px-4 sm:px-6 py-4">
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium {{ $statusColors[$order->status] ?? 'bg-slate-100 text-slate-800' }}">
                            {{ $order->status_label }}
                        </span>
                    </td>
                    <td class="px-4 sm:px-6 py-4 text-sm text-slate-500">
                        {{ $order->created_at->format('Y-m-d') }}<br>
                        <span class="text-xs">{{ $order->created_at->format('H:i') }}</span>
                    </td>
                    <td class="px-4 sm:px-6 py-4">
                        <form action="{{ route('sales.destroy-order', $order) }}" method="POST" class="inline" onsubmit="return confirm('هل أنت متأكد من حذف هذه المبيعة؟ سيتم حذف الطلب نهائياً.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-red-600 hover:bg-red-50 hover:text-red-700 transition-colors" title="حذف">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="px-4 sm:px-6 py-16 text-center">
                        <div class="flex flex-col items-center">
                            <div class="w-14 h-14 rounded-2xl bg-slate-100 flex items-center justify-center mb-4">
                                <svg class="w-7 h-7 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                </svg>
                            </div>
                            <h3 class="text-base font-semibold text-slate-900">لا توجد مبيعات</h3>
                            <p class="mt-1 text-sm text-slate-500">ستظهر هنا الأوردرات المقبولة فقط</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($orders->hasPages())
    <div class="bg-slate-50/50 px-4 py-3 border-t border-slate-100 sm:px-6">
        {{ $orders->withQueryString()->links() }}
    </div>
    @endif
</div>

{{-- المبيعات اليدوية --}}
<div class="mt-8">
    <h2 class="text-lg font-semibold text-slate-900 mb-4">المبيعات اليدوية</h2>
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50/80">
                    <tr>
                        <th scope="col" class="px-4 sm:px-6 py-3.5 text-right text-xs font-semibold text-slate-600 uppercase tracking-wider">المنتج</th>
                        <th scope="col" class="px-4 sm:px-6 py-3.5 text-right text-xs font-semibold text-slate-600 uppercase tracking-wider">الكمية</th>
                        <th scope="col" class="px-4 sm:px-6 py-3.5 text-right text-xs font-semibold text-slate-600 uppercase tracking-wider">سعر البيع</th>
                        <th scope="col" class="px-4 sm:px-6 py-3.5 text-right text-xs font-semibold text-slate-600 uppercase tracking-wider">الربح</th>
                        <th scope="col" class="px-4 sm:px-6 py-3.5 text-right text-xs font-semibold text-slate-600 uppercase tracking-wider">المحافظة</th>
                        <th scope="col" class="px-4 sm:px-6 py-3.5 text-right text-xs font-semibold text-slate-600 uppercase tracking-wider">التاريخ</th>
                        <th scope="col" class="px-4 sm:px-6 py-3.5 text-right text-xs font-semibold text-slate-600 uppercase tracking-wider">حذف</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-slate-100">
                    @forelse($manualSales as $sale)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-4 sm:px-6 py-4">
                            <div class="text-sm font-medium text-slate-900">{{ $sale->product->name ?? '—' }}</div>
                            @if($sale->size || $sale->color)
                            <div class="text-xs text-slate-500">
                                {{ $sale->size ? 'مقاس: ' . $sale->size : '' }}{{ $sale->size && $sale->color ? ' | ' : '' }}{{ $sale->color ? 'لون: ' . $sale->color : '' }}
                            </div>
                            @endif
                        </td>
                        <td class="px-4 sm:px-6 py-4 text-sm text-slate-900">{{ $sale->quantity }}</td>
                        <td class="px-4 sm:px-6 py-4 text-sm font-medium text-slate-900">{{ number_format($sale->selling_price, 2) }} ج.م</td>
                        <td class="px-4 sm:px-6 py-4">
                            <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium {{ $sale->profit >= 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ number_format($sale->profit, 2) }} ج.م
                            </span>
                        </td>
                        <td class="px-4 sm:px-6 py-4 text-sm text-slate-600">{{ $sale->governorate ?? '—' }}</td>
                        <td class="px-4 sm:px-6 py-4 text-sm text-slate-500">{{ $sale->created_at->format('Y-m-d H:i') }}</td>
                        <td class="px-4 sm:px-6 py-4">
                            <form action="{{ route('sales.destroy', $sale) }}" method="POST" class="inline" onsubmit="return confirm('هل أنت متأكد من حذف هذا البيع؟ سيتم استعادة الكمية للمنتج.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-red-600 hover:bg-red-50 hover:text-red-700 transition-colors" title="حذف">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-4 sm:px-6 py-8 text-center text-sm text-slate-500">
                            لا توجد مبيعات يدوية. اضغط "تسجيل بيع يدوي" لإضافة بيع.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($manualSales->hasPages())
        <div class="bg-slate-50/50 px-4 py-3 border-t border-slate-100 sm:px-6">
            {{ $manualSales->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
