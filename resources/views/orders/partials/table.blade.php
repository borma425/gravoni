@php
    $statusColors = [
        'pending' => 'bg-yellow-100 text-yellow-800',
        'accepted' => 'bg-emerald-100 text-emerald-800',
        'delivery_fees_paid' => 'bg-blue-100 text-blue-800',
        'shipped' => 'bg-green-100 text-green-800',
        'cancelled' => 'bg-red-100 text-red-800',
    ];
@endphp

{{-- Mobile Card Layout (شاشات أقل من md) --}}
<div class="md:hidden -mx-3 sm:mx-0 divide-y divide-gray-100">
    @forelse($orders as $order)
    @php
        $daysLimit = (int) config('orders.order_reject_delete_days_limit', 14);
        $isAcceptedOrder = in_array($order->status, ['accepted', 'delivery_fees_paid', 'shipped']);
        $isTooOldToReject = $isAcceptedOrder && $order->created_at->copy()->startOfDay()->addDays($daysLimit)->isPast();
        $rowBg = ($order->status === 'cancelled') ? 'bg-red-50/80' : ($order->seen_at ? 'bg-emerald-50/70' : 'bg-white');
    @endphp
    <div class="p-4 {{ $rowBg }} active:bg-gray-50/80 transition-colors">
        <a href="{{ route('orders.show', $order) }}" class="block -m-4 p-4">
            <div class="flex items-start justify-between gap-3">
                <div class="min-w-0 flex-1">
                    <div class="flex flex-wrap items-center gap-2 mb-1">
                        <span class="text-base font-semibold text-gray-900 truncate">{{ $order->customer_name }}</span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium flex-shrink-0 {{ $statusColors[$order->status] ?? 'bg-gray-100 text-gray-800' }}">
                            {{ $order->status_label }}
                        </span>
                    </div>
                    <p class="text-sm text-gray-500 truncate">{{ $order->customer_address }}</p>
                    <div class="mt-1 flex flex-wrap gap-x-3 gap-y-0.5 text-xs text-gray-500">
                        <span>{{ number_format($order->items_revenue, 2) }} ج.م</span>
                        <span>•</span>
                        <span>{{ count($order->items) }} منتج</span>
                        <span>•</span>
                        <span>
                            @if($order->created_at->isToday())
                                اليوم {{ $order->created_at->format('H:i') }}
                            @else
                                {{ $order->created_at->format('Y-m-d') }}
                            @endif
                        </span>
                    </div>
                </div>
                <span class="flex-shrink-0 text-slate-400" aria-hidden="true">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </span>
            </div>
        </a>

        {{-- أزرار الإجراءات على الموبايل: touch-friendly (min 44px) --}}
        <div class="mt-3 pt-3 border-t border-gray-100 flex flex-wrap gap-2">
            @if($order->tracking_id)
            @php $trackUrl = url(route('store.track', $order->tracking_id)); @endphp
            <a href="{{ $trackUrl }}" target="_blank" class="inline-flex items-center justify-center gap-1.5 min-h-[44px] px-4 py-2.5 bg-slate-900 text-white text-sm font-medium rounded-xl hover:bg-slate-800 active:bg-slate-700 touch-manipulation">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                فتح التتبع
            </a>
            <button type="button" onclick="navigator.clipboard.writeText('{{ $trackUrl }}'); this.textContent='تم النسخ!'; setTimeout(()=>this.textContent='نسخ الرابط', 1500)" class="inline-flex items-center justify-center gap-1.5 min-h-[44px] px-4 py-2.5 bg-slate-100 text-slate-700 text-sm font-medium rounded-xl hover:bg-slate-200 active:bg-slate-300 touch-manipulation">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"/></svg>
                نسخ الرابط
            </button>
            @endif
            @if(!empty($order->shipping_data['barcode']))
            <a href="{{ route('orders.mylerz-label', $order) }}" target="_blank" class="inline-flex items-center justify-center gap-1.5 min-h-[44px] px-4 py-2.5 bg-amber-100 text-amber-800 text-sm font-medium rounded-xl hover:bg-amber-200 active:bg-amber-300 touch-manipulation">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                طباعة الملصق
            </a>
            @endif
            @if($order->status !== 'cancelled')
                @if(!$isTooOldToReject)
                <form action="{{ route('orders.reject', $order) }}" method="POST" class="inline" onsubmit="return confirm('هل أنت متأكد من رفض هذا الطلب؟ سيتم إلغاؤه في Mylerz أيضاً.')">
                    @csrf
                    <button type="submit" class="inline-flex items-center justify-center gap-1.5 min-h-[44px] px-4 py-2.5 bg-red-600 text-white text-sm font-medium rounded-xl hover:bg-red-700 active:bg-red-800 touch-manipulation">
                        رفض
                    </button>
                </form>
                @endif
                @if(!$order->seen_at)
                <form action="{{ route('orders.mark-seen', $order) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="inline-flex items-center justify-center gap-1.5 min-h-[44px] px-4 py-2.5 bg-emerald-600 text-white text-sm font-medium rounded-xl hover:bg-emerald-700 active:bg-emerald-800 touch-manipulation">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        رأيت الأوردر
                    </button>
                </form>
                @else
                <span class="inline-flex items-center gap-1.5 min-h-[44px] px-4 py-2.5 bg-emerald-100 text-emerald-800 text-sm font-medium rounded-xl" title="تم التأشير في {{ $order->seen_at->format('Y-m-d H:i') }}">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    مُجهَّز
                </span>
                @endif
            @endif
            @if($order->status === 'cancelled')
            <form action="{{ route('orders.destroy', $order) }}" method="POST" class="inline" onsubmit="return confirm('هل أنت متأكد من حذف هذا الطلب نهائياً؟ لن يظهر بعدها في القائمة.')">
                @csrf
                @method('DELETE')
                <button type="submit" class="inline-flex items-center justify-center gap-1.5 min-h-[44px] px-4 py-2.5 bg-gray-600 text-white text-sm font-medium rounded-xl hover:bg-gray-700 active:bg-gray-800 touch-manipulation">
                    حذف
                </button>
            </form>
            @endif
            <a href="{{ route('orders.show', $order) }}" class="inline-flex items-center justify-center min-h-[44px] min-w-[44px] p-2.5 rounded-xl border border-gray-200 text-gray-600 hover:bg-gray-50 hover:border-gray-300 active:bg-gray-100 touch-manipulation" title="عرض التفاصيل">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
            </a>
        </div>
    </div>
    @empty
    <div class="p-8 text-center bg-white">
        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
        </svg>
        <h3 class="mt-2 text-sm font-medium text-gray-900">لا توجد طلبات</h3>
        <p class="mt-1 text-sm text-gray-500">ابدأ بإضافة طلب جديد</p>
    </div>
    @endforelse
</div>

{{-- Desktop Table Layout (شاشات md فأكبر) --}}
<div class="hidden md:block overflow-x-auto -mx-3 sm:mx-0 sm:rounded-b-xl">
    <table class="min-w-[900px] w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th scope="col" class="px-4 sm:px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">المصدر</th>
                <th scope="col" class="px-4 sm:px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">رقم التتبع</th>
                <th scope="col" class="px-4 sm:px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">اسم العميل</th>
                <th scope="col" class="px-4 sm:px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">العنوان</th>
                <th scope="col" class="px-4 sm:px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">أرقام الهاتف</th>
                <th scope="col" class="px-4 sm:px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">المنتجات</th>
                <th scope="col" class="px-4 sm:px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">عدد المنتجات</th>
                <th scope="col" class="px-4 sm:px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">سعر البيع</th>
                <th scope="col" class="px-4 sm:px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الحالة</th>
                <th scope="col" class="px-4 sm:px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">التاريخ</th>
                <th scope="col" class="px-4 sm:px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الإجراءات</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($orders as $order)
            @php
                $daysLimit = (int) config('orders.order_reject_delete_days_limit', 14);
                $isAcceptedOrder = in_array($order->status, ['accepted', 'delivery_fees_paid', 'shipped']);
                $isTooOldToReject = $isAcceptedOrder && $order->created_at->copy()->startOfDay()->addDays($daysLimit)->isPast();
                $rowBgDesktop = ($order->status === 'cancelled') ? 'bg-red-50/80 hover:bg-red-100/80' : ($order->seen_at ? 'bg-emerald-50/70 hover:bg-emerald-100/70' : 'hover:bg-gray-50');
            @endphp
            <tr class="transition-colors {{ $rowBgDesktop }}">
                <td class="px-4 sm:px-6 py-4 whitespace-nowrap">
                    @if($order->tracking_id)
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-medium bg-emerald-100 text-emerald-800" title="تم الشراء من الموقع">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/></svg>
                        من الموقع
                    </span>
                    @else
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-medium bg-gray-100 text-gray-600" title="لم يتم الشراء من الموقع">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        سوشيال ميديا
                    </span>
                    @endif
                </td>
                <td class="px-4 sm:px-6 py-4">
                    @if($order->tracking_id)
                    @php $trackUrl = url(route('store.track', $order->tracking_id)); @endphp
                    <div class="flex flex-col gap-2">
                        <span class="text-sm font-bold text-slate-900 font-mono">{{ $order->tracking_id }}</span>
                        <div class="flex flex-wrap gap-1">
                            <a href="{{ $trackUrl }}" target="_blank" class="inline-flex items-center gap-1 px-3 py-1.5 bg-slate-900 text-white text-xs font-medium rounded-lg hover:bg-slate-800">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                فتح التتبع
                            </a>
                            <button type="button" onclick="navigator.clipboard.writeText('{{ $trackUrl }}'); this.textContent='تم النسخ!'; setTimeout(()=>this.textContent='نسخ الرابط', 1500)" class="inline-flex items-center gap-1 px-3 py-1.5 bg-slate-100 text-slate-700 text-xs font-medium rounded-lg hover:bg-slate-200">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"/></svg>
                                نسخ الرابط
                            </button>
                        </div>
                    </div>
                    @else
                    <span class="text-sm text-gray-400">—</span>
                    @endif
                    @if(!empty($order->shipping_data['barcode']))
                    <div class="mt-2 flex flex-wrap items-center gap-1">
                        <span class="text-xs text-slate-500">Mylerz: <span class="font-mono">{{ $order->shipping_data['barcode'] }}</span></span>
                        <a href="{{ route('orders.mylerz-label', $order) }}" target="_blank" class="inline-flex items-center gap-1 px-2 py-1 bg-amber-100 text-amber-800 text-xs font-medium rounded hover:bg-amber-200" title="طباعة ملصق Mylerz">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                            طباعة الملصق
                        </a>
                    </div>
                    @endif
                </td>
                <td class="px-4 sm:px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-medium text-gray-900">{{ $order->customer_name }}</div>
                </td>
                <td class="px-4 sm:px-6 py-4">
                    <div class="text-sm text-gray-500 max-w-xs truncate">{{ $order->customer_address }}</div>
                </td>
                <td class="px-4 sm:px-6 py-4">
                    <div class="text-sm text-gray-500">
                        @foreach($order->customer_numbers as $number)
                            <div>{{ $number }}</div>
                        @endforeach
                    </div>
                </td>
                <td class="px-4 sm:px-6 py-4 text-sm text-gray-900 max-w-[200px]">
                    {{ collect($order->items ?? [])->pluck('product_name')->filter()->implode('، ') ?: '—' }}
                </td>
                <td class="px-4 sm:px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    {{ count($order->items) }}
                </td>
                <td class="px-4 sm:px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                    {{ number_format($order->items_revenue, 2) }} ج.م
                </td>
                <td class="px-4 sm:px-6 py-4 whitespace-nowrap">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$order->status] ?? 'bg-gray-100 text-gray-800' }}">
                        {{ $order->status_label }}
                    </span>
                </td>
                <td class="px-4 sm:px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    @if($order->created_at->isToday())
                    <span class="font-medium text-emerald-600">اليوم</span><br><span class="text-xs">{{ $order->created_at->format('H:i') }}</span>
                    @else
                    {{ $order->created_at->format('Y-m-d') }}<br><span class="text-xs">{{ $order->created_at->format('H:i') }}</span>
                    @endif
                </td>
                <td class="px-4 sm:px-6 py-4 whitespace-nowrap text-sm font-medium">
                    <div class="flex flex-wrap items-center gap-2">
                        @if($order->status !== 'cancelled')
                        @if(!$isTooOldToReject)
                        <form action="{{ route('orders.reject', $order) }}" method="POST" class="inline" onsubmit="return confirm('هل أنت متأكد من رفض هذا الطلب؟ سيتم إلغاؤه في Mylerz أيضاً.')">
                            @csrf
                            <button type="submit" class="inline-flex items-center gap-1 px-3 py-1.5 bg-red-600 text-white text-xs font-medium rounded-lg hover:bg-red-700">رفض</button>
                        </form>
                        @endif
                        @endif
                        @if($order->status !== 'cancelled' && !$order->seen_at)
                        <form action="{{ route('orders.mark-seen', $order) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="inline-flex items-center gap-1 px-3 py-1.5 bg-emerald-600 text-white text-xs font-medium rounded-lg hover:bg-emerald-700" title="رأيت الأوردر وسأقوم بتجهيزه">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                رأيت الأوردر
                            </button>
                        </form>
                        @elseif($order->seen_at)
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-emerald-100 text-emerald-800 text-xs font-medium rounded-lg" title="تم التأشير في {{ $order->seen_at->format('Y-m-d H:i') }}">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            مُجهَّز
                        </span>
                        @endif
                        @if($order->status === 'cancelled')
                        <form action="{{ route('orders.destroy', $order) }}" method="POST" class="inline" onsubmit="return confirm('هل أنت متأكد من حذف هذا الطلب نهائياً؟ لن يظهر بعدها في القائمة.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="inline-flex items-center gap-1 px-3 py-1.5 bg-gray-600 text-white text-xs font-medium rounded-lg hover:bg-gray-700" title="حذف من لدينا">حذف</button>
                        </form>
                        @endif
                        <a href="{{ route('orders.show', $order) }}" class="inline-flex items-center justify-center min-h-[36px] min-w-[36px] p-1.5 rounded-lg text-slate-600 hover:text-slate-900 hover:bg-slate-100 transition-colors" title="عرض">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </a>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="11" class="px-4 sm:px-6 py-12 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">لا توجد طلبات</h3>
                    <p class="mt-1 text-sm text-gray-500">ابدأ بإضافة طلب جديد</p>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($orders->hasPages())
<div class="bg-white px-3 sm:px-6 py-3 border-t border-gray-200 orders-pagination">
    {{ $orders->links() }}
</div>
@endif
