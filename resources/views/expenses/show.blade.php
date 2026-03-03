@extends('layouts.app')

@section('title', 'تفاصيل المصروف')

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
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">تفاصيل المصروف</h1>
                <p class="mt-0.5 text-sm text-gray-600">{{ $expense->description }}</p>
            </div>
        </div>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('expenses.edit', $expense) }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-medium text-white bg-gradient-to-r from-amber-500 to-orange-600 hover:from-amber-600 hover:to-orange-700 shadow-lg shadow-amber-500/25 transition-all duration-200">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
            </svg>
            تعديل
        </a>
        <a href="{{ route('expenses.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 border border-gray-200 rounded-xl text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-all duration-200">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            العودة
        </a>
    </div>
</div>

<div class="max-w-2xl bg-white rounded-2xl shadow-xl shadow-gray-200/50 border border-gray-100 overflow-hidden">
    <div class="px-6 sm:px-8 py-5 bg-gradient-to-l from-amber-50/80 to-transparent border-b border-amber-100/50">
        <h2 class="text-lg font-semibold text-gray-900">معلومات المصروف</h2>
    </div>
    <dl class="p-6 sm:p-8 grid grid-cols-1 gap-6 sm:grid-cols-2">
        <div class="sm:col-span-2 p-4 rounded-xl bg-red-50 border border-red-100">
            <dt class="text-sm font-medium text-red-800">المبلغ</dt>
            <dd class="mt-1 text-2xl font-bold text-red-600">{{ number_format($expense->amount, 2) }} ج.م</dd>
        </div>
        <div>
            <dt class="text-sm font-medium text-gray-500">التاريخ</dt>
            <dd class="mt-1 text-base font-medium text-gray-900">{{ $expense->expense_date->format('Y-m-d') }}</dd>
        </div>
        <div>
            <dt class="text-sm font-medium text-gray-500">التصنيف</dt>
            <dd class="mt-1">
                @php $cats = \App\Models\Expense::categories(); @endphp
                <span class="inline-flex items-center px-3 py-1 rounded-lg text-sm font-medium bg-amber-100 text-amber-800">
                    {{ $cats[$expense->category] ?? $expense->category }}
                </span>
            </dd>
        </div>
        <div class="sm:col-span-2">
            <dt class="text-sm font-medium text-gray-500">الوصف</dt>
            <dd class="mt-1 text-base text-gray-900">{{ $expense->description }}</dd>
        </div>
        @if($expense->note)
        <div class="sm:col-span-2 p-4 rounded-xl bg-gray-50 border border-gray-100">
            <dt class="text-sm font-medium text-gray-500">ملاحظة</dt>
            <dd class="mt-1 text-sm text-gray-700">{{ $expense->note }}</dd>
        </div>
        @endif
    </dl>
</div>
@endsection
