@extends('layouts.store')

@section('title', $product->name . ' - جرافوني')

@php
    $colors = $product->available_colors ?? [];
    $sizes = $product->available_sizes ?? [];
    $firstColor = collect($colors)->first();
    
    function buildMediaItems($color) {
        $items = [];
        foreach ($color['images'] ?? [] as $path) {
            $items[] = ['type' => 'image', 'path' => $path];
        }
        foreach ($color['videos'] ?? [] as $path) {
            $items[] = ['type' => 'video', 'path' => $path];
        }
        return $items;
    }
    $firstColorMedia = buildMediaItems($firstColor ?? []);
    $firstColorImages = $firstColor['images'] ?? [];
    $hasDiscount = $product->discounted_price && $product->discounted_price < $product->selling_price;
    $discountPercent = $hasDiscount ? round((($product->selling_price - $product->discounted_price) / $product->selling_price) * 100) : 0;
    
    $stockMap = [];
    foreach($sizes as $size) {
        $sizeName = $size['size'] ?? '';
        foreach(($size['colors'] ?? []) as $colorStock) {
            $colorName = $colorStock['color'] ?? '';
            $stock = (int)($colorStock['stock'] ?? 0);
            $stockMap[$sizeName][$colorName] = $stock;
        }
    }
@endphp

@section('content')
<div class="bg-white min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 md:py-10">
        <!-- Breadcrumb -->
        <nav class="mb-6">
            <ol class="flex items-center gap-2 text-sm text-slate-500">
                <li><a href="{{ route('store.index') }}" class="hover:text-slate-900 transition-colors">الرئيسية</a></li>
                <li><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg></li>
                <li><a href="{{ route('store.index') }}#products" class="hover:text-slate-900 transition-colors">المنتجات</a></li>
                <li><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg></li>
                <li class="text-slate-900 font-medium truncate max-w-[200px]">{{ $product->name }}</li>
            </ol>
        </nav>
        
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-12">
            <!-- Gallery Section -->
            <div class="space-y-4">
                <!-- Main Media (Image/Video) with Zoom -->
                <div class="relative bg-gray-100 rounded-2xl overflow-hidden aspect-square" id="main-media-container">
                    @php $firstMedia = $firstColorMedia[0] ?? null; @endphp
                    <img src="{{ ($firstMedia && $firstMedia['type'] === 'image') ? asset('storage/' . $firstMedia['path']) : '' }}" 
                         alt="{{ $product->name }}"
                         class="w-full h-full object-cover cursor-crosshair {{ ($firstMedia && $firstMedia['type'] === 'video') ? 'hidden' : '' }}"
                         id="main-image">
                    <video src="{{ ($firstMedia && $firstMedia['type'] === 'video') ? asset('storage/' . $firstMedia['path']) : '' }}" 
                           class="w-full h-full object-cover {{ ($firstMedia && $firstMedia['type'] === 'video') ? '' : 'hidden' }}" 
                           id="main-video" 
                           controls></video>
                    
                    @if(empty($firstColorMedia))
                    <div class="absolute inset-0 flex items-center justify-center">
                        <svg class="w-24 h-24 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    @endif
                    
                    <!-- Zoom Result (images only) -->
                    <div class="zoom-result hidden lg:block" id="zoom-result"></div>
                    
                    <!-- Badges -->
                    @if($hasDiscount)
                    <div class="absolute top-4 right-4">
                        <span class="badge-sale bg-red-500 text-white text-sm font-bold px-3 py-1.5 rounded-lg shadow-md">
                            خصم {{ $discountPercent }}%
                        </span>
                    </div>
                    @endif
                    
                    <!-- Fullscreen Button -->
                    <button class="absolute bottom-4 left-4 bg-white/90 backdrop-blur-sm p-2 rounded-lg shadow-md hover:bg-white transition-colors" id="fullscreen-btn">
                        <svg class="w-5 h-5 text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/>
                        </svg>
                    </button>
                </div>
                
                <!-- Thumbnails Gallery - Images & Videos -->
                <div class="flex gap-2 overflow-x-auto pb-2" id="thumbnails-container">
                    @foreach($firstColorMedia as $index => $item)
                    <button class="gallery-thumb flex-shrink-0 w-20 h-20 rounded-xl overflow-hidden border-2 {{ $index === 0 ? 'border-blue-500' : 'border-gray-200' }}" 
                            data-type="{{ $item['type'] }}"
                            data-path="{{ $item['path'] }}"
                            data-index="{{ $index }}">
                        @if($item['type'] === 'video')
                        <div class="relative w-full h-full bg-slate-800 flex items-center justify-center">
                            <video src="{{ asset('storage/' . $item['path']) }}" class="w-full h-full object-cover opacity-70" muted preload="metadata"></video>
                            <div class="absolute inset-0 flex items-center justify-center">
                                <div class="w-10 h-10 rounded-full bg-white/90 flex items-center justify-center">
                                    <svg class="w-5 h-5 text-slate-900 mr-0.5" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                                </div>
                            </div>
                        </div>
                        @else
                        <img src="{{ asset('storage/' . $item['path']) }}" alt="صورة {{ $index + 1 }}" class="w-full h-full object-cover">
                        @endif
                    </button>
                    @endforeach
                </div>
            </div>
            
            <!-- Product Details -->
            <div class="space-y-6">
                <!-- Product Name & SKU -->
                <div>
                    <p class="text-sm text-slate-500 mb-2">SKU: {{ $product->sku }}</p>
                    <h1 class="text-2xl md:text-3xl font-bold text-slate-900 leading-tight">{{ $product->name }}</h1>
                </div>
                
                <!-- Price -->
                <div class="flex items-center gap-4">
                    @if($hasDiscount)
                        <span class="text-3xl font-bold text-red-600">{{ number_format($product->discounted_price, 0) }} ج.م</span>
                        <span class="text-xl text-slate-400 line-through">{{ number_format($product->selling_price, 0) }} ج.م</span>
                        <span class="bg-red-100 text-red-700 text-sm font-semibold px-3 py-1 rounded-lg">وفر {{ number_format($product->selling_price - $product->discounted_price, 0) }} ج.م</span>
                    @else
                        <span class="text-3xl font-bold text-slate-900">{{ number_format($product->selling_price, 0) }} ج.م</span>
                    @endif
                </div>
                
                <!-- Description -->
                @if($product->description)
                <div class="bg-slate-50 rounded-xl p-4">
                    <h3 class="font-semibold text-slate-900 mb-2">وصف المنتج</h3>
                    <p class="text-slate-600 leading-relaxed">{{ $product->description }}</p>
                </div>
                @endif
                
                <!-- Color Selection -->
                @if(count($colors) > 0)
                <div>
                    <h3 class="font-semibold text-slate-900 mb-3">
                        اللون: <span class="font-normal text-slate-600" id="selected-color-name">{{ $firstColor['color'] ?? '' }}</span>
                    </h3>
                    <div class="flex flex-wrap gap-3" id="color-options">
                        @foreach($colors as $index => $color)
                        @php
                            $colorHex = \App\Helpers\ColorHelper::getHex($color['color'] ?? '');
                        @endphp
                        <button class="color-option w-10 h-10 rounded-full border-2 {{ $index === 0 ? 'active border-blue-500' : 'border-gray-300' }}" 
                                style="background-color: {{ $colorHex }}"
                                data-color="{{ $color['color'] ?? '' }}"
                                data-index="{{ $index }}"
                                data-media='@json(buildMediaItems($color))'
                                title="{{ $color['color'] ?? '' }}">
                        </button>
                        @endforeach
                    </div>
                </div>
                @endif
                
                <!-- Size Selection -->
                @if(count($sizes) > 0)
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="font-semibold text-slate-900">
                            المقاس: <span class="font-normal text-slate-600" id="selected-size-name">اختر المقاس</span>
                        </h3>
                        <button type="button" class="inline-flex items-center gap-2 px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium rounded-xl transition-colors" id="size-guide-btn">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/>
                            </svg>
                            <span>دليل المقاسات</span>
                        </button>
                    </div>
                    <div class="grid grid-cols-4 sm:grid-cols-5 gap-2" id="size-options">
                        @foreach($sizes as $size)
                        @php
                            $sizeName = $size['size'] ?? '';
                            $totalSizeStock = 0;
                            foreach(($size['colors'] ?? []) as $cs) {
                                $totalSizeStock += (int)($cs['stock'] ?? 0);
                            }
                        @endphp
                        <button class="size-option py-3 px-4 border-2 border-gray-200 rounded-xl text-center font-medium {{ $totalSizeStock <= 0 ? 'out-of-stock' : '' }}"
                                data-size="{{ $sizeName }}"
                                data-chest="{{ $size['chest_width_cm'] ?? '' }}"
                                data-weight-min="{{ $size['weight_kg']['min'] ?? '' }}"
                                data-weight-max="{{ $size['weight_kg']['max'] ?? '' }}"
                                data-height-min="{{ $size['height_cm']['min'] ?? '' }}"
                                data-height-max="{{ $size['height_cm']['max'] ?? '' }}"
                                {{ $totalSizeStock <= 0 ? 'disabled' : '' }}>
                            {{ $sizeName }}
                        </button>
                        @endforeach
                    </div>
                </div>
                @endif
                
                <!-- Stock Status -->
                <div id="stock-status" class="hidden">
                    <div class="flex items-center gap-2 text-sm">
                        <span class="w-2 h-2 rounded-full bg-green-500"></span>
                        <span class="text-slate-600">متوفر في المخزون: <strong id="stock-count">0</strong> قطعة</span>
                    </div>
                </div>
                
                <!-- Quantity & Add to Cart -->
                <div class="space-y-4 pt-4 border-t border-gray-100">
                    <!-- Quantity -->
                    <div class="flex items-center gap-4">
                        <span class="font-semibold text-slate-900">الكمية:</span>
                        <div class="flex items-center border-2 border-gray-200 rounded-xl overflow-hidden">
                            <button class="px-4 py-2 hover:bg-gray-100 transition-colors text-lg font-medium" id="qty-minus">−</button>
                            <input type="number" value="1" min="1" max="99" class="w-16 text-center border-0 focus:ring-0 text-lg font-medium" id="quantity-input">
                            <button class="px-4 py-2 hover:bg-gray-100 transition-colors text-lg font-medium" id="qty-plus">+</button>
                        </div>
                    </div>
                    
                    <!-- Add to Cart Button -->
                    <button class="btn-primary w-full py-4 rounded-xl text-white font-semibold text-lg flex items-center justify-center gap-3 disabled:opacity-50 disabled:cursor-not-allowed" 
                            id="add-to-cart-btn" disabled>
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        <span id="btn-text">اختر المقاس واللون أولاً</span>
                    </button>
                    
                    <!-- WhatsApp Order -->
                    <a href="#" class="flex items-center justify-center gap-3 w-full py-3 rounded-xl border-2 border-green-500 text-green-600 font-semibold hover:bg-green-50 transition-colors" id="whatsapp-btn">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                        </svg>
                        <span>اطلب عبر واتساب</span>
                    </a>
                </div>
                
                <!-- Features -->
                <div class="grid grid-cols-2 gap-3 pt-4">
                    <div class="flex items-center gap-3 bg-slate-50 rounded-xl p-3">
                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                            </svg>
                        </div>
                        <div>
                            <p class="font-medium text-slate-900 text-sm">توصيل سريع</p>
                            <p class="text-slate-500 text-xs">لجميع المحافظات</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 bg-slate-50 rounded-xl p-3">
                        <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="font-medium text-slate-900 text-sm">جودة مضمونة</p>
                            <p class="text-slate-500 text-xs">100% أصلي</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 bg-slate-50 rounded-xl p-3">
                        <div class="w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                        </div>
                        <div>
                            <p class="font-medium text-slate-900 text-sm">إرجاع سهل</p>
                            <p class="text-slate-500 text-xs">خلال 14 يوم</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 bg-slate-50 rounded-xl p-3">
                        <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="font-medium text-slate-900 text-sm">دفع آمن</p>
                            <p class="text-slate-500 text-xs">عند الاستلام</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Size Guide Modal -->
<div class="fixed inset-0 z-50 hidden" id="size-guide-modal">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" id="size-guide-overlay"></div>
    <div class="absolute inset-4 md:inset-auto md:top-1/2 md:left-1/2 md:-translate-x-1/2 md:-translate-y-1/2 md:max-w-2xl md:w-full bg-white rounded-2xl shadow-2xl overflow-hidden">
        <div class="flex items-center justify-between p-4 border-b">
            <h3 class="text-lg font-bold text-slate-900">دليل المقاسات</h3>
            <button class="p-2 hover:bg-gray-100 rounded-lg transition-colors" id="close-size-guide">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <div class="p-4 overflow-auto max-h-[70vh]">
            @if(count($sizes) > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-slate-100">
                        <tr>
                            <th class="px-4 py-3 text-right font-semibold text-slate-900">المقاس</th>
                            <th class="px-4 py-3 text-right font-semibold text-slate-900">عرض الصدر (سم)</th>
                            <th class="px-4 py-3 text-right font-semibold text-slate-900">الوزن (كجم)</th>
                            <th class="px-4 py-3 text-right font-semibold text-slate-900">الطول (سم)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($sizes as $size)
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-3 font-bold text-slate-900">{{ $size['size'] ?? '-' }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ $size['chest_width_cm'] ?? '-' }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ $size['weight_kg']['min'] ?? '-' }} - {{ $size['weight_kg']['max'] ?? '-' }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ $size['height_cm']['min'] ?? '-' }} - {{ $size['height_cm']['max'] ?? '-' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <p class="text-center text-slate-500 py-8">لا يوجد دليل مقاسات لهذا المنتج</p>
            @endif
        </div>
    </div>
</div>

<!-- Fullscreen Media Modal (Image/Video) -->
<div class="fixed inset-0 z-50 hidden bg-black" id="fullscreen-modal">
    <button class="absolute top-4 right-4 z-10 p-2 bg-white/10 hover:bg-white/20 rounded-full transition-colors" id="close-fullscreen">
        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
        </svg>
    </button>
    <div class="absolute inset-0 flex items-center justify-center p-4">
        <img src="" alt="" class="max-w-full max-h-full object-contain hidden" id="fullscreen-image">
        <video src="" class="max-w-full max-h-full object-contain hidden" id="fullscreen-video" controls></video>
    </div>
    <button class="absolute left-4 top-1/2 -translate-y-1/2 p-2 bg-white/10 hover:bg-white/20 rounded-full transition-colors" id="fullscreen-prev">
        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
    </button>
    <button class="absolute right-4 top-1/2 -translate-y-1/2 p-2 bg-white/10 hover:bg-white/20 rounded-full transition-colors" id="fullscreen-next">
        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
    </button>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const stockMap = @json($stockMap);
    const colors = @json($colors);
    const productName = @json($product->name);
    const productPrice = {{ $hasDiscount ? $product->discounted_price : $product->selling_price }};
    
    let selectedColor = colors[0]?.color || '';
    let selectedSize = '';
    let currentMedia = @json($firstColorMedia);
    let currentMediaIndex = 0;
    
    const mainImage = document.getElementById('main-image');
    const mainVideo = document.getElementById('main-video');
    const mainMediaContainer = document.getElementById('main-media-container');
    const thumbnailsContainer = document.getElementById('thumbnails-container');
    const colorOptions = document.querySelectorAll('.color-option');
    const sizeOptions = document.querySelectorAll('.size-option:not(.out-of-stock)');
    const stockStatus = document.getElementById('stock-status');
    const stockCount = document.getElementById('stock-count');
    const addToCartBtn = document.getElementById('add-to-cart-btn');
    const btnText = document.getElementById('btn-text');
    const selectedColorName = document.getElementById('selected-color-name');
    const selectedSizeName = document.getElementById('selected-size-name');
    const quantityInput = document.getElementById('quantity-input');
    const whatsappBtn = document.getElementById('whatsapp-btn');
    
    function showMediaItem(item, index) {
        if (!item) return;
        if (item.type === 'video') {
            mainImage.style.display = 'none';
            mainVideo.style.display = 'block';
            mainVideo.src = '{{ asset("storage") }}/' + item.path;
            mainVideo.load();
            document.getElementById('zoom-result')?.classList.add('hidden');
        } else {
            mainVideo.style.display = 'none';
            mainVideo.pause();
            mainImage.style.display = 'block';
            mainImage.src = '{{ asset("storage") }}/' + item.path;
            document.getElementById('zoom-result')?.classList.remove('hidden');
        }
        currentMediaIndex = index;
    }
    
    function updateGallery(media) {
        currentMedia = media && media.length ? media : [];
        currentMediaIndex = 0;
        
        if (currentMedia.length > 0) {
            showMediaItem(currentMedia[0], 0);
        } else {
            mainImage.style.display = 'none';
            mainVideo.style.display = 'none';
        }
        
        thumbnailsContainer.innerHTML = '';
        currentMedia.forEach((item, index) => {
            const btn = document.createElement('button');
            btn.className = `gallery-thumb flex-shrink-0 w-20 h-20 rounded-xl overflow-hidden border-2 ${index === 0 ? 'border-blue-500' : 'border-gray-200'}`;
            btn.dataset.type = item.type;
            btn.dataset.path = item.path;
            btn.dataset.index = index;
            if (item.type === 'video') {
                btn.innerHTML = `<div class="relative w-full h-full bg-slate-800 flex items-center justify-center">
                    <video src="{{ asset("storage") }}/${item.path}" class="w-full h-full object-cover opacity-70" muted preload="metadata"></video>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <div class="w-10 h-10 rounded-full bg-white/90 flex items-center justify-center">
                            <svg class="w-5 h-5 text-slate-900 mr-0.5" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                        </div>
                    </div>
                </div>`;
            } else {
                btn.innerHTML = `<img src="{{ asset("storage") }}/${item.path}" alt="صورة ${index + 1}" class="w-full h-full object-cover">`;
            }
            thumbnailsContainer.appendChild(btn);
            
            btn.addEventListener('click', function() {
                const idx = parseInt(this.dataset.index);
                showMediaItem(currentMedia[idx], idx);
                document.querySelectorAll('.gallery-thumb').forEach(t => t.classList.remove('border-blue-500'));
                this.classList.add('border-blue-500');
            });
        });
    }
    
    function updateStock() {
        if (selectedSize && selectedColor) {
            const stock = stockMap[selectedSize]?.[selectedColor] || 0;
            stockStatus.classList.remove('hidden');
            stockCount.textContent = stock;
            
            if (stock > 0) {
                addToCartBtn.disabled = false;
                btnText.textContent = 'أضف إلى السلة';
                quantityInput.max = stock;
                if (parseInt(quantityInput.value) > stock) {
                    quantityInput.value = stock;
                }
            } else {
                addToCartBtn.disabled = true;
                btnText.textContent = 'غير متوفر بهذا اللون والمقاس';
            }
        } else {
            stockStatus.classList.add('hidden');
            addToCartBtn.disabled = true;
            if (!selectedSize && !selectedColor) {
                btnText.textContent = 'اختر المقاس واللون أولاً';
            } else if (!selectedSize) {
                btnText.textContent = 'اختر المقاس أولاً';
            } else {
                btnText.textContent = 'اختر اللون أولاً';
            }
        }
        
        updateWhatsAppLink();
    }
    
    function updateWhatsAppLink() {
        const qty = quantityInput.value;
        let message = `مرحباً، أريد طلب المنتج التالي:\n\n`;
        message += `📦 المنتج: ${productName}\n`;
        if (selectedColor) message += `🎨 اللون: ${selectedColor}\n`;
        if (selectedSize) message += `📏 المقاس: ${selectedSize}\n`;
        message += `🔢 الكمية: ${qty}\n`;
        message += `💰 السعر: ${productPrice} ج.م\n\n`;
        message += `الرابط: ${window.location.href}`;
        
        whatsappBtn.href = `https://wa.me/201234567890?text=${encodeURIComponent(message)}`;
    }
    
    colorOptions.forEach(btn => {
        btn.addEventListener('click', function() {
            colorOptions.forEach(b => b.classList.remove('active', 'border-blue-500'));
            this.classList.add('active', 'border-blue-500');
            
            selectedColor = this.dataset.color;
            selectedColorName.textContent = selectedColor;
            
            const media = JSON.parse(this.dataset.media || '[]');
            updateGallery(media);
            updateStock();
        });
    });
    
    sizeOptions.forEach(btn => {
        btn.addEventListener('click', function() {
            sizeOptions.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            
            selectedSize = this.dataset.size;
            selectedSizeName.textContent = selectedSize;
            updateStock();
        });
    });
    
    document.getElementById('qty-minus')?.addEventListener('click', function() {
        const val = parseInt(quantityInput.value);
        if (val > 1) quantityInput.value = val - 1;
        updateWhatsAppLink();
    });
    
    document.getElementById('qty-plus')?.addEventListener('click', function() {
        const val = parseInt(quantityInput.value);
        const max = parseInt(quantityInput.max) || 99;
        if (val < max) quantityInput.value = val + 1;
        updateWhatsAppLink();
    });
    
    quantityInput?.addEventListener('change', function() {
        const max = parseInt(this.max) || 99;
        if (parseInt(this.value) > max) this.value = max;
        if (parseInt(this.value) < 1) this.value = 1;
        updateWhatsAppLink();
    });
    
    // Size Guide Modal
    const sizeGuideModal = document.getElementById('size-guide-modal');
    document.getElementById('size-guide-btn')?.addEventListener('click', () => sizeGuideModal.classList.remove('hidden'));
    document.getElementById('close-size-guide')?.addEventListener('click', () => sizeGuideModal.classList.add('hidden'));
    document.getElementById('size-guide-overlay')?.addEventListener('click', () => sizeGuideModal.classList.add('hidden'));
    
    // Fullscreen Modal
    const fullscreenModal = document.getElementById('fullscreen-modal');
    const fullscreenImage = document.getElementById('fullscreen-image');
    const fullscreenVideo = document.getElementById('fullscreen-video');
    
    function showFullscreenMedia() {
        if (currentMedia.length === 0) return;
        const item = currentMedia[currentMediaIndex];
        if (item.type === 'video') {
            fullscreenImage.classList.add('hidden');
            fullscreenVideo.classList.remove('hidden');
            fullscreenVideo.src = '{{ asset("storage") }}/' + item.path;
            fullscreenVideo.load();
            fullscreenVideo.play();
        } else {
            fullscreenVideo.classList.add('hidden');
            fullscreenVideo.pause();
            fullscreenImage.classList.remove('hidden');
            fullscreenImage.src = '{{ asset("storage") }}/' + item.path;
        }
        fullscreenModal.classList.remove('hidden');
    }
    
    document.getElementById('fullscreen-btn')?.addEventListener('click', function() {
        if (currentMedia.length > 0) showFullscreenMedia();
    });
    
    document.getElementById('close-fullscreen')?.addEventListener('click', () => {
        fullscreenModal.classList.add('hidden');
        fullscreenVideo.pause();
    });
    
    document.getElementById('fullscreen-next')?.addEventListener('click', function() {
        if (currentMedia.length > 0) {
            currentMediaIndex = (currentMediaIndex + 1) % currentMedia.length;
            showFullscreenMedia();
        }
    });
    
    document.getElementById('fullscreen-prev')?.addEventListener('click', function() {
        if (currentMedia.length > 0) {
            currentMediaIndex = (currentMediaIndex - 1 + currentMedia.length) % currentMedia.length;
            showFullscreenMedia();
        }
    });
    
    // Zoom functionality (desktop only, images only)
    const mainImageContainer = document.getElementById('main-media-container');
    const zoomResult = document.getElementById('zoom-result');
    
    if (window.innerWidth >= 1024 && zoomResult) {
        mainImage?.addEventListener('mousemove', function(e) {
            if (currentMedia[currentMediaIndex]?.type === 'video') return;
            const rect = mainImageContainer.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;
            
            const xPercent = (x / rect.width) * 100;
            const yPercent = (y / rect.height) * 100;
            
            zoomResult.style.display = 'block';
            zoomResult.style.left = rect.width + 20 + 'px';
            zoomResult.style.top = '0';
            zoomResult.style.backgroundImage = `url(${mainImage.src})`;
            zoomResult.style.backgroundSize = `${rect.width * 2.5}px ${rect.height * 2.5}px`;
            zoomResult.style.backgroundPosition = `${xPercent}% ${yPercent}%`;
        });
        
        mainImage?.addEventListener('mouseleave', function() {
            zoomResult.style.display = 'none';
        });
    }
    
    // Keyboard navigation for fullscreen
    document.addEventListener('keydown', function(e) {
        if (!fullscreenModal.classList.contains('hidden')) {
            if (e.key === 'Escape') fullscreenModal.classList.add('hidden');
            if (e.key === 'ArrowLeft') document.getElementById('fullscreen-next')?.click();
            if (e.key === 'ArrowRight') document.getElementById('fullscreen-prev')?.click();
        }
    });
    
    // Initialize - build thumbnail click handlers
    updateGallery(currentMedia);
    updateStock();
});
</script>
@endsection
