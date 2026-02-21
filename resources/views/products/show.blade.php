@extends('layouts.app')

@section('title', 'تفاصيل المنتج')

@section('content')
<div class="mb-8">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">تفاصيل المنتج</h1>
            <p class="mt-2 text-sm text-gray-600">معلومات المنتج وحركة المخزون</p>
        </div>
        <a href="{{ route('products.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-slate-500 transition-colors">
            <svg class="ml-2 -mr-1 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            العودة
        </a>
    </div>
</div>

<div class="grid grid-cols-1 gap-6">
    <!-- Product Info -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">معلومات المنتج</h3>
            <dl class="space-y-4">
                <div>
                    <dt class="text-sm font-medium text-gray-500">اسم المنتج</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $product->name }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">SKU</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $product->sku }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">السعر الأساسي</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ number_format($product->selling_price, 2) }} ج.م</dd>
                </div>
                @if($product->discounted_price)
                <div>
                    <dt class="text-sm font-medium text-gray-500">سعر بعد التخفيض</dt>
                    <dd class="mt-1 text-sm text-green-600 font-medium">{{ number_format($product->discounted_price, 2) }} ج.م</dd>
                </div>
                @endif
                <div>
                    <dt class="text-sm font-medium text-gray-500">الكمية المتاحة</dt>
                    <dd class="mt-1">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $product->quantity > 10 ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                            {{ $product->quantity }}
                        </span>
                    </dd>
                </div>
                @if($product->available_sizes && count($product->available_sizes) > 0)
                <div>
                    <dt class="text-sm font-medium text-gray-500">الأحجام المتاحة</dt>
                    <dd class="mt-1.5 flex flex-wrap gap-2">
                        @foreach($product->available_sizes as $s)
                            <span class="inline-flex px-3 py-1 rounded-lg text-sm font-medium bg-emerald-100 text-emerald-800 border border-emerald-200">{{ $s }}</span>
                        @endforeach
                    </dd>
                </div>
                @endif
                @if($product->available_colors && count($product->available_colors) > 0)
                <div>
                    <dt class="text-sm font-medium text-gray-500">الألوان المتاحة</dt>
                    <dd class="mt-1.5 flex flex-wrap gap-2">
                        @foreach($product->available_colors as $c)
                            <span class="inline-flex px-3 py-1 rounded-lg text-sm font-medium bg-violet-100 text-violet-800 border border-violet-200">{{ $c }}</span>
                        @endforeach
                    </dd>
                </div>
                @endif
                <div>
                    <dt class="text-sm font-medium text-gray-500">متوسط التكلفة</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ number_format($product->average_cost, 2) }} ج.م</dd>
                </div>
                @if($product->description)
                <div>
                    <dt class="text-sm font-medium text-gray-500">الوصف</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $product->description }}</dd>
                </div>
                @endif
                @php $samples = $product->samples ?? []; @endphp
                @if(!empty($samples))
                <div>
                    <dt class="text-sm font-medium text-gray-500">صور العينة</dt>
                    <dd class="mt-3">
                        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                            @foreach($samples as $idx => $path)
                                <a href="{{ Storage::url($path) }}" target="_blank" class="block group rounded-xl overflow-hidden border-2 border-gray-200 hover:border-slate-400 shadow-sm hover:shadow-md transition-all">
                                    <img src="{{ Storage::url($path) }}" alt="عينة {{ $idx + 1 }}" class="w-full h-40 object-cover group-hover:scale-105 transition-transform duration-200">
                                    <span class="block py-1.5 text-center text-xs text-gray-500 bg-gray-50">صورة {{ $idx + 1 }}</span>
                                </a>
                            @endforeach
                        </div>
                    </dd>
                </div>
                @endif
            </dl>
        </div>
    </div>

</div>
@endsection
