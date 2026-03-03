@extends('layouts.app')

@section('title', 'إضافة مصروف')

@section('content')
<div class="mb-6 sm:mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div class="min-w-0">
        <div class="flex items-center gap-3">
            <div class="flex-shrink-0 w-12 h-12 rounded-2xl bg-gradient-to-br from-amber-500 to-orange-600 flex items-center justify-center shadow-lg shadow-amber-500/25">
                <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2h-2m-4-1V7a2 2 0 012-2h2a2 2 0 012 2v1" />
                </svg>
            </div>
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">إضافة مصروف</h1>
                <p class="mt-0.5 text-sm text-gray-600">سجّل المصاريف التشغيلية لخصمها من الأرباح</p>
            </div>
        </div>
    </div>
    <a href="{{ route('expenses.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 border border-gray-200 rounded-xl text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 hover:border-gray-300 shadow-sm transition-all duration-200">
        <svg class="h-5 w-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
        </svg>
        العودة للمصاريف
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 lg:gap-8">
    {{-- نموذج الإضافة --}}
    <div class="lg:col-span-2">
        <div class="bg-white rounded-2xl shadow-xl shadow-gray-200/50 border border-gray-100 overflow-hidden">
            <div class="px-6 sm:px-8 py-5 bg-gradient-to-l from-amber-50/80 to-transparent border-b border-amber-100/50">
                <h2 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                    <span class="w-8 h-8 rounded-lg bg-amber-500/10 flex items-center justify-center">
                        <svg class="h-4 w-4 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </span>
                    تفاصيل المصروف
                </h2>
            </div>
            <form action="{{ route('expenses.store') }}" method="POST" class="p-6 sm:p-8">
                @csrf

                <div class="space-y-6">
                    {{-- المبلغ --}}
                    <div class="group">
                        <label for="amount" class="flex items-center gap-2 text-sm font-medium text-gray-700 mb-2">
                            <svg class="h-4 w-4 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            المبلغ (ج.م) *
                        </label>
                        <div class="relative">
                            <input type="number" name="amount" id="amount" step="0.01" min="0.01" value="{{ old('amount') }}" required
                                placeholder="0.00"
                                class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-amber-500 focus:ring-amber-500 pr-4 pl-14 py-3.5 text-lg font-semibold text-gray-900 placeholder-gray-400 transition-all duration-200 @error('amount') border-red-500 focus:border-red-500 focus:ring-red-500 @enderror">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 font-medium text-sm">ج.م</span>
                        </div>
                        @error('amount')
                        <p class="mt-2 text-sm text-red-600 flex items-center gap-1">
                            <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                            {{ $message }}
                        </p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        {{-- التاريخ --}}
                        <div class="group">
                            <label for="expense_date" class="flex items-center gap-2 text-sm font-medium text-gray-700 mb-2">
                                <svg class="h-4 w-4 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                تاريخ المصروف *
                            </label>
                            <input type="date" name="expense_date" id="expense_date" value="{{ old('expense_date', date('Y-m-d')) }}" required
                                class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-amber-500 focus:ring-amber-500 py-3 transition-all duration-200 @error('expense_date') border-red-500 @enderror">
                            @error('expense_date')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- التصنيف --}}
                        <div class="group">
                            <label for="category" class="flex items-center gap-2 text-sm font-medium text-gray-700 mb-2">
                                <svg class="h-4 w-4 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                </svg>
                                التصنيف *
                            </label>
                            <select name="category" id="category" required
                                class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-amber-500 focus:ring-amber-500 py-3 transition-all duration-200 @error('category') border-red-500 @enderror">
                                <option value="">اختر التصنيف</option>
                                @foreach($categories as $key => $label)
                                <option value="{{ $key }}" {{ old('category') == $key ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('category')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- الوصف --}}
                    <div class="group">
                        <label for="description" class="flex items-center gap-2 text-sm font-medium text-gray-700 mb-2">
                            <svg class="h-4 w-4 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7" />
                            </svg>
                            الوصف *
                        </label>
                        <input type="text" name="description" id="description" value="{{ old('description') }}" required
                            placeholder="مثال: حملة إعلانية على فيسبوك"
                            class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-amber-500 focus:ring-amber-500 py-3 transition-all duration-200 @error('description') border-red-500 @enderror">
                        @error('description')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- ملاحظة --}}
                    <div class="group">
                        <label for="note" class="flex items-center gap-2 text-sm font-medium text-gray-700 mb-2">
                            <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                            </svg>
                            ملاحظة <span class="text-gray-400 font-normal">(اختياري)</span>
                        </label>
                        <textarea name="note" id="note" rows="3" placeholder="أي تفاصيل إضافية..."
                            class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-amber-500 focus:ring-amber-500 py-3 transition-all duration-200 resize-none @error('note') border-red-500 @enderror">{{ old('note') }}</textarea>
                        @error('note')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mt-8 pt-6 border-t border-gray-100 flex flex-wrap gap-3">
                    <button type="submit" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl text-sm font-semibold text-white bg-gradient-to-r from-amber-500 to-orange-600 hover:from-amber-600 hover:to-orange-700 shadow-lg shadow-amber-500/25 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        حفظ المصروف
                    </button>
                    <a href="{{ route('expenses.index') }}" class="inline-flex items-center gap-2 px-5 py-3 rounded-xl border border-gray-200 text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-all duration-200">
                        إلغاء
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- لوحة جانبية للمساعدة --}}
    <div class="lg:col-span-1 space-y-4">
        <div class="bg-gradient-to-br from-amber-50 to-orange-50 rounded-2xl border border-amber-100 p-6">
            <h3 class="text-sm font-semibold text-amber-900 mb-3 flex items-center gap-2">
                <svg class="h-5 w-5 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                نصائح سريعة
            </h3>
            <ul class="space-y-2.5 text-sm text-amber-800/90">
                <li class="flex gap-2">
                    <span class="text-amber-500 font-bold">•</span>
                    سجّل جميع المصاريف التشغيلية لاحتساب صافي الربح بدقة
                </li>
                <li class="flex gap-2">
                    <span class="text-amber-500 font-bold">•</span>
                    اختر تصنيفاً مناسباً لتسهيل التقارير والتحليل
                </li>
                <li class="flex gap-2">
                    <span class="text-amber-500 font-bold">•</span>
                    المصاريف تُخصم تلقائياً من تقرير الأرباح
                </li>
            </ul>
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <h3 class="text-sm font-semibold text-gray-800 mb-3">تصنيفات شائعة</h3>
            <div class="flex flex-wrap gap-2">
                @php $cats = $categories; @endphp
                @foreach(['advertising', 'shipping', 'rent'] as $key)
                @if(isset($cats[$key]))
                <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-medium bg-amber-100 text-amber-800">
                    {{ $cats[$key] }}
                </span>
                @endif
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
