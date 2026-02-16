@extends('layouts.app')

@section('title', 'مرتجع بيع')

@section('content')
<div class="mb-8">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">تسجيل مرتجع بيع</h1>
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
        <form action="{{ route('stock-movements.sales-return.store') }}" method="POST" class="space-y-6">
            @csrf
            <div>
                <label for="sale_id" class="block text-sm font-medium text-gray-700">البيع</label>
                <select name="sale_id" id="sale_id" required
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-slate-500 focus:border-slate-500 sm:text-sm @error('sale_id') border-red-300 @enderror">
                    <option value="">اختر بيع</option>
                    @foreach($sales as $sale)
                    <option value="{{ $sale->id }}" {{ old('sale_id') == $sale->id ? 'selected' : '' }}>
                        {{ $sale->product->name }} - كمية: {{ $sale->quantity }} - تاريخ: {{ $sale->created_at->format('Y-m-d') }}
                    </option>
                    @endforeach
                </select>
                @error('sale_id')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="quantity" class="block text-sm font-medium text-gray-700">الكمية المرتجعة</label>
                <input type="number" name="quantity" id="quantity" value="{{ old('quantity') }}" required min="1"
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
    </div>
</div>
@endsection
