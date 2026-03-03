@extends('layouts.app')

@section('title', 'المصاريف')

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
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">المصاريف</h1>
                <p class="mt-0.5 text-sm text-gray-600">المصاريف التشغيلية التي تُخصم من الأرباح</p>
            </div>
        </div>
    </div>
    <a href="{{ route('expenses.create') }}" class="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl text-sm font-semibold text-white bg-gradient-to-r from-amber-500 to-orange-600 hover:from-amber-600 hover:to-orange-700 shadow-lg shadow-amber-500/25 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500 flex-shrink-0">
        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
        </svg>
        إضافة مصروف
    </a>
</div>

{{-- بطاقة التصفية --}}
<div class="mb-6 bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <form method="GET" action="{{ route('expenses.index') }}" class="p-5 sm:p-6">
        <div class="flex flex-wrap gap-4 items-end">
            <div class="flex-1 min-w-[140px]">
                <label for="category" class="block text-sm font-medium text-gray-700 mb-1.5">التصنيف</label>
                <select name="category" id="category" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-amber-500 focus:ring-amber-500 py-2.5">
                    <option value="">الكل</option>
                    @foreach($categories as $key => $label)
                    <option value="{{ $key }}" {{ request('category') == $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="min-w-[140px]">
                <label for="from_date" class="block text-sm font-medium text-gray-700 mb-1.5">من تاريخ</label>
                <input type="date" name="from_date" id="from_date" value="{{ request('from_date') }}" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-amber-500 focus:ring-amber-500 py-2.5">
            </div>
            <div class="min-w-[140px]">
                <label for="to_date" class="block text-sm font-medium text-gray-700 mb-1.5">إلى تاريخ</label>
                <input type="date" name="to_date" id="to_date" value="{{ request('to_date') }}" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-amber-500 focus:ring-amber-500 py-2.5">
            </div>
            <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-medium text-white bg-slate-700 hover:bg-slate-800 transition-all duration-200">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                </svg>
                تصفية
            </button>
        </div>
    </form>
</div>

{{-- إجمالي المصاريف --}}
<div class="mb-6 flex flex-wrap gap-4">
    <div class="flex-1 min-w-[200px] bg-gradient-to-br from-amber-50 to-orange-50 rounded-2xl border border-amber-100 p-5">
        <p class="text-sm font-medium text-amber-800/80 mb-1">إجمالي المصاريف (حسب التصفية)</p>
        <p class="text-2xl font-bold text-amber-900">{{ number_format($totalAmount, 2) }} <span class="text-base font-medium text-amber-700">ج.م</span></p>
    </div>
</div>

{{-- جدول المصاريف --}}
<div class="bg-white rounded-2xl shadow-xl shadow-gray-200/50 border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead>
                <tr class="bg-gradient-to-l from-gray-50 to-white">
                    <th scope="col" class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">التاريخ</th>
                    <th scope="col" class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">التصنيف</th>
                    <th scope="col" class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">الوصف</th>
                    <th scope="col" class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">المبلغ</th>
                    <th scope="col" class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider hidden md:table-cell">ملاحظة</th>
                    <th scope="col" class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">الإجراءات</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-100">
                @forelse($expenses as $expense)
                <tr class="hover:bg-amber-50/30 transition-colors duration-150">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="text-sm font-medium text-gray-900">{{ $expense->expense_date->format('Y-m-d') }}</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @php $cats = \App\Models\Expense::categories(); @endphp
                        <span class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-medium bg-amber-100 text-amber-800">
                            {{ $cats[$expense->category] ?? $expense->category }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-sm text-gray-900">{{ $expense->description }}</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="text-sm font-bold text-red-600">{{ number_format($expense->amount, 2) }} ج.م</span>
                    </td>
                    <td class="px-6 py-4 hidden md:table-cell">
                        <span class="text-sm text-gray-500 max-w-xs truncate block">{{ $expense->note ?? '-' }}</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('expenses.edit', $expense) }}" class="p-2 rounded-lg text-slate-600 hover:text-slate-900 hover:bg-slate-100 transition-colors" title="تعديل">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </a>
                            <form action="{{ route('expenses.destroy', $expense) }}" method="POST" class="inline" onsubmit="return confirm('هل أنت متأكد من حذف هذا المصروف؟')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 rounded-lg text-red-500 hover:text-red-700 hover:bg-red-50 transition-colors" title="حذف">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-16 text-center">
                        <div class="flex flex-col items-center justify-center">
                            <div class="w-16 h-16 rounded-2xl bg-amber-100 flex items-center justify-center mb-4">
                                <svg class="h-8 w-8 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2h-2m-4-1V7a2 2 0 012-2h2a2 2 0 012 2v1" />
                                </svg>
                            </div>
                            <h3 class="text-base font-semibold text-gray-900">لا توجد مصاريف</h3>
                            <p class="mt-1 text-sm text-gray-500 mb-5">قم بإضافة مصروف جديد مثل إعلانات أو شحن</p>
                            <a href="{{ route('expenses.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-semibold text-white bg-gradient-to-r from-amber-500 to-orange-600 hover:from-amber-600 hover:to-orange-700 shadow-lg shadow-amber-500/25 transition-all duration-200">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                </svg>
                                إضافة مصروف
                            </a>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($expenses->hasPages())
    <div class="bg-gray-50/50 px-6 py-4 border-t border-gray-100">
        {{ $expenses->links() }}
    </div>
    @endif
</div>
@endsection
