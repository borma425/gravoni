@extends('layouts.app')

@section('title', 'تفاصيل المحافظة')

@section('content')
<div class="mb-8">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">تفاصيل المحافظة</h1>
            <p class="mt-2 text-sm text-gray-600">معلومات المحافظة ورسوم الشحن</p>
        </div>
        <div class="flex items-center space-x-reverse space-x-3">
            <a href="{{ route('governorates.edit', $governorate) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-slate-500 transition-colors">
                <svg class="ml-2 -mr-1 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                تعديل
            </a>
            <a href="{{ route('governorates.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-slate-500 transition-colors">
                <svg class="ml-2 -mr-1 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                العودة
            </a>
        </div>
    </div>
</div>

<div class="bg-white shadow rounded-lg">
    <div class="px-4 py-5 sm:p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">معلومات المحافظة</h3>
        <dl class="space-y-4">
            <div>
                <dt class="text-sm font-medium text-gray-500">اسم المحافظة</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $governorate->name }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">رسوم الشحن</dt>
                <dd class="mt-1 text-sm text-gray-900 font-medium">{{ number_format($governorate->shipping_fee, 2) }} ج.م</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">تاريخ الإنشاء</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $governorate->created_at->format('Y-m-d H:i') }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">آخر تحديث</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $governorate->updated_at->format('Y-m-d H:i') }}</dd>
            </div>
        </dl>
    </div>
</div>
@endsection

