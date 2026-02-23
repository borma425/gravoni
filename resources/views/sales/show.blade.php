@extends('layouts.app')

@section('title', 'تفاصيل البيع')

@section('content')
<div class="mb-8">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">تفاصيل البيع</h1>
            <p class="mt-2 text-sm text-gray-600">معلومات عملية البيع</p>
        </div>
        <a href="{{ route('sales.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-slate-500 transition-colors">
            <svg class="ml-2 -mr-1 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            العودة
        </a>
    </div>
</div>

<div class="bg-white shadow rounded-lg">
    <div class="px-4 py-5 sm:p-6">
        <dl class="grid grid-cols-1 gap-6 sm:grid-cols-2">
            <div>
                <dt class="text-sm font-medium text-gray-500">المنتج</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $sale->product->name }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">SKU</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $sale->product->sku }}</dd>
            </div>
            @if($sale->size || $sale->color)
            <div>
                <dt class="text-sm font-medium text-gray-500">النوع</dt>
                <dd class="mt-1 text-sm font-bold text-violet-600">
                    {{ $sale->size ? 'مقاس: ' . $sale->size : '' }}
                    {{ $sale->size && $sale->color ? ' | ' : '' }}
                    {{ $sale->color ? 'لون: ' . $sale->color : '' }}
                </dd>
            </div>
            @endif
            <div>
                <dt class="text-sm font-medium text-gray-500">الكمية</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $sale->quantity }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">سعر البيع</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ number_format($sale->selling_price, 2) }} ج.م</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">سعر الشراء</dt>
                <dd class="mt-1 text-sm text-gray-600">{{ number_format($sale->cost_price_at_sale, 2) }} ج.م</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">الربح</dt>
                <dd class="mt-1">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $sale->profit >= 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ number_format($sale->profit, 2) }} ج.م
                    </span>
                </dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">إجمالي المبيعات</dt>
                <dd class="mt-1 text-sm font-semibold text-gray-900">{{ number_format($sale->quantity * $sale->selling_price, 2) }} ج.م</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">المحافظة</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $sale->governorate ?? '-' }}</dd>
            </div>
            @if($sale->returned_quantity > 0)
            <div>
                <dt class="text-sm font-medium text-gray-500">الكمية المرتجعة</dt>
                <dd class="mt-1">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                        {{ $sale->returned_quantity }} وحدة
                    </span>
                </dd>
            </div>
            @endif
            <div>
                <dt class="text-sm font-medium text-gray-500">التاريخ</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $sale->created_at->format('Y-m-d H:i') }}</dd>
            </div>
        </dl>
    </div>
</div>
@endsection
