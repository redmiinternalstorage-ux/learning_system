@extends('admin.layout')

@section('title', 'Learning Curve Internal Team Chat')
@section('page-title', 'Learning Curve Internal Team Chat')

@section('content')
<div class="h-[calc(100vh-7rem)] flex flex-col bg-slate-900 rounded-3xl border border-slate-800 shadow-2xl overflow-hidden text-slate-100">

    <div class="flex-1 flex overflow-hidden">

        {{-- ═══════════════════ LEFT SIDEBAR: CONTACTS LIST (Learning Curve Style) ═══════════════════ --}}
        <div class="w-80 lg:w-96 bg-slate-950/90 border-r border-slate-800/80 flex flex-col flex-shrink-0">

            {{-- Top Header --}}
            <div class="p-4 bg-slate-900/90 border-b border-slate-800 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center text-white font-black text-sm shadow-lg shadow-emerald-500/20">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>
                    <div>
                        <h2 class="font-extrabold text-white text-sm font-outfit leading-tight">{{ Auth::user()->name }}</h2>
                        <span class="inline-flex items-center gap-1.5 text-[10px] font-bold text-emerald-400">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span> Online
                        </span>
                    </div>
                </div>
                <div class="flex items-center gap-2 text-slate-400">
                    <span class="px-2.5 py-1 rounded-full bg-slate-800 text-[10px] font-bold text-slate-300 border border-slate-700 flex items-center gap-1">
                        <i class="fa-solid fa-graduation-cap text-emerald-400"></i> {{ count($teamMembers) }} Staff
                    </span>
                </div>
            </div>

            {{-- Search Bar --}}
            <div class="p-3 bg-slate-950/50 border-b border-slate-800/50">
                <div class="relative">
                    <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-3 text-slate-500 text-xs"></i>
                    <input type="text" id="contact-search" placeholder="Search team members or admin..."
                           onkeyup="filterContacts()"
                           class="w-full pl-9 pr-4 py-2 bg-slate-900 rounded-xl border border-slate-800 text-xs text-slate-200 placeholder-slate-500 focus:outline-none focus:border-emerald-500">
                </div>
            </div>

            {{-- Contacts List --}}
            <div id="contacts-list" class="flex-1 overflow-y-auto divide-y divide-slate-900/60">
                @forelse($teamMembers as $member)
                @php $isActive = $activeUser && $activeUser->id === $member->id; @endphp
                <a href="{{ route('team-chat.index', ['user_id' => $member->id]) }}"
                   class="contact-item flex items-center gap-3.5 p-3.5 transition hover:bg-slate-900/80 {{ $isActive ? 'bg-slate-900 border-l-4 border-emerald-500' : '' }}"
                   data-name="{{ strtolower($member->name) }}" data-role="{{ strtolower($member->role) }}">

                    <div class="relative flex-shrink-0">
                        <div class="w-11 h-11 rounded-2xl bg-gradient-to-br from-slate-700 to-slate-800 border border-slate-700 flex items-center justify-center font-black text-white text-sm shadow-md">
                            {{ strtoupper(substr($member->name, 0, 1)) }}
                        </div>
                        <span class="absolute -bottom-0.5 -right-0.5 w-3.5 h-3.5 rounded-full bg-emerald-500 border-2 border-slate-950" title="Online"></span>
                    </div>

                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between">
                            <h3 class="font-bold text-slate-100 text-xs truncate">{{ $member->name }}</h3>
                            @if($member->last_message)
                            <span class="text-[10px] text-slate-400 font-medium">
                                {{ $member->last_message->created_at->format('h:i A') }}
                            </span>
                            @endif
                        </div>

                        <div class="flex items-center justify-between mt-1">
                            <p class="text-[11px] text-slate-400 truncate max-w-[170px]">
                                @if($member->last_message)
                                    @if($member->last_message->sender_id === Auth::id())
                                        <span class="text-emerald-400">You:</span> {{ $member->last_message->message }}
                                    @else
                                        {{ $member->last_message->message }}
                                    @endif
                                @else
                                    <span class="text-slate-500 italic">No messages yet</span>
                                @endif
                            </p>

                            <div class="flex items-center gap-1.5 flex-shrink-0">
                                @if($member->isAdmin())
                                    <span class="px-1.5 py-0.5 rounded text-[9px] font-black bg-emerald-950 text-emerald-400 border border-emerald-800">Admin</span>
                                @elseif($member->isStaff())
                                    <span class="px-1.5 py-0.5 rounded text-[9px] font-black bg-cyan-950 text-cyan-400 border border-cyan-800">Staff</span>
                                @elseif($member->isFinance())
                                    <span class="px-1.5 py-0.5 rounded text-[9px] font-black bg-amber-950 text-amber-400 border border-amber-800">Finance</span>
                                @endif

                                @if($member->unread_count > 0)
                                    <span class="w-5 h-5 rounded-full bg-emerald-500 text-slate-950 font-black text-[10px] flex items-center justify-center shadow-md animate-bounce">
                                        {{ $member->unread_count }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </a>
                @empty
                <div class="p-8 text-center text-slate-500">
                    <i class="fa-solid fa-users-slash text-2xl mb-2"></i>
                    <p class="text-xs">No other staff members found.</p>
                </div>
                @endforelse
            </div>
        </div>

        {{-- ═══════════════════ RIGHT PANEL: CHAT WINDOW ═══════════════════ --}}
        <div class="flex-1 flex flex-col bg-slate-900/60 relative">

            @if($activeUser)

            {{-- Active Header --}}
            <div class="p-4 bg-slate-950/90 border-b border-slate-800/80 flex items-center justify-between z-10">
                <div class="flex items-center gap-3.5">
                    <div class="relative">
                        <div class="w-10 h-10 rounded-2xl bg-gradient-to-br from-emerald-600 to-teal-700 flex items-center justify-center font-black text-white text-sm shadow-md">
                            {{ strtoupper(substr($activeUser->name, 0, 1)) }}
                        </div>
                        <span class="absolute -bottom-0.5 -right-0.5 w-3 h-3 rounded-full bg-emerald-500 border-2 border-slate-950"></span>
                    </div>
                    <div>
                        <h2 class="font-extrabold text-white text-sm font-outfit leading-tight flex items-center gap-2">
                            {{ $activeUser->name }}
                            @if($activeUser->isAdmin())
                                <span class="px-2 py-0.5 rounded-full text-[9px] font-black bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">Main Admin</span>
                            @elseif($activeUser->isStaff())
                                <span class="px-2 py-0.5 rounded-full text-[9px] font-black bg-cyan-500/20 text-cyan-300 border border-cyan-500/30">Staff Admin</span>
                            @elseif($activeUser->isFinance())
                                <span class="px-2 py-0.5 rounded-full text-[9px] font-black bg-amber-500/20 text-amber-300 border border-amber-500/30">Finance Dept</span>
                            @endif
                        </h2>
                        <p class="text-slate-400 text-[10px] mt-0.5 flex items-center gap-1.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                            Active Now &bull; {{ $activeUser->email }}
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-3 text-slate-400">
                    <div class="hidden sm:flex items-center gap-2 bg-slate-900 border border-slate-800 px-3.5 py-1.5 rounded-xl text-xs font-bold text-emerald-400">
                        <i class="fa-solid fa-graduation-cap text-emerald-400 text-sm"></i> Learning Curve Team Chat
                    </div>
                </div>
            </div>

            {{-- Message Stream Container --}}
            <div id="messages-container" class="flex-1 overflow-y-auto p-4 lg:p-6 space-y-4 bg-slate-950/40" style="background-image: radial-gradient(#1e293b 1px, transparent 1px); background-size: 24px 24px;">

                <div class="text-center my-2">
                    <span class="px-3.5 py-1 rounded-full bg-slate-800/80 border border-slate-700/60 text-slate-400 text-[10px] font-bold">
                        🔒 Learning Curve Encrypted Internal Staff Workspace
                    </span>
                </div>

                @forelse($messages as $msg)
                    @php $isMe = $msg->sender_id === Auth::id(); @endphp

                    <div class="flex flex-col {{ $isMe ? 'items-end' : 'items-start' }} msg-bubble" data-id="{{ $msg->id }}" data-ts="{{ $msg->created_at->valueOf() }}">
                        <div class="max-w-md lg:max-w-xl rounded-2xl p-3.5 shadow-lg text-xs leading-relaxed {{ $isMe ? 'bg-gradient-to-br from-emerald-600 to-teal-700 text-white rounded-tr-none' : 'bg-slate-800 border border-slate-700/80 text-slate-100 rounded-tl-none' }}">
                            @if(!$isMe)
                                <p class="text-[10px] font-extrabold text-emerald-400 mb-1 font-outfit">{{ $activeUser->name }}</p>
                            @endif
                            <p class="whitespace-pre-wrap font-sans text-xs">{!! nl2br(e($msg->message)) !!}</p>
                            <div class="flex items-center justify-end gap-1.5 mt-1.5 text-[9px] {{ $isMe ? 'text-emerald-200/90' : 'text-slate-400' }}">
                                <span>{{ $msg->created_at->format('h:i A') }}</span>
                                @if($isMe)
                                    <i class="fa-solid fa-check-double text-[10px] {{ $msg->is_read ? 'text-cyan-300' : 'text-emerald-200' }}"></i>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div id="no-messages-notice" class="py-16 text-center text-slate-500">
                        <div class="w-16 h-16 rounded-full bg-slate-800/80 border border-slate-700 flex items-center justify-center mx-auto mb-3 text-emerald-400 text-2xl shadow-inner">
                            <i class="fa-solid fa-comments"></i>
                        </div>
                        <h4 class="font-extrabold text-slate-300 text-sm font-outfit mb-1">Start Chatting with {{ $activeUser->name }}</h4>
                        <p class="text-xs text-slate-500 max-w-sm mx-auto">Send an instant message to discuss tasks, student queries, or team updates.</p>
                    </div>
                @endforelse

            </div>

            {{-- Message Input Bar --}}
            <div class="p-3 lg:p-4 bg-slate-950/95 border-t border-slate-800 flex items-end gap-3 z-10">
                <div class="flex-1 bg-slate-900 border border-slate-800 rounded-2xl flex items-center px-4 py-2 focus-within:border-emerald-500 transition">
                    <textarea id="chat-input" rows="1" placeholder="Type a message to {{ $activeUser->name }}..."
                               onkeydown="handleKeyDown(event)"
                               class="w-full bg-transparent text-xs text-slate-100 placeholder-slate-500 focus:outline-none resize-none max-h-32 py-1 leading-relaxed"></textarea>
                </div>
                <button onclick="sendMessage()" id="send-btn"
                        class="w-11 h-11 rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-600 hover:from-emerald-400 hover:to-teal-500 text-slate-950 font-black text-sm flex items-center justify-center shadow-lg shadow-emerald-500/25 transition active:scale-95 flex-shrink-0">
                    <i class="fa-solid fa-paper-plane"></i>
                </button>
            </div>

            @else
            <div class="flex-1 flex items-center justify-center p-8 text-center">
                <div>
                    <div class="w-20 h-20 rounded-3xl bg-slate-800 border border-slate-700 flex items-center justify-center mx-auto mb-4 text-emerald-400 text-3xl shadow-xl">
                        <i class="fa-solid fa-comments"></i>
                    </div>
                    <h3 class="font-extrabold text-white text-base font-outfit mb-1">Select a Staff Member</h3>
                    <p class="text-slate-400 text-xs max-w-sm mx-auto">Choose a main admin or staff member from the left list to open live internal chat.</p>
                </div>
            </div>
            @endif

        </div>

    </div>

</div>

@push('scripts')
@if($activeUser)
<script>
    const activeUserId = {{ $activeUser->id }};
    const sendUrl = "{{ route('team-chat.send', ['user' => $activeUser->id]) }}";
    const pollUrl = "{{ route('team-chat.poll', ['user' => $activeUser->id]) }}";
    const csrfToken = "{{ csrf_token() }}";
    let lastTs = 0;

    // Set initial lastTs
    const bubbles = document.querySelectorAll('.msg-bubble');
    if (bubbles.length > 0) {
        lastTs = parseInt(bubbles[bubbles.length - 1].getAttribute('data-ts')) || 0;
    }

    function scrollToBottom() {
        const container = document.getElementById('messages-container');
        if (container) {
            container.scrollTop = container.scrollHeight;
        }
    }
    scrollToBottom();

    function filterContacts() {
        const q = document.getElementById('contact-search').value.toLowerCase();
        const items = document.querySelectorAll('.contact-item');
        items.forEach(item => {
            const name = item.getAttribute('data-name') || '';
            const role = item.getAttribute('data-role') || '';
            if (name.includes(q) || role.includes(q)) {
                item.style.display = 'flex';
            } else {
                item.style.display = 'none';
            }
        });
    }

    function handleKeyDown(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            sendMessage();
        }
    }

    async function sendMessage() {
        const input = document.getElementById('chat-input');
        const text = input.value.trim();
        if (!text) return;

        input.value = '';
        input.focus();

        const notice = document.getElementById('no-messages-notice');
        if (notice) notice.remove();

        // Append pending message bubble
        const container = document.getElementById('messages-container');
        const tempId = 'temp-' + Date.now();
        const html = `
            <div class="flex flex-col items-end msg-bubble" id="${tempId}">
                <div class="max-w-md lg:max-w-xl rounded-2xl p-3.5 shadow-lg text-xs leading-relaxed bg-gradient-to-br from-emerald-600 to-teal-700 text-white rounded-tr-none opacity-80">
                    <p class="whitespace-pre-wrap font-sans text-xs">${escapeHtml(text)}</p>
                    <div class="flex items-center justify-end gap-1.5 mt-1.5 text-[9px] text-emerald-200/90">
                        <span>Just now</span>
                        <i class="fa-solid fa-clock text-[9px] animate-spin"></i>
                    </div>
                </div>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', html);
        scrollToBottom();

        try {
            const res = await fetch(sendUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ message: text })
            });

            const data = await res.json();
            if (data.ok) {
                const el = document.getElementById(tempId);
                if (el) {
                    el.classList.remove('opacity-80');
                    el.querySelector('.fa-clock').className = 'fa-solid fa-check-double text-[10px] text-emerald-200';
                    el.setAttribute('data-ts', data.message.ts);
                    lastTs = Math.max(lastTs, data.message.ts);
                }
            }
        } catch (err) {
            console.error('Failed to send:', err);
        }
    }

    function escapeHtml(str) {
        return str.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;").replace(/'/g, "&#039;");
    }

    async function pollMessages() {
        try {
            const res = await fetch(`${pollUrl}?since=${lastTs}`);
            const data = await res.json();
            if (data.messages && data.messages.length > 0) {
                const notice = document.getElementById('no-messages-notice');
                if (notice) notice.remove();

                const container = document.getElementById('messages-container');
                data.messages.forEach(m => {
                    lastTs = Math.max(lastTs, m.ts);
                    const html = `
                        <div class="flex flex-col items-start msg-bubble" data-id="${m.id}" data-ts="${m.ts}">
                            <div class="max-w-md lg:max-w-xl rounded-2xl p-3.5 shadow-lg text-xs leading-relaxed bg-slate-800 border border-slate-700/80 text-slate-100 rounded-tl-none">
                                <p class="text-[10px] font-extrabold text-emerald-400 mb-1 font-outfit">${escapeHtml(m.sender_name)}</p>
                                <p class="whitespace-pre-wrap font-sans text-xs">${m.message}</p>
                                <div class="flex items-center justify-end gap-1.5 mt-1.5 text-[9px] text-slate-400">
                                    <span>${m.time}</span>
                                </div>
                            </div>
                        </div>
                    `;
                    container.insertAdjacentHTML('beforeend', html);
                });
                scrollToBottom();
            }
        } catch (err) {
            // silent fail on poll error
        }
    }

    // Auto poll every 2.5 seconds
    setInterval(pollMessages, 2500);
</script>
@endif
@endpush
@endsection
