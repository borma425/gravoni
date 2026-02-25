@extends('layouts.app')

@section('title', 'إضافة منتج')

@section('content')
<div class="mb-6 sm:mb-8">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="min-w-0">
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">إضافة منتج جديد</h1>
            <p class="mt-2 text-sm text-gray-600">أضف منتجاً جديداً إلى النظام</p>
        </div>
        <a href="{{ route('products.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
            <svg class="ml-2 -mr-1 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
            العودة
        </a>
    </div>
</div>

<form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    @csrf

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
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required class="mt-1.5 block w-full border border-gray-300 rounded-lg shadow-sm py-2.5 px-3 focus:outline-none focus:ring-2 focus:ring-violet-500 focus:border-violet-500 sm:text-sm transition-colors @error('name') border-red-300 @enderror">
                    @error('name') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="sku" class="block text-sm font-semibold text-gray-700">رمز التخزين (SKU)</label>
                    <input type="text" name="sku" id="sku" value="{{ old('sku') }}" required class="mt-1.5 block w-full border border-gray-300 rounded-lg shadow-sm py-2.5 px-3 focus:outline-none focus:ring-2 focus:ring-violet-500 focus:border-violet-500 sm:text-sm transition-colors @error('sku') border-red-300 @enderror">
                    @error('sku') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="description" class="block text-sm font-semibold text-gray-700">الوصف</label>
                    <textarea name="description" id="description" rows="4" class="mt-1.5 block w-full border border-gray-300 rounded-lg shadow-sm py-2.5 px-3 focus:outline-none focus:ring-2 focus:ring-violet-500 focus:border-violet-500 sm:text-sm transition-colors @error('description') border-red-300 @enderror">{{ old('description') }}</textarea>
                    @error('description') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        <!-- ============================================================ -->
        <!-- 1. COLORS CARD — define colors + media here FIRST -->
        <!-- ============================================================ -->
        <div class="bg-white shadow-sm border border-slate-200 rounded-xl p-6">
            <h2 class="text-lg font-bold text-gray-900 mb-1 flex items-center gap-2">
                <svg class="w-5 h-5 text-fuchsia-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"></path></svg>
                الألوان المتاحة
            </h2>
            <p class="text-xs text-slate-500 mb-4">عرّف الألوان مع صورها وفيديوهاتها أولاً، ثم في قسم الأحجام ستظهر تلقائياً لتحديد المخزون.</p>

            <div class="bg-gradient-to-br from-slate-50 to-gray-50 border border-slate-200 shadow-sm rounded-xl p-5 mb-5">
                <div class="flex flex-wrap sm:flex-nowrap gap-2 items-center mb-3">
                    <input type="text" id="new-color-name" placeholder="اسم اللون (مثال: أحمر)" class="flex-1 min-w-[150px] border border-gray-300 rounded-lg py-2 px-3 text-sm focus:ring-violet-500 focus:border-violet-500 shadow-sm bg-white">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-3">
                    <div>
                        <label class="block text-[11px] font-bold text-slate-600 mb-1">صور هذا اللون</label>
                        <div class="relative border-2 border-dashed border-gray-300 rounded-lg p-3 text-center hover:border-violet-400 hover:bg-violet-50/30 transition-colors bg-slate-50 cursor-pointer">
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

                <div id="color-upload-progress" class="hidden mb-3 bg-slate-100 p-3 rounded-lg border border-slate-200">
                    <div class="flex justify-between text-xs font-bold text-slate-700 mb-1">
                        <span id="color-upload-label">جاري الرفع...</span>
                        <span id="color-upload-text" class="text-violet-600">0%</span>
                    </div>
                    <div class="w-full bg-slate-200 rounded-full h-2 overflow-hidden">
                        <div id="color-upload-bar" class="bg-gradient-to-r from-violet-500 to-fuchsia-500 h-full rounded-full transition-all duration-300" style="width: 0%"></div>
                    </div>
                </div>

                <button type="button" id="btn-add-color" onclick="addColorCard()" class="w-full flex items-center justify-center gap-1.5 px-4 py-2 bg-violet-600 text-white rounded-lg text-sm font-bold hover:bg-violet-700 shadow-sm transition-all focus:ring-2 focus:ring-offset-2 focus:ring-violet-500">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                    <span id="btn-add-color-text">إضافة هذا اللون</span>
                </button>
            </div>

            <div id="colors-container" class="flex flex-col gap-3 min-h-[44px]"></div>
            @error('available_colors') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <!-- ============================================================ -->
        <!-- 2. SIZES CARD — auto-reads colors from above -->
        <!-- ============================================================ -->
        <div class="bg-white shadow-sm border border-slate-200 rounded-xl p-6">
            <h2 class="text-lg font-bold text-gray-900 mb-1 flex items-center gap-2">
                <svg class="w-5 h-5 text-violet-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                الأحجام المتاحة
            </h2>
            <p class="text-xs text-slate-500 mb-4">أضف المقاسات وحدد مخزون كل لون لكل مقاس. الألوان تظهر تلقائياً من القسم أعلاه.</p>

            <div class="bg-gradient-to-br from-slate-50 to-gray-50 border border-slate-200 shadow-sm rounded-xl p-5 mb-5">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5 mb-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">المقاس (مثال: M)</label>
                        <input type="text" id="size-name" placeholder="أدخل المقاس.." class="block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 text-sm focus:ring-violet-500 focus:border-violet-500 bg-white">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">عرض الصدر (سم)</label>
                        <input type="number" id="size-chest" min="0" placeholder="مثال: 50" class="block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 text-sm focus:ring-violet-500 focus:border-violet-500 bg-white">
                    </div>
                    <div class="sm:col-span-2 lg:col-span-1">
                        <label class="block text-xs font-bold text-slate-700 mb-1.5 text-center">الوزن (كجم)</label>
                        <div class="flex items-center gap-2">
                            <input type="number" id="size-w-min" placeholder="أدنى" min="0" class="block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-2 text-sm text-center focus:ring-violet-500 focus:border-violet-500 bg-white">
                            <span class="text-slate-400 font-medium">-</span>
                            <input type="number" id="size-w-max" placeholder="أقصى" min="0" class="block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-2 text-sm text-center focus:ring-violet-500 focus:border-violet-500 bg-white">
                        </div>
                    </div>
                    <div class="sm:col-span-2 lg:col-span-1 border-t sm:border-t-0 pt-4 sm:pt-0">
                        <label class="block text-xs font-bold text-slate-700 mb-1.5 text-center">الطول (سم)</label>
                        <div class="flex items-center gap-2">
                            <input type="number" id="size-h-min" placeholder="من" min="0" class="block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-2 text-sm text-center focus:ring-violet-500 focus:border-violet-500 bg-white">
                            <span class="text-slate-400 font-medium">-</span>
                            <input type="number" id="size-h-max" placeholder="إلى" min="0" class="block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-2 text-sm text-center focus:ring-violet-500 focus:border-violet-500 bg-white">
                        </div>
                    </div>

                    <!-- Smart Color Stocks — auto-populated from colors section -->
                    <div class="sm:col-span-2 lg:col-span-3 border-t pt-4 mt-2 md:mt-0 bg-white/60 p-4 rounded-xl border border-slate-200">
                        <label class="block text-xs font-bold text-slate-800 mb-2">مخزون كل لون لهذا المقاس</label>
                        <div id="size-color-stocks" class="space-y-2">
                            <p class="text-xs text-slate-400 italic" id="no-colors-hint">أضف ألوان أولاً من قسم "الألوان المتاحة" أعلاه</p>
                        </div>
                    </div>
                </div>
                <div class="flex justify-end mt-2 pt-4 border-t border-slate-200/60">
                    <button type="button" id="add-size-btn" class="flex items-center justify-center gap-2 px-6 py-2.5 bg-slate-800 text-white shadow-sm hover:shadow-md rounded-lg hover:bg-slate-900 transition-all text-sm font-bold w-full sm:w-auto">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        اعتماد المقاس
                    </button>
                </div>
            </div>

            <div id="sizes-container" class="flex flex-col gap-4 min-h-[44px]"></div>
            @error('available_sizes') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

    </div>

    <!-- Sidebar -->
    <div class="lg:col-span-1 space-y-6">
        <div class="bg-white shadow-sm border border-slate-200 rounded-xl p-6">
            <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                التسعير
            </h2>
            <div class="space-y-5">
                <div>
                    <label for="selling_price" class="block text-sm font-semibold text-gray-700">السعر الأساسي</label>
                    <div class="mt-1.5 relative rounded-lg shadow-sm">
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none"><span class="text-gray-500 sm:text-sm">EGP</span></div>
                        <input type="number" step="0.01" name="selling_price" id="selling_price" value="{{ old('selling_price') }}" required min="0" dir="ltr" class="block w-full border border-gray-300 rounded-lg py-2.5 pl-3 pr-12 focus:ring-violet-500 focus:border-violet-500 sm:text-sm text-right @error('selling_price') border-red-300 @enderror">
                    </div>
                </div>
                <div>
                    <label for="discounted_price" class="block text-sm font-semibold text-gray-700">سعر التخفيض <span class="text-xs text-gray-400 font-normal">(اختياري)</span></label>
                    <div class="mt-1.5 relative rounded-lg shadow-sm">
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none"><span class="text-gray-500 sm:text-sm">EGP</span></div>
                        <input type="number" step="0.01" name="discounted_price" id="discounted_price" value="{{ old('discounted_price') }}" min="0" dir="ltr" class="block w-full border border-gray-300 rounded-lg py-2.5 pl-3 pr-12 focus:ring-violet-500 focus:border-violet-500 sm:text-sm text-right @error('discounted_price') border-red-300 @enderror">
                    </div>
                </div>
            </div>
        </div>
        <div class="bg-white shadow-sm border border-slate-200 rounded-xl p-6 sticky top-6">
            <h2 class="text-lg font-bold text-gray-900 mb-4">النشر والحفظ</h2>
            <p class="text-sm text-gray-500 mb-6">تأكد من مراجعة كافة بيانات المنتج قبل الحفظ.</p>
            <div class="flex flex-col gap-3">
                <button type="submit" id="submit-btn" class="w-full inline-flex justify-center items-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-bold text-white bg-violet-600 hover:bg-violet-700 transition-all">
                    <svg class="w-5 h-5 ml-2 -mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    حفظ ونشر المنتج
                </button>
                <a href="{{ route('products.index') }}" class="w-full inline-flex justify-center items-center py-3 px-4 border border-gray-300 rounded-lg shadow-sm text-sm font-bold text-gray-700 bg-white hover:bg-gray-50 transition-all">إلغاء والعودة</a>
            </div>
        </div>
    </div>

</form>

<script>
    const UPLOAD_URL = '{{ route("products.upload-media") }}';
    const CSRF_TOKEN = '{{ csrf_token() }}';
    const STORAGE_URL = '{{ asset("storage") }}/';

    // ================================================================
    // COLORS SECTION
    // ================================================================
    let tempColorImages = [];   // [{path, url}, ...]
    let tempColorVideos = [];
    let colorIndexCounter = 0;
    let editingColorCard = null;

    function uploadFile(file, type) {
        return new Promise((resolve, reject) => {
            const fd = new FormData();
            fd.append('file', file);
            fd.append('type', type);
            fd.append('_token', CSRF_TOKEN);
            const xhr = new XMLHttpRequest();
            xhr.open('POST', UPLOAD_URL);
            const progress = document.getElementById('color-upload-progress');
            const bar = document.getElementById('color-upload-bar');
            const text = document.getElementById('color-upload-text');
            const label = document.getElementById('color-upload-label');
            progress.classList.remove('hidden');
            label.textContent = 'جاري رفع: ' + file.name;
            xhr.upload.onprogress = (e) => {
                if (e.lengthComputable) { const pct = Math.round((e.loaded / e.total) * 100); bar.style.width = pct + '%'; text.textContent = pct + '%'; }
            };
            xhr.onload = () => { if (xhr.status >= 200 && xhr.status < 300) resolve(JSON.parse(xhr.responseText)); else reject(new Error('Upload failed: ' + xhr.status)); };
            xhr.onerror = () => reject(new Error('Network error'));
            xhr.send(fd);
        });
    }

    async function uploadFilesSequentially(files, type) {
        const results = [];
        for (const file of files) {
            try { const res = await uploadFile(file, type); if (res.success) results.push({ path: res.path, url: res.url }); } catch (e) { alert('فشل رفع الملف: ' + file.name); console.error(e); }
        }
        document.getElementById('color-upload-progress').classList.add('hidden');
        return results;
    }

    document.getElementById('color-images-input').addEventListener('change', async function() {
        const files = Array.from(this.files || []); if (!files.length) return;
        const uploaded = await uploadFilesSequentially(files, 'image');
        tempColorImages.push(...uploaded); renderTempColorMedia(); this.value = '';
    });

    document.getElementById('color-videos-input').addEventListener('change', async function() {
        const files = Array.from(this.files || []); if (!files.length) return;
        const uploaded = await uploadFilesSequentially(files, 'video');
        tempColorVideos.push(...uploaded); renderTempColorMedia(); this.value = '';
    });

    function renderTempColorMedia() {
        const imgC = document.getElementById('color-images-preview');
        imgC.innerHTML = '';
        tempColorImages.forEach((img, idx) => {
            const el = document.createElement('div'); el.className = 'relative group';
            el.innerHTML = `<img src="${img.url}" class="w-14 h-14 rounded-lg object-cover border border-slate-200 shadow-sm"><button type="button" onclick="tempColorImages.splice(${idx},1); renderTempColorMedia();" class="absolute -top-1.5 -right-1.5 bg-red-500 text-white rounded-full w-4 h-4 text-[10px] flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity shadow">×</button>`;
            imgC.appendChild(el);
        });
        const vidC = document.getElementById('color-videos-preview');
        vidC.innerHTML = '';
        tempColorVideos.forEach((vid, idx) => {
            const el = document.createElement('div'); el.className = 'flex items-center gap-2 bg-slate-100 px-2.5 py-1.5 rounded-lg border border-slate-200 text-xs';
            el.innerHTML = `<svg class="w-4 h-4 text-violet-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg><span class="truncate max-w-[100px] text-slate-700 font-medium">${vid.path.split('/').pop()}</span><button type="button" onclick="tempColorVideos.splice(${idx},1); renderTempColorMedia();" class="text-red-400 hover:text-red-600"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>`;
            vidC.appendChild(el);
        });
    }

    /**
     * Add a finalized color card. Called from the UI button or from loadExisting.
     * @param {object|null} data - if provided, use these values instead of the form inputs
     */
    function addColorCard(data) {
        const name = data ? data.color : document.getElementById('new-color-name').value.trim();
        const images = data ? data.images : [...tempColorImages];
        const videos = data ? data.videos : [...tempColorVideos];
        if (!name) { alert('يرجى إدخال اسم اللون'); return; }

        if (editingColorCard) {
            const oldName = editingColorCard.getAttribute('data-color-name');
            if (oldName !== name) {
                document.querySelectorAll('#sizes-container .border-l-4').forEach(sizeCard => {
                    sizeCard.querySelectorAll('input[name*="[colors]"][name$="[color]"]').forEach(inp => {
                        if (inp.value === oldName) inp.value = name;
                    });
                    sizeCard.querySelectorAll('input[name*="[colors]"][name$="[stock]"]').forEach(inp => {
                        const parent = inp.closest('.inline-flex');
                        if (parent && parent.textContent.includes(oldName)) {
                            parent.innerHTML = parent.innerHTML.replace(oldName, name);
                        }
                    });
                });
            }
            editingColorCard.remove();
            editingColorCard = null;
            document.getElementById('btn-add-color-text').textContent = 'إضافة هذا اللون';
        }

        const idx = colorIndexCounter++;
        const container = document.getElementById('colors-container');
        const card = document.createElement('div');
        card.className = 'border-l-4 border-fuchsia-500 bg-gradient-to-r from-fuchsia-50 to-white rounded-lg p-4 shadow-sm';
        card.setAttribute('data-color-name', name);

        let mediaHtml = '';
        if (images.length) {
            mediaHtml += '<div class="flex flex-wrap gap-1.5 mt-2">';
            images.forEach(img => { mediaHtml += `<img src="${img.url}" class="w-12 h-12 rounded-md object-cover border border-slate-200 shadow-sm">`; });
            mediaHtml += '</div>';
        }
        if (videos.length) {
            mediaHtml += '<div class="flex flex-wrap gap-1 mt-1">';
            videos.forEach(() => { mediaHtml += `<span class="inline-flex items-center gap-1 bg-violet-50 text-violet-700 text-[10px] px-2 py-0.5 rounded border border-violet-200"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg> فيديو</span>`; });
            mediaHtml += '</div>';
        }

        let hidden = `<input type="hidden" name="available_colors[${idx}][color]" value="${name}">`;
        images.forEach((img, i) => { hidden += `<input type="hidden" name="available_colors[${idx}][images][${i}]" value="${img.path}">`; });
        videos.forEach((vid, i) => { hidden += `<input type="hidden" name="available_colors[${idx}][videos][${i}]" value="${vid.path}">`; });

        card.innerHTML = `
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-fuchsia-500"></span>
                    <span class="text-sm font-bold text-slate-800">${name}</span>
                    <span class="text-[10px] text-slate-400">${images.length} صور · ${videos.length} فيديو</span>
                </div>
                <div class="flex items-center gap-1 bg-white/50 rounded-md p-1 border border-slate-100">
                    <button type="button" class="text-blue-500 hover:text-blue-700 bg-blue-50 hover:bg-blue-100 rounded p-1" onclick="editColorCard(this)" title="تعديل"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg></button>
                    <button type="button" class="text-red-400 hover:text-red-600 bg-red-50 hover:bg-red-100 rounded p-1 transition-colors" onclick="removeColorCard(this)" title="حذف">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    </button>
                </div>
            </div>
            ${mediaHtml}
            ${hidden}
        `;
        container.appendChild(card);

        if (!data) {
            document.getElementById('new-color-name').value = '';
            tempColorImages = [];
            tempColorVideos = [];
            renderTempColorMedia();
        }

        // Refresh the size section's color stock inputs
        refreshSizeColorStocks();
    }

    function editColorCard(btn) {
        const card = btn.closest('.border-l-4');
        editingColorCard = card;
        document.getElementById('new-color-name').value = card.getAttribute('data-color-name');
        
        tempColorImages = [];
        card.querySelectorAll('input[name*="[images]"]').forEach(inp => {
            tempColorImages.push({ path: inp.value, url: STORAGE_URL + inp.value });
        });
        
        tempColorVideos = [];
        card.querySelectorAll('input[name*="[videos]"]').forEach(inp => {
            tempColorVideos.push({ path: inp.value, url: STORAGE_URL + inp.value });
        });
        
        renderTempColorMedia();
        card.style.display = 'none';
        document.getElementById('btn-add-color-text').textContent = 'تحديث اللون';
    }

    /**
     * Get all defined color names from the colors container.
     */
    function getDefinedColorNames() {
        const names = [];
        document.querySelectorAll('#colors-container [data-color-name]').forEach(card => {
            names.push(card.getAttribute('data-color-name'));
        });
        return names;
    }

    /**
     * Remove a color card AND cascade-remove that color from all existing size cards.
     */
    function removeColorCard(btn) {
        const card = btn.closest('.border-l-4');
        const colorName = card.getAttribute('data-color-name');
        card.remove();

        // Cascade: remove this color from all size cards
        document.querySelectorAll('#sizes-container .border-l-4').forEach(sizeCard => {
            // Remove hidden inputs for this color
            const colorInputs = sizeCard.querySelectorAll('input[name*="[colors]"][name$="[color]"]');
            colorInputs.forEach((inp, idx) => {
                if (inp.value === colorName) {
                    // Remove the corresponding stock input too
                    const stockInp = sizeCard.querySelector(`input[name*="[colors][${idx}][stock]"]`);
                    if (stockInp) stockInp.remove();
                    inp.remove();
                }
            });

            // Remove the badge from display
            sizeCard.querySelectorAll('.inline-flex').forEach(badge => {
                if (badge.textContent.includes(colorName)) badge.remove();
            });

            // Re-index remaining color hidden inputs
            const remaining = sizeCard.querySelectorAll('input[name*="[colors]"][name$="[color]"]');
            const sizeIndex = sizeCard.querySelector('input[name$="[size]"]')?.name.match(/\[(\d+)\]/)?.[1] || '0';
            remaining.forEach((inp, newIdx) => {
                const oldName = inp.name;
                const stockInp = inp.nextElementSibling || sizeCard.querySelector(`input[name*="[stock]"]`);
                inp.name = `available_sizes[${sizeIndex}][colors][${newIdx}][color]`;
                // Find matching stock input
                const allStocks = sizeCard.querySelectorAll('input[name*="[colors]"][name$="[stock]"]');
                if (allStocks[newIdx]) allStocks[newIdx].name = `available_sizes[${sizeIndex}][colors][${newIdx}][stock]`;
            });
        });

        refreshSizeColorStocks();
    }

    // ================================================================
    // SIZES SECTION — smart-linked to colors
    // ================================================================
    let sizeIndexCounter = 0;

    /**
     * Refresh the stock inputs in the size form based on currently defined colors.
     */
    function refreshSizeColorStocks() {
        const colors = getDefinedColorNames();
        const container = document.getElementById('size-color-stocks');
        const hint = document.getElementById('no-colors-hint');

        if (colors.length === 0) {
            container.innerHTML = '';
            const p = document.createElement('p');
            p.className = 'text-xs text-slate-400 italic';
            p.id = 'no-colors-hint';
            p.textContent = 'أضف ألوان أولاً من قسم "الألوان المتاحة" أعلاه';
            container.appendChild(p);
            return;
        }

        // Preserve current stock values
        const currentStocks = {};
        container.querySelectorAll('[data-stock-color]').forEach(inp => {
            currentStocks[inp.getAttribute('data-stock-color')] = inp.value;
        });

        container.innerHTML = '';
        colors.forEach(colorName => {
            const row = document.createElement('div');
            row.className = 'flex items-center gap-3 bg-slate-50 px-3 py-2 rounded-lg border border-slate-200';
            row.innerHTML = `
                <span class="w-2.5 h-2.5 rounded-full bg-fuchsia-500 flex-shrink-0"></span>
                <span class="text-sm font-bold text-slate-800 flex-1">${colorName}</span>
                <div class="relative w-28">
                    <input type="number" min="0" placeholder="الكمية" data-stock-color="${colorName}" value="${currentStocks[colorName] || ''}" class="w-full border border-gray-300 rounded-lg py-1.5 px-3 text-sm focus:ring-violet-500 focus:border-violet-500 shadow-sm bg-white pr-8">
                    <span class="absolute right-3 top-2 text-slate-400 text-xs font-medium pointer-events-none">ق</span>
                </div>
            `;
            container.appendChild(row);
        });
    }

    document.getElementById('add-size-btn').addEventListener('click', addSizeFromInputs);
    ['size-name','size-chest','size-w-min','size-w-max','size-h-min','size-h-max'].forEach(id => {
        document.getElementById(id).addEventListener('keypress', (e) => { if (e.key === 'Enter') { e.preventDefault(); addSizeFromInputs(); } });
    });

    function addSizeFromInputs() {
        const sizeName = document.getElementById('size-name').value.trim();
        if (!sizeName) { alert('يرجى إدخال اسم المقاس'); return; }

        // Read color stocks from the auto-populated inputs
        const colors = [];
        document.querySelectorAll('#size-color-stocks [data-stock-color]').forEach(inp => {
            const stock = parseInt(inp.value) || 0;
            if (stock > 0 || inp.value.trim() !== '') {
                colors.push({ color: inp.getAttribute('data-stock-color'), stock: stock });
            }
        });

        const data = {
            size: sizeName,
            chest: document.getElementById('size-chest').value.trim(),
            wMin: document.getElementById('size-w-min').value.trim(),
            wMax: document.getElementById('size-w-max').value.trim(),
            hMin: document.getElementById('size-h-min').value.trim(),
            hMax: document.getElementById('size-h-max').value.trim(),
            colors: colors
        };

        addSizeCard(data, sizeIndexCounter++);

        // Clear size inputs + reset stock values
        ['size-name','size-chest','size-w-min','size-w-max','size-h-min','size-h-max'].forEach(id => document.getElementById(id).value = '');
        document.querySelectorAll('#size-color-stocks [data-stock-color]').forEach(inp => inp.value = '');
        document.getElementById('size-name').focus();
    }

    function addSizeCard(data, index) {
        const c = document.getElementById('sizes-container');
        const d = document.createElement('div');
        d.className = 'border-l-4 border-violet-500 bg-gradient-to-r from-violet-50 to-white rounded-lg p-4 shadow-sm';

        let hidden = '';
        hidden += `<input type="hidden" name="available_sizes[${index}][size]" value="${data.size}">`;
        hidden += `<input type="hidden" name="available_sizes[${index}][chest_width_cm]" value="${data.chest}">`;
        hidden += `<input type="hidden" name="available_sizes[${index}][weight_kg][min]" value="${data.wMin}">`;
        hidden += `<input type="hidden" name="available_sizes[${index}][weight_kg][max]" value="${data.wMax}">`;
        hidden += `<input type="hidden" name="available_sizes[${index}][height_cm][min]" value="${data.hMin}">`;
        hidden += `<input type="hidden" name="available_sizes[${index}][height_cm][max]" value="${data.hMax}">`;

        let colorsHtml = '';
        if (data.colors.length) {
            colorsHtml = '<div class="flex flex-wrap gap-2 mt-2">';
            data.colors.forEach((col, cIdx) => {
                hidden += `<input type="hidden" name="available_sizes[${index}][colors][${cIdx}][color]" value="${col.color}">`;
                hidden += `<input type="hidden" name="available_sizes[${index}][colors][${cIdx}][stock]" value="${col.stock}">`;
                colorsHtml += `<span class="inline-flex items-center gap-1 bg-slate-100 px-2 py-1 rounded-lg text-xs border border-slate-200"><span class="w-2 h-2 rounded-full bg-fuchsia-500"></span><span class="font-bold text-slate-800">${col.color}</span><span class="text-slate-500">${col.stock} ق</span></span>`;
            });
            colorsHtml += '</div>';
        }

        d.innerHTML = `
            <div class="flex items-start gap-4 w-full">
                <div class="flex-shrink-0 w-12 h-12 bg-gradient-to-br from-violet-100 to-purple-100 text-violet-800 rounded-full flex items-center justify-center font-black text-lg shadow-inner border border-violet-200">${data.size}</div>
                <div class="flex-1 min-w-0">
                    <div class="flex flex-wrap gap-2 text-sm text-gray-700">
                        <div class="flex items-center gap-1.5 bg-slate-50 px-2.5 py-1 rounded-lg border border-slate-100 text-xs sm:text-sm"><span class="font-bold text-slate-800">الصدر:</span> ${data.chest || '-'} سم</div>
                        <div class="flex items-center gap-1.5 bg-slate-50 px-2.5 py-1 rounded-lg border border-slate-100 text-xs sm:text-sm"><span class="font-bold text-slate-800">الوزن:</span> ${data.wMin || '-'} - ${data.wMax || '-'} كجم</div>
                        <div class="flex items-center gap-1.5 bg-slate-50 px-2.5 py-1 rounded-lg border border-slate-100 text-xs sm:text-sm"><span class="font-bold text-slate-800">الطول:</span> ${data.hMin || '-'} - ${data.hMax || '-'} سم</div>
                    </div>
                </div>
                <div class="flex-shrink-0 flex items-center gap-1 bg-slate-50 rounded-lg p-1 border border-slate-200">
                    <button type="button" class="text-blue-600 hover:text-blue-800 bg-blue-50 hover:bg-blue-100 rounded-md p-1.5" onclick="editSize(this)" title="تعديل"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg></button>
                    <button type="button" class="text-red-500 hover:text-red-700 bg-red-50 hover:bg-red-100 rounded-md p-1.5" onclick="this.closest('.border-l-4').remove()" title="حذف"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg></button>
                </div>
                ${hidden}
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

        // Fill color stocks from card data
        const colorInputs = card.querySelectorAll('input[name*="[colors]"][name$="[color]"]');
        const stockInputs = card.querySelectorAll('input[name*="[colors]"][name$="[stock]"]');
        // Refresh stocks first to get current colors
        refreshSizeColorStocks();
        // Then fill in saved values
        setTimeout(() => {
            for (let i = 0; i < colorInputs.length; i++) {
                const colorName = colorInputs[i].value;
                const stockVal = stockInputs[i].value;
                const stockInp = document.querySelector(`#size-color-stocks [data-stock-color="${colorName}"]`);
                if (stockInp) stockInp.value = stockVal;
            }
        }, 50);

        card.remove();
        document.getElementById('size-name').focus();
    }

    // ================================================================
    // Form Submit — native (no AJAX)
    // ================================================================
</script>
@endsection
