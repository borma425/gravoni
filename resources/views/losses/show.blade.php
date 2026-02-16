@extends('layouts.app')

@section('title', 'تفاصيل الخسارة')

@section('content')
<div class="mb-8">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">تفاصيل الخسارة</h1>
            <p class="mt-2 text-sm text-gray-600">معلومات الخسارة من التلف</p>
        </div>
        <a href="{{ route('losses.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-slate-500 transition-colors">
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
                <dd class="mt-1 text-sm text-gray-900">{{ $loss->product->name }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">SKU</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $loss->product->sku }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">الكمية التالفة</dt>
                <dd class="mt-1">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                        {{ $loss->quantity }} وحدة
                    </span>
                </dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">سعر التكلفة وقت التلف</dt>
                <dd class="mt-1 text-sm text-gray-600">{{ number_format($loss->cost_price_at_loss, 2) }} ج.م</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">إجمالي الخسارة</dt>
                <dd class="mt-1 text-sm font-semibold text-red-600">{{ number_format($loss->total_loss, 2) }} ج.م</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">ملاحظة</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $loss->note ?? '-' }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">التاريخ</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $loss->created_at->format('Y-m-d H:i') }}</dd>
            </div>
        </dl>
    </div>
</div>
@endsection

