@extends('layouts.store')

@section('title', 'إتمام الطلب - جرافوني')

@section('content')
<div class="bg-gray-50 min-h-screen py-8 md:py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-2xl md:text-3xl font-bold text-slate-900 mb-8">إتمام الطلب</h1>

        @if(session('error'))
        <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl text-red-800">
            {{ session('error') }}
        </div>
        @endif

        <form id="checkout-form" action="{{ route('store.checkout.place') }}" method="POST" class="space-y-8">
            @csrf

            <div class="bg-white rounded-2xl p-6 shadow-sm">
                <h2 class="text-lg font-bold text-slate-900 mb-4">معلومات التوصيل</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">الاسم الكامل *</label>
                        <input type="text" name="customer_name" value="{{ old('customer_name') }}" required class="w-full border border-gray-200 rounded-xl px-4 py-3">
                        @error('customer_name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">رقم الهاتف *</label>
                        <input type="tel" name="customer_phone" value="{{ old('customer_phone') }}" required class="w-full border border-gray-200 rounded-xl px-4 py-3">
                        @error('customer_phone')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-slate-700 mb-2">عنوان التوصيل *</label>
                        <textarea name="customer_address" rows="3" required class="w-full border border-gray-200 rounded-xl px-4 py-3">{{ old('customer_address') }}</textarea>
                        @error('customer_address')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">المحافظة @if($cashupEnabled ?? false)<span class="text-red-500">*</span>@endif</label>
                        <select name="governorate_id" id="delivery-fees" class="w-full border border-gray-200 rounded-xl px-4 py-3" @if($cashupEnabled ?? false) required @endif>
                            <option value="">اختر المحافظة</option>
                            @foreach($governorates as $gov)
                            <option value="{{ $gov->id }}" data-fee="{{ $gov->shipping_fee }}">{{ $gov->name }} - {{ number_format($gov->shipping_fee, 0) }} ج.م</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl p-6 shadow-sm">
                <h2 class="text-lg font-bold text-slate-900 mb-4">ملخص الطلب</h2>
                @foreach($items as $item)
                <div class="flex justify-between py-2"><span>{{ $item['product']->name }} ({{ $item['quantity'] }}x)</span><span>{{ number_format($item['row_total'], 0) }} ج.م</span></div>
                @endforeach
                <div class="border-t mt-4 pt-4 flex justify-between"><span>رسوم التوصيل</span><span id="delivery-display">0 ج.م</span></div>
                <div class="border-t mt-2 pt-4 flex justify-between text-lg font-bold"><span>ستدفع عند الاستلام فقط المبلغ هذا</span><span id="total-display">{{ number_format($subtotal, 0) }} ج.م</span></div>
                <input type="hidden" name="total_amount" id="total-amount" value="{{ $subtotal }}">
            </div>

            @if($cashupEnabled ?? false)
            {{-- صندوق الدفع: يظهر بعد اختيار المحافظة، إنشاء تلقائي --}}
            <div id="cashup-section" class="hidden relative overflow-hidden rounded-2xl shadow-xl border-2 border-emerald-300 bg-gradient-to-br from-emerald-50 via-white to-teal-50">
                <div class="absolute top-0 right-0 w-32 h-32 bg-emerald-200/30 rounded-bl-full"></div>
                <div class="absolute bottom-0 left-0 w-24 h-24 bg-teal-200/20 rounded-tr-full"></div>
                <div class="relative p-6 md:p-8">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-12 h-12 rounded-xl bg-emerald-500 flex items-center justify-center text-white shadow-lg">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2h-2m-4-1V9a2 2 0 012-2h2a2 2 0 012 2v1m0 13a2 2 0 01-2 2h-2a2 2 0 01-2-2v-4a2 2 0 012-2h2a2 2 0 012 2v4"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-xl font-bold text-slate-900">دفع رسوم التوصيل — <span id="cashup-amount-title" class="text-emerald-700">--</span> ج.م</h2>
                            <p class="text-sm text-slate-600">إجراء إجباري لتأكيد الأوردر</p>
                            <p class="text-sm text-slate-600 mt-1">المحافظة: <span id="cashup-gov-name" class="font-semibold text-emerald-700">--</span> — المبلغ: <span id="cashup-amount-display" class="font-bold text-slate-900">--</span> ج.م</p>
                        </div>
                    </div>

                    <div id="cashup-loading" class="hidden py-8 text-center">
                        <div class="inline-flex items-center gap-2 text-emerald-600">
                            <svg class="animate-spin w-6 h-6" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span>جاري التحميل...</span>
                        </div>
                    </div>

                    <div id="cashup-retry" class="hidden py-6 text-center">
                        <div class="bg-amber-50 border-2 border-amber-200 rounded-xl p-6 max-w-md mx-auto">
                            <p class="text-amber-800 font-semibold mb-2">هناك مشكلة في الاتصال بالخادم</p>
                            <p class="text-amber-700 text-sm mb-4">جاري محاولة مرة أخرى خلال <span id="cashup-countdown" class="font-bold text-amber-900 text-lg">50</span> ثانية</p>
                            <div class="w-full bg-amber-200 rounded-full h-2 overflow-hidden">
                                <div id="cashup-countdown-bar" class="h-full bg-amber-500 transition-all duration-1000" style="width: 100%"></div>
                            </div>
                        </div>
                    </div>

                    <div id="cashup-step2" class="space-y-5 hidden">
                        <div class="bg-white/80 backdrop-blur rounded-xl p-5 border-2 border-emerald-200 shadow-inner">
                            <p class="text-sm text-slate-600 mb-2">حوّل المبلغ التالي إلى:</p>
                            <p id="cashup-receiver-number" class="text-2xl md:text-3xl font-mono font-bold text-emerald-700 tracking-wider bg-emerald-100/50 py-3 px-4 rounded-lg text-center">--</p>
                            <p class="text-xs text-slate-500 mt-2">محفظة إلكترونية أو InstaPay من البنك للمحفظة</p>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-3">طريقة الدفع</label>
                            <div class="grid grid-cols-2 gap-3">
                                <label class="flex items-center gap-3 p-4 rounded-xl border-2 border-gray-200 cursor-pointer hover:border-emerald-400 hover:bg-emerald-50/50 transition-all has-[:checked]:border-emerald-500 has-[:checked]:bg-emerald-50">
                                    <input type="radio" name="cashup_payment_method" value="wallet" class="w-5 h-5 text-emerald-600">
                                    <span class="font-medium">محفظة إلكترونية</span>
                                </label>
                                <label class="flex items-center gap-3 p-4 rounded-xl border-2 border-gray-200 cursor-pointer hover:border-emerald-400 hover:bg-emerald-50/50 transition-all has-[:checked]:border-emerald-500 has-[:checked]:bg-emerald-50">
                                    <input type="radio" name="cashup_payment_method" value="instapay" class="w-5 h-5 text-emerald-600">
                                    <span class="font-medium">InstaPay</span>
                                </label>
                            </div>
                        </div>
                        <div id="cashup-instapay-warning" class="hidden p-4 bg-amber-50 border border-amber-200 rounded-xl text-amber-800 text-sm leading-relaxed">
                            <strong>خلي بالك:</strong> إن ده رقم محفظة مش حساب انستا باي بنكي. بعد ما تحول، اكتب اسمك ثنائي أو ثلاثي كما يظهر في التطبيق، أو ارفع صورة التحويل يظهر فيها اسمك.
                        </div>
                        <div id="cashup-wallet-input" class="hidden">
                            <label class="block text-sm font-medium text-slate-700 mb-2">رقم الهاتف اللي حولت منه</label>
                            <input type="tel" id="cashup-sender-phone" class="w-full border-2 border-gray-200 rounded-xl px-4 py-3 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200" placeholder="01xxxxxxxxx" maxlength="11">
                        </div>
                        <div id="cashup-instapay-input" class="hidden space-y-4">
                            <p class="text-sm text-slate-600">اكتب اسمك ثنائي أو ثلاثي كما في تطبيق البنك، أو ارفع صورة التحويل يظهر فيها اسمك</p>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">الاسم ثنائي أو ثلاثي كما في التطبيق</label>
                                <input type="text" id="cashup-sender-name" class="w-full border-2 border-gray-200 rounded-xl px-4 py-3 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200" placeholder="مثال: أحمد محمد علي">
                            </div>
                            <div class="flex items-center gap-2 text-slate-600 text-sm">— أو —</div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">رفع صورة التحويل</label>
                                <input type="file" id="cashup-transfer-image" accept="image/jpeg,image/jpg,image/png,image/gif,image/webp" class="hidden">
                                <label for="cashup-transfer-image" id="cashup-upload-zone" class="flex flex-col items-center justify-center gap-3 p-6 border-2 border-dashed border-gray-300 rounded-xl cursor-pointer hover:border-emerald-400 hover:bg-emerald-50/30 transition-all duration-200 min-h-[140px]">
                                    <div class="w-12 h-12 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-600">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                    <div id="cashup-upload-prompt" class="text-center">
                                        <span class="text-slate-700 font-medium">اضغط لاختيار صورة التحويل</span>
                                        <p class="text-xs text-slate-500 mt-1">أو اسحب الصورة وأفلتها هنا</p>
                                    </div>
                                    <span id="cashup-image-status" class="text-sm font-semibold text-emerald-600 hidden">تم الرفع بنجاح ✓</span>
                                </label>
                                <p class="text-xs text-slate-500 mt-2">jpeg, png, gif, webp — حتى 5 ميجا</p>
                            </div>
                        </div>
                        <button type="button" id="cashup-validate-btn" class="w-full py-4 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-bold text-lg shadow-lg hover:shadow-xl transition-all flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            تم التحويل — تأكيد الدفع
                        </button>
                    </div>

                    <div id="cashup-verified" class="hidden p-5 rounded-xl bg-emerald-100 border-2 border-emerald-300 flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-emerald-500 flex items-center justify-center text-white flex-shrink-0">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <p class="text-emerald-800 font-semibold">تم التحقق من الدفع. يمكنك تأكيد الطلب الآن.</p>
                    </div>
                    <div id="cashup-message" class="hidden mt-4 p-4 rounded-xl"></div>
                </div>
                <input type="hidden" name="cashup_payment_intent_id" id="cashup-payment-intent-id" value="">
                <input type="hidden" name="cashup_sender_identifier" id="cashup-sender-identifier" value="">
            </div>
            @endif

            <div class="flex gap-4">
                <a href="{{ route('store.cart') }}" class="px-6 py-3 border-2 rounded-xl font-semibold text-center">العودة للسلة</a>
                <button type="submit" id="confirm-order-btn" class="flex-1 bg-slate-900 text-white py-4 rounded-xl font-bold">
                    @if($cashupEnabled ?? false)
                    تأكيد الطلب
                    @else
                    تأكيد الطلب - الدفع عند الاستلام
                    @endif
                </button>
            </div>
        </form>
    </div>
</div>
<script>
    document.getElementById('delivery-fees').addEventListener('change', function() {
    var opt = this.options[this.selectedIndex];
    var d = parseFloat(opt.dataset.fee || 0) || 0, s = {{ $subtotal }};
    document.getElementById('delivery-display').textContent = d.toFixed(0) + ' ج.م';
    document.getElementById('total-display').textContent = s.toFixed(0) + ' ج.م';
    document.getElementById('total-amount').value = s + d;
});

@if($cashupEnabled ?? false)
(function() {
    document.getElementById('checkout-form')?.addEventListener('submit', function(e) {
        const fee = document.getElementById('delivery-fees')?.options[document.getElementById('delivery-fees')?.selectedIndex]?.dataset?.fee;
        const d = parseFloat(fee || 0) || 0;
        const isVerified = document.getElementById('cashup-verified') && !document.getElementById('cashup-verified').classList.contains('hidden');
        if (d > 0 && !isVerified) {
            e.preventDefault();
            const msg = document.getElementById('cashup-message');
            if (msg) {
                msg.className = 'mt-4 p-4 rounded-xl bg-red-100 text-red-800';
                msg.textContent = 'يجب التحقق من دفع رسوم التوصيل فعلياً قبل تأكيد الطلب.';
                msg.classList.remove('hidden');
            }
            return false;
        }
    });
    const loadingEl = document.getElementById('cashup-loading');
    const step2 = document.getElementById('cashup-step2');
    const verified = document.getElementById('cashup-verified');
    const confirmBtn = document.getElementById('confirm-order-btn');
    const walletInput = document.getElementById('cashup-wallet-input');
    const instapayInput = document.getElementById('cashup-instapay-input');
    const instapayWarning = document.getElementById('cashup-instapay-warning');
    const msgDiv = document.getElementById('cashup-message');
    const cashupSection = document.getElementById('cashup-section');
    const govSelect = document.getElementById('delivery-fees');

    function getDeliveryFee() {
        const opt = govSelect?.options[govSelect.selectedIndex];
        return parseFloat(opt?.dataset?.fee || 0) || 0;
    }
    function getGovName() {
        const opt = govSelect?.options[govSelect.selectedIndex];
        return opt?.text?.split(' - ')[0] || '--';
    }

    function updateCashUpUI() {
        const fee = getDeliveryFee();
        const isVerified = verified && !verified.classList.contains('hidden');
        if (fee <= 0) {
            cashupSection?.classList.add('hidden');
            confirmBtn.disabled = false;
            confirmBtn?.classList.remove('opacity-50', 'cursor-not-allowed');
        } else {
            cashupSection?.classList.remove('hidden');
            document.getElementById('cashup-amount-display').textContent = fee.toFixed(0);
            const titleEl = document.getElementById('cashup-amount-title');
            if (titleEl) titleEl.textContent = fee.toFixed(0);
            document.getElementById('cashup-gov-name').textContent = getGovName();
            if (!isVerified) {
                confirmBtn.disabled = true;
                confirmBtn?.classList.add('opacity-50', 'cursor-not-allowed');
            }
        }
    }

    let retryTimer = null;
    let retryInterval = null;
    const RETRY_SECONDS = 50;

    function clearRetryTimer() {
        if (retryTimer) clearTimeout(retryTimer);
        if (retryInterval) clearInterval(retryInterval);
        retryTimer = retryInterval = null;
    }

    function startRetryCountdown() {
        loadingEl?.classList.add('hidden');
        step2?.classList.add('hidden');
        const retryEl = document.getElementById('cashup-retry');
        const countEl = document.getElementById('cashup-countdown');
        const barEl = document.getElementById('cashup-countdown-bar');
        if (!retryEl || !countEl || !barEl) return;
        retryEl.classList.remove('hidden');
        let sec = RETRY_SECONDS;
        countEl.textContent = sec;
        barEl.style.width = '100%';
        clearRetryTimer();
        retryInterval = setInterval(function() {
            sec--;
            countEl.textContent = sec;
            barEl.style.width = (sec / RETRY_SECONDS * 100) + '%';
            if (sec <= 0) {
                clearRetryTimer();
                retryEl.classList.add('hidden');
                createPaymentIntent();
            }
        }, 1000);
    }

    function createPaymentIntent() {
        const deliveryFee = getDeliveryFee();
        if (deliveryFee <= 0) return;
        clearRetryTimer();
        document.getElementById('cashup-retry')?.classList.add('hidden');
        loadingEl?.classList.remove('hidden');
        step2?.classList.add('hidden');
        verified?.classList.add('hidden');
        showMsgEl(false);
        fetch('{{ route("store.checkout.cashup.create-intent") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ amount: deliveryFee })
        })
        .then(r => r.json())
        .then(data => {
            loadingEl?.classList.add('hidden');
            if (data.success) {
                sessionStorage.setItem('cashup_payment_intent_id', data.payment_intent_id || '');
                document.getElementById('cashup-receiver-number').textContent = data.receiver_number || '--';
                step2?.classList.remove('hidden');
                showMsgEl(false);
            } else {
                startRetryCountdown();
            }
        })
        .catch(e => {
            startRetryCountdown();
        });
    }

    govSelect?.addEventListener('change', function() {
        clearRetryTimer();
        document.getElementById('cashup-retry')?.classList.add('hidden');
        const fee = getDeliveryFee();
        updateCashUpUI();
        if (fee > 0) createPaymentIntent();
    });
    updateCashUpUI();

    let uploadedImageUrl = null;
    const transferImageInput = document.getElementById('cashup-transfer-image');
    const imageStatusEl = document.getElementById('cashup-image-status');
    const uploadPromptEl = document.getElementById('cashup-upload-prompt');

    function resetUploadZone() {
        uploadedImageUrl = null;
        if (transferImageInput) transferImageInput.value = '';
        imageStatusEl?.classList.add('hidden');
        uploadPromptEl?.classList.remove('hidden');
    }

    document.querySelectorAll('input[name="cashup_payment_method"]').forEach(r => {
        r.addEventListener('change', function() {
            const v = this.value;
            walletInput?.classList.toggle('hidden', v !== 'wallet');
            instapayInput?.classList.toggle('hidden', v !== 'instapay');
            instapayWarning?.classList.toggle('hidden', v !== 'instapay');
            if (v !== 'instapay') resetUploadZone();
        });
    });

    const uploadZone = document.getElementById('cashup-upload-zone');

    function doUpload(file) {
        if (!file || !file.type.startsWith('image/')) return;
        const formData = new FormData();
        formData.append('image', file);
        formData.append('_token', document.querySelector('meta[name="csrf-token"]')?.content || '');
        uploadPromptEl?.classList.add('hidden');
        imageStatusEl?.classList.remove('hidden');
        imageStatusEl.textContent = 'جاري الرفع...';
        imageStatusEl.classList.remove('text-emerald-600', 'text-red-600');
        imageStatusEl.classList.add('text-slate-600');
        fetch('{{ route("store.checkout.cashup.upload-image") }}', {
            method: 'POST',
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            body: formData
        })
        .then(r => r.json())
        .then(data => {
            if (data.success && data.url) {
                uploadedImageUrl = data.url;
                imageStatusEl.textContent = 'تم الرفع بنجاح ✓';
                imageStatusEl.classList.remove('text-slate-600', 'text-red-600');
                imageStatusEl.classList.add('text-emerald-600');
            } else {
                resetUploadZone();
                imageStatusEl.textContent = 'فشل الرفع';
                imageStatusEl.classList.add('text-red-600');
                imageStatusEl.classList.remove('hidden');
                showMsg(data.message || 'فشل رفع الصورة', 'error');
            }
        })
        .catch(() => {
            resetUploadZone();
            imageStatusEl.textContent = 'فشل الرفع';
            imageStatusEl.classList.add('text-red-600');
            imageStatusEl.classList.remove('hidden');
            showMsg('حدث خطأ أثناء رفع الصورة', 'error');
        });
    }

    transferImageInput?.addEventListener('change', function() {
        const file = this.files?.[0];
        if (!file) { resetUploadZone(); return; }
        doUpload(file);
    });

    uploadZone?.addEventListener('dragover', function(e) { e.preventDefault(); this.classList.add('border-emerald-500', 'bg-emerald-50/50'); });
    uploadZone?.addEventListener('dragleave', function(e) { e.preventDefault(); this.classList.remove('border-emerald-500', 'bg-emerald-50/50'); });
    uploadZone?.addEventListener('drop', function(e) {
        e.preventDefault();
        this.classList.remove('border-emerald-500', 'bg-emerald-50/50');
        const file = e.dataTransfer?.files?.[0];
        if (file) doUpload(file);
    });

    function showMsgEl(show) { if (msgDiv) msgDiv.classList.toggle('hidden', !show); }

    document.getElementById('cashup-validate-btn')?.addEventListener('click', function() {
        const method = document.querySelector('input[name="cashup_payment_method"]:checked')?.value;
        let senderId = '';
        if (method === 'wallet') {
            senderId = document.getElementById('cashup-sender-phone').value.trim();
            if (!/^01[0-9]{9}$/.test(senderId)) {
                showMsg('أدخل رقم الهاتف بشكل صحيح (11 رقم يبدأ بـ 01)', 'error');
                return;
            }
        } else if (method === 'instapay') {
            const nameVal = document.getElementById('cashup-sender-name').value.trim();
            if (uploadedImageUrl) {
                senderId = uploadedImageUrl;
            } else if (nameVal && nameVal.length >= 2) {
                senderId = nameVal;
            } else {
                showMsg('اكتب اسمك ثنائي أو ثلاثي كما في التطبيق، أو ارفع صورة التحويل', 'error');
                return;
            }
        } else {
            showMsg('اختر طريقة الدفع وأدخل البيانات', 'error');
            return;
        }
        const paymentIntentId = sessionStorage.getItem('cashup_payment_intent_id') || '';
        if (!paymentIntentId) {
            showMsg('يرجى اختيار المحافظة مرة أخرى وأعد المحاولة.', 'error');
            return;
        }
        const btn = this;
        btn.disabled = true;
        btn.innerHTML = '<svg class="animate-spin inline-block w-5 h-5 ml-1" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> جاري التحقق...';
        fetch('{{ route("store.checkout.cashup.validate") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                payment_intent_id: paymentIntentId,
                sender_identifier: senderId
            })
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                document.getElementById('cashup-payment-intent-id').value = paymentIntentId;
                document.getElementById('cashup-sender-identifier').value = senderId;
                step2.classList.add('hidden');
                verified.classList.remove('hidden');
                confirmBtn.disabled = false;
                confirmBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                showMsg(data.message || 'تم التحقق بنجاح', 'success');
            } else {
                showMsg(data.message || 'فشل التحقق', 'error');
            }
        })
        .catch(e => showMsg('حدث خطأ', 'error'))
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg> تم التحويل — تأكيد الدفع';
        });
    });

    function showMsg(text, type) {
        if (!msgDiv) return;
        msgDiv.className = 'p-4 rounded-xl ' + (type === 'success' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800');
        msgDiv.textContent = text;
        msgDiv.classList.remove('hidden');
    }
})();
@endif
</script>
@endsection
