@extends('layouts.store')

@section('title', 'جرافوني - متجر الأزياء العصرية')

@section('content')
<!-- Hero Section - Fashion Style with Model Image -->
<section class="relative overflow-hidden bg-gradient-to-bl from-slate-900 via-slate-800 to-zinc-900">
    <!-- Background Pattern -->
    <div class="absolute inset-0 opacity-10">
        <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'0.4\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>
    </div>
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative">
        <div class="flex flex-col lg:flex-row items-center min-h-[400px] lg:min-h-[450px] py-8 lg:py-0">
            <!-- Content -->
            <div class="flex-1 text-center lg:text-right z-10 py-8 lg:py-12">
                <div class="inline-flex items-center gap-2 px-4 py-2 bg-amber-500/20 backdrop-blur-sm rounded-full text-amber-400 text-sm font-medium mb-6 border border-amber-500/30">
                    <span class="w-2 h-2 bg-amber-400 rounded-full animate-pulse"></span>
                    تشكيلة {{ date('Y') }} الجديدة
                </div>
                
                <h1 class="text-4xl md:text-5xl lg:text-6xl font-black text-white leading-tight mb-6">
                    <span class="block">اكتشف</span>
                    <span class="text-transparent bg-clip-text bg-gradient-to-l from-amber-400 to-orange-500">أناقتك</span>
                </h1>
                
                <p class="text-slate-300 text-lg md:text-xl leading-relaxed mb-8 max-w-lg mx-auto lg:mx-0 lg:mr-0">
                    تصاميم عصرية تناسب ذوقك، جودة استثنائية بأسعار منافسة
                </p>
                
                <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start">
                    <a href="#products" class="inline-flex items-center justify-center gap-3 bg-gradient-to-l from-amber-500 to-orange-500 text-white px-8 py-4 rounded-2xl font-bold text-lg hover:from-amber-600 hover:to-orange-600 transition-all shadow-lg shadow-amber-500/25 hover:shadow-xl hover:shadow-amber-500/30 hover:-translate-y-1">
                        <span>تسوق الآن</span>
                        <svg class="w-5 h-5 rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                        </svg>
                    </a>
                    <div class="flex items-center justify-center gap-6 text-white/80">
                        <div class="text-center">
                            <div class="text-2xl font-bold text-white">{{ $products->total() }}+</div>
                            <div class="text-xs text-slate-400">منتج</div>
                        </div>
                        <div class="w-px h-10 bg-white/20"></div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-amber-400">24h</div>
                            <div class="text-xs text-slate-400">توصيل</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Model Image Container -->
            <div class="flex-1 relative flex items-end justify-center lg:justify-end h-full">
                <!-- Glow Effect Behind Model -->
                <div class="absolute bottom-0 left-1/2 -translate-x-1/2 w-80 h-80 bg-gradient-to-t from-amber-500/20 to-transparent rounded-full blur-3xl"></div>
                
                <!-- Model Image Placeholder - Add your image here -->
                <div class="relative z-10 w-full max-w-sm lg:max-w-md">
                    <!-- Replace this with your actual model image -->
                    <!-- <img src="{{ asset('images/hero-model.png') }}" alt="Fashion Model" class="w-full h-auto object-contain drop-shadow-2xl"> -->
                    
                    <!-- Placeholder SVG - Remove when you add real image -->
                    <div class="aspect-[3/4] flex items-center justify-center">
                        <div class="text-center text-white/30">
                            <svg class="w-32 h-32 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            <p class="text-sm">ضع صورة الموديل هنا</p>
                            <p class="text-xs mt-1 text-white/20">hero-model.png</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Bottom Wave -->
    <div class="absolute bottom-0 left-0 right-0">
        <svg viewBox="0 0 1440 60" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full">
            <path d="M0 60V20C240 0 480 0 720 20C960 40 1200 40 1440 20V60H0Z" fill="#f9fafb"/>
        </svg>
    </div>
</section>

<!-- Products Section -->
<section id="products" class="py-12 md:py-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Section Header -->
        <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4 mb-10">
            <div>
                <span class="inline-block px-3 py-1 bg-slate-900 text-white text-xs font-bold rounded-full mb-3">COLLECTION</span>
                <h2 class="text-3xl md:text-4xl font-black text-slate-900">أحدث المنتجات</h2>
            </div>
            <p class="text-slate-500 md:text-left max-w-md">اكتشف تشكيلتنا المميزة من الأزياء الرجالية والنسائية</p>
        </div>
        
        @if($products->count() > 0)
        <!-- Products Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach($products as $product)
            @php
                $colors = $product->available_colors ?? [];
                $sizes = $product->available_sizes ?? [];
                $firstColor = collect($colors)->first();
                $firstImage = $firstColor['images'][0] ?? null;
                $secondImage = $firstColor['images'][1] ?? null;
                $hasDiscount = $product->discounted_price && $product->discounted_price < $product->selling_price;
                $discountPercent = $hasDiscount ? round((($product->selling_price - $product->discounted_price) / $product->selling_price) * 100) : 0;
                $savedAmount = $hasDiscount ? $product->selling_price - $product->discounted_price : 0;
                
                $totalStock = 0;
                $availableSizes = [];
                foreach($sizes as $size) {
                    $sizeStock = 0;
                    foreach(($size['colors'] ?? []) as $colorStock) {
                        $sizeStock += (int)($colorStock['stock'] ?? 0);
                    }
                    $totalStock += $sizeStock;
                    if ($sizeStock > 0) {
                        $availableSizes[] = $size['size'] ?? '';
                    }
                }
                
                $colorCount = count($colors);
                $sizeCount = count($availableSizes);
            @endphp
            
            <div class="group">
                <a href="{{ route('store.product', $product) }}" class="block bg-white rounded-3xl overflow-hidden shadow-sm hover:shadow-2xl transition-all duration-500 border border-gray-100 hover:border-gray-200">
                    <!-- Image Container -->
                    <div class="relative aspect-[3/4] overflow-hidden bg-gradient-to-br from-gray-100 to-gray-50">
                        @if($firstImage)
                            <!-- Main Image -->
                            <img src="{{ asset('storage/' . $firstImage) }}" 
                                 alt="{{ $product->name }}" 
                                 class="absolute inset-0 w-full h-full object-cover transition-all duration-700 {{ $secondImage ? 'group-hover:opacity-0' : 'group-hover:scale-110' }}"
                                 loading="lazy">
                            
                            <!-- Second Image on Hover -->
                            @if($secondImage)
                            <img src="{{ asset('storage/' . $secondImage) }}" 
                                 alt="{{ $product->name }}" 
                                 class="absolute inset-0 w-full h-full object-cover opacity-0 group-hover:opacity-100 transition-all duration-700 scale-105 group-hover:scale-100"
                                 loading="lazy">
                            @endif
                        @else
                            <div class="absolute inset-0 flex items-center justify-center">
                                <svg class="w-20 h-20 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                        @endif
                        
                        <!-- Badges Container -->
                        <div class="absolute top-3 right-3 flex flex-col gap-2">
                            @if($hasDiscount)
                            <div class="bg-gradient-to-l from-red-500 to-rose-600 text-white text-xs font-bold px-3 py-1.5 rounded-xl shadow-lg shadow-red-500/30 flex items-center gap-1">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M12.395 2.553a1 1 0 00-1.45-.385c-.345.23-.614.558-.822.88-.214.33-.403.713-.57 1.116-.334.804-.614 1.768-.84 2.734a31.365 31.365 0 00-.613 3.58 2.64 2.64 0 01-.945-1.067c-.328-.68-.398-1.534-.398-2.654A1 1 0 005.05 6.05 6.981 6.981 0 003 11a7 7 0 1011.95-4.95c-.592-.591-.98-.985-1.348-1.467-.363-.476-.724-1.063-1.207-2.03zM12.12 15.12A3 3 0 017 13s.879.5 2.5.5c0-1 .5-4 1.25-4.5.5 1 .786 1.293 1.371 1.879A2.99 2.99 0 0113 13a2.99 2.99 0 01-.879 2.121z" clip-rule="evenodd"/>
                                </svg>
                                <span>-{{ $discountPercent }}%</span>
                            </div>
                            @endif
                            
                            @if($totalStock <= 0)
                            <div class="bg-slate-900 text-white text-xs font-medium px-3 py-1.5 rounded-xl">
                                نفذت الكمية
                            </div>
                            @elseif($totalStock <= 5)
                            <div class="bg-gradient-to-l from-amber-500 to-orange-500 text-white text-xs font-medium px-3 py-1.5 rounded-xl shadow-lg shadow-amber-500/30 flex items-center gap-1">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M12.395 2.553a1 1 0 00-1.45-.385c-.345.23-.614.558-.822.88-.214.33-.403.713-.57 1.116-.334.804-.614 1.768-.84 2.734a31.365 31.365 0 00-.613 3.58 2.64 2.64 0 01-.945-1.067c-.328-.68-.398-1.534-.398-2.654A1 1 0 005.05 6.05 6.981 6.981 0 003 11a7 7 0 1011.95-4.95c-.592-.591-.98-.985-1.348-1.467-.363-.476-.724-1.063-1.207-2.03zM12.12 15.12A3 3 0 017 13s.879.5 2.5.5c0-1 .5-4 1.25-4.5.5 1 .786 1.293 1.371 1.879A2.99 2.99 0 0113 13a2.99 2.99 0 01-.879 2.121z" clip-rule="evenodd"/>
                                </svg>
                                <span>آخر {{ $totalStock }} قطع</span>
                            </div>
                            @endif
                        </div>
                        
                        <!-- Quick Actions -->
                        <div class="absolute bottom-3 left-3 right-3 opacity-0 translate-y-4 group-hover:opacity-100 group-hover:translate-y-0 transition-all duration-300">
                            <div class="bg-white/95 backdrop-blur-sm rounded-2xl p-3 shadow-xl">
                                <div class="flex items-center justify-center gap-2 text-slate-900 font-semibold text-sm">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    <span>عرض التفاصيل</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Product Info -->
                    <div class="p-4 space-y-3">
                        <!-- Product Name -->
                        <h3 class="font-bold text-slate-900 text-base leading-snug line-clamp-2 min-h-[48px] group-hover:text-amber-600 transition-colors">
                            {{ $product->name }}
                        </h3>
                        
                        <!-- Colors & Sizes Info -->
                        <div class="flex items-center justify-between gap-2">
                            <!-- Available Colors -->
                            @if($colorCount > 0)
                            <div class="flex items-center gap-1">
                                <div class="flex -space-x-1 rtl:space-x-reverse">
                                    @foreach(array_slice($colors, 0, 5) as $color)
                                    @php
                                        $colorHex = \App\Helpers\ColorHelper::getHex($color['color'] ?? '');
                                    @endphp
                                    <div class="w-5 h-5 rounded-full border-2 border-white shadow-sm ring-1 ring-gray-200" 
                                         style="background-color: {{ $colorHex }}"
                                         title="{{ $color['color'] ?? '' }}">
                                    </div>
                                    @endforeach
                                </div>
                                @if($colorCount > 5)
                                <span class="text-xs text-slate-400 font-medium">+{{ $colorCount - 5 }}</span>
                                @endif
                            </div>
                            @endif
                            
                            <!-- Available Sizes -->
                            @if($sizeCount > 0)
                            <div class="flex items-center gap-1 text-xs text-slate-500">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/>
                                </svg>
                                <span class="font-medium">{{ implode(' • ', array_slice($availableSizes, 0, 4)) }}{{ $sizeCount > 4 ? '...' : '' }}</span>
                            </div>
                            @endif
                        </div>
                        
                        <!-- Price Section -->
                        <div class="pt-2 border-t border-gray-100">
                            @if($hasDiscount)
                            <div class="flex items-center justify-between">
                                <div>
                                    <span class="text-xl font-black text-red-600">{{ number_format($product->discounted_price, 0) }}</span>
                                    <span class="text-sm font-bold text-red-600">ج.م</span>
                                </div>
                                <div class="text-left">
                                    <span class="text-sm text-slate-400 line-through">{{ number_format($product->selling_price, 0) }} ج.م</span>
                                    <div class="text-xs text-green-600 font-semibold">وفر {{ number_format($savedAmount, 0) }} ج.م</div>
                                </div>
                            </div>
                            @else
                            <div class="flex items-baseline gap-1">
                                <span class="text-xl font-black text-slate-900">{{ number_format($product->selling_price, 0) }}</span>
                                <span class="text-sm font-bold text-slate-600">ج.م</span>
                            </div>
                            @endif
                        </div>
                        
                        <!-- Stock & Shipping Info -->
                        <div class="flex items-center gap-3 text-xs">
                            @if($totalStock > 0)
                            <span class="flex items-center gap-1 text-green-600">
                                <span class="w-1.5 h-1.5 bg-green-500 rounded-full"></span>
                                متوفر
                            </span>
                            @else
                            <span class="flex items-center gap-1 text-red-500">
                                <span class="w-1.5 h-1.5 bg-red-500 rounded-full"></span>
                                غير متوفر
                            </span>
                            @endif
                            <span class="text-slate-400">|</span>
                            <span class="flex items-center gap-1 text-slate-500">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                                </svg>
                                توصيل سريع
                            </span>
                        </div>
                    </div>
                </a>
            </div>
            @endforeach
        </div>
        
        <!-- Pagination -->
        @if($products->hasPages())
        <div class="mt-12 flex justify-center">
            {{ $products->links() }}
        </div>
        @endif
        
        @else
        <!-- Empty State -->
        <div class="text-center py-20">
            <div class="w-24 h-24 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-12 h-12 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
            </div>
            <h3 class="text-2xl font-bold text-slate-700 mb-3">لا توجد منتجات حالياً</h3>
            <p class="text-slate-500 max-w-md mx-auto">سيتم إضافة منتجات جديدة قريباً، تابعنا لتكون أول من يعرف</p>
        </div>
        @endif
    </div>
</section>

<!-- Features Section -->
<section class="bg-white py-16 border-t border-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-2xl md:text-3xl font-bold text-slate-900 mb-3">لماذا جرافوني؟</h2>
            <p class="text-slate-500">نقدم لك تجربة تسوق استثنائية</p>
        </div>
        
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6 md:gap-8">
            <div class="text-center group">
                <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg shadow-blue-500/30 group-hover:scale-110 transition-transform">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                    </svg>
                </div>
                <h3 class="font-bold text-slate-900 mb-1">توصيل سريع</h3>
                <p class="text-slate-500 text-sm">لجميع المحافظات خلال 24-48 ساعة</p>
            </div>
            
            <div class="text-center group">
                <div class="w-16 h-16 bg-gradient-to-br from-green-500 to-emerald-600 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg shadow-green-500/30 group-hover:scale-110 transition-transform">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h3 class="font-bold text-slate-900 mb-1">جودة مضمونة</h3>
                <p class="text-slate-500 text-sm">منتجات أصلية 100% بضمان</p>
            </div>
            
            <div class="text-center group">
                <div class="w-16 h-16 bg-gradient-to-br from-amber-500 to-orange-600 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg shadow-amber-500/30 group-hover:scale-110 transition-transform">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                </div>
                <h3 class="font-bold text-slate-900 mb-1">استبدال واسترجاع</h3>
                <p class="text-slate-500 text-sm">خلال 14 يوم بدون أي مشاكل</p>
            </div>
            
            <div class="text-center group">
                <div class="w-16 h-16 bg-gradient-to-br from-purple-500 to-violet-600 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg shadow-purple-500/30 group-hover:scale-110 transition-transform">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
                <h3 class="font-bold text-slate-900 mb-1">الدفع عند الاستلام</h3>
                <p class="text-slate-500 text-sm">ادفع بعد استلام طلبك</p>
            </div>
        </div>
    </div>
</section>
@endsection
