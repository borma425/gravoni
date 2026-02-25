@extends('layouts.app')

@section('title', 'دردشة محولة')

@section('content')
<div class="mb-6 sm:mb-8">
    <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">دردشة محولة</h1>
    <p class="mt-2 text-sm text-gray-600">المحادثات التي تحتاج رد بشري فقط — بعد الرد يمكن تحويلها لذكاء اصطناعي</p>
</div>

<div class="space-y-4">
    @foreach($chats as $chat)
    <div class="chat-card bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-shadow duration-200"
         data-chat-id="{{ $chat->user_id }}">
        <div class="p-5 sm:p-6 flex flex-col sm:flex-row sm:items-center gap-4">
            {{-- اسم المستخدم واسم الوكيل --}}
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-3">
                    <div class="flex-shrink-0 w-11 h-11 rounded-full bg-gradient-to-br from-slate-500 to-slate-700 flex items-center justify-center text-white font-bold text-lg shadow-inner">
                        {{ mb_substr($chat->user_name ?? $chat->user_id, 0, 1) }}
                    </div>
                    <div class="flex flex-col gap-1">
                        <div class="flex items-baseline gap-2">
                            <span class="text-xs font-medium text-gray-400 uppercase tracking-wider">اسم العميل</span>
                            <span class="text-base font-semibold text-gray-900">{{ $chat->user_name ?? $chat->user_id ?? 'غير معروف' }}</span>
                        </div>
                        <div class="flex items-baseline gap-2">
                            <span class="text-xs font-medium text-gray-400 uppercase tracking-wider">تكلم معه : </span>
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
                            data-type="greeting">
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
                            data-type="human">
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
    @endforeach
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
@endif
@endsection

@push('scripts')
<script>
(function() {
    const typeUrl = "{{ url('/admin/transferred-chat') }}";

    document.querySelectorAll('.chat-type-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const chatId = this.dataset.chatId;
            const type = this.dataset.type;
            const card = this.closest('.chat-card');
            const buttons = card.querySelectorAll('.chat-type-btn');

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
                    card.remove();
                }
            })
            .catch(function() {});
        });
    });
})();
</script>
@endpush
