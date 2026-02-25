@extends('layouts.store')

@section('title', 'تم استلام طلبك - جرافوني')

@section('content')
<div class="min-h-screen bg-gray-50 flex items-center justify-center py-12 px-4">
    <div class="max-w-lg w-full text-center">
        <div class="w-24 h-24 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-8">
            <svg class="w-12 h-12 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
        </div>

        <h1 class="text-2xl md:text-3xl font-bold text-slate-900 mb-4">لقد استلمنا طلبك</h1>
        <p class="text-lg text-slate-600 mb-2">وسنتواصل معك في أقرب وقت</p>
        <p class="text-slate-600 mb-8">يرجى الانتظار لمكالمتنا</p>

        @if(session('mylerz_error'))
        <div class="mb-6 p-4 bg-amber-50 border border-amber-200 rounded-xl text-amber-800 text-sm">
            تنبيه: {{ session('mylerz_error') }} — سنتواصل معك لإتمام الشحن.
        </div>
        @endif

        @if(!empty($order->shipping_data['barcode']))
        <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 rounded-xl text-emerald-800 text-sm">
            تم إرسال الشحنة لـ Mylerz بنجاح. الباركود: <strong>{{ $order->shipping_data['barcode'] }}</strong>
        </div>
        @endif

        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 mb-8">
            <p class="text-slate-600 mb-4">رقم تتبع الطلب: <strong class="text-slate-900">{{ $order->tracking_id }}</strong></p>
            <button type="button" onclick="copyTrackingLink()" 
                    class="inline-flex items-center gap-2 px-6 py-3 bg-slate-900 text-white rounded-xl font-semibold hover:bg-slate-800">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"/>
                </svg>
                <span id="copy-btn-text">اضغط هنا لنسخ رابط التتبع</span>
            </button>
            <p class="text-xs text-slate-500 mt-3">يمكنك متابعة حالة طلبك عبر الرابط</p>
        </div>

        <a href="{{ route('store.index') }}" class="inline-block text-slate-600 hover:text-slate-900 font-medium">
            العودة للمتجر
        </a>
    </div>
</div>

<script>
const trackingUrl = "{{ url(route('store.track', $order->tracking_id)) }}";
function copyTrackingLink() {
    navigator.clipboard.writeText(trackingUrl).then(function() {
        document.getElementById('copy-btn-text').textContent = 'تم النسخ!';
        setTimeout(function() { document.getElementById('copy-btn-text').textContent = 'اضغط هنا لنسخ رابط التتبع'; }, 2000);
    });
}
</script>
@endsection
