@extends('layouts.app')

@section('title', 'مخزون منخفض')

@section('content')
<div class="mb-6 sm:mb-8">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="min-w-0">
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">منتجات مخزون منخفض</h1>
            <p class="mt-2 text-sm text-gray-600">المنتجات التي يقل مخزونها عن 10 وحدات</p>
        </div>
        <a href="{{ route('dashboard') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-slate-500 transition-colors">
            <svg class="ml-2 -mr-1 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            العودة
        </a>
    </div>
</div>

@if($products->count() > 0)
<div class="bg-white shadow rounded-lg overflow-hidden">
    <div class="px-4 py-5 sm:p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-medium text-gray-900">المنتجات</h3>
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                {{ $products->count() }} منتج
            </span>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">اسم المنتج</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">SKU</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">المخزون الحالي</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">سعر البيع</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($products as $product)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $product->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $product->sku }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                {{ $product->total_stock }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ number_format($product->selling_price, 2) }} ج.م</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="{{ route('products.show', $product) }}" class="text-slate-600 hover:text-slate-900 transition-colors">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @if($products->hasPages())
    <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
        {{ $products->links() }}
    </div>
    @endif
</div>
@else
<div class="bg-white shadow rounded-lg">
    <div class="px-4 py-12 text-center">
        <svg class="mx-auto h-12 w-12 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <h3 class="mt-2 text-sm font-medium text-gray-900">لا توجد منتجات بمخزون منخفض</h3>
        <p class="mt-1 text-sm text-gray-500">جميع المنتجات لديها مخزون كافٍ</p>
    </div>
</div>
@endif
@endsection
