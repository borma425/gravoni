@extends('layouts.app')

@section('title', 'تعديل منتج')

@section('content')
<div class="mb-8">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">تعديل منتج</h1>
            <p class="mt-2 text-sm text-gray-600">تعديل معلومات المنتج</p>
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
        <form action="{{ route('products.update', $product) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700">اسم المنتج</label>
                <input type="text" name="name" id="name" value="{{ old('name', $product->name) }}" required
                       class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-slate-500 focus:border-slate-500 sm:text-sm @error('name') border-red-300 @enderror">
                @error('name')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="sku" class="block text-sm font-medium text-gray-700">SKU</label>
                <input type="text" name="sku" id="sku" value="{{ old('sku', $product->sku) }}" required
                       class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-slate-500 focus:border-slate-500 sm:text-sm @error('sku') border-red-300 @enderror">
                @error('sku')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="selling_price" class="block text-sm font-medium text-gray-700">السعر الأساسي</label>
                <input type="number" step="0.01" name="selling_price" id="selling_price" value="{{ old('selling_price', $product->selling_price) }}" required min="0"
                       class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-slate-500 focus:border-slate-500 sm:text-sm @error('selling_price') border-red-300 @enderror">
                <p class="mt-1 text-xs text-gray-500">السعر الأصلي للمنتج قبل التخفيض</p>
                @error('selling_price')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="discounted_price" class="block text-sm font-medium text-gray-700">سعر بعد التخفيض</label>
                <input type="number" step="0.01" name="discounted_price" id="discounted_price" value="{{ old('discounted_price', $product->discounted_price) }}" min="0"
                       class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-slate-500 focus:border-slate-500 sm:text-sm @error('discounted_price') border-red-300 @enderror">
                <p class="mt-1 text-xs text-gray-500">السعر بعد تطبيق التخفيض (اختياري)</p>
                @error('discounted_price')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="quantity" class="block text-sm font-medium text-gray-700">الكمية المتاحة</label>
                <input type="number" name="quantity" id="quantity" value="{{ old('quantity', $product->quantity) }}" required min="0"
                       class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-slate-500 focus:border-slate-500 sm:text-sm @error('quantity') border-red-300 @enderror">
                @error('quantity')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">الأحجام المتاحة ومخطط المقاسات</label>
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 mb-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 mb-3">
                        <div>
                            <label class="block text-xs text-gray-500 mb-1">المقاس (مثال: M)</label>
                            <input type="text" id="size-name" class="block w-full border border-gray-300 rounded-md shadow-sm py-1.5 px-3 focus:ring-slate-500 focus:border-slate-500 sm:text-sm">
                        </div>
                        <div>
                            <label class="block text-xs text-gray-500 mb-1">عرض الصدر (سم)</label>
                            <input type="number" id="size-chest" min="0" class="block w-full border border-gray-300 rounded-md shadow-sm py-1.5 px-3 focus:ring-slate-500 focus:border-slate-500 sm:text-sm">
                        </div>
                        <div class="sm:col-span-2 md:col-span-1">
                            <label class="block text-xs text-gray-500 mb-1">الوزن (كجم)</label>
                            <div class="flex items-center gap-2">
                                <input type="number" id="size-w-min" placeholder="أدنى" min="0" class="block w-full border border-gray-300 rounded-md shadow-sm py-1.5 px-2 focus:ring-slate-500 focus:border-slate-500 sm:text-sm">
                                <span class="text-gray-400">-</span>
                                <input type="number" id="size-w-max" placeholder="أقصى" min="0" class="block w-full border border-gray-300 rounded-md shadow-sm py-1.5 px-2 focus:ring-slate-500 focus:border-slate-500 sm:text-sm">
                            </div>
                        </div>
                        <div class="sm:col-span-2 md:col-span-1 border-t sm:border-t-0 pt-3 sm:pt-0">
                            <label class="block text-xs text-gray-500 mb-1">الطول (سم)</label>
                            <div class="flex items-center gap-2">
                                <input type="number" id="size-h-min" placeholder="أدنى" min="0" class="block w-full border border-gray-300 rounded-md shadow-sm py-1.5 px-2 focus:ring-slate-500 focus:border-slate-500 sm:text-sm">
                                <span class="text-gray-400">-</span>
                                <input type="number" id="size-h-max" placeholder="أقصى" min="0" class="block w-full border border-gray-300 rounded-md shadow-sm py-1.5 px-2 focus:ring-slate-500 focus:border-slate-500 sm:text-sm">
                            </div>
                        </div>
                    </div>
                    <div class="flex justify-end">
                        <button type="button" id="add-size-btn" class="px-4 py-2 bg-slate-700 text-white rounded-md hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-slate-500 transition-colors text-sm font-medium">
                            إضافة المقاس
                        </button>
                    </div>
                </div>

                <div id="sizes-container" class="flex flex-col gap-3 min-h-[44px]">
                    @php
                        $sizes = old('available_sizes', $product->available_sizes ?? []);
                        if (is_string($sizes)) $sizes = json_decode($sizes, true) ?? [];
                    @endphp
                    @foreach($sizes as $index => $sizeObj)
                        @if(is_array($sizeObj) && isset($sizeObj['size']))
                            <div class="relative bg-white border border-emerald-200 shadow-sm rounded-lg p-3 flex flex-wrap md:flex-nowrap items-center gap-4">
                                <div class="flex-shrink-0 w-12 h-12 bg-emerald-100 text-emerald-800 rounded-full flex items-center justify-center font-bold text-lg">
                                    {{ $sizeObj['size'] }}
                                </div>
                                <div class="flex-1 grid grid-cols-3 gap-2 text-sm text-gray-600">
                                    <div><span class="font-medium text-gray-900">الصدر:</span> {{ $sizeObj['chest_width_cm'] ?? '-' }} سم</div>
                                    <div><span class="font-medium text-gray-900">الوزن:</span> {{ $sizeObj['weight_kg']['min'] ?? '-' }} - {{ $sizeObj['weight_kg']['max'] ?? '-' }} كجم</div>
                                    <div><span class="font-medium text-gray-900">الطول:</span> {{ $sizeObj['height_cm']['min'] ?? '-' }} - {{ $sizeObj['height_cm']['max'] ?? '-' }} سم</div>
                                </div>
                                <input type="hidden" name="available_sizes[{{$index}}][size]" value="{{ $sizeObj['size'] }}">
                                <input type="hidden" name="available_sizes[{{$index}}][chest_width_cm]" value="{{ $sizeObj['chest_width_cm'] ?? '' }}">
                                <input type="hidden" name="available_sizes[{{$index}}][weight_kg][min]" value="{{ $sizeObj['weight_kg']['min'] ?? '' }}">
                                <input type="hidden" name="available_sizes[{{$index}}][weight_kg][max]" value="{{ $sizeObj['weight_kg']['max'] ?? '' }}">
                                <input type="hidden" name="available_sizes[{{$index}}][height_cm][min]" value="{{ $sizeObj['height_cm']['min'] ?? '' }}">
                                <input type="hidden" name="available_sizes[{{$index}}][height_cm][max]" value="{{ $sizeObj['height_cm']['max'] ?? '' }}">
                                <button type="button" class="absolute top-2 right-2 text-gray-400 hover:text-red-500 transition-colors" onclick="removeSize(this)" title="حذف المقاس">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                </button>
                            </div>
                        @elseif(is_string($sizeObj))
                            <!-- Fallback for old simple string sizes -->
                            <div class="relative bg-white border border-gray-200 shadow-sm rounded-lg p-3 flex flex-wrap md:flex-nowrap items-center gap-4">
                                <div class="flex-shrink-0 bg-gray-100 text-gray-800 px-3 py-1 rounded font-bold">{{ $sizeObj }}</div>
                                <div class="flex-1 text-sm text-gray-500">بيانات غير مكتملة</div>
                                <input type="hidden" name="available_sizes[{{$index}}][size]" value="{{ $sizeObj }}">
                                <button type="button" class="absolute top-2 right-2 text-gray-400 hover:text-red-500 transition-colors" onclick="removeSize(this)">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                </button>
                            </div>
                        @endif
                    @endforeach
                </div>
                @error('available_sizes')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
                @error('available_sizes.*')
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
                <div id="colors-container" class="flex flex-wrap gap-2 min-h-[44px] p-3 border border-gray-200 rounded-lg bg-gray-50/50">
                    @php
                        $colors = old('available_colors', $product->available_colors ?? []);
                        if (is_string($colors)) $colors = json_decode($colors, true) ?? [];
                    @endphp
                    @foreach($colors as $color)
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm font-medium bg-violet-100 text-violet-800 border border-violet-200 shadow-sm">
                            {{ $color }}
                            <input type="hidden" name="available_colors[]" value="{{ $color }}">
                            <button type="button" class="p-0.5 rounded hover:bg-violet-200/80" onclick="removeColor(this)"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
                        </span>
                    @endforeach
                </div>
                <p class="mt-1 text-xs text-gray-500">أدخل كل لون واضغط إضافة</p>
                @error('available_colors')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="description" class="block text-sm font-medium text-gray-700">الوصف</label>
                <textarea name="description" id="description" rows="4"
                          class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-slate-500 focus:border-slate-500 sm:text-sm @error('description') border-red-300 @enderror">{{ old('description', $product->description) }}</textarea>
                @error('description')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">صور العينة</label>
                @php $existingSamples = $product->samples ?? []; @endphp
                <div id="samples-remove-inputs" class="hidden"></div>
                @if(count($existingSamples) > 0)
                    <p class="text-sm text-gray-600 mb-2">الصور الحالية — اضغط على ✕ لحذف الصورة عند حفظ التعديلات</p>
                    <div id="existing-samples" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4 mb-4">
                        @foreach($existingSamples as $idx => $path)
                            <div class="sample-card relative rounded-xl overflow-hidden border-2 border-gray-200 bg-white shadow-sm hover:shadow-md transition-all group" data-idx="{{ $idx }}">
                                <img src="{{ asset('storage/' . $path) }}" alt="عينة {{ $idx + 1 }}" class="w-full h-32 object-cover">
                                <button type="button" class="sample-remove-btn absolute top-1 right-1 z-20 w-6 h-6 flex items-center justify-center bg-red-500 hover:bg-red-600 text-white rounded-full shadow-md transition-all cursor-pointer opacity-0 group-hover:opacity-100" data-idx="{{ $idx }}" title="حذف الصورة">
                                    <svg class="w-3.5 h-3.5 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"></path></svg>
                                </button>
                                <span class="absolute bottom-1 left-1 text-[10px] bg-black/60 text-white px-1.5 py-0.5 rounded">صورة {{ $idx + 1 }}</span>
                            </div>
                        @endforeach
                    </div>
                @endif
                <div class="relative border-2 border-dashed border-gray-300 rounded-xl p-6 text-center hover:border-slate-400 transition-colors bg-gray-50/50 min-h-[140px]">
                    <input type="file" name="samples[]" id="samples" accept="image/*" multiple
                           class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                    <svg class="mx-auto h-10 w-10 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    <p class="mt-2 text-sm text-gray-600">إضافة صور جديدة</p>
                    <p class="mt-1 text-xs text-gray-500">JPEG, PNG, JPG, GIF, WebP (حد أقصى 2MB)</p>
                </div>
                <div id="samples-preview" class="mt-3 grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3"></div>
                @error('samples')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-end space-x-reverse space-x-3">
                <a href="{{ route('products.index') }}" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-slate-500 transition-colors">
                    إلغاء
                </a>
                <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-slate-700 hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-slate-500 transition-colors">
                    حفظ التغييرات
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // Sizes detailed logic
    let sizeIndexCounter = document.querySelectorAll('#sizes-container > div').length || 0;
    
    document.getElementById('add-size-btn').addEventListener('click', () => addSizeFromInputs());
    const sizeInputs = ['size-name', 'size-chest', 'size-w-min', 'size-w-max', 'size-h-min', 'size-h-max'];
    sizeInputs.forEach(id => {
        document.getElementById(id).addEventListener('keypress', (e) => {
            if (e.key === 'Enter') { e.preventDefault(); addSizeFromInputs(); }
        });
    });

    function addSizeFromInputs() {
        const sizeName = document.getElementById('size-name').value.trim();
        const chest = document.getElementById('size-chest').value.trim();
        const wMin = document.getElementById('size-w-min').value.trim();
        const wMax = document.getElementById('size-w-max').value.trim();
        const hMin = document.getElementById('size-h-min').value.trim();
        const hMax = document.getElementById('size-h-max').value.trim();

        if (!sizeName) {
            alert('يرجى إدخال اسم المقاس على الأقل (مثال: M أو L)');
            return;
        }

        addSizeCard({ size: sizeName, chest, wMin, wMax, hMin, hMax }, sizeIndexCounter++);
        
        // Clear inputs
        sizeInputs.forEach(id => document.getElementById(id).value = '');
        document.getElementById('size-name').focus();
    }

    function addSizeCard(data, index) {
        const c = document.getElementById('sizes-container');
        const d = document.createElement('div');
        d.className = 'relative bg-white border border-emerald-200 shadow-sm rounded-lg p-3 flex flex-wrap md:flex-nowrap items-center gap-4 animate-fade-in-up';
        
        d.innerHTML = `
            <div class="flex-shrink-0 w-12 h-12 bg-emerald-100 text-emerald-800 rounded-full flex items-center justify-center font-bold text-lg">
                ${data.size}
            </div>
            <div class="flex-1 grid grid-cols-3 gap-2 text-sm text-gray-600">
                <div><span class="font-medium text-gray-900">الصدر:</span> ${data.chest || '-'} سم</div>
                <div><span class="font-medium text-gray-900">الوزن:</span> ${data.wMin || '-'} - ${data.wMax || '-'} كجم</div>
                <div><span class="font-medium text-gray-900">الطول:</span> ${data.hMin || '-'} - ${data.hMax || '-'} سم</div>
            </div>
            <input type="hidden" name="available_sizes[${index}][size]" value="${data.size}">
            <input type="hidden" name="available_sizes[${index}][chest_width_cm]" value="${data.chest}">
            <input type="hidden" name="available_sizes[${index}][weight_kg][min]" value="${data.wMin}">
            <input type="hidden" name="available_sizes[${index}][weight_kg][max]" value="${data.wMax}">
            <input type="hidden" name="available_sizes[${index}][height_cm][min]" value="${data.hMin}">
            <input type="hidden" name="available_sizes[${index}][height_cm][max]" value="${data.hMax}">
            <button type="button" class="absolute top-2 right-2 text-gray-400 hover:text-red-500 transition-colors" onclick="removeSize(this)" title="حذف المقاس">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        `;
        c.appendChild(d);
    }
    function removeSize(btn) { btn.closest('div.relative.bg-white').remove(); }

    // Colors
    document.getElementById('add-color-btn').addEventListener('click', () => addColorFromInput());
    document.getElementById('color-input').addEventListener('keypress', (e) => { if (e.key === 'Enter') { e.preventDefault(); addColorFromInput(); } });
    function addColorFromInput() {
        const v = document.getElementById('color-input').value.trim();
        if (v) { addColorBadge(v); document.getElementById('color-input').value = ''; }
    }
    function addColorBadge(color) {
        const c = document.getElementById('colors-container');
        const b = document.createElement('span');
        b.className = 'inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm font-medium bg-violet-100 text-violet-800 border border-violet-200 shadow-sm';
        b.innerHTML = `${color}<input type="hidden" name="available_colors[]" value="${color}"><button type="button" class="p-0.5 rounded hover:bg-violet-200/80" onclick="removeColor(this)"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>`;
        c.appendChild(b);
    }
    function removeColor(btn) { btn.closest('span').remove(); }

    // Samples remove - immediate visual removal
    const toRemove = new Set();
    const existingSamplesEl = document.getElementById('existing-samples');
    if (existingSamplesEl) {
        existingSamplesEl.addEventListener('click', function(e) {
            const btn = e.target.closest('.sample-remove-btn');
            if (!btn) return;
            e.preventDefault();
            e.stopPropagation();
            const idx = parseInt(btn.getAttribute('data-idx'), 10);
            if (isNaN(idx)) return;
            const card = btn.closest('.sample-card');
            
            if (!toRemove.has(idx)) {
                toRemove.add(idx);
                if (card) {
                    card.style.display = 'none'; // Visually hide it immediately
                }
                updateSamplesRemoveInputs();
            }
        });
    }
    function updateSamplesRemoveInputs() {
        const cont = document.getElementById('samples-remove-inputs');
        if (!cont) return;
        cont.innerHTML = '';
        [...toRemove].sort((a,b)=>a-b).forEach(idx => {
            const inp = document.createElement('input');
            inp.type = 'hidden';
            inp.name = 'samples_remove[]';
            inp.value = idx;
            cont.appendChild(inp);
        });
    }

    // New images preview and management
    let selectedFiles = new DataTransfer();

    document.getElementById('samples').addEventListener('change', function(e) {
        const container = document.getElementById('samples-preview');
        
        // Add new files to our DataTransfer object
        Array.from(this.files || []).forEach(file => {
            selectedFiles.items.add(file);
        });
        
        // Update the actual input files
        this.files = selectedFiles.files;
        
        renderPreviews();
    });

    function renderPreviews() {
        const container = document.getElementById('samples-preview');
        container.innerHTML = '';
        
        Array.from(selectedFiles.files).forEach((file, index) => {
            const reader = new FileReader();
            reader.onload = (ev) => {
                const div = document.createElement('div');
                div.className = 'relative group rounded-lg overflow-hidden border border-gray-200 shadow-sm';
                div.innerHTML = `
                    <img src="${ev.target.result}" alt="معاينة" class="w-full h-24 object-cover">
                    <button type="button" class="absolute top-1 right-1 w-6 h-6 bg-red-500 hover:bg-red-600 text-white rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity" onclick="removeNewImage(${index})" title="حذف هذه الصورة">
                        <svg class="w-3.5 h-3.5 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                    <span class="absolute bottom-1 left-1 text-[10px] bg-black/60 text-white px-1.5 py-0.5 rounded truncate max-w-[90%]">${file.name}</span>
                `;
                container.appendChild(div);
            };
            reader.readAsDataURL(file);
        });
    }

    function removeNewImage(indexToRemove) {
        const dt = new DataTransfer();
        const files = selectedFiles.files;
        
        for (let i = 0; i < files.length; i++) {
            if (i !== indexToRemove) {
                dt.items.add(files[i]);
            }
        }
        
        selectedFiles = dt;
        document.getElementById('samples').files = selectedFiles.files; // Update the actual input
        renderPreviews(); // Re-render previews
    }
</script>
@endsection
