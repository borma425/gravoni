@extends('layouts.app')

@section('title', 'أوردرات تحتاج التأكيد')

@section('content')
<div class="mb-6 sm:mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div class="min-w-0">
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">أوردرات تحتاج التأكيد</h1>
        <p class="mt-1 sm:mt-2 text-sm text-gray-600">إدارة جميع الطلبات</p>
    </div>
    <a href="{{ route('orders.create') }}" class="inline-flex items-center justify-center px-4 py-2.5 flex-shrink-0 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-slate-700 hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-slate-500 transition-colors">
        <svg class="ml-2 -mr-1 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
        </svg>
        إضافة طلب جديد
    </a>
</div>

@if(session('success'))
<div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg text-green-800">{{ session('success') }}</div>
@endif
@if(session('error'))
<div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg text-red-800">{{ session('error') }}</div>
@endif

{{-- Modern Search Box --}}
<div class="mb-6">
    <div class="relative max-w-2xl">
        <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none text-gray-400">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
        </div>
        <input type="text" id="orders-search" value="{{ request('q') }}"
               placeholder="ابحث باسم العميل، العنوان، رقم التتبع، رقم الهاتف..."
               class="w-full py-3.5 pl-4 pr-12 rounded-2xl border border-gray-200 bg-white shadow-sm
                      focus:ring-2 focus:ring-slate-400 focus:border-slate-400 focus:outline-none
                      placeholder-gray-400 text-gray-900 transition-all duration-200">
        <div id="search-spinner" class="absolute inset-y-0 left-4 flex items-center hidden">
            <svg class="animate-spin h-5 w-5 text-slate-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        </div>
    </div>
</div>

<div id="orders-container" class="bg-white shadow rounded-xl overflow-hidden">
    @include('orders.partials.table', ['orders' => $orders])
</div>
@endsection

@push('scripts')
<script>
(function() {
    const container = document.getElementById('orders-container');
    const searchInput = document.getElementById('orders-search');
    const searchSpinner = document.getElementById('search-spinner');
    const baseUrl = "{{ route('orders.index') }}";

    let debounceTimer;
    const DEBOUNCE_MS = 350;

    function buildUrl(q, page) {
        const params = new URLSearchParams();
        if (q) params.set('q', q);
        if (page && page > 1) params.set('page', page);
        const query = params.toString();
        return baseUrl + (query ? '?' + query : '');
    }

    function showLoading(show) {
        searchSpinner.classList.toggle('hidden', !show);
    }

    function fetchOrders(q, page) {
        showLoading(true);
        const url = buildUrl(q, page || 1);
        fetch(url, {
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        })
        .then(r => r.json())
        .then(data => {
            container.innerHTML = data.html;
            bindPagination();
        })
        .catch(err => console.error(err))
        .finally(() => showLoading(false));
    }

    function bindPagination() {
        container.querySelectorAll('.orders-pagination a[href]').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const href = this.getAttribute('href');
                if (!href || href === '#' || this.getAttribute('aria-disabled') === 'true') return;
                showLoading(true);
                fetch(href, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
                })
                .then(r => r.json())
                .then(data => {
                    container.innerHTML = data.html;
                    bindPagination();
                    history.replaceState(null, '', href);
                })
                .catch(err => console.error(err))
                .finally(() => showLoading(false));
            });
        });
    }

    searchInput.addEventListener('input', function() {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => {
            const q = this.value.trim();
            fetchOrders(q, 1);
            const url = buildUrl(q, 1);
            history.replaceState(null, '', url);
        }, DEBOUNCE_MS);
    });

    searchInput.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            this.value = '';
            fetchOrders('', 1);
            history.replaceState(null, '', baseUrl);
        }
    });

    bindPagination();
})();
</script>
@endpush
