@extends('layouts.app')

@section('title', 'إضافة طلب جديد')

@section('content')
<div class="mb-8">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">إضافة طلب جديد</h1>
            <p class="mt-2 text-sm text-gray-600">أضف طلباً جديداً يحتاج التأكيد</p>
        </div>
        <a href="{{ route('orders.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-slate-500 transition-colors">
            <svg class="ml-2 -mr-1 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            العودة
        </a>
    </div>
</div>

<div class="bg-white shadow rounded-lg">
    <div class="px-4 py-5 sm:p-6">
        <form action="{{ route('orders.store') }}" method="POST" class="space-y-6" id="order-form">
            @csrf
            
            <!-- Customer Information -->
            <div class="border-b border-gray-200 pb-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">معلومات العميل</h3>
                
                <div class="grid grid-cols-1 gap-6">
                    <div>
                        <label for="customer_name" class="block text-sm font-medium text-gray-700">اسم العميل</label>
                        <input type="text" name="customer_name" id="customer_name" value="{{ old('customer_name') }}" required
                               class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-slate-500 focus:border-slate-500 sm:text-sm @error('customer_name') border-red-300 @enderror">
                        @error('customer_name')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="customer_address" class="block text-sm font-medium text-gray-700">عنوان العميل</label>
                        <textarea name="customer_address" id="customer_address" rows="3" required
                                  class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-slate-500 focus:border-slate-500 sm:text-sm @error('customer_address') border-red-300 @enderror">{{ old('customer_address') }}</textarea>
                        @error('customer_address')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">أرقام الهاتف</label>
                        <div id="phone-numbers-container" class="space-y-2">
                            <div class="flex gap-2">
                                <input type="text" name="customer_numbers[]" placeholder="رقم الهاتف" required
                                       class="flex-1 border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-slate-500 focus:border-slate-500 sm:text-sm">
                                <button type="button" onclick="removePhoneNumber(this)" class="px-3 py-2 text-red-600 hover:text-red-800">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <button type="button" onclick="addPhoneNumber()" class="mt-2 text-sm text-slate-600 hover:text-slate-800">
                            + إضافة رقم آخر
                        </button>
                        @error('customer_numbers')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Order Items -->
            <div class="border-b border-gray-200 pb-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900">المنتجات</h3>
                    <button type="button" onclick="addOrderItem()" class="text-sm text-slate-600 hover:text-slate-800">
                        + إضافة منتج
                    </button>
                </div>
                
                <div id="order-items-container" class="space-y-4">
                    <!-- Items will be added here dynamically -->
                </div>
            </div>

            <!-- Order Summary -->
            <div class="border-b border-gray-200 pb-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">ملخص الطلب</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="delivery_fees" class="block text-sm font-medium text-gray-700">رسوم التوصيل</label>
                        <input type="number" step="0.01" name="delivery_fees" id="delivery_fees" value="{{ old('delivery_fees', 0) }}" required min="0"
                               class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-slate-500 focus:border-slate-500 sm:text-sm @error('delivery_fees') border-red-300 @enderror">
                        @error('delivery_fees')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="total_amount" class="block text-sm font-medium text-gray-700">المبلغ الإجمالي</label>
                        <input type="number" step="0.01" name="total_amount" id="total_amount" value="{{ old('total_amount', 0) }}" required min="0" readonly
                               class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 bg-gray-50 focus:outline-none focus:ring-slate-500 focus:border-slate-500 sm:text-sm @error('total_amount') border-red-300 @enderror">
                        <p class="mt-1 text-xs text-gray-500">سيتم حسابه تلقائياً</p>
                        @error('total_amount')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700">الحالة</label>
                        <select name="status" id="status" required
                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-slate-500 focus:border-slate-500 sm:text-sm @error('status') border-red-300 @enderror">
                            <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>قيد الانتظار</option>
                            <option value="accepted" {{ old('status') == 'accepted' ? 'selected' : '' }}>تم القبول</option>
                            <option value="delivery_fees_paid" {{ old('status') == 'delivery_fees_paid' ? 'selected' : '' }}>تم دفع رسوم التوصيل</option>
                            <option value="shipped" {{ old('status') == 'shipped' ? 'selected' : '' }}>تم الشحن</option>
                            <option value="cancelled" {{ old('status') == 'cancelled' ? 'selected' : '' }}>مرفوض</option>
                        </select>
                        @error('status')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="payment_method" class="block text-sm font-medium text-gray-700">طريقة الدفع</label>
                        <select name="payment_method" id="payment_method"
                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-slate-500 focus:border-slate-500 sm:text-sm @error('payment_method') border-red-300 @enderror">
                            <option value="">اختر طريقة الدفع</option>
                            <option value="InstaPay" {{ old('payment_method') == 'InstaPay' ? 'selected' : '' }}>InstaPay</option>
                            <option value="wallet" {{ old('payment_method') == 'wallet' ? 'selected' : '' }}>محفظة</option>
                        </select>
                        @error('payment_method')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end space-x-reverse space-x-3">
                <a href="{{ route('orders.index') }}" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-slate-500 transition-colors">
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
    const products = @json($products);
    let itemIndex = 0;

    function addOrderItem() {
        const container = document.getElementById('order-items-container');
        const itemDiv = document.createElement('div');
        itemDiv.className = 'border border-gray-200 rounded-lg p-4';
        itemDiv.innerHTML = `
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">المنتج</label>
                    <select name="items[${itemIndex}][product_id]" class="product-select block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-slate-500 focus:border-slate-500 sm:text-sm" onchange="updateProductInfo(this, ${itemIndex})" required>
                        <option value="">اختر منتج</option>
                        ${products.map(p => `<option value="${p.id}" data-name="${p.name}" data-price="${p.discounted_price || p.selling_price}">${p.name}</option>`).join('')}
                    </select>
                    <input type="hidden" name="items[${itemIndex}][product_name]" class="product-name">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">الكمية</label>
                    <input type="number" name="items[${itemIndex}][quantity]" min="1" value="1" required onchange="calculateTotal()"
                           class="item-quantity block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-slate-500 focus:border-slate-500 sm:text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">السعر</label>
                    <input type="number" step="0.01" name="items[${itemIndex}][price]" min="0" required onchange="calculateTotal()"
                           class="item-price block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-slate-500 focus:border-slate-500 sm:text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">الحجم</label>
                    <input type="text" name="items[${itemIndex}][size]" 
                           class="block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-slate-500 focus:border-slate-500 sm:text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">اللون</label>
                    <input type="text" name="items[${itemIndex}][color]" 
                           class="block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-slate-500 focus:border-slate-500 sm:text-sm">
                </div>
                <div class="flex items-end">
                    <button type="button" onclick="removeOrderItem(this)" class="w-full px-4 py-2 bg-red-50 text-red-600 rounded-md hover:bg-red-100 transition-colors">
                        حذف
                    </button>
                </div>
            </div>
        `;
        container.appendChild(itemDiv);
        itemIndex++;
    }

    function removeOrderItem(button) {
        button.closest('.border').remove();
        calculateTotal();
    }

    function updateProductInfo(select, index) {
        const option = select.options[select.selectedIndex];
        const productName = option.getAttribute('data-name');
        const productPrice = option.getAttribute('data-price');
        
        const itemDiv = select.closest('.border');
        itemDiv.querySelector('.product-name').value = productName;
        itemDiv.querySelector('.item-price').value = productPrice || '';
        calculateTotal();
    }

    function addPhoneNumber() {
        const container = document.getElementById('phone-numbers-container');
        const div = document.createElement('div');
        div.className = 'flex gap-2';
        div.innerHTML = `
            <input type="text" name="customer_numbers[]" placeholder="رقم الهاتف" required
                   class="flex-1 border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-slate-500 focus:border-slate-500 sm:text-sm">
            <button type="button" onclick="removePhoneNumber(this)" class="px-3 py-2 text-red-600 hover:text-red-800">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        `;
        container.appendChild(div);
    }

    function removePhoneNumber(button) {
        if (document.getElementById('phone-numbers-container').children.length > 1) {
            button.closest('.flex').remove();
        }
    }

    function calculateTotal() {
        let total = 0;
        document.querySelectorAll('.item-price').forEach((priceInput, index) => {
            const quantityInput = priceInput.closest('.border').querySelector('.item-quantity');
            const price = parseFloat(priceInput.value) || 0;
            const quantity = parseInt(quantityInput.value) || 0;
            total += price * quantity;
        });
        
        const deliveryFees = parseFloat(document.getElementById('delivery_fees').value) || 0;
        document.getElementById('total_amount').value = (total + deliveryFees).toFixed(2);
    }

    // Initialize with one item
    addOrderItem();

    // Calculate total when delivery fees change
    document.getElementById('delivery_fees').addEventListener('input', calculateTotal);
</script>
@endsection

