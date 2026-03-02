@extends('layouts.app')

@section('title', 'دردشة محولة')

@section('content')
<div class="mb-6 sm:mb-8">
    <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">دردشة محولة</h1>
    <p class="mt-2 text-sm text-gray-600">آخر 10 محادثات محولة — اختر محادثة للتبديل بينها</p>
</div>

@if(count($chats) === 0)
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center">
    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 text-gray-400 mb-4">
        <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
        </svg>
    </div>
    <h3 class="text-lg font-medium text-gray-900">لا توجد محادثات محولة</h3>
    <p class="mt-1 text-sm text-gray-500">ستظهر هنا المحادثات المحولة عند توفرها.</p>
</div>
@else
<div class="flex flex-col lg:flex-row gap-4 lg:gap-6 h-[calc(100vh-12rem)] min-h-[400px]">
    {{-- قائمة آخر 10 محادثات — للتبديل السريع --}}
    <div class="flex-shrink-0 w-full lg:w-80 bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden flex flex-col">
        <div class="p-3 border-b border-gray-100 bg-gray-50/50">
            <h2 class="text-sm font-semibold text-gray-700">المحادثات ({{ count($chats) }})</h2>
        </div>
        <div class="flex-1 overflow-y-auto divide-y divide-gray-100">
            @foreach($chats as $index => $chat)
            <button type="button"
                    class="chat-tab w-full text-right p-4 hover:bg-slate-50 transition-colors {{ $index === 0 ? 'bg-slate-100 border-r-4 border-slate-600' : '' }}"
                    data-chat-index="{{ $index }}">
                <div class="flex items-center gap-3">
                    <div class="flex-shrink-0 w-9 h-9 rounded-full bg-gradient-to-br from-slate-500 to-slate-700 flex items-center justify-center text-white font-bold text-sm">
                        {{ mb_substr($chat->user_name ?? $chat->user_id, 0, 1) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-medium text-gray-900 truncate">{{ $chat->user_name ?? $chat->user_id ?? 'غير معروف' }}</p>
                        <p class="text-xs text-gray-500">{{ $chat->updated_at?->format('Y-m-d H:i') ?? '-' }}</p>
                    </div>
                </div>
            </button>
            @endforeach
        </div>
    </div>

    {{-- محتوى المحادثة المختارة --}}
    <div class="flex-1 min-w-0 bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden flex flex-col">
        @foreach($chats as $index => $chat)
        <div class="chat-panel {{ $index === 0 ? '' : 'hidden' }} flex flex-col h-full" data-panel-index="{{ $index }}">
            <div class="p-5 sm:p-6 flex-1">
                <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                    {{-- اسم المستخدم واسم الوكيل --}}
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-3">
                            <div class="flex-shrink-0 w-12 h-12 rounded-full bg-gradient-to-br from-slate-500 to-slate-700 flex items-center justify-center text-white font-bold text-lg shadow-inner">
                                {{ mb_substr($chat->user_name ?? $chat->user_id, 0, 1) }}
                            </div>
                            <div class="flex flex-col gap-1">
                                <div class="flex items-baseline gap-2">
                                    <span class="text-xs font-medium text-gray-400 uppercase tracking-wider">اسم العميل</span>
                                    <span class="text-base font-semibold text-gray-900">{{ $chat->user_name ?? $chat->user_id ?? 'غير معروف' }}</span>
                                </div>
                                <div class="flex items-baseline gap-2">
                                    <span class="text-xs font-medium text-gray-400 uppercase tracking-wider">تكلم معه:</span>
                                    <span class="text-sm text-gray-700">{{ $chat->agent_name ?? '—' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- نوع المحادثة: تبديل بين ذكاء اصطناعي / إنسان بشري --}}
                    <div class="flex items-center gap-2 flex-shrink-0">
                        <span class="text-sm font-medium text-gray-600 ml-2">نوع المحادثة:</span>
                        <div class="inline-flex rounded-lg bg-gray-100 p-1 gap-0.5" role="group">
                            <button type="button"
                                    class="chat-type-btn px-4 py-2 rounded-md text-sm font-medium transition-all duration-200 {{ $chat->conversation_type === 'greeting' ? 'bg-slate-700 text-white shadow' : 'text-gray-600 hover:bg-gray-200' }}"
                                    data-chat-id="{{ $chat->user_id }}"
                                    data-type="greeting"
                                    data-panel-index="{{ $index }}">
                                <span class="inline-flex items-center gap-1.5">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                    ذكاء اصطناعي
                                </span>
                            </button>
                            <button type="button"
                                    class="chat-type-btn px-4 py-2 rounded-md text-sm font-medium transition-all duration-200 {{ $chat->conversation_type === 'human' ? 'bg-slate-700 text-white shadow' : 'text-gray-600 hover:bg-gray-200' }}"
                                    data-chat-id="{{ $chat->user_id }}"
                                    data-type="human"
                                    data-panel-index="{{ $index }}">
                                <span class="inline-flex items-center gap-1.5">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                    إنسان بشري
                                </span>
                            </button>
                        </div>
                    </div>

                    {{-- تاريخ التحديث --}}
                    <div class="flex-shrink-0">
                        <p class="text-sm text-gray-500">
                            <span class="font-medium text-gray-700">آخر تحديث:</span>
                            {{ $chat->updated_at?->format('Y-m-d H:i') ?? '-' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endif
@endsection

@push('scripts')
<script>
(function() {
    const typeUrl = "{{ url('/admin/transferred-chat') }}";

    // التبديل بين المحادثات
    document.querySelectorAll('.chat-tab').forEach(function(tab) {
        tab.addEventListener('click', function() {
            const idx = this.dataset.chatIndex;

            document.querySelectorAll('.chat-tab').forEach(function(t) {
                t.classList.remove('bg-slate-100', 'border-r-4', 'border-slate-600');
            });
            this.classList.add('bg-slate-100', 'border-r-4', 'border-slate-600');

            document.querySelectorAll('.chat-panel').forEach(function(p) {
                p.classList.toggle('hidden', p.dataset.panelIndex !== idx);
            });
        });
    });

    // تحديث نوع المحادثة
    document.querySelectorAll('.chat-type-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const chatId = this.dataset.chatId;
            const type = this.dataset.type;
            const panelIdx = this.dataset.panelIndex;
            const panel = document.querySelector('.chat-panel[data-panel-index="' + panelIdx + '"]');
            const buttons = panel.querySelectorAll('.chat-type-btn');

            buttons.forEach(function(b) {
                b.classList.remove('bg-slate-700', 'text-white', 'shadow');
                b.classList.add('text-gray-600');
            });
            this.classList.add('bg-slate-700', 'text-white', 'shadow');
            this.classList.remove('text-gray-600');

            fetch(typeUrl + '/' + encodeURIComponent(chatId) + '/type', {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ type: type })
            })
            .then(function(res) { return res.json().then(function(data) { return { ok: res.ok, data: data }; }); })
            .then(function(result) {
                if (result.ok && result.data.success && type === 'greeting') {
                    const tab = document.querySelector('.chat-tab[data-chat-index="' + panelIdx + '"]');
                    if (tab) tab.remove();
                    panel.remove();
                }
            })
            .catch(function() {});
        });
    });
})();
</script>
@endpush
