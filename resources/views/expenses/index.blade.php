@extends('layouts.app')

@section('title', 'المصاريف')

@section('content')
<div class="mb-6 sm:mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div class="min-w-0">
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">المصاريف</h1>
        <p class="mt-2 text-sm text-gray-600">المصاريف التشغيلية التي تُخصم من الأرباح (إعلانات، شحن، إيجار، إلخ)</p>
    </div>
    <a href="{{ route('expenses.create') }}" class="inline-flex items-center justify-center px-4 py-2.5 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-amber-600 hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500 transition-colors flex-shrink-0">
        <svg class="ml-2 -mr-1 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
        </svg>
        إضافة مصروف
    </a>
</div>

<form method="GET" action="{{ route('expenses.index') }}" class="mb-6 bg-white p-4 rounded-lg shadow">
    <div class="flex flex-wrap gap-4 items-end">
        <div>
            <label for="category" class="block text-sm font-medium text-gray-700 mb-1">التصنيف</label>
            <select name="category" id="category" class="rounded-md border-gray-300 shadow-sm focus:border-amber-500 focus:ring-amber-500">
                <option value="">الكل</option>
                @foreach($categories as $key => $label)
                <option value="{{ $key }}" {{ request('category') == $key ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="from_date" class="block text-sm font-medium text-gray-700 mb-1">من تاريخ</label>
            <input type="date" name="from_date" id="from_date" value="{{ request('from_date') }}" class="rounded-md border-gray-300 shadow-sm focus:border-amber-500 focus:ring-amber-500">
        </div>
        <div>
            <label for="to_date" class="block text-sm font-medium text-gray-700 mb-1">إلى تاريخ</label>
            <input type="date" name="to_date" id="to_date" value="{{ request('to_date') }}" class="rounded-md border-gray-300 shadow-sm focus:border-amber-500 focus:ring-amber-500">
        </div>
        <button type="submit" class="px-4 py-2 bg-slate-600 text-white rounded-md hover:bg-slate-700 text-sm font-medium">تصفية</button>
    </div>
</form>

<div class="mb-6 bg-amber-50 border border-amber-200 rounded-lg p-4">
    <p class="text-sm text-amber-800">
        <span class="font-semibold">إجمالي المصاريف (حسب التصفية):</span>
        <span class="font-bold text-lg">{{ number_format($totalAmount, 2) }} ج.م</span>
    </p>
</div>

<div class="bg-white shadow rounded-lg overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">التاريخ</th>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">التصنيف</th>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الوصف</th>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">المبلغ</th>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">ملاحظة</th>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الإجراءات</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($expenses as $expense)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $expense->expense_date->format('Y-m-d') }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @php $cats = \App\Models\Expense::categories(); @endphp
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">
                            {{ $cats[$expense->category] ?? $expense->category }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-900">{{ $expense->description }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-red-600">{{ number_format($expense->amount, 2) }} ج.م</td>
                    <td class="px-6 py-4 text-sm text-gray-500 max-w-xs truncate">{{ $expense->note ?? '-' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <div class="flex items-center space-x-reverse space-x-2">
                            <a href="{{ route('expenses.edit', $expense) }}" class="text-slate-600 hover:text-slate-900 transition-colors">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </a>
                            <form action="{{ route('expenses.destroy', $expense) }}" method="POST" class="inline" onsubmit="return confirm('هل أنت متأكد من حذف هذا المصروف؟')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900 transition-colors">
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
                    <td colspan="6" class="px-6 py-12 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2h-2m-4-1V7a2 2 0 012-2h2a2 2 0 012 2v1" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">لا توجد مصاريف</h3>
                        <p class="mt-1 text-sm text-gray-500">قم بإضافة مصروف جديد مثل إعلانات أو شحن</p>
                        <a href="{{ route('expenses.create') }}" class="mt-4 inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-amber-600 hover:bg-amber-700">إضافة مصروف</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($expenses->hasPages())
    <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
        {{ $expenses->links() }}
    </div>
    @endif
</div>
@endsection
