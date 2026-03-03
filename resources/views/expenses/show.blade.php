@extends('layouts.app')

@section('title', 'تفاصيل المصروف')

@section('content')
<div class="mb-6 sm:mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div class="min-w-0">
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">تفاصيل المصروف</h1>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('expenses.edit', $expense) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">تعديل</a>
        <a href="{{ route('expenses.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">العودة</a>
    </div>
</div>

<div class="max-w-2xl bg-white shadow rounded-lg p-6">
    <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <div>
            <dt class="text-sm font-medium text-gray-500">المبلغ</dt>
            <dd class="mt-1 text-lg font-semibold text-red-600">{{ number_format($expense->amount, 2) }} ج.م</dd>
        </div>
        <div>
            <dt class="text-sm font-medium text-gray-500">التاريخ</dt>
            <dd class="mt-1 text-sm text-gray-900">{{ $expense->expense_date->format('Y-m-d') }}</dd>
        </div>
        <div>
            <dt class="text-sm font-medium text-gray-500">التصنيف</dt>
            <dd class="mt-1">
                @php $cats = \App\Models\Expense::categories(); @endphp
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">
                    {{ $cats[$expense->category] ?? $expense->category }}
                </span>
            </dd>
        </div>
        <div class="sm:col-span-2">
            <dt class="text-sm font-medium text-gray-500">الوصف</dt>
            <dd class="mt-1 text-sm text-gray-900">{{ $expense->description }}</dd>
        </div>
        @if($expense->note)
        <div class="sm:col-span-2">
            <dt class="text-sm font-medium text-gray-500">ملاحظة</dt>
            <dd class="mt-1 text-sm text-gray-600">{{ $expense->note }}</dd>
        </div>
        @endif
    </dl>
</div>
@endsection
