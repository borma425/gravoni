@extends('layouts.app')

@section('title', 'لوحة التحكم')

@section('content')
<div class="mb-6 sm:mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div class="min-w-0">
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 truncate">لوحة التحكم</h1>
        <p class="mt-1 sm:mt-2 text-sm text-gray-600">نظرة عامة على نظام إدارة المخزون</p>
    </div>
    <a href="{{ route('dashboard.export') }}" class="inline-flex items-center justify-center px-4 py-2.5 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors flex-shrink-0">
        <svg class="ml-2 -mr-1 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
        </svg>
        تنزيل الإحصائيات (Excel)
    </a>
</div>

<!-- Stats Cards -->
<div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 mb-8">
    <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="bg-slate-600 rounded-md p-3">
                        <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                    </div>
                </div>
                <div class="mr-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">إجمالي المنتجات</dt>
                        <dd class="text-lg font-semibold text-gray-900">{{ $totalProducts }}</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="bg-green-500 rounded-md p-3">
                        <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                    </div>
                </div>
                <div class="mr-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">إجمالي المبيعات</dt>
                        <dd class="text-lg font-semibold text-gray-900">{{ $totalSales }}</dd>
                        <dd class="text-xs text-gray-400 mt-1">أوردرات مقبولة + مبيعات يدوية</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="bg-blue-500 rounded-md p-3">
                        <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                <div class="mr-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">إجمالي الإيرادات</dt>
                        <dd class="text-lg font-semibold text-gray-900">{{ number_format($totalRevenue, 2) }} ج.م</dd>
                        <dd class="text-xs text-gray-400 mt-1">إجمالي قيمة المبيعات (أوردرات + يدوية)</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="bg-yellow-500 rounded-md p-3">
                        <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                <div class="mr-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">إجمالي الأرباح</dt>
                        <dd class="text-lg font-semibold text-gray-900">{{ number_format($totalProfit, 2) }} ج.م</dd>
                        <dd class="text-xs text-gray-400 mt-1">الإيرادات - تكلفة البضاعة (أوردرات + يدوية)</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="bg-red-500 rounded-md p-3">
                        <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                </div>
                <div class="mr-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">إجمالي التلف</dt>
                        <dd class="text-lg font-semibold text-gray-900">{{ abs($totalDamaged) }} وحدة</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="bg-red-600 rounded-md p-3">
                        <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                <div class="mr-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">إجمالي الخسائر</dt>
                        <dd class="text-lg font-semibold text-red-600">{{ number_format($totalLosses, 2) }} ج.م</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Low Stock Alert -->
@if($lowStockProducts->count() > 0)
<div class="bg-white shadow rounded-lg">
    <div class="px-4 py-5 sm:p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-medium text-gray-900">منتجات مخزون منخفض</h3>
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                {{ $lowStockProducts->count() }} منتج
            </span>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-4 sm:px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">اسم المنتج</th>
                        <th scope="col" class="px-4 sm:px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">SKU</th>
                        <th scope="col" class="px-4 sm:px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">المخزون الحالي</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($lowStockProducts as $product)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 sm:px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $product->name }}</td>
                        <td class="px-4 sm:px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $product->sku }}</td>
                        <td class="px-4 sm:px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                {{ $product->total_stock }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@else
<div class="bg-white shadow rounded-lg">
    <div class="px-4 py-5 sm:p-6 text-center">
        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <h3 class="mt-2 text-sm font-medium text-gray-900">لا توجد منتجات بمخزون منخفض</h3>
        <p class="mt-1 text-sm text-gray-500">جميع المنتجات لديها مخزون كافٍ</p>
    </div>
</div>
@endif

<!-- Recent Damages -->
@if($recentDamages->count() > 0)
<div class="bg-white shadow rounded-lg mt-6">
    <div class="px-4 py-5 sm:p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-medium text-gray-900">التلفيات الأخيرة</h3>
            <a href="{{ route('stock-movements.damage.index') }}" class="text-sm text-slate-600 hover:text-slate-900 transition-colors">
                عرض الكل
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-4 sm:px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">المنتج</th>
                        <th scope="col" class="px-4 sm:px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الكمية التالفة</th>
                        <th scope="col" class="px-4 sm:px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">ملاحظة</th>
                        <th scope="col" class="px-4 sm:px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">التاريخ</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($recentDamages as $damage)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 sm:px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $damage->product->name }}</td>
                        <td class="px-4 sm:px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                {{ abs($damage->quantity) }}
                            </span>
                        </td>
                        <td class="px-4 sm:px-6 py-4 text-sm text-gray-500">{{ $damage->note ?? '-' }}</td>
                        <td class="px-4 sm:px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $damage->created_at->format('Y-m-d H:i') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif

<!-- Recent Losses -->
@if($recentLosses->count() > 0)
<div class="bg-white shadow rounded-lg mt-6">
    <div class="px-4 py-5 sm:p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-medium text-gray-900">الخسائر الأخيرة</h3>
            <a href="{{ route('losses.index') }}" class="text-sm text-slate-600 hover:text-slate-900 transition-colors">
                عرض الكل
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-4 sm:px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">المنتج</th>
                        <th scope="col" class="px-4 sm:px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الكمية</th>
                        <th scope="col" class="px-4 sm:px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">إجمالي الخسارة</th>
                        <th scope="col" class="px-4 sm:px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">التاريخ</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($recentLosses as $loss)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 sm:px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $loss->product->name }}</td>
                        <td class="px-4 sm:px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                {{ $loss->quantity }}
                            </span>
                        </td>
                        <td class="px-4 sm:px-6 py-4 whitespace-nowrap text-sm font-semibold text-red-600">{{ number_format($loss->total_loss, 2) }} ج.م</td>
                        <td class="px-4 sm:px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $loss->created_at->format('Y-m-d H:i') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif
@endsection
