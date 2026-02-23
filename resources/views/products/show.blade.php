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
                <div class="sm:col-span-2">
                    <dt class="text-sm font-medium text-gray-500 mb-2">مخطط المقاسات المتاح</dt>
                    <dd class="mt-1 flex flex-col">
                        <div class="overflow-x-auto rounded-lg border border-gray-200 shadow-sm">
                            <table class="min-w-full divide-y divide-gray-200 text-sm">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-4 py-2.5 text-right font-semibold text-gray-900">المقاس</th>
                                        <th scope="col" class="px-4 py-2.5 text-right font-semibold text-gray-900">الصدر (سم)</th>
                                        <th scope="col" class="px-4 py-2.5 text-right font-semibold text-gray-900">الوزن (كجم)</th>
                                        <th scope="col" class="px-4 py-2.5 text-right font-semibold text-gray-900">الطول (سم)</th>
                                        <th scope="col" class="px-4 py-2.5 text-right font-semibold text-gray-900">الألوان والكميات</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 bg-white">
                                    @foreach($product->available_sizes as $s)
                                        @if(is_array($s))
                                        <tr class="hover:bg-gray-50/50">
                                            <td class="whitespace-nowrap px-4 py-2 font-bold text-emerald-700 bg-emerald-50/30">{{ $s['size'] ?? '-' }}</td>
                                            <td class="whitespace-nowrap px-4 py-2 text-gray-700">{{ $s['chest_width_cm'] ?? '-' }}</td>
                                            <td class="whitespace-nowrap px-4 py-2 text-gray-700">{{ $s['weight_kg']['min'] ?? '-' }} - {{ $s['weight_kg']['max'] ?? '-' }}</td>
                                            <td class="whitespace-nowrap px-4 py-2 text-gray-700">{{ $s['height_cm']['min'] ?? '-' }} - {{ $s['height_cm']['max'] ?? '-' }}</td>
                                            <td class="px-4 py-2">
                                                @if(isset($s['colors']) && is_array($s['colors']) && count($s['colors']) > 0)
                                                    <div class="flex flex-wrap gap-1">
                                                        @foreach($s['colors'] as $cObj)
                                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-violet-100 text-violet-800 border border-violet-200">
                                                                {{ $cObj['color'] ?? 'بدون اسم' }} (كمية: {{ $cObj['stock'] ?? 0 }})
                                                            </span>
                                                        @endforeach
                                                    </div>
                                                @else
                                                    <span class="text-xs text-gray-400">لا توجد ألوان</span>
                                                @endif
                                            </td>
                                        </tr>
                                        @else
                                        <tr class="hover:bg-gray-50/50">
                                            <td class="whitespace-nowrap px-4 py-2 font-bold text-gray-700 bg-gray-50">{{ $s }}</td>
                                            <td colspan="4" class="whitespace-nowrap px-4 py-2 text-gray-400 text-center">لا يوجد تفاصيل إضافية لهذا المقاس</td>
                                        </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </dd>
                </div>
                @endif
                <!-- Independent Colors Section Removed -->
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
                                <a href="{{ asset('storage/' . $path) }}" target="_blank" class="block group rounded-xl overflow-hidden border-2 border-gray-200 hover:border-slate-400 shadow-sm hover:shadow-md transition-all">
                                    <img src="{{ asset('storage/' . $path) }}" alt="عينة {{ $idx + 1 }}" class="w-full h-40 object-cover group-hover:scale-105 transition-transform duration-200">
                                    <span class="block py-1.5 text-center text-xs text-gray-500 bg-gray-50">صورة {{ $idx + 1 }}</span>
                                </a>
                            @endforeach
                        </div>
                    </dd>
                </div>
                @endif
                
                @php $videos = $product->videos ?? []; @endphp
                @if(!empty($videos))
                <div>
                    <dt class="text-sm font-medium text-gray-500">فيديوهات المنتج</dt>
                    <dd class="mt-3">
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($videos as $idx => $path)
                                <div class="block group rounded-xl overflow-hidden border-2 border-gray-200 bg-black shadow-sm transition-all focus-within:ring-2 focus-within:ring-slate-500">
                                    <video src="{{ asset('storage/' . $path) }}" class="w-full h-40 object-cover" controls preload="metadata"></video>
                                    <span class="block py-1.5 text-center text-xs text-white bg-gray-900">فيديو {{ $idx + 1 }}</span>
                                </div>
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
