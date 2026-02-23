@extends('layouts.app')

@section('title', 'تسجيل بيع')

@section('content')
<div class="mb-8">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">تسجيل بيع جديد</h1>
            <p class="mt-2 text-sm text-gray-600">تسجيل عملية بيع جديدة</p>
        </div>
        <a href="{{ route('sales.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-slate-500 transition-colors">
            <svg class="ml-2 -mr-1 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            العودة
        </a>
    </div>
</div>

<div class="bg-white shadow rounded-lg">
    <div class="px-4 py-5 sm:p-6">
        <form action="{{ route('sales.store') }}" method="POST" class="space-y-6">
            @csrf
            <div>
                <label for="product_id" class="block text-sm font-medium text-gray-700">المنتج</label>
                <select name="product_id" id="product_id" required
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-slate-500 focus:border-slate-500 sm:text-sm @error('product_id') border-red-300 @enderror">
                    <option value="">اختر منتج</option>
                    @foreach($products as $product)
                    <option value="{{ $product->id }}" 
                            data-selling-price="{{ $product->selling_price }}"
                            data-sizes="{{ json_encode($product->available_sizes ?? []) }}"
                            {{ old('product_id') == $product->id ? 'selected' : '' }}>
                        {{ $product->name }} ({{ $product->sku }}) - المخزون الإجمالي: {{ $product->quantity ?? 0 }}
                    </option>
                    @endforeach
                </select>
                @error('product_id')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- حاوية المقاسات الديناميكية -->
            <div id="size-container" style="display: none;">
                <label for="size" class="block text-sm font-medium text-gray-700">المقاس <span class="text-xs text-gray-500">(اختياري إذا لم يكن المنتج مقسماً)</span></label>
                <select name="size" id="size" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-slate-500 focus:border-slate-500 sm:text-sm">
                    <option value="">اختر المقاس</option>
                </select>
            </div>

            <!-- حاوية الألوان الديناميكية -->
            <div id="color-container" style="display: none;">
                <label for="color" class="block text-sm font-medium text-gray-700">اللون <span class="text-xs text-gray-500">(اختياري)</span></label>
                <select name="color" id="color" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-slate-500 focus:border-slate-500 sm:text-sm">
                    <option value="">اختر اللون</option>
                </select>
                <p id="stock-info" class="mt-1 text-sm font-bold text-violet-600 hidden"></p>
            </div>

            <div>
                <label for="quantity" class="block text-sm font-medium text-gray-700">الكمية</label>
                <input type="number" name="quantity" id="quantity" value="{{ old('quantity') }}" required min="1"
                       class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-slate-500 focus:border-slate-500 sm:text-sm @error('quantity') border-red-300 @enderror">
                @error('quantity')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="selling_price" class="block text-sm font-medium text-gray-700">
                    سعر البيع
                    <span class="text-gray-500 font-normal text-xs">(اختياري - سيتم استخدام السعر الافتراضي للمنتج إذا لم يتم إدخاله)</span>
                </label>
                <input type="number" step="0.01" name="selling_price" id="selling_price" value="{{ old('selling_price') }}" min="0"
                       class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-slate-500 focus:border-slate-500 sm:text-sm @error('selling_price') border-red-300 @enderror"
                       placeholder="اتركه فارغاً لاستخدام السعر الافتراضي">
                <p class="mt-1 text-xs text-gray-500">إذا تركت الحقل فارغاً، سيتم استخدام سعر البيع الافتراضي للمنتج تلقائياً</p>
                @error('selling_price')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="governorate" class="block text-sm font-medium text-gray-700">المحافظة <span class="text-gray-500 font-normal text-xs">(اختياري)</span></label>
                <select name="governorate" id="governorate"
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-slate-500 focus:border-slate-500 sm:text-sm @error('governorate') border-red-300 @enderror">
                    <option value="">اختر المحافظة (اختياري)</option>
                    <option value="القاهرة" {{ old('governorate') == 'القاهرة' ? 'selected' : '' }}>القاهرة</option>
                    <option value="الجيزة" {{ old('governorate') == 'الجيزة' ? 'selected' : '' }}>الجيزة</option>
                    <option value="الإسكندرية" {{ old('governorate') == 'الإسكندرية' ? 'selected' : '' }}>الإسكندرية</option>
                    <option value="الدقهلية" {{ old('governorate') == 'الدقهلية' ? 'selected' : '' }}>الدقهلية</option>
                    <option value="البحيرة" {{ old('governorate') == 'البحيرة' ? 'selected' : '' }}>البحيرة</option>
                    <option value="المنيا" {{ old('governorate') == 'المنيا' ? 'selected' : '' }}>المنيا</option>
                    <option value="القليوبية" {{ old('governorate') == 'القليوبية' ? 'selected' : '' }}>القليوبية</option>
                    <option value="أسيوط" {{ old('governorate') == 'أسيوط' ? 'selected' : '' }}>أسيوط</option>
                    <option value="الغربية" {{ old('governorate') == 'الغربية' ? 'selected' : '' }}>الغربية</option>
                    <option value="سوهاج" {{ old('governorate') == 'سوهاج' ? 'selected' : '' }}>سوهاج</option>
                    <option value="كفر الشيخ" {{ old('governorate') == 'كفر الشيخ' ? 'selected' : '' }}>كفر الشيخ</option>
                    <option value="المنوفية" {{ old('governorate') == 'المنوفية' ? 'selected' : '' }}>المنوفية</option>
                    <option value="الشرقية" {{ old('governorate') == 'الشرقية' ? 'selected' : '' }}>الشرقية</option>
                    <option value="قنا" {{ old('governorate') == 'قنا' ? 'selected' : '' }}>قنا</option>
                    <option value="بني سويف" {{ old('governorate') == 'بني سويف' ? 'selected' : '' }}>بني سويف</option>
                    <option value="الإسماعيلية" {{ old('governorate') == 'الإسماعيلية' ? 'selected' : '' }}>الإسماعيلية</option>
                    <option value="الأقصر" {{ old('governorate') == 'الأقصر' ? 'selected' : '' }}>الأقصر</option>
                    <option value="أسوان" {{ old('governorate') == 'أسوان' ? 'selected' : '' }}>أسوان</option>
                    <option value="البحر الأحمر" {{ old('governorate') == 'البحر الأحمر' ? 'selected' : '' }}>البحر الأحمر</option>
                    <option value="مطروح" {{ old('governorate') == 'مطروح' ? 'selected' : '' }}>مطروح</option>
                    <option value="شمال سيناء" {{ old('governorate') == 'شمال سيناء' ? 'selected' : '' }}>شمال سيناء</option>
                    <option value="جنوب سيناء" {{ old('governorate') == 'جنوب سيناء' ? 'selected' : '' }}>جنوب سيناء</option>
                    <option value="الفيوم" {{ old('governorate') == 'الفيوم' ? 'selected' : '' }}>الفيوم</option>
                    <option value="دمياط" {{ old('governorate') == 'دمياط' ? 'selected' : '' }}>دمياط</option>
                    <option value="بورسعيد" {{ old('governorate') == 'بورسعيد' ? 'selected' : '' }}>بورسعيد</option>
                    <option value="السويس" {{ old('governorate') == 'السويس' ? 'selected' : '' }}>السويس</option>
                </select>
                @error('governorate')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-end space-x-reverse space-x-3">
                <a href="{{ route('sales.index') }}" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-slate-500 transition-colors">
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
document.addEventListener('DOMContentLoaded', function() {
    const productSelect = document.getElementById('product_id');
    const sellingPriceInput = document.getElementById('selling_price');
    const sizeContainer = document.getElementById('size-container');
    const sizeSelect = document.getElementById('size');
    const colorContainer = document.getElementById('color-container');
    const colorSelect = document.getElementById('color');
    const stockInfo = document.getElementById('stock-info');
    let currentSizes = [];

    productSelect.addEventListener('change', function() {
        const selectedOption = productSelect.options[productSelect.selectedIndex];
        if (!selectedOption.value) {
            sizeContainer.style.display = 'none';
            colorContainer.style.display = 'none';
            stockInfo.classList.add('hidden');
            return;
        }

        const sellingPrice = selectedOption.getAttribute('data-selling-price');
        if (sellingPrice && !sellingPriceInput.value) {
            sellingPriceInput.placeholder = 'السعر الافتراضي: ' + parseFloat(sellingPrice).toFixed(2) + ' ج.م';
        }

        const sizesData = selectedOption.getAttribute('data-sizes');
        currentSizes = sizesData ? JSON.parse(sizesData) : [];

        // Reset dropdowns
        sizeSelect.innerHTML = '<option value="">اختر المقاس</option>';
        colorSelect.innerHTML = '<option value="">اختر اللون</option>';
        stockInfo.classList.add('hidden');

        if (currentSizes && currentSizes.length > 0) {
            sizeContainer.style.display = 'block';
            colorContainer.style.display = 'none'; // Will show when size is selected
            currentSizes.forEach(size => {
                const opt = document.createElement('option');
                opt.value = size.size;
                opt.textContent = size.size;
                sizeSelect.appendChild(opt);
            });
        } else {
            sizeContainer.style.display = 'none';
            colorContainer.style.display = 'none';
        }
    });

    sizeSelect.addEventListener('change', function() {
        const selectedSizeName = this.value;
        colorSelect.innerHTML = '<option value="">اختر اللون</option>';
        stockInfo.classList.add('hidden');

        if (!selectedSizeName) {
            colorContainer.style.display = 'none';
            return;
        }

        const selectedSizeData = currentSizes.find(s => s.size === selectedSizeName);
        if (selectedSizeData && selectedSizeData.colors && selectedSizeData.colors.length > 0) {
            colorContainer.style.display = 'block';
            selectedSizeData.colors.forEach(color => {
                const opt = document.createElement('option');
                opt.value = color.color;
                opt.textContent = color.color + ` (المتاح: ${color.stock || 0})`;
                opt.setAttribute('data-stock', color.stock || 0);
                colorSelect.appendChild(opt);
            });
        } else {
            colorContainer.style.display = 'none';
        }
    });

    colorSelect.addEventListener('change', function() {
        const option = this.options[this.selectedIndex];
        if (option.value) {
            stockInfo.textContent = 'المخزون المتوفر لهذا اللون والمقاس: ' + option.getAttribute('data-stock');
            stockInfo.classList.remove('hidden');
        } else {
            stockInfo.classList.add('hidden');
        }
    });
});
</script>
@endsection
