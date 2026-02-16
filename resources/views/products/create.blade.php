@extends('layouts.app')

@section('title', 'إضافة منتج')

@section('content')
<div class="mb-8">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">إضافة منتج جديد</h1>
            <p class="mt-2 text-sm text-gray-600">أضف منتجاً جديداً إلى النظام</p>
        </div>
        <a href="{{ route('products.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-slate-500 transition-colors">
            <svg class="ml-2 -mr-1 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            العودة
        </a>
    </div>
</div>

<div class="bg-white shadow rounded-lg">
    <div class="px-4 py-5 sm:p-6">
        <form action="{{ route('products.store') }}" method="POST" class="space-y-6">
            @csrf
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700">اسم المنتج</label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" required
                       class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-slate-500 focus:border-slate-500 sm:text-sm @error('name') border-red-300 @enderror">
                @error('name')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="sku" class="block text-sm font-medium text-gray-700">SKU</label>
                <input type="text" name="sku" id="sku" value="{{ old('sku') }}" required
                       class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-slate-500 focus:border-slate-500 sm:text-sm @error('sku') border-red-300 @enderror">
                @error('sku')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="selling_price" class="block text-sm font-medium text-gray-700">سعر البيع</label>
                <input type="number" step="0.01" name="selling_price" id="selling_price" value="{{ old('selling_price') }}" required min="0"
                       class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-slate-500 focus:border-slate-500 sm:text-sm @error('selling_price') border-red-300 @enderror">
                @error('selling_price')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="quantity" class="block text-sm font-medium text-gray-700">الكمية المتاحة</label>
                <input type="number" name="quantity" id="quantity" value="{{ old('quantity', 0) }}" required min="0"
                       class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-slate-500 focus:border-slate-500 sm:text-sm @error('quantity') border-red-300 @enderror">
                @error('quantity')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="description" class="block text-sm font-medium text-gray-700">الوصف</label>
                <textarea name="description" id="description" rows="4"
                          class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-slate-500 focus:border-slate-500 sm:text-sm @error('description') border-red-300 @enderror">{{ old('description') }}</textarea>
                @error('description')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-end space-x-reverse space-x-3">
                <a href="{{ route('products.index') }}" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-slate-500 transition-colors">
                    إلغاء
                </a>
                <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-slate-700 hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-slate-500 transition-colors">
                    حفظ
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
