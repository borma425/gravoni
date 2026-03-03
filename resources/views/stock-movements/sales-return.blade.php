@extends('layouts.app')

@section('title', 'مرتجع بيع')

@section('content')
<div class="mb-6 sm:mb-8">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="min-w-0">
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">تسجيل مرتجع بيع</h1>
            <p class="mt-2 text-sm text-gray-600">تسجيل مرتجع لعملية بيع</p>
        </div>
        <a href="{{ route('dashboard') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-slate-500 transition-colors">
            <svg class="ml-2 -mr-1 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            العودة
        </a>
    </div>
</div>

<div class="bg-white shadow rounded-lg">
    <div class="px-4 py-5 sm:p-6">
        @if($returnables->isEmpty())
        <div class="rounded-lg bg-amber-50 border border-amber-200 p-6 text-center">
            <svg class="mx-auto h-12 w-12 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
            </svg>
            <h3 class="mt-2 text-sm font-medium text-amber-800">لا توجد مبيعات أو أوردرات</h3>
            <p class="mt-1 text-sm text-amber-700">لا يمكن تسجيل مرتجع - لم يتم تسجيل أي مبيعات يدوية أو أوردرات مقبولة حتى الآن.</p>
            <p class="mt-2 text-xs text-amber-600">المبيعات اليدوية من صفحة المبيعات، والأوردرات من الموقع.</p>
        </div>
        @else
        <form action="{{ route('stock-movements.sales-return.store') }}" method="POST" class="space-y-6">
            @csrf
            <div>
                <label for="returnable_key" class="block text-sm font-medium text-gray-700">البيع / الأوردر</label>
                <select name="returnable_key" id="returnable_key" required
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-slate-500 focus:border-slate-500 sm:text-sm @error('returnable_key') border-red-300 @enderror">
                    <option value="">اختر بيع أو أوردر</option>
                    @foreach($returnables as $key => $r)
                    <option value="{{ $key }}" {{ old('returnable_key') == $key ? 'selected' : '' }}
                            data-type="{{ $r['type'] }}" data-max="{{ $r['max_qty'] }}">
                        [{{ $r['type'] === 'sale' ? 'بيع يدوي' : 'أوردر' }}] {{ $r['label'] }} - كمية قابلة للإرجاع: {{ $r['max_qty'] }} - تاريخ: {{ $r['date'] }}
                    </option>
                    @endforeach
                </select>
                @error('returnable_key')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="quantity" class="block text-sm font-medium text-gray-700">الكمية المرتجعة</label>
                <input type="number" name="quantity" id="quantity" value="{{ old('quantity') }}" required min="1" max="999999"
                       class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-slate-500 focus:border-slate-500 sm:text-sm @error('quantity') border-red-300 @enderror">
                @error('quantity')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-end space-x-reverse space-x-3">
                <a href="{{ route('dashboard') }}" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-slate-500 transition-colors">
                    إلغاء
                </a>
                <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-slate-700 hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-slate-500 transition-colors">
                    حفظ
                </button>
            </div>
        </form>
        @endif
    </div>
</div>
@if(!$returnables->isEmpty())
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const sel = document.getElementById('returnable_key');
    const qtyInput = document.getElementById('quantity');
    function updateMax() {
        const opt = sel.options[sel.selectedIndex];
        if (opt && opt.value) {
            const max = parseInt(opt.getAttribute('data-max') || '999999', 10);
            qtyInput.max = max;
            if (parseInt(qtyInput.value, 10) > max) qtyInput.value = max;
        }
    }
    sel.addEventListener('change', updateMax);
    updateMax();
});
</script>
@endpush
@endif
@endsection
