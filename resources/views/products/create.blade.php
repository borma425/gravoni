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
                <label class="block text-sm font-medium text-gray-700 mb-2">الأحجام المتاحة ومخطط المقاسات</label>
                <div class="bg-gradient-to-br from-slate-50 to-gray-50 border border-slate-200 shadow-sm rounded-xl p-5 mb-5 transition-all hover:shadow-md">
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-5 mb-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1.5 flex items-center gap-1">
                                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                                المقاس (مثال: M)
                            </label>
                            <input type="text" id="size-name" placeholder="أدخل المقاس.." class="block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 text-sm focus:ring-violet-500 focus:border-violet-500 transition-colors bg-white">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1.5 flex items-center gap-1">
                                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path></svg>
                                عرض الصدر (سم)
                            </label>
                            <input type="number" id="size-chest" min="0" placeholder="مثال: 50" class="block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 text-sm focus:ring-violet-500 focus:border-violet-500 transition-colors bg-white">
                        </div>
                        <div class="sm:col-span-2 md:col-span-1">
                            <label class="block text-xs font-semibold text-slate-600 mb-1.5 text-center">الوزن (كجم)</label>
                            <div class="flex items-center gap-2">
                                <input type="number" id="size-w-min" placeholder="من" min="0" class="block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-2 text-sm text-center focus:ring-violet-500 focus:border-violet-500 transition-colors bg-white">
                                <span class="text-slate-400 font-medium">-</span>
                                <input type="number" id="size-w-max" placeholder="إلى" min="0" class="block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-2 text-sm text-center focus:ring-violet-500 focus:border-violet-500 transition-colors bg-white">
                            </div>
                        </div>
                        <div class="sm:col-span-2 md:col-span-1 border-t sm:border-t-0 pt-4 sm:pt-0">
                            <label class="block text-xs font-semibold text-slate-600 mb-1.5 text-center">الطول (سم)</label>
                            <div class="flex items-center gap-2">
                                <input type="number" id="size-h-min" placeholder="من" min="0" class="block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-2 text-sm text-center focus:ring-violet-500 focus:border-violet-500 transition-colors bg-white">
                                <span class="text-slate-400 font-medium">-</span>
                                <input type="number" id="size-h-max" placeholder="إلى" min="0" class="block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-2 text-sm text-center focus:ring-violet-500 focus:border-violet-500 transition-colors bg-white">
                            </div>
                        </div>
                        <div class="sm:col-span-2 md:col-span-3 border-t sm:border-t-0 pt-4 sm:pt-0 mt-2 md:mt-0 bg-white/50 p-4 rounded-lg border border-slate-100">
                            <label class="block text-xs font-semibold text-slate-700 mb-2">أضف ألوان لهذا المقاس (قبل حفظ المقاس)</label>
                            <div class="flex flex-wrap sm:flex-nowrap gap-2 items-center">
                                <input type="text" id="temp-color-name" placeholder="اللون (مثال: أحمر، أسود..)" class="flex-1 min-w-[150px] border border-gray-300 rounded-lg py-2 px-3 text-sm focus:ring-violet-500 focus:border-violet-500 transition-colors shadow-sm bg-white">
                                <div class="relative w-32">
                                    <input type="number" id="temp-color-stock" placeholder="الكمية" min="0" class="w-full border border-gray-300 rounded-lg py-2 px-3 text-sm focus:ring-violet-500 focus:border-violet-500 transition-colors shadow-sm bg-white pr-8">
                                    <span class="absolute right-3 top-2.5 text-slate-400 text-xs font-medium pointer-events-none">ق</span>
                                </div>
                                <button type="button" onclick="addTempColor()" class="whitespace-nowrap flex items-center gap-1.5 px-4 py-2 bg-violet-600 text-white rounded-full text-sm font-medium hover:bg-violet-700 shadow-md hover:shadow-lg transition-all focus:ring-2 focus:ring-offset-2 focus:ring-violet-500">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                                    إضافة لون
                                </button>
                            </div>
                            <!-- Container for temporary colors before size is saved -->
                            <div id="temp-colors-list" class="mt-3 flex flex-wrap gap-2 empty:hidden"></div>
                        </div>
                    </div>
                    <div class="flex justify-end mt-2 pt-4 border-t border-slate-200/60">
                        <button type="button" id="add-size-btn" class="flex items-center gap-2 px-6 py-2.5 bg-slate-800 text-white shadow-md hover:shadow-lg rounded-lg hover:bg-slate-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-slate-700 transition-all text-sm font-bold w-full sm:w-auto">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            اعتماد المقاس وإضافته
                        </button>
                    </div>
                </div>

                <div id="sizes-container" class="flex flex-col gap-3 min-h-[44px]">
                    @if(is_array(old('available_sizes')))
                        @foreach(old('available_sizes') as $index => $sizeObj)
                            @if(is_array($sizeObj) && isset($sizeObj['size']))
                                <div class="relative bg-white border border-emerald-200 shadow-sm rounded-lg p-3 flex flex-col gap-3">
                                    <div class="flex flex-wrap md:flex-nowrap items-center gap-4 w-full">
                                        <div class="flex-shrink-0 w-12 h-12 bg-emerald-100 text-emerald-800 rounded-full flex items-center justify-center font-bold text-lg">
                                            {{ $sizeObj['size'] }}
                                        </div>
                                        <div class="flex-1 grid grid-cols-3 gap-2 text-sm text-gray-600 pr-2 border-r border-emerald-100">
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
                                    @if(isset($sizeObj['colors']) && is_array($sizeObj['colors']) && count($sizeObj['colors']) > 0)
                                        <div class="pt-3 mt-1 border-t border-gray-100">
                                            <div class="flex flex-wrap gap-2">
                                                @foreach($sizeObj['colors'] as $cIdx => $cObj)
                                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-violet-100 text-violet-800 border border-violet-200">
                                                        {{ $cObj['color'] }} (الكمية: {{ $cObj['stock'] }})
                                                        <input type="hidden" name="available_sizes[{{$index}}][colors][{{$cIdx}}][color]" value="{{ $cObj['color'] }}">
                                                        <input type="hidden" name="available_sizes[{{$index}}][colors][{{$cIdx}}][stock]" value="{{ $cObj['stock'] }}">
                                                    </span>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @elseif(is_string($sizeObj))
                                <!-- Fallback for old simple string sizes if validation fails over old data -->
                                <div class="relative bg-white border border-gray-200 shadow-sm rounded-xl p-3 flex items-center gap-4">
                                    <div class="flex-shrink-0 bg-gray-100 text-gray-800 px-3 py-1 rounded-lg font-bold">{{ $sizeObj }}</div>
                                    <div class="flex-1 text-sm text-gray-500">بيانات غير مكتملة</div>
                                    <input type="hidden" name="available_sizes[{{$index}}][size]" value="{{ $sizeObj }}">
                                    <button type="button" class="absolute top-2 right-2 text-gray-400 hover:text-red-500 transition-colors" onclick="removeSize(this)">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    </button>
                                </div>
                            @endif
                        @endforeach
                    @endif
                </div>
                @error('available_sizes')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
                @error('available_sizes.*')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Independent Colors Section Removed -->

            <div>
                <label for="description" class="block text-sm font-medium text-gray-700">الوصف</label>
                <textarea name="description" id="description" rows="4"
                          class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-slate-500 focus:border-slate-500 sm:text-sm @error('description') border-red-300 @enderror">{{ old('description') }}</textarea>
                @error('description')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">صور العينة</label>
                <div class="relative border-2 border-dashed border-gray-300 rounded-xl p-6 text-center hover:border-slate-400 transition-colors bg-gray-50/50">
                    <input type="file" name="samples[]" id="samples" accept="image/*" multiple
                           class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <p class="mt-2 text-sm text-gray-600">اسحب الصور هنا أو <span class="text-slate-600 font-medium">اختر ملفات</span></p>
                    <p class="mt-1 text-xs text-gray-500">JPEG, PNG, JPG, GIF, WebP (حد أقصى 2MB للصورة)</p>
                </div>
                <div id="samples-preview" class="mt-3 grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3"></div>
                @error('samples')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
                @error('samples.*')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">فيديوهات المنتج (اختياري)</label>
                <div class="relative border-2 border-dashed border-gray-300 rounded-xl p-6 text-center hover:border-slate-400 transition-colors bg-gray-50/50">
                    <!-- Note: Removed name="videos[]" so the actual large file isn't submitted with the main form. -->
                    <input type="file" id="videos" accept="video/mp4,video/quicktime,video/ogg" multiple
                           class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                    </svg>
                    <p class="mt-2 text-sm text-gray-600">اسحب الفيديوهات هنا أو <span class="text-slate-600 font-medium">اختر ملفات</span></p>
                    <p class="mt-1 text-xs text-gray-500">MP4, MOV, OGG (حد أقصى 20MB للفيديو الواحد)</p>
                </div>
                <!-- Container for pre-uploaded videos hidden inputs -->
                <div id="hidden-videos-container"></div>
                <div id="videos-preview" class="mt-3 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3"></div>
                @error('videos')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
                @error('videos.*')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div id="upload-progress-container" class="hidden mt-4 bg-white p-4 rounded-lg border border-gray-200 shadow-sm">
                <div class="flex justify-between text-sm font-medium text-gray-700 mb-2">
                    <span id="upload-progress-label">جاري الرفع والحفظ...</span>
                    <span id="upload-progress-text">0%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2.5 overflow-hidden">
                    <div id="upload-progress-bar" class="bg-violet-600 h-full rounded-full transition-all duration-300" style="width: 0%"></div>
                </div>
                <p class="text-xs text-gray-500 mt-2 text-center" id="upload-progress-hint">يرجى عدم إغلاق الصفحة حتى يكتمل الرفع</p>
            </div>

            <div class="flex items-center justify-end space-x-reverse space-x-3 mt-6">
                <a href="{{ route('products.index') }}" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-slate-500 transition-colors">
                    إلغاء
                </a>
                <button type="submit" id="submit-btn" class="inline-flex justify-center flex-row-reverse py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-slate-700 hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-slate-500 transition-colors">
                    <span>حفظ</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // Temporary colors logic
    let tempColors = [];
    
    function addTempColor() {
        const cName = document.getElementById('temp-color-name').value.trim();
        const cStock = document.getElementById('temp-color-stock').value.trim();
        
        if(!cName) { alert('يرجى إدخال اسم اللون'); return; }
        if(!cStock || isNaN(cStock) || cStock < 0) { alert('يرجى إدخال كمية صحيحة'); return; }
        
        tempColors.push({ color: cName, stock: cStock });
        renderTempColors();
        
        document.getElementById('temp-color-name').value = '';
        document.getElementById('temp-color-stock').value = '';
        document.getElementById('temp-color-name').focus();
    }
    
    function removeTempColor(idx) {
        tempColors.splice(idx, 1);
        renderTempColors();
    }
    
    function renderTempColors() {
        const container = document.getElementById('temp-colors-list');
        container.innerHTML = '';
        tempColors.forEach((tc, idx) => {
            const badge = document.createElement('span');
            badge.className = 'inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold bg-white text-slate-700 border border-slate-200 shadow-sm animate-fade-in-up';
            badge.innerHTML = `
                <span class="w-2 h-2 rounded-full bg-violet-500"></span>
                ${tc.color} <span class="bg-slate-100 text-slate-600 px-1.5 py-0.5 rounded text-[10px] mr-1">${tc.stock} ق</span>
                <button type="button" class="text-slate-400 hover:text-red-500 transition-colors ml-1 border-r border-slate-200 pr-2" onclick="removeTempColor(${idx})">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            `;
            container.appendChild(badge);
        });
    }

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

        // Pass a copy of tempColors
        addSizeCard({ size: sizeName, chest, wMin, wMax, hMin, hMax, colors: [...tempColors] }, sizeIndexCounter++);
        
        // Clear inputs
        sizeInputs.forEach(id => document.getElementById(id).value = '');
        
        // Clear temp colors
        tempColors = [];
        renderTempColors();
        
        document.getElementById('size-name').focus();
    }

    function addSizeCard(data, index) {
        const c = document.getElementById('sizes-container');
        const d = document.createElement('div');
        d.className = 'relative bg-white border-l-4 border-violet-500 border-y border-r border-slate-200 shadow-md hover:shadow-lg transition-shadow rounded-xl p-4 flex flex-col gap-3 animate-fade-in-up';
        
        let colorsHtml = '';
        let colorsHiddenInputs = '';
        
        if (data.colors && data.colors.length > 0) {
            colorsHtml += '<div class="pt-3 mt-2 border-t border-slate-100"><div class="flex flex-wrap gap-2">';
            data.colors.forEach((col, cIdx) => {
                colorsHtml += `
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold bg-white text-slate-700 border border-slate-200 shadow-sm">
                        <span class="w-2 h-2 rounded-full bg-violet-500"></span>
                        ${col.color} <span class="bg-slate-100 text-slate-600 px-1.5 py-0.5 rounded text-[10px] mr-1">${col.stock} ق</span>
                    </span>
                `;
                colorsHiddenInputs += `
                    <input type="hidden" name="available_sizes[${index}][colors][${cIdx}][color]" value="${col.color}">
                    <input type="hidden" name="available_sizes[${index}][colors][${cIdx}][stock]" value="${col.stock}">
                `;
            });
            colorsHtml += '</div></div>';
        }

        d.innerHTML = `
            <div class="flex flex-wrap md:flex-nowrap items-center gap-4 w-full">
                <div class="flex-shrink-0 w-14 h-14 bg-gradient-to-br from-violet-100 to-purple-100 text-violet-800 rounded-full flex items-center justify-center font-black text-xl shadow-inner border border-violet-200">
                    ${data.size}
                </div>
                <div class="flex-1 flex flex-wrap gap-4 text-sm text-gray-700 md:pr-4 md:border-r border-slate-100">
                    <div class="flex items-center gap-1.5 bg-slate-50 px-3 py-1.5 rounded-lg border border-slate-100"><span class="font-bold text-slate-800">الصدر:</span> ${data.chest || '-'} سم</div>
                    <div class="flex items-center gap-1.5 bg-slate-50 px-3 py-1.5 rounded-lg border border-slate-100"><span class="font-bold text-slate-800">الوزن:</span> ${data.wMin || '-'} - ${data.wMax || '-'} كجم</div>
                    <div class="flex items-center gap-1.5 bg-slate-50 px-3 py-1.5 rounded-lg border border-slate-100"><span class="font-bold text-slate-800">الطول:</span> ${data.hMin || '-'} - ${data.hMax || '-'} سم</div>
                </div>
                
                <input type="hidden" name="available_sizes[${index}][size]" value="${data.size}">
                <input type="hidden" name="available_sizes[${index}][chest_width_cm]" value="${data.chest}">
                <input type="hidden" name="available_sizes[${index}][weight_kg][min]" value="${data.wMin}">
                <input type="hidden" name="available_sizes[${index}][weight_kg][max]" value="${data.wMax}">
                <input type="hidden" name="available_sizes[${index}][height_cm][min]" value="${data.hMin}">
                <input type="hidden" name="available_sizes[${index}][height_cm][max]" value="${data.hMax}">
                ${colorsHiddenInputs}
                
                <div class="absolute top-3 right-3 flex items-center gap-1 bg-white/80 backdrop-blur rounded-lg p-1 shadow-sm border border-slate-100">
                    <button type="button" class="text-blue-600 hover:text-blue-800 transition-colors bg-blue-50 hover:bg-blue-100 rounded-md p-1.5" onclick="editSize(this)" title="تعديل المقاس">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                    </button>
                    <button type="button" class="text-red-500 hover:text-red-700 transition-colors bg-red-50 hover:bg-red-100 rounded-md p-1.5" onclick="removeSize(this)" title="حذف المقاس">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    </button>
                </div>
            </div>
            ${colorsHtml}
        `;
        c.appendChild(d);
    }
    
    function editSize(btn) {
        const card = btn.closest('div.relative.bg-white');
        
        // Extract basic data
        document.getElementById('size-name').value = card.querySelector('input[name$="[size]"]').value;
        document.getElementById('size-chest').value = card.querySelector('input[name$="[chest_width_cm]"]').value;
        document.getElementById('size-w-min').value = card.querySelector('input[name$="[weight_kg][min]"]').value;
        document.getElementById('size-w-max').value = card.querySelector('input[name$="[weight_kg][max]"]').value;
        document.getElementById('size-h-min').value = card.querySelector('input[name$="[height_cm][min]"]').value;
        document.getElementById('size-h-max').value = card.querySelector('input[name$="[height_cm][max]"]').value;
        
        // Extract colors
        tempColors = [];
        const colorInputs = card.querySelectorAll('input[name*="[colors]"][name$="[color]"]');
        const stockInputs = card.querySelectorAll('input[name*="[colors]"][name$="[stock]"]');
        
        for(let i = 0; i < colorInputs.length; i++) {
            tempColors.push({
                color: colorInputs[i].value,
                stock: stockInputs[i].value
            });
        }
        
        // Re-render
        renderTempColors();
        
        // Remove card
        card.remove();
        
        // Focus
        document.getElementById('size-name').focus();
    }
    
    function removeSize(btn) { btn.closest('div.relative.bg-white').remove(); }

    // Multiple images preview and management
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

    // Standalone asynchronous video uploads
    let autoUploadVidIndex = 0;

    document.getElementById('videos').addEventListener('change', function(e) {
        const files = Array.from(this.files || []);
        if (files.length === 0) return;

        const container = document.getElementById('videos-preview');
        const hiddenInputsContainer = document.getElementById('hidden-videos-container');
        const URL = window.URL || window.webkitURL;

        files.forEach(file => {
            const currentIndex = autoUploadVidIndex++;
            const videoUrl = URL.createObjectURL(file);
            const domId = 'auto-vid-' + currentIndex;

            // 1. Create Preview Thumbnail & Progress UI
            const div = document.createElement('div');
            div.className = 'relative group rounded-lg overflow-hidden border border-gray-200 shadow-sm bg-black object-cover';
            div.id = domId;
            div.innerHTML = `
                <video src="${videoUrl}" class="w-full h-32 object-cover opacity-50" muted></video>
                <div class="absolute inset-0 bg-black/60 pointer-events-none transition-colors" id="${domId}-overlay"></div>
                
                <!-- Progress Center UI -->
                <div class="absolute inset-0 flex flex-col items-center justify-center p-4">
                    <span class="text-white text-xs font-medium mb-2 drop-shadow-md" id="${domId}-status">جاري الرفع...</span>
                    <div class="w-full bg-gray-700/50 rounded-full h-2 overflow-hidden shadow-inner">
                        <div id="${domId}-progress" class="bg-violet-500 h-full rounded-full transition-all duration-300" style="width: 0%"></div>
                    </div>
                    <span class="text-white text-[10px] font-bold mt-1 shadow-sm" id="${domId}-text">0%</span>
                </div>

                <button type="button" class="hidden absolute top-2 right-2 w-8 h-8 bg-red-500 hover:bg-red-600 text-white rounded-full items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity z-10" id="${domId}-remove-btn" title="حذف هذا الفيديو">
                    <svg class="w-4 h-4 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
                <span class="absolute bottom-1 left-1 right-1 text-[11px] bg-black/60 text-white px-1.5 py-0.5 rounded truncate text-center">${file.name}</span>
            `;
            container.appendChild(div);

            // 2. Prepare XHR Payload
            const formData = new FormData();
            formData.append('video', file);
            formData.append('_token', '{{ csrf_token() }}');

            const xhr = new XMLHttpRequest();
            xhr.open('POST', '{{ route('products.upload-video') }}');
            xhr.setRequestHeader('Accept', 'application/json');

            // 3. Track Progress
            xhr.upload.onprogress = function(event) {
                if (event.lengthComputable) {
                    const percentComplete = Math.round((event.loaded / event.total) * 100);
                    document.getElementById(domId + '-progress').style.width = percentComplete + '%';
                    document.getElementById(domId + '-text').innerText = percentComplete + '%';
                    if(percentComplete === 100) {
                         document.getElementById(domId + '-status').innerText = 'المعالجة...';
                    }
                }
            };

            // 4. Handle Response
            xhr.onload = function() {
                if (xhr.status >= 200 && xhr.status < 300) {
                    try {
                        const res = JSON.parse(xhr.responseText);
                        if (res.success) {
                            // Success UI
                            document.getElementById(domId + '-progress').classList.replace('bg-violet-500', 'bg-emerald-500');
                            document.getElementById(domId + '-status').innerText = 'تم الرفع بنجاح';
                            setTimeout(() => {
                                // Fade out overlay
                                document.getElementById(domId + '-overlay').style.opacity = '0';
                                div.querySelector('.flex-col').style.display = 'none'; // hide progress center UI
                                div.querySelector('video').classList.remove('opacity-50');
                                
                                // Show remove button
                                const removeBtn = document.getElementById(domId + '-remove-btn');
                                removeBtn.classList.remove('hidden');
                                removeBtn.classList.add('flex');
                                
                                // Inject hidden input so it gets submitted with the main form
                                const hiddenInput = document.createElement('input');
                                hiddenInput.type = 'hidden';
                                hiddenInput.name = 'videos[]';
                                hiddenInput.value = res.path;
                                hiddenInput.id = domId + '-hidden-input';
                                hiddenInputsContainer.appendChild(hiddenInput);

                                // Setup removal logic
                                removeBtn.onclick = function() {
                                    div.remove();
                                    hiddenInput.remove();
                                };
                            }, 1000);
                        }
                    } catch(e) {
                        displayError('خطأ داخلي أثناء المعالجة.');
                    }
                } else {
                    let errorMessage = 'فشل الرفع.';
                    if (xhr.status === 422) {
                        try {
                            const res = JSON.parse(xhr.responseText);
                            errorMessage = res.errors.video[0] || 'تنسيق غير مدعوم أو حجم كبير.';
                        } catch(e) {}
                    }
                    displayError(errorMessage);
                }
                
                function displayError(msg) {
                    document.getElementById(domId + '-progress').classList.replace('bg-violet-500', 'bg-red-500');
                    document.getElementById(domId + '-status').innerText = msg;
                    document.getElementById(domId + '-status').classList.replace('text-white', 'text-red-300');
                    
                    const removeBtn = document.getElementById(domId + '-remove-btn');
                    removeBtn.classList.remove('hidden');
                    removeBtn.classList.add('flex');
                    removeBtn.onclick = function() { div.remove(); };
                }
            };
            
            xhr.onerror = function() {
                 document.getElementById(domId + '-status').innerText = 'انقطع الاتصال.';
                 document.getElementById(domId + '-progress').classList.replace('bg-violet-500', 'bg-red-500');
            }

            // Let's go!
            xhr.send(formData);
        });

        // Clear the actual input so selecting the same file again triggers change event
        this.value = '';
    });

    // AJAX Form Submit for Upload Progress
    const form = document.querySelector('form');
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Clear previous ajax error messages and borders
        document.querySelectorAll('.ajax-error').forEach(el => el.remove());
        document.querySelectorAll('.border-red-300').forEach(el => el.classList.remove('border-red-300', 'text-red-900', 'placeholder-red-300'));
        
        const btn = document.getElementById('submit-btn');
        btn.disabled = true;
        btn.innerHTML = `<svg class="animate-spin ml-2 h-4 w-4 text-white inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg><span>جاري الحفظ...</span>`;
        
        const progressContainer = document.getElementById('upload-progress-container');
        const progressBar = document.getElementById('upload-progress-bar');
        const progressText = document.getElementById('upload-progress-text');
        const progressLabel = document.getElementById('upload-progress-label');
        const progressHint = document.getElementById('upload-progress-hint');
        
        progressContainer.classList.remove('hidden');
        progressBar.style.width = '0%';
        progressText.innerText = '0%';
        progressLabel.innerText = 'جاري الرفع...';
        progressHint.innerText = 'يرجى عدم إغلاق الصفحة حتى يكتمل الرفع';
        
        const formData = new FormData(form);
        const xhr = new XMLHttpRequest();
        xhr.open(form.method, form.action);
        xhr.setRequestHeader('Accept', 'application/json');
        
        xhr.upload.onprogress = function(event) {
            if (event.lengthComputable) {
                const percentComplete = Math.round((event.loaded / event.total) * 100);
                progressBar.style.width = percentComplete + '%';
                progressText.innerText = percentComplete + '%';
                
                if (percentComplete === 100) {
                    progressLabel.innerText = 'جاري معالجة البيانات...';
                    progressHint.innerText = 'اكتمل الرفع، بانتظار استجابة الخادم';
                }
            }
        };
        
        xhr.onload = function() {
            if (xhr.status >= 200 && xhr.status < 300) {
                // Success
                window.location.href = "{{ route('products.index') }}";
            } else if (xhr.status === 422) {
                // Validation error
                btn.disabled = false;
                btn.innerHTML = '<span>حفظ</span>';
                progressContainer.classList.add('hidden');
                
                try {
                    const response = JSON.parse(xhr.responseText);
                    const errors = response.errors;
                    let firstErrorEl = null;
                    
                    for (const [field, messages] of Object.entries(errors)) {
                        const inputName = field.replace(/\.(\w+)/g, '[$1]'); // convert videos.0 to videos[0]
                        const inputEl = form.querySelector(`[name="${inputName}"]`) || form.querySelector(`[name="${inputName}[]"]`) || document.getElementById(field);
                        
                        const errorP = document.createElement('p');
                        errorP.className = 'mt-2 text-sm text-red-600 ajax-error font-medium';
                        errorP.innerText = messages[0];
                        
                        if (inputEl) {
                            inputEl.classList.add('border-red-300', 'text-red-900', 'placeholder-red-300');
                            inputEl.parentNode.appendChild(errorP);
                            if (!firstErrorEl) firstErrorEl = inputEl;
                        } else {
                            if (field.startsWith('samples')) {
                                document.getElementById('samples-preview').parentNode.appendChild(errorP);
                                if (!firstErrorEl) firstErrorEl = document.getElementById('samples-preview');
                            } else if (field.startsWith('videos')) {
                                document.getElementById('videos-preview').parentNode.appendChild(errorP);
                                if (!firstErrorEl) firstErrorEl = document.getElementById('videos-preview');
                            } else {
                                alert(messages[0]);
                            }
                        }
                    }
                    if (firstErrorEl) firstErrorEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
                } catch(e) {
                    alert('يوجد خطأ في البيانات المدخلة.');
                }
            } else {
                alert('حدث خطأ أثناء الاتصال بالخادم. يرجى المحاولة مرة أخرى.');
                btn.disabled = false;
                btn.innerHTML = '<span>حفظ</span>';
                progressContainer.classList.add('hidden');
            }
        };
        
        xhr.onerror = function() {
            alert('حدث خطأ في الاتصال.');
            btn.disabled = false;
            btn.innerHTML = '<span>حفظ</span>';
            progressContainer.classList.add('hidden');
        };
        
        xhr.send(formData);
    });
</script>
@endsection
