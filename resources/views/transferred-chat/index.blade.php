@extends('layouts.app')

@section('title', 'دردشة محولة')

@section('content')
<div class="mb-6 sm:mb-8">
    <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">دردشة محولة</h1>
    <p class="mt-2 text-sm text-gray-600">آخر 10 محادثات — اختر محادثة للتبديل بينها (لا تُزال عند التحويل لذكاء اصطناعي)</p>
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
        <div class="flex-1 overflow-y-auto">
            {{-- قسم: إنسان بشري --}}
            @php $humanChats = $chats->filter(fn($c) => $c->conversation_type === 'human'); @endphp
            @if($humanChats->count())
            <div class="px-3 pt-3 pb-1">
                <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-full">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                    إنسان بشري ({{ $humanChats->count() }})
                </span>
            </div>
            <div class="divide-y divide-gray-100">
                @foreach($humanChats as $index => $chat)
                <button type="button"
                        class="chat-tab w-full text-right p-4 hover:bg-slate-50 transition-colors {{ $loop->first ? 'bg-slate-100 border-r-4 border-slate-600' : '' }}"
                        data-chat-index="{{ $index }}">
                    <div class="flex items-center gap-3">
                        <div class="flex-shrink-0 w-9 h-9 rounded-full flex items-center justify-center">
                            @if(Str::startsWith($chat->user_id, 'ig-'))
                            {{-- Instagram Logo --}}
                            <svg class="w-9 h-9" viewBox="0 0 48 48" fill="none"><defs><radialGradient id="ig1_{{ $index }}" cx="19.38" cy="42.04" r="44.9" gradientUnits="userSpaceOnUse"><stop stop-color="#FD5"/><stop offset=".1" stop-color="#FD5"/><stop offset=".5" stop-color="#FF543E"/><stop offset="1" stop-color="#C837AB"/></radialGradient><radialGradient id="ig2_{{ $index }}" cx="11.79" cy="-2.99" r="65.29" gradientUnits="userSpaceOnUse"><stop stop-color="#3771C8"/><stop offset=".13" stop-color="#3771C8"/><stop offset="1" stop-color="#6600FF" stop-opacity="0"/></radialGradient></defs><rect x="2" y="2" width="44" height="44" rx="14" fill="url(#ig1_{{ $index }})"/><rect x="2" y="2" width="44" height="44" rx="14" fill="url(#ig2_{{ $index }})"/><circle cx="24" cy="24" r="9" stroke="#fff" stroke-width="3"/><circle cx="35" cy="13" r="2" fill="#fff"/></svg>
                            @else
                            {{-- Facebook Logo --}}
                            <svg class="w-9 h-9" viewBox="0 0 48 48" fill="none"><circle cx="24" cy="24" r="22" fill="#1877F2"/><path d="M33.1 30.9l1.1-7h-6.7v-4.5c0-1.9.9-3.8 4-3.8h3.1v-6s-2.8-.5-5.5-.5c-5.6 0-9.3 3.4-9.3 9.6v5.3h-6.2v7h6.2V48.3a24.7 24.7 0 007.6 0V30.9h5.7z" fill="#fff"/></svg>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-medium text-gray-900 truncate">{{ $chat->user_name ?? $chat->user_id ?? 'غير معروف' }}</p>
                            <p class="text-xs text-gray-500">{{ $chat->updated_at?->format('Y-m-d H:i') ?? '-' }}</p>
                        </div>
                    </div>
                </button>
                @endforeach
            </div>
            @endif

            {{-- قسم: ذكاء اصطناعي --}}
            @php $aiChats = $chats->filter(fn($c) => $c->conversation_type === 'greeting'); @endphp
            @if($aiChats->count())
            <div class="px-3 pt-3 pb-1">
                <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-blue-700 bg-blue-50 px-2.5 py-1 rounded-full">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                    ذكاء اصطناعي ({{ $aiChats->count() }})
                </span>
            </div>
            <div class="divide-y divide-gray-100">
                @foreach($aiChats as $index => $chat)
                <button type="button"
                        class="chat-tab w-full text-right p-4 hover:bg-slate-50 transition-colors"
                        data-chat-index="{{ $index }}">
                    <div class="flex items-center gap-3">
                        <div class="flex-shrink-0 w-9 h-9 rounded-full flex items-center justify-center">
                            @if(Str::startsWith($chat->user_id, 'ig-'))
                            {{-- Instagram Logo --}}
                            <svg class="w-9 h-9" viewBox="0 0 48 48" fill="none"><defs><radialGradient id="ig1a_{{ $index }}" cx="19.38" cy="42.04" r="44.9" gradientUnits="userSpaceOnUse"><stop stop-color="#FD5"/><stop offset=".1" stop-color="#FD5"/><stop offset=".5" stop-color="#FF543E"/><stop offset="1" stop-color="#C837AB"/></radialGradient><radialGradient id="ig2a_{{ $index }}" cx="11.79" cy="-2.99" r="65.29" gradientUnits="userSpaceOnUse"><stop stop-color="#3771C8"/><stop offset=".13" stop-color="#3771C8"/><stop offset="1" stop-color="#6600FF" stop-opacity="0"/></radialGradient></defs><rect x="2" y="2" width="44" height="44" rx="14" fill="url(#ig1a_{{ $index }})"/><rect x="2" y="2" width="44" height="44" rx="14" fill="url(#ig2a_{{ $index }})"/><circle cx="24" cy="24" r="9" stroke="#fff" stroke-width="3"/><circle cx="35" cy="13" r="2" fill="#fff"/></svg>
                            @else
                            {{-- Facebook Logo --}}
                            <svg class="w-9 h-9" viewBox="0 0 48 48" fill="none"><circle cx="24" cy="24" r="22" fill="#1877F2"/><path d="M33.1 30.9l1.1-7h-6.7v-4.5c0-1.9.9-3.8 4-3.8h3.1v-6s-2.8-.5-5.5-.5c-5.6 0-9.3 3.4-9.3 9.6v5.3h-6.2v7h6.2V48.3a24.7 24.7 0 007.6 0V30.9h5.7z" fill="#fff"/></svg>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-medium text-gray-900 truncate">{{ $chat->user_name ?? $chat->user_id ?? 'غير معروف' }}</p>
                            <p class="text-xs text-gray-500">{{ $chat->updated_at?->format('Y-m-d H:i') ?? '-' }}</p>
                        </div>
                    </div>
                </button>
                @endforeach
            </div>
            @endif
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
                            <div class="flex-shrink-0 w-12 h-12 rounded-full flex items-center justify-center">
                                @if(Str::startsWith($chat->user_id, 'ig-'))
                                {{-- Instagram Logo --}}
                                <svg class="w-12 h-12" viewBox="0 0 48 48" fill="none"><defs><radialGradient id="igp_{{ $index }}" cx="19.38" cy="42.04" r="44.9" gradientUnits="userSpaceOnUse"><stop stop-color="#FD5"/><stop offset=".1" stop-color="#FD5"/><stop offset=".5" stop-color="#FF543E"/><stop offset="1" stop-color="#C837AB"/></radialGradient><radialGradient id="igp2_{{ $index }}" cx="11.79" cy="-2.99" r="65.29" gradientUnits="userSpaceOnUse"><stop stop-color="#3771C8"/><stop offset=".13" stop-color="#3771C8"/><stop offset="1" stop-color="#6600FF" stop-opacity="0"/></radialGradient></defs><rect x="2" y="2" width="44" height="44" rx="14" fill="url(#igp_{{ $index }})"/><rect x="2" y="2" width="44" height="44" rx="14" fill="url(#igp2_{{ $index }})"/><circle cx="24" cy="24" r="9" stroke="#fff" stroke-width="3"/><circle cx="35" cy="13" r="2" fill="#fff"/></svg>
                                @else
                                {{-- Facebook Logo --}}
                                <svg class="w-12 h-12" viewBox="0 0 48 48" fill="none"><circle cx="24" cy="24" r="22" fill="#1877F2"/><path d="M33.1 30.9l1.1-7h-6.7v-4.5c0-1.9.9-3.8 4-3.8h3.1v-6s-2.8-.5-5.5-.5c-5.6 0-9.3 3.4-9.3 9.6v5.3h-6.2v7h6.2V48.3a24.7 24.7 0 007.6 0V30.9h5.7z" fill="#fff"/></svg>
                                @endif
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
            .catch(function() {});
        });
    });
})();
</script>
@endpush
