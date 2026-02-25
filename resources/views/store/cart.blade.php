@extends('layouts.store')

@section('title', 'السلة - جرافوني')

@section('content')
<div class="bg-gray-50 min-h-screen py-8 md:py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-2xl md:text-3xl font-bold text-slate-900 mb-6">سلة التسوق</h1>

        @if(session('success'))
        <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-xl text-green-800">
            {{ session('success') }}
        </div>
        @endif

        @if(empty($items))
        <div class="bg-white rounded-2xl p-12 text-center">
            <svg class="w-20 h-20 text-slate-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
            <h2 class="text-xl font-semibold text-slate-700 mb-2">السلة فارغة</h2>
            <a href="{{ route('store.index') }}" class="inline-flex items-center gap-2 bg-slate-900 text-white px-6 py-3 rounded-xl font-semibold hover:bg-slate-800">
                تصفح المنتجات
            </a>
        </div>
        @else
        <div class="space-y-6">
            @foreach($items as $item)
            <div class="bg-white rounded-2xl p-4 md:p-6 flex flex-col md:flex-row gap-4 items-start">
                <a href="{{ route('store.product', $item['product']) }}" class="flex-shrink-0 w-full md:w-24 h-24 rounded-xl overflow-hidden bg-gray-100">
                    @php
                        $firstImg = collect($item['product']->available_colors ?? [])->first();
                        $imgPath = $firstImg['images'][0] ?? null;
                    @endphp
                    @if($imgPath)
                    <img src="{{ asset('storage/' . $imgPath) }}" alt="" class="w-full h-full object-cover">
                    @else
                    <div class="w-full h-full flex items-center justify-center">
                        <svg class="w-10 h-10 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    @endif
                </a>
                <div class="flex-1 min-w-0">
                    <h3 class="font-semibold text-slate-900">{{ $item['product']->name }}</h3>
                    @if($item['color'] || $item['size'])
                    <p class="text-sm text-slate-500 mt-1">
                        @if($item['color']) اللون: {{ $item['color'] }} @endif
                        @if($item['color'] && $item['size']) | @endif
                        @if($item['size']) المقاس: {{ $item['size'] }} @endif
                    </p>
                    @endif
                    <p class="text-lg font-bold text-slate-900 mt-2">{{ number_format($item['row_total'], 0) }} ج.م</p>
                </div>
                <div class="flex items-center gap-4">
                    <form action="{{ route('store.cart.update') }}" method="POST" class="flex items-center gap-1">
                        @csrf
                        <input type="hidden" name="key" value="{{ $item['key'] }}">
                        <input type="number" name="quantity" value="{{ $item['quantity'] }}" min="1" max="99" 
                               class="w-14 text-center border border-gray-200 rounded-lg py-1.5"
                               onchange="this.form.submit()">
                    </form>
                    <form action="{{ route('store.cart.remove', $item['key']) }}" method="POST" onsubmit="return confirm('حذف من السلة؟')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-800 p-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
            @endforeach

            <div class="bg-white rounded-2xl p-6 flex justify-between items-center">
                <span class="text-lg font-bold text-slate-900">الإجمالي: {{ number_format($subtotal, 0) }} ج.م</span>
                <a href="{{ route('store.checkout') }}" class="bg-slate-900 text-white px-8 py-3 rounded-xl font-semibold hover:bg-slate-800">
                    إتمام الطلب
                </a>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
