@extends('layouts.app')

@section('title', 'رسوم المحافظات')

@section('content')
<div class="mb-6 sm:mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div class="min-w-0">
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">رسوم المحافظات</h1>
        <p class="mt-1 sm:mt-2 text-sm text-gray-600">إدارة رسوم الشحن لكل محافظة</p>
    </div>
    <a href="{{ route('governorates.create') }}" class="inline-flex items-center justify-center px-4 py-2.5 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-slate-700 hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-slate-500 transition-colors flex-shrink-0">
        <svg class="ml-2 -mr-1 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
        </svg>
        إضافة محافظة
    </a>
</div>

@if($transferConfigured)
{{-- رصيد الحساب وإرسال مبلغ --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    {{-- بطاقة الرصيد --}}
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden border border-gray-100 ring-1 ring-black/5">
        <div class="px-6 py-5 bg-gradient-to-br from-emerald-50 via-white to-teal-50/30 border-b border-gray-100">
            <div class="flex items-center gap-3">
                <div class="flex items-center justify-center w-10 h-10 rounded-xl bg-emerald-500/10 text-emerald-600">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-base font-semibold text-gray-900">رصيد الحساب</h3>
                    <p class="text-xs text-gray-500 mt-0.5">رصيد أرقام الحساب المرتبطة بـ CashUp</p>
                </div>
            </div>
        </div>
        <div class="p-6">
            <div class="flex items-center justify-between flex-wrap gap-4">
                <div>
                    <p class="text-xs font-medium text-gray-400 uppercase tracking-wider mb-1">الرصيد الإجمالي</p>
                    <p id="accountBalanceDisplay" class="text-3xl font-bold text-gray-900 tracking-tight">{{ number_format((float) $accountBalance, 2) }} <span class="text-lg font-medium text-gray-500">ج.م</span></p>
                    <p id="balanceStatusMessage" class="text-xs text-gray-500 mt-2">{{ $balanceMessage }}</p>
                </div>
                <button type="button" id="refreshBalanceBtn" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-medium text-emerald-700 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200/60 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 transition-all">
                    <span id="refreshBalanceContent" class="inline-flex items-center gap-2">
                        <svg id="refreshIcon" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        <span id="refreshBalanceText">تحديث الرصيد</span>
                    </span>
                    <span id="refreshBalanceLoading" class="hidden" style="display: none;">
                        <span class="inline-flex items-center gap-1">
                            <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            جاري التحديث...
                        </span>
                    </span>
                </button>
            </div>
        </div>
    </div>

    {{-- إرسال مبلغ لأي رقم --}}
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden border border-gray-100 ring-1 ring-black/5">
        <div class="px-6 py-5 bg-gradient-to-br from-slate-50 via-white to-blue-50/30 border-b border-gray-100">
            <div class="flex items-center gap-3">
                <div class="flex items-center justify-center w-10 h-10 rounded-xl bg-slate-700/10 text-slate-700">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-base font-semibold text-gray-900">إرسال مبلغ لأي رقم</h3>
                    <p class="text-xs text-gray-500 mt-0.5">حول مبلغاً من أحد أرقام الحساب إلى أي رقم آخر</p>
                </div>
            </div>
        </div>
        <div class="p-6">
            @if(count($phoneNumbers) > 0)
            <form id="transferForm" class="space-y-5">
                <div>
                    <label for="senderNumber" class="block text-sm font-medium text-gray-700 mb-1.5">الرقم المرسل</label>
                    <select id="senderNumber" name="sender_number" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 bg-gray-50/50 focus:bg-white focus:ring-2 focus:ring-slate-500/20 focus:border-slate-400 text-sm transition-colors" required>
                        <option value="">اختر الرقم المرسل</option>
                        @foreach($phoneNumbers as $num)
                        <option value="{{ $num }}">{{ $num }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="receiverNumber" class="block text-sm font-medium text-gray-700 mb-1.5">الرقم المستقبل</label>
                    <input type="tel" id="receiverNumber" name="receiver_number" placeholder="مثال: 01147544303" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 bg-gray-50/50 focus:bg-white focus:ring-2 focus:ring-slate-500/20 focus:border-slate-400 text-sm transition-colors" required>
                </div>
                <div>
                    <label for="amount" class="block text-sm font-medium text-gray-700 mb-1.5">المبلغ (ج.م)</label>
                    <input type="number" id="amount" name="amount" step="0.01" min="0.01" max="999999.99" placeholder="0.00" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 bg-gray-50/50 focus:bg-white focus:ring-2 focus:ring-slate-500/20 focus:border-slate-400 text-sm transition-colors" required>
                </div>
                <div class="pt-1">
                    <button type="submit" id="transferBtn" class="w-full inline-flex items-center justify-center gap-2 px-4 py-3 rounded-xl text-sm font-semibold text-white bg-slate-700 hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-slate-500 shadow-sm transition-all">
                        <span id="transferContent" class="inline-flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                            </svg>
                            <span id="transferText">إرسال المبلغ</span>
                        </span>
                        <span id="transferLoading" class="hidden" style="display: none;">
                            <span class="inline-flex items-center gap-2">
                                <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                جاري الإرسال...
                            </span>
                        </span>
                    </button>
                </div>
            </form>
            <div id="transferResult" class="mt-5 hidden">
                <div id="transferSuccess" class="flex items-start gap-3 p-4 rounded-xl bg-emerald-50 border border-emerald-200/60 hidden">
                    <div class="flex-shrink-0 w-9 h-9 rounded-full bg-emerald-500/20 flex items-center justify-center">
                        <svg class="w-5 h-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <p id="transferSuccessMessage" class="text-sm font-medium text-emerald-800 leading-relaxed"></p>
                </div>
                <div id="transferError" class="flex items-start gap-3 p-4 rounded-xl bg-red-50 border border-red-200/60 hidden">
                    <div class="flex-shrink-0 w-9 h-9 rounded-full bg-red-500/20 flex items-center justify-center">
                        <svg class="w-5 h-5 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <p id="transferErrorMessage" class="text-sm font-medium text-red-800 leading-relaxed"></p>
                </div>
            </div>
            @else
            <div class="flex items-center gap-3 p-4 rounded-xl bg-amber-50 border border-amber-200/60">
                <svg class="w-5 h-5 text-amber-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <p class="text-sm text-amber-800">لا توجد أرقام مرتبطة بالحساب. تأكد من إعداد CASHUP_TRANSFER_API_KEY وأرقام الحساب في الخدمة.</p>
            </div>
            @endif
        </div>
    </div>
</div>
@endif

<div class="bg-white shadow rounded-lg overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">اسم المحافظة</th>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">رسوم الشحن</th>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">تاريخ الإنشاء</th>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الإجراءات</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($governorates as $governorate)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">{{ $governorate->name }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">{{ number_format($governorate->shipping_fee, 2) }} ج.م</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $governorate->created_at->format('Y-m-d') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <div class="flex items-center space-x-reverse space-x-2">
                            <a href="{{ route('governorates.edit', $governorate) }}" class="text-yellow-600 hover:text-yellow-900 transition-colors">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </a>
                            <form action="{{ route('governorates.destroy', $governorate) }}" method="POST" class="inline" onsubmit="return confirm('هل أنت متأكد من الحذف؟')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900 transition-colors">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-12 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">لا توجد محافظات</h3>
                        <p class="mt-1 text-sm text-gray-500">ابدأ بإضافة محافظة جديدة</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($governorates->hasPages())
    <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
        {{ $governorates->links() }}
    </div>
    @endif
</div>
@endsection

@push('scripts')
@if($transferConfigured)
<script>
document.addEventListener('DOMContentLoaded', function() {
    var refreshBalanceBtn = document.getElementById('refreshBalanceBtn');
    var refreshBalanceContent = document.getElementById('refreshBalanceContent');
    var refreshBalanceLoading = document.getElementById('refreshBalanceLoading');
    var accountBalanceDisplay = document.getElementById('accountBalanceDisplay');
    var balanceStatusMessage = document.getElementById('balanceStatusMessage');

    if (refreshBalanceBtn) {
        refreshBalanceBtn.addEventListener('click', function() {
            refreshBalanceBtn.disabled = true;
            if (refreshBalanceContent) refreshBalanceContent.style.display = 'none';
            if (refreshBalanceLoading) { refreshBalanceLoading.classList.remove('hidden'); refreshBalanceLoading.style.display = ''; }
            fetch('{{ route("governorates.account-balance") }}', {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(function(r) { return r.json(); })
            .then(function(data) {
                if (data.success && data.balance !== undefined) {
                    accountBalanceDisplay.textContent = parseFloat(data.balance).toFixed(2) + ' ج.م';
                }
                if (balanceStatusMessage) balanceStatusMessage.textContent = data.message || '';
            })
            .catch(function() {
                if (balanceStatusMessage) balanceStatusMessage.textContent = 'فشل تحديث الرصيد';
            })
            .finally(function() {
                refreshBalanceBtn.disabled = false;
                if (refreshBalanceContent) refreshBalanceContent.style.display = '';
                if (refreshBalanceLoading) { refreshBalanceLoading.classList.add('hidden'); refreshBalanceLoading.style.display = 'none'; }
            });
        });
    }

    var transferForm = document.getElementById('transferForm');
    var transferBtn = document.getElementById('transferBtn');
    var transferContent = document.getElementById('transferContent');
    var transferLoading = document.getElementById('transferLoading');
    var transferResult = document.getElementById('transferResult');
    var transferSuccess = document.getElementById('transferSuccess');
    var transferError = document.getElementById('transferError');
    var transferSuccessMessage = document.getElementById('transferSuccessMessage');
    var transferErrorMessage = document.getElementById('transferErrorMessage');

    if (transferForm) {
        transferForm.addEventListener('submit', function(e) {
            e.preventDefault();
            var sender = document.getElementById('senderNumber').value;
            var receiver = document.getElementById('receiverNumber').value;
            var amount = document.getElementById('amount').value;
            if (!sender || !receiver || !amount || parseFloat(amount) <= 0) {
                transferErrorMessage.textContent = 'يرجى ملء جميع الحقول بشكل صحيح';
                transferError.classList.remove('hidden');
                transferSuccess.classList.add('hidden');
                transferResult.classList.remove('hidden');
                return;
            }
            transferBtn.disabled = true;
            if (transferContent) transferContent.style.display = 'none';
            if (transferLoading) { transferLoading.classList.remove('hidden'); transferLoading.style.display = ''; }
            transferResult.classList.add('hidden');
            fetch('{{ route("governorates.transfer-money") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    sender_number: sender,
                    receiver_number: receiver,
                    amount: amount
                })
            })
            .then(function(r) { return r.json(); })
            .then(function(data) {
                if (data.success) {
                    var amountFormatted = parseFloat(amount).toFixed(2);
                    var msg = 'تم تحويل مبلغ ' + amountFormatted + ' ج.م بنجاح إلى الرقم ' + receiver + '.';
                    transferSuccessMessage.textContent = msg;
                    transferSuccess.classList.remove('hidden');
                    transferError.classList.add('hidden');
                    transferForm.reset();
                } else {
                    transferErrorMessage.textContent = data.message || 'حدث خطأ في التحويل';
                    transferError.classList.remove('hidden');
                    transferSuccess.classList.add('hidden');
                }
                transferResult.classList.remove('hidden');
            })
            .catch(function() {
                transferErrorMessage.textContent = 'حدث خطأ في الاتصال بالخدمة';
                transferError.classList.remove('hidden');
                transferSuccess.classList.add('hidden');
                transferResult.classList.remove('hidden');
            })
            .finally(function() {
                transferBtn.disabled = false;
                if (transferContent) transferContent.style.display = '';
                if (transferLoading) { transferLoading.classList.add('hidden'); transferLoading.style.display = 'none'; }
            });
        });
    }
});
</script>
@endif
@endpush

