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
        <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
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
                <label for="selling_price" class="block text-sm font-medium text-gray-700">السعر الأساسي</label>
                <input type="number" step="0.01" name="selling_price" id="selling_price" value="{{ old('selling_price') }}" required min="0"
                       class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-slate-500 focus:border-slate-500 sm:text-sm @error('selling_price') border-red-300 @enderror">
                <p class="mt-1 text-xs text-gray-500">السعر الأصلي للمنتج قبل التخفيض</p>
                @error('selling_price')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="discounted_price" class="block text-sm font-medium text-gray-700">سعر بعد التخفيض</label>
                <input type="number" step="0.01" name="discounted_price" id="discounted_price" value="{{ old('discounted_price') }}" min="0"
                       class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-slate-500 focus:border-slate-500 sm:text-sm @error('discounted_price') border-red-300 @enderror">
                <p class="mt-1 text-xs text-gray-500">السعر بعد تطبيق التخفيض (اختياري)</p>
                @error('discounted_price')
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
                <label for="available_sizes" class="block text-sm font-medium text-gray-700">الأحجام المتاحة</label>
                <input type="text" name="available_sizes_input" id="available_sizes" value="{{ old('available_sizes_input', is_array(old('available_sizes')) ? implode(',', old('available_sizes')) : '') }}"
                       placeholder="مثال: S, M, L, XL (مفصولة بفواصل)"
                       class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-slate-500 focus:border-slate-500 sm:text-sm @error('available_sizes') border-red-300 @enderror">
                <p class="mt-1 text-xs text-gray-500">أدخل الأحجام مفصولة بفواصل</p>
                @error('available_sizes')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">الألوان المتاحة</label>
                <div class="flex gap-2 mb-2">
                    <input type="text" id="color-input" placeholder="أدخل لون جديد"
                           class="flex-1 border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-slate-500 focus:border-slate-500 sm:text-sm">
                    <button type="button" id="add-color-btn" class="px-4 py-2 bg-slate-700 text-white rounded-md hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-slate-500 transition-colors">
                        إضافة
                    </button>
                </div>
                <div id="colors-container" class="flex flex-wrap gap-2 mt-2 min-h-[40px] p-2 border border-gray-200 rounded-md">
                    @if(is_array(old('available_colors')))
                        @foreach(old('available_colors') as $color)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                {{ $color }}
                                <input type="hidden" name="available_colors[]" value="{{ $color }}">
                                <button type="button" class="mr-2 text-blue-600 hover:text-blue-800" onclick="removeColor(this)">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </span>
                        @endforeach
                    @endif
                </div>
                <p class="mt-1 text-xs text-gray-500">أدخل كل لون واضغط إضافة</p>
                @error('available_colors')
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

            <div>
                <label for="sample" class="block text-sm font-medium text-gray-700">عينة (صورة)</label>
                <input type="file" name="sample" id="sample" accept="image/*"
                       class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-slate-500 focus:border-slate-500 sm:text-sm @error('sample') border-red-300 @enderror">
                <p class="mt-1 text-xs text-gray-500">الصيغ المدعومة: JPEG, PNG, JPG, GIF, WebP (حد أقصى 2MB)</p>
                <div id="sample-preview" class="mt-2 hidden">
                    <img id="preview-image" src="" alt="معاينة الصورة" class="max-w-xs rounded-md border border-gray-300">
                </div>
                @error('sample')
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

<script>
    // Handle color addition
    document.getElementById('add-color-btn').addEventListener('click', function() {
        const input = document.getElementById('color-input');
        const color = input.value.trim();
        
        if (color) {
            addColorBadge(color);
            input.value = '';
        }
    });
    
    // Allow Enter key to add color
    document.getElementById('color-input').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            document.getElementById('add-color-btn').click();
        }
    });
    
    function addColorBadge(color) {
        const container = document.getElementById('colors-container');
        const badge = document.createElement('span');
        badge.className = 'inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800';
        badge.innerHTML = `
            ${color}
            <input type="hidden" name="available_colors[]" value="${color}">
            <button type="button" class="mr-2 text-blue-600 hover:text-blue-800" onclick="removeColor(this)">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        `;
        container.appendChild(badge);
    }
    
    function removeColor(button) {
        button.closest('span').remove();
    }
    
    // Handle image preview
    document.getElementById('sample').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.getElementById('sample-preview');
                const img = document.getElementById('preview-image');
                img.src = e.target.result;
                preview.classList.remove('hidden');
            };
            reader.readAsDataURL(file);
        } else {
            document.getElementById('sample-preview').classList.add('hidden');
        }
    });
</script>
@endsection
