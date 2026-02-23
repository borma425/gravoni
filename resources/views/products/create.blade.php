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

<form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    @csrf

    <!-- Main Content Column (Left Side) -->
    <div class="lg:col-span-2 space-y-6">
        
        <!-- Basic Info Card -->
        <div class="bg-white shadow-sm border border-slate-200 rounded-xl p-6">
            <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-violet-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                المعلومات الأساسية
            </h2>
            <div class="space-y-5">
                <div>
                    <label for="name" class="block text-sm font-semibold text-gray-700">اسم المنتج</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required
                           class="mt-1.5 block w-full border border-gray-300 rounded-lg shadow-sm py-2.5 px-3 focus:outline-none focus:ring-2 focus:ring-violet-500 focus:border-violet-500 sm:text-sm transition-colors @error('name') border-red-300 ring-red-100 @enderror">
                    @error('name')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="sku" class="block text-sm font-semibold text-gray-700">رمز التخزين (SKU)</label>
                    <input type="text" name="sku" id="sku" value="{{ old('sku') }}" required
                           class="mt-1.5 block w-full border border-gray-300 rounded-lg shadow-sm py-2.5 px-3 focus:outline-none focus:ring-2 focus:ring-violet-500 focus:border-violet-500 sm:text-sm transition-colors @error('sku') border-red-300 ring-red-100 @enderror">
                    @error('sku')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="description" class="block text-sm font-semibold text-gray-700">الوصف</label>
                    <textarea name="description" id="description" rows="4"
                              class="mt-1.5 block w-full border border-gray-300 rounded-lg shadow-sm py-2.5 px-3 focus:outline-none focus:ring-2 focus:ring-violet-500 focus:border-violet-500 sm:text-sm transition-colors @error('description') border-red-300 ring-red-100 @enderror">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Availability (Sizes & Colors) Card -->
        <div class="bg-white shadow-sm border border-slate-200 rounded-xl p-6">
            <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-violet-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                الأحجام والألوان المتاحة
            </h2>
            <div class="bg-gradient-to-br from-slate-50 to-gray-50 border border-slate-200 shadow-sm rounded-xl p-5 mb-5 transition-all hover:shadow-md">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5 mb-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">المقاس (مثال: M)</label>
                        <input type="text" id="size-name" placeholder="أدخل المقاس.." class="block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 text-sm focus:ring-violet-500 focus:border-violet-500 transition-colors bg-white">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">عرض الصدر (سم)</label>
                        <input type="number" id="size-chest" min="0" placeholder="مثال: 50" class="block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 text-sm focus:ring-violet-500 focus:border-violet-500 transition-colors bg-white">
                    </div>
                    <div class="sm:col-span-2 lg:col-span-1">
                        <label class="block text-xs font-bold text-slate-700 mb-1.5 text-center">الوزن (كجم)</label>
                        <div class="flex items-center gap-2">
                            <input type="number" id="size-w-min" placeholder="أدنى" min="0" class="block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-2 text-sm text-center focus:ring-violet-500 focus:border-violet-500 transition-colors bg-white">
                            <span class="text-slate-400 font-medium">-</span>
                            <input type="number" id="size-w-max" placeholder="أقصى" min="0" class="block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-2 text-sm text-center focus:ring-violet-500 focus:border-violet-500 transition-colors bg-white">
                        </div>
                    </div>
                    <div class="sm:col-span-2 lg:col-span-1 border-t sm:border-t-0 pt-4 sm:pt-0">
                        <label class="block text-xs font-bold text-slate-700 mb-1.5 text-center">الطول (سم)</label>
                        <div class="flex items-center gap-2">
                            <input type="number" id="size-h-min" placeholder="من" min="0" class="block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-2 text-sm text-center focus:ring-violet-500 focus:border-violet-500 transition-colors bg-white">
                            <span class="text-slate-400 font-medium">-</span>
                            <input type="number" id="size-h-max" placeholder="إلى" min="0" class="block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-2 text-sm text-center focus:ring-violet-500 focus:border-violet-500 transition-colors bg-white">
                        </div>
                    </div>
                    <!-- Color + Stock + Media Input -->
                    <div class="sm:col-span-2 lg:col-span-3 border-t pt-4 mt-2 md:mt-0 bg-white/60 p-4 rounded-xl border border-slate-200">
                        <label class="block text-xs font-bold text-slate-800 mb-2">أضف ألوان المتوفرة لهذا المقاس</label>
                        <div class="flex flex-wrap sm:flex-nowrap gap-2 items-center mb-3">
                            <input type="text" id="temp-color-name" placeholder="اللون (مثال: أحمر)" class="flex-1 min-w-[150px] border border-gray-300 rounded-lg py-2 px-3 text-sm focus:ring-violet-500 focus:border-violet-500 transition-colors shadow-sm bg-white">
                            <div class="relative w-32">
                                <input type="number" id="temp-color-stock" placeholder="الكمية" min="0" class="w-full border border-gray-300 rounded-lg py-2 px-3 text-sm focus:ring-violet-500 focus:border-violet-500 transition-colors shadow-sm bg-white pr-8">
                                <span class="absolute right-3 top-2.5 text-slate-400 text-xs font-medium pointer-events-none">ق</span>
                            </div>
                        </div>
                        <!-- Per-Color Media Upload Area -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-3">
                            <div>
                                <label class="block text-[11px] font-bold text-slate-600 mb-1">صور هذا اللون</label>
                                <div class="relative border-2 border-dashed border-gray-300 rounded-lg p-3 text-center hover:border-violet-400 hover:bg-violet-50/30 transition-colors bg-slate-50 cursor-pointer" id="color-images-dropzone">
                                    <input type="file" id="color-images-input" accept="image/*" multiple class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                                    <svg class="mx-auto h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                    <p class="text-[11px] text-gray-500 mt-1">اختر صور <span class="text-violet-600">أو اسحب هنا</span></p>
                                </div>
                                <div id="color-images-preview" class="mt-2 flex flex-wrap gap-2"></div>
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold text-slate-600 mb-1">فيديوهات هذا اللون</label>
                                <div class="relative border-2 border-dashed border-gray-300 rounded-lg p-3 text-center hover:border-violet-400 hover:bg-violet-50/30 transition-colors bg-slate-50 cursor-pointer">
                                    <input type="file" id="color-videos-input" accept="video/mp4,video/quicktime,video/ogg" multiple class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                                    <svg class="mx-auto h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" /></svg>
                                    <p class="text-[11px] text-gray-500 mt-1">اختر فيديوهات <span class="text-violet-600">أو اسحب هنا</span></p>
                                </div>
                                <div id="color-videos-preview" class="mt-2 flex flex-wrap gap-2"></div>
                            </div>
                        </div>
                        <!-- Upload Progress -->
                        <div id="color-upload-progress" class="hidden mb-3 bg-slate-100 p-3 rounded-lg border border-slate-200">
                            <div class="flex justify-between text-xs font-bold text-slate-700 mb-1">
                                <span id="color-upload-label">جاري الرفع...</span>
                                <span id="color-upload-text" class="text-violet-600">0%</span>
                            </div>
                            <div class="w-full bg-slate-200 rounded-full h-2 overflow-hidden">
                                <div id="color-upload-bar" class="bg-gradient-to-r from-violet-500 to-fuchsia-500 h-full rounded-full transition-all duration-300" style="width: 0%"></div>
                            </div>
                        </div>
                        <button type="button" onclick="addTempColor()" class="w-full flex items-center justify-center gap-1.5 px-4 py-2 bg-violet-600 text-white rounded-lg text-sm font-bold hover:bg-violet-700 shadow-sm transition-all focus:ring-2 focus:ring-offset-2 focus:ring-violet-500">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                            إضافة لون مع الوسائط
                        </button>
                        <div id="temp-colors-list" class="mt-3 space-y-2 empty:hidden"></div>
                    </div>
                </div>
                <div class="flex justify-end mt-2 pt-4 border-t border-slate-200/60">
                    <button type="button" id="add-size-btn" class="flex items-center justify-center gap-2 px-6 py-2.5 bg-slate-800 text-white shadow-sm hover:shadow-md rounded-lg hover:bg-slate-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-slate-700 transition-all text-sm font-bold w-full sm:w-auto">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        اعتماد المقاس
                    </button>
                </div>
            </div>

            <div id="sizes-container" class="flex flex-col gap-4 min-h-[44px]"></div>
            @error('available_sizes')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
            @error('available_sizes.*')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

    </div>

    <!-- Sidebar Column (Right Side) -->
    <div class="lg:col-span-1 space-y-6">
        
        <!-- Pricing Card -->
        <div class="bg-white shadow-sm border border-slate-200 rounded-xl p-6">
            <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                التسعير
            </h2>
            <div class="space-y-5">
                <div>
                    <label for="selling_price" class="block text-sm font-semibold text-gray-700">السعر الأساسي</label>
                    <div class="mt-1.5 relative rounded-lg shadow-sm">
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <span class="text-gray-500 sm:text-sm">EGP</span>
                        </div>
                        <input type="number" step="0.01" name="selling_price" id="selling_price" value="{{ old('selling_price') }}" required min="0" dir="ltr"
                               class="block w-full border border-gray-300 rounded-lg py-2.5 pl-3 pr-12 focus:ring-violet-500 focus:border-violet-500 sm:text-sm transition-colors text-right @error('selling_price') border-red-300 ring-red-100 @enderror">
                    </div>
                </div>

                <div>
                    <label for="discounted_price" class="block text-sm font-semibold text-gray-700">سعر التخفيض <span class="text-xs text-gray-400 font-normal">(اختياري)</span></label>
                    <div class="mt-1.5 relative rounded-lg shadow-sm">
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <span class="text-gray-500 sm:text-sm">EGP</span>
                        </div>
                        <input type="number" step="0.01" name="discounted_price" id="discounted_price" value="{{ old('discounted_price') }}" min="0" dir="ltr"
                               class="block w-full border border-gray-300 rounded-lg py-2.5 pl-3 pr-12 focus:ring-violet-500 focus:border-violet-500 sm:text-sm transition-colors text-right @error('discounted_price') border-red-300 ring-red-100 @enderror">
                    </div>
                </div>
            </div>
        </div>

        <!-- Publish / Actions Card (Sticky) -->
        <div class="bg-white shadow-sm border border-slate-200 rounded-xl p-6 sticky top-6">
            <h2 class="text-lg font-bold text-gray-900 mb-4">النشر والحفظ</h2>
            <p class="text-sm text-gray-500 mb-6">تأكد من مراجعة كافة بيانات وتفاصيل المنتج قبل الحفظ لضمان ظهورها بشكل صحيح في المتجر.</p>
            
            <div class="flex flex-col gap-3">
                <button type="submit" id="submit-btn" class="w-full inline-flex justify-center items-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-bold text-white bg-violet-600 hover:bg-violet-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-violet-500 transition-all">
                    <svg class="w-5 h-5 ml-2 -mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    حفظ ونشر المنتج
                </button>
                <a href="{{ route('products.index') }}" class="w-full inline-flex justify-center items-center py-3 px-4 border border-gray-300 rounded-lg shadow-sm text-sm font-bold text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-slate-500 transition-all">
                    إلغاء والعودة
                </a>
            </div>
        </div>

    </div>

</form>

<script>
    const UPLOAD_URL = '{{ route("products.upload-media") }}';
    const CSRF_TOKEN = '{{ csrf_token() }}';
    const STORAGE_URL = '{{ asset("storage") }}/';

    // ================================================================
    // Per-Color Temp Media State
    // ================================================================
    let tempColors = [];
    let tempColorImages = []; // [{path, url}] — uploaded via AJAX
    let tempColorVideos = []; // [{path, url}]

    // ================================================================
    // AJAX Media Upload Helper
    // ================================================================
    function uploadFile(file, type) {
        return new Promise((resolve, reject) => {
            const formData = new FormData();
            formData.append('file', file);
            formData.append('type', type);
            formData.append('_token', CSRF_TOKEN);

            const xhr = new XMLHttpRequest();
            xhr.open('POST', UPLOAD_URL);
            xhr.setRequestHeader('Accept', 'application/json');

            const progressContainer = document.getElementById('color-upload-progress');
            const progressBar = document.getElementById('color-upload-bar');
            const progressText = document.getElementById('color-upload-text');
            const progressLabel = document.getElementById('color-upload-label');
            progressContainer.classList.remove('hidden');

            xhr.upload.onprogress = function(event) {
                if (event.lengthComputable) {
                    const pct = Math.round((event.loaded / event.total) * 100);
                    progressBar.style.width = pct + '%';
                    progressText.innerText = pct + '%';
                    if (pct === 100) progressLabel.innerText = 'المعالجة...';
                }
            };

            xhr.onload = function() {
                if (xhr.status >= 200 && xhr.status < 300) {
                    try {
                        const res = JSON.parse(xhr.responseText);
                        if (res.success) {
                            resolve({ path: res.path, url: res.url });
                        } else {
                            reject('Upload failed');
                        }
                    } catch (e) { reject('Parse error'); }
                } else {
                    let msg = 'فشل الرفع';
                    try { msg = JSON.parse(xhr.responseText).message || msg; } catch(e){}
                    reject(msg);
                }
            };
            xhr.onerror = () => reject('Network error');
            xhr.send(formData);
        });
    }

    async function uploadFilesSequentially(files, type) {
        const results = [];
        const progressContainer = document.getElementById('color-upload-progress');
        const progressLabel = document.getElementById('color-upload-label');

        for (let i = 0; i < files.length; i++) {
            progressLabel.innerText = `رفع ${type === 'image' ? 'صورة' : 'فيديو'} ${i + 1} من ${files.length}...`;
            try {
                const result = await uploadFile(files[i], type);
                results.push(result);
            } catch (err) {
                alert(`فشل رفع الملف: ${files[i].name}\n${err}`);
            }
        }
        progressContainer.classList.add('hidden');
        return results;
    }

    // ================================================================
    // Image Upload Handler
    // ================================================================
    document.getElementById('color-images-input').addEventListener('change', async function() {
        const files = Array.from(this.files || []);
        if (files.length === 0) return;
        this.value = '';

        const uploaded = await uploadFilesSequentially(files, 'image');
        tempColorImages.push(...uploaded);
        renderTempColorMedia();
    });

    // ================================================================
    // Video Upload Handler
    // ================================================================
    document.getElementById('color-videos-input').addEventListener('change', async function() {
        const files = Array.from(this.files || []);
        if (files.length === 0) return;
        this.value = '';

        const uploaded = await uploadFilesSequentially(files, 'video');
        tempColorVideos.push(...uploaded);
        renderTempColorMedia();
    });

    // ================================================================
    // Render Temp Media Previews (in the color input area)
    // ================================================================
    function renderTempColorMedia() {
        // Images
        const imgContainer = document.getElementById('color-images-preview');
        imgContainer.innerHTML = '';
        tempColorImages.forEach((img, idx) => {
            const el = document.createElement('div');
            el.className = 'relative group w-14 h-14 rounded-lg overflow-hidden border border-slate-200 shadow-sm';
            el.innerHTML = `
                <img src="${img.url}" class="w-full h-full object-cover">
                <button type="button" onclick="removeTempImage(${idx})" class="absolute top-0 right-0 w-5 h-5 bg-red-500 text-white rounded-bl-lg flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            `;
            imgContainer.appendChild(el);
        });

        // Videos
        const vidContainer = document.getElementById('color-videos-preview');
        vidContainer.innerHTML = '';
        tempColorVideos.forEach((vid, idx) => {
            const el = document.createElement('div');
            el.className = 'relative group flex items-center gap-2 bg-slate-100 px-2.5 py-1.5 rounded-lg border border-slate-200 text-xs';
            el.innerHTML = `
                <svg class="w-4 h-4 text-violet-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                <span class="truncate max-w-[100px] text-slate-700 font-medium">${vid.path.split('/').pop()}</span>
                <button type="button" onclick="removeTempVideo(${idx})" class="text-red-400 hover:text-red-600 flex-shrink-0">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            `;
            vidContainer.appendChild(el);
        });
    }

    function removeTempImage(idx) { tempColorImages.splice(idx, 1); renderTempColorMedia(); }
    function removeTempVideo(idx) { tempColorVideos.splice(idx, 1); renderTempColorMedia(); }

    // ================================================================
    // Add Color to Temp List
    // ================================================================
    function addTempColor() {
        const cName = document.getElementById('temp-color-name').value.trim();
        const cStock = document.getElementById('temp-color-stock').value.trim();
        
        if (!cName) { alert('يرجى إدخال اسم اللون'); return; }
        if (!cStock || isNaN(cStock) || cStock < 0) { alert('يرجى إدخال كمية صحيحة'); return; }
        
        tempColors.push({
            color: cName,
            stock: cStock,
            images: [...tempColorImages],
            videos: [...tempColorVideos]
        });
        
        // Clear inputs and temp media
        document.getElementById('temp-color-name').value = '';
        document.getElementById('temp-color-stock').value = '';
        tempColorImages = [];
        tempColorVideos = [];
        renderTempColorMedia();
        renderTempColors();
        
        document.getElementById('temp-color-name').focus();
    }
    
    function removeTempColor(idx) {
        tempColors.splice(idx, 1);
        renderTempColors();
    }
    
    // ================================================================
    // Render Temp Colors List (below the "add color" button)
    // ================================================================
    function renderTempColors() {
        const container = document.getElementById('temp-colors-list');
        container.innerHTML = '';
        tempColors.forEach((tc, idx) => {
            const div = document.createElement('div');
            div.className = 'bg-white border border-slate-200 rounded-lg p-3 shadow-sm';
            
            let mediaHtml = '';
            if (tc.images.length > 0) {
                mediaHtml += '<div class="flex flex-wrap gap-1 mt-2">';
                tc.images.forEach(img => {
                    mediaHtml += `<img src="${img.url}" class="w-10 h-10 rounded object-cover border border-slate-200">`;
                });
                mediaHtml += '</div>';
            }
            if (tc.videos.length > 0) {
                mediaHtml += '<div class="flex flex-wrap gap-1 mt-1">';
                tc.videos.forEach(vid => {
                    mediaHtml += `<span class="inline-flex items-center gap-1 bg-violet-50 text-violet-700 text-[10px] px-2 py-0.5 rounded border border-violet-200">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                        ${vid.path.split('/').pop()}
                    </span>`;
                });
                mediaHtml += '</div>';
            }
            
            div.innerHTML = `
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-violet-500"></span>
                        <span class="text-sm font-bold text-slate-800">${tc.color}</span>
                        <span class="bg-slate-100 text-slate-600 px-2 py-0.5 rounded text-[11px] font-medium">${tc.stock} قطعة</span>
                        <span class="text-[10px] text-slate-400">${tc.images.length} صور · ${tc.videos.length} فيديو</span>
                    </div>
                    <button type="button" class="text-red-400 hover:text-red-600 transition-colors" onclick="removeTempColor(${idx})">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                ${mediaHtml}
            `;
            container.appendChild(div);
        });
    }

    // ================================================================
    // Size Management
    // ================================================================
    let sizeIndexCounter = 0;
    
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

        // Auto-flush pending color if the user filled in color name/stock but didn't click "إضافة لون"
        const pendingColorName = document.getElementById('temp-color-name').value.trim();
        const pendingColorStock = document.getElementById('temp-color-stock').value.trim();
        
        if (pendingColorName && pendingColorStock) {
            tempColors.push({
                color: pendingColorName,
                stock: pendingColorStock,
                images: [...tempColorImages],
                videos: [...tempColorVideos]
            });
            document.getElementById('temp-color-name').value = '';
            document.getElementById('temp-color-stock').value = '';
            tempColorImages = [];
            tempColorVideos = [];
            renderTempColorMedia();
        } else if (tempColorImages.length > 0 || tempColorVideos.length > 0) {
            // Media uploaded but no color name — warn and stop
            alert('لديك وسائط مرفوعة بدون لون! يرجى إدخال اسم اللون والكمية أولاً ثم اضغط "إضافة لون مع الوسائط"');
            return;
        }

        if (tempColors.length === 0) {
            alert('يرجى إضافة لون واحد على الأقل قبل اعتماد المقاس');
            return;
        }

        addSizeCard({ size: sizeName, chest, wMin, wMax, hMin, hMax, colors: [...tempColors] }, sizeIndexCounter++);
        
        sizeInputs.forEach(id => document.getElementById(id).value = '');
        tempColors = [];
        tempColorImages = [];
        tempColorVideos = [];
        renderTempColors();
        renderTempColorMedia();
        
        document.getElementById('size-name').focus();
    }

    function addSizeCard(data, index) {
        const c = document.getElementById('sizes-container');
        const d = document.createElement('div');
        d.className = 'bg-white border-l-4 border-violet-500 border-y border-r border-slate-200 shadow-md hover:shadow-lg transition-shadow rounded-xl p-4 flex flex-col gap-3 animate-fade-in-up';
        
        let colorsHtml = '';
        let hiddenInputs = '';
        
        if (data.colors && data.colors.length > 0) {
            colorsHtml += '<div class="pt-3 mt-2 border-t border-slate-100 space-y-3">';
            data.colors.forEach((col, cIdx) => {
                // Hidden inputs for color data
                hiddenInputs += `
                    <input type="hidden" name="available_sizes[${index}][colors][${cIdx}][color]" value="${col.color}">
                    <input type="hidden" name="available_sizes[${index}][colors][${cIdx}][stock]" value="${col.stock}">
                `;
                // Hidden inputs for images
                if (col.images) {
                    col.images.forEach((img, iIdx) => {
                        hiddenInputs += `<input type="hidden" name="available_sizes[${index}][colors][${cIdx}][images][${iIdx}]" value="${img.path}">`;
                    });
                }
                // Hidden inputs for videos
                if (col.videos) {
                    col.videos.forEach((vid, vIdx) => {
                        hiddenInputs += `<input type="hidden" name="available_sizes[${index}][colors][${cIdx}][videos][${vIdx}]" value="${vid.path}">`;
                    });
                }

                // Color display card
                colorsHtml += `<div class="bg-slate-50 rounded-lg p-3 border border-slate-100">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-violet-500"></span>
                        <span class="text-sm font-bold text-slate-800">${col.color}</span>
                        <span class="bg-white text-slate-600 px-2 py-0.5 rounded text-[11px] font-medium border border-slate-200">${col.stock} ق</span>
                    </div>`;
                
                // Image thumbnails
                if (col.images && col.images.length > 0) {
                    colorsHtml += '<div class="flex flex-wrap gap-1.5">';
                    col.images.forEach(img => {
                        colorsHtml += `<img src="${img.url}" class="w-12 h-12 rounded-md object-cover border border-slate-200 shadow-sm">`;
                    });
                    colorsHtml += '</div>';
                }
                // Video badges
                if (col.videos && col.videos.length > 0) {
                    colorsHtml += '<div class="flex flex-wrap gap-1 mt-1">';
                    col.videos.forEach(vid => {
                        colorsHtml += `<span class="inline-flex items-center gap-1 bg-violet-50 text-violet-700 text-[10px] px-2 py-0.5 rounded border border-violet-200">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                            فيديو
                        </span>`;
                    });
                    colorsHtml += '</div>';
                }
                
                colorsHtml += '</div>';
            });
            colorsHtml += '</div>';
        }

        d.innerHTML = `
            <div class="flex items-start gap-4 w-full">
                <div class="flex-shrink-0 w-12 h-12 sm:w-14 sm:h-14 bg-gradient-to-br from-violet-100 to-purple-100 text-violet-800 rounded-full flex items-center justify-center font-black text-lg sm:text-xl shadow-inner border border-violet-200">
                    ${data.size}
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex flex-wrap gap-2 text-sm text-gray-700">
                        <div class="flex items-center gap-1.5 bg-slate-50 px-2.5 py-1 rounded-lg border border-slate-100 text-xs sm:text-sm"><span class="font-bold text-slate-800">الصدر:</span> ${data.chest || '-'} سم</div>
                        <div class="flex items-center gap-1.5 bg-slate-50 px-2.5 py-1 rounded-lg border border-slate-100 text-xs sm:text-sm"><span class="font-bold text-slate-800">الوزن:</span> ${data.wMin || '-'} - ${data.wMax || '-'} كجم</div>
                        <div class="flex items-center gap-1.5 bg-slate-50 px-2.5 py-1 rounded-lg border border-slate-100 text-xs sm:text-sm"><span class="font-bold text-slate-800">الطول:</span> ${data.hMin || '-'} - ${data.hMax || '-'} سم</div>
                    </div>
                </div>
                <div class="flex-shrink-0 flex items-center gap-1 bg-slate-50 rounded-lg p-1 border border-slate-200">
                    <button type="button" class="text-blue-600 hover:text-blue-800 transition-colors bg-blue-50 hover:bg-blue-100 rounded-md p-1.5" onclick="editSize(this)" title="تعديل المقاس">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                    </button>
                    <button type="button" class="text-red-500 hover:text-red-700 transition-colors bg-red-50 hover:bg-red-100 rounded-md p-1.5" onclick="removeSize(this)" title="حذف المقاس">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    </button>
                </div>
                <input type="hidden" name="available_sizes[${index}][size]" value="${data.size}">
                <input type="hidden" name="available_sizes[${index}][chest_width_cm]" value="${data.chest}">
                <input type="hidden" name="available_sizes[${index}][weight_kg][min]" value="${data.wMin}">
                <input type="hidden" name="available_sizes[${index}][weight_kg][max]" value="${data.wMax}">
                <input type="hidden" name="available_sizes[${index}][height_cm][min]" value="${data.hMin}">
                <input type="hidden" name="available_sizes[${index}][height_cm][max]" value="${data.hMax}">
                ${hiddenInputs}
            </div>
            ${colorsHtml}
        `;
        c.appendChild(d);
    }
    
    function editSize(btn) {
        const card = btn.closest('.border-l-4');
        
        document.getElementById('size-name').value = card.querySelector('input[name$="[size]"]').value;
        document.getElementById('size-chest').value = card.querySelector('input[name$="[chest_width_cm]"]')?.value || '';
        document.getElementById('size-w-min').value = card.querySelector('input[name$="[weight_kg][min]"]')?.value || '';
        document.getElementById('size-w-max').value = card.querySelector('input[name$="[weight_kg][max]"]')?.value || '';
        document.getElementById('size-h-min').value = card.querySelector('input[name$="[height_cm][min]"]')?.value || '';
        document.getElementById('size-h-max').value = card.querySelector('input[name$="[height_cm][max]"]')?.value || '';
        
        // Extract colors with their media
        tempColors = [];
        const colorInputs = card.querySelectorAll('input[name*="[colors]"][name$="[color]"]');
        const stockInputs = card.querySelectorAll('input[name*="[colors]"][name$="[stock]"]');
        
        for (let i = 0; i < colorInputs.length; i++) {
            const colorObj = { color: colorInputs[i].value, stock: stockInputs[i].value, images: [], videos: [] };
            
            // Extract image paths
            const imgInputs = card.querySelectorAll(`input[name*="[colors][${i}][images]"]`);
            imgInputs.forEach(inp => {
                colorObj.images.push({ path: inp.value, url: STORAGE_URL + inp.value });
            });
            
            // Extract video paths
            const vidInputs = card.querySelectorAll(`input[name*="[colors][${i}][videos]"]`);
            vidInputs.forEach(inp => {
                colorObj.videos.push({ path: inp.value, url: STORAGE_URL + inp.value });
            });
            
            tempColors.push(colorObj);
        }
        
        renderTempColors();
        card.remove();
        document.getElementById('size-name').focus();
    }
    
    function removeSize(btn) { btn.closest('.border-l-4').remove(); }

    // ================================================================
    // Form Submit — use native submit (no AJAX)
    // ================================================================
    // No custom handler needed. The form submits natively with all hidden inputs.
    // The controller returns a redirect which the browser follows automatically.
</script>
@endsection
