@extends('layouts.app')
@section('title','Live Chat')
@section('page-title','Live Chat Support')

@push('styles')
<style>
#chat-box { height:440px; overflow-y:auto; scroll-behavior:smooth; }
.bubble-me    { background:linear-gradient(135deg,#4f46e5,#7c3aed); color:white; border-radius:1.25rem 1.25rem 0.25rem 1.25rem; }
.bubble-agent { background:white; color:#1e293b; border:1px solid #e2e8f0; border-radius:1.25rem 1.25rem 1.25rem 0.25rem; }
.dark .bubble-agent { background:rgba(255,255,255,.07); color:#e2e8f0; border-color:rgba(255,255,255,.1); }
.typing-dot { width:6px; height:6px; border-radius:50%; background:#94a3b8; animation:typingBounce 1.2s infinite; }
.typing-dot:nth-child(2) { animation-delay:.2s; }
.typing-dot:nth-child(3) { animation-delay:.4s; }
@keyframes typingBounce { 0%,60%,100%{transform:translateY(0)} 30%{transform:translateY(-6px)} }
</style>
@endpush

@section('content')
<div class="max-w-2xl mx-auto">

  {{-- Header Card --}}
  <div class="relative rounded-3xl overflow-hidden p-5 mb-4"
       style="background:linear-gradient(135deg,#0f0035,#1a0060,#0a1a40)">
    <div class="absolute inset-0 pointer-events-none">
      <div class="absolute -top-10 -right-10 w-40 h-40 rounded-full blur-3xl" style="background:rgba(99,102,241,.2)"></div>
    </div>
    <div class="relative z-10 flex items-center justify-between gap-4">
      <div class="flex items-center gap-3">
        <div class="relative">
          <div class="w-11 h-11 rounded-2xl flex items-center justify-center"
               style="background:linear-gradient(135deg,#4f46e5,#7c3aed)">
            <i class="fa-solid fa-headset text-white text-lg"></i>
          </div>
          <span id="agent-dot" class="absolute -bottom-0.5 -right-0.5 w-3 h-3 rounded-full border-2 border-[#0f0035]
              {{ $chatStatus==='active' ? 'bg-emerald-400' : ($chatStatus==='closed' ? 'bg-slate-500' : 'bg-amber-400 animate-pulse') }}"></span>
        </div>
        <div>
          <p class="text-white font-extrabold text-sm font-outfit">Support Agent</p>
          <p id="agent-status" class="text-[10px] font-bold
              {{ $chatStatus==='active' ? 'text-emerald-300' : ($chatStatus==='closed' ? 'text-slate-400' : 'text-amber-300') }}">
            {{ $chatStatus==='active' ? '🟢 Online — Agent Connected' : ($chatStatus==='closed' ? '🔒 Session Ended' : '⏳ Waiting for agent…') }}
          </p>
        </div>
      </div>
      <div class="flex items-center gap-2">
        <a href="{{ route('help.index') }}"
           class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-[10px] font-bold transition"
           style="background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.1);color:rgba(255,255,255,.7)">
          <i class="fa-solid fa-ticket"></i> Tickets
        </a>
        @if($chatStatus !== 'closed')
        <button onclick="endChat()"
                class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-[10px] font-bold transition"
                style="background:rgba(239,68,68,.15);border:1px solid rgba(239,68,68,.25);color:rgba(252,165,165,.9)">
          <i class="fa-solid fa-xmark"></i> End Chat
        </button>
        @endif
      </div>
    </div>

    {{-- Waiting notice --}}
    @if($chatStatus === 'waiting')
    <div class="relative z-10 mt-4 px-4 py-3 rounded-2xl" style="background:rgba(245,158,11,.15);border:1px solid rgba(245,158,11,.25)">
      <p class="text-amber-200 text-xs flex items-center gap-2">
        <i class="fa-solid fa-clock-rotate-left"></i>
        Your message has been received. An agent will connect with you shortly. Average response time: 2–5 minutes.
      </p>
    </div>
    @endif
  </div>

  {{-- Chat Window --}}
  <div class="bg-white dark:bg-slate-900/60 border border-slate-200 dark:border-white/10 rounded-3xl overflow-hidden shadow-sm">
    <div id="chat-box" class="p-5 space-y-4 bg-slate-50/50 dark:bg-slate-950/20">

      @if($messages->isEmpty())
      <div class="flex flex-col items-center justify-center py-12 text-center">
        <div class="w-14 h-14 rounded-2xl bg-indigo-50 dark:bg-indigo-950/20 flex items-center justify-center mb-3">
          <i class="fa-solid fa-comments text-2xl text-indigo-400/50"></i>
        </div>
        <p class="text-slate-500 dark:text-slate-400 text-sm font-semibold">Start the conversation</p>
        <p class="text-slate-400 text-xs mt-1">Type your message below to connect with a support agent</p>
      </div>
      @else
      @foreach($messages as $m)
      <div class="flex {{ !$m->is_admin ? 'justify-end' : 'justify-start' }} gap-2.5 items-end">
        @if($m->is_admin)
        <div class="w-7 h-7 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white text-[10px] font-black flex-shrink-0">A</div>
        @endif
        <div class="max-w-[78%]">
          <div class="px-4 py-2.5 text-sm leading-relaxed {{ !$m->is_admin ? 'bubble-me' : 'bubble-agent' }}">
            {{ $m->message }}
          </div>
          <p class="text-[9px] text-slate-400 mt-1 {{ !$m->is_admin ? 'text-right' : 'text-left' }}">
            {{ $m->created_at->format('h:i A') }}
            @if(!$m->is_admin) <i class="fa-solid fa-check-double text-indigo-400"></i> @endif
          </p>
        </div>
        @if(!$m->is_admin)
        <div class="w-7 h-7 rounded-xl bg-gradient-to-br from-indigo-400 to-indigo-600 flex items-center justify-center text-white text-[10px] font-black flex-shrink-0">
          {{ strtoupper(substr(Auth::user()->name,0,1)) }}
        </div>
        @endif
      </div>
      @endforeach
      @endif
      {{-- Typing indicator --}}
      <div id="typing-indicator" class="hidden flex gap-2.5 items-end">
        <div class="w-7 h-7 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white text-[10px] font-black flex-shrink-0">A</div>
        <div class="bubble-agent px-4 py-3">
          <div class="flex gap-1.5 items-center">
            <div class="typing-dot"></div>
            <div class="typing-dot"></div>
            <div class="typing-dot"></div>
          </div>
        </div>
      </div>
      <div id="anchor"></div>
    </div>

    {{-- Input area --}}
    @if($chatStatus !== 'closed')
    <div class="px-4 py-3.5 border-t border-slate-100 dark:border-white/[0.06] bg-white dark:bg-slate-900/60">
      <div class="flex gap-2.5 items-end">
        <div class="flex-1 relative">
          <textarea id="msg-input" rows="1" placeholder="Type your message…"
                    class="inp resize-none text-sm leading-relaxed pr-2 max-h-32 overflow-y-auto"
                    style="min-height:40px"
                    onkeydown="if(event.key==='Enter'&&!event.shiftKey){event.preventDefault();sendMsg();}"
                    oninput="this.style.height='auto';this.style.height=Math.min(this.scrollHeight,128)+'px'"></textarea>
        </div>
        <button onclick="sendMsg()"
                class="w-10 h-10 rounded-xl flex items-center justify-center text-white flex-shrink-0 transition hover:shadow-lg hover:-translate-y-0.5"
                style="background:linear-gradient(135deg,#4f46e5,#7c3aed)">
          <i class="fa-solid fa-paper-plane text-sm"></i>
        </button>
      </div>
      <p class="text-[9px] text-slate-400 mt-1.5 text-center">Press <kbd class="bg-slate-100 dark:bg-white/10 px-1 rounded text-[9px]">Enter</kbd> to send · <kbd class="bg-slate-100 dark:bg-white/10 px-1 rounded text-[9px]">Shift+Enter</kbd> for new line</p>
    </div>
    @else
    <div class="px-5 py-4 border-t border-slate-100 dark:border-white/5 text-center">
      <p class="text-slate-400 text-xs"><i class="fa-solid fa-lock mr-1"></i> This session has ended.</p>
      <button onclick="newChat()" class="mt-2 text-xs font-bold text-indigo-500 hover:underline">
        Start a new chat →
      </button>
    </div>
    @endif
  </div>

</div>
@endsection

@push('scripts')
<script>
const CSRF = '{{ csrf_token() }}';
let lastTs = Date.now() - 500;
let pollInterval;

function scrollDown(smooth=true) {
  const b = document.getElementById('chat-box');
  b.scrollTo({top:b.scrollHeight, behavior: smooth?'smooth':'instant'});
}
scrollDown(false);

function escHtml(t) {
  return t.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/\n/g,'<br>');
}

function appendMsg(m) {
  const anchor = document.getElementById('anchor');
  const isMe = !m.is_admin;
  const myInitial = '{{ strtoupper(substr(Auth::user()->name,0,1)) }}';
  const html = `
    <div class="flex ${isMe?'justify-end':'justify-start'} gap-2.5 items-end">
      ${m.is_admin?`<div class="w-7 h-7 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white text-[10px] font-black flex-shrink-0">A</div>`:''}
      <div class="max-w-[78%]">
        <div class="px-4 py-2.5 text-sm leading-relaxed ${isMe?'bubble-me':'bubble-agent'}">${escHtml(m.message)}</div>
        <p class="text-[9px] text-slate-400 mt-1 ${isMe?'text-right':'text-left'}">${m.time}${isMe?'<i class="fa-solid fa-check-double text-indigo-400 ml-1"></i>':''}</p>
      </div>
      ${isMe?`<div class="w-7 h-7 rounded-xl bg-gradient-to-br from-indigo-400 to-indigo-600 flex items-center justify-center text-white text-[10px] font-black flex-shrink-0">${myInitial}</div>`:''}
    </div>`;
  anchor.insertAdjacentHTML('beforebegin', html);
  scrollDown();
  lastTs = Math.max(lastTs, m.ts);
}

function setStatus(s) {
  const dot  = document.getElementById('agent-dot');
  const text = document.getElementById('agent-status');
  if (!dot||!text) return;
  if (s==='active') {
    dot.className  = dot.className.replace(/bg-\S+/,'bg-emerald-400').replace('animate-pulse','');
    text.className = text.className.replace(/text-\S+-\d+/g,'text-emerald-300');
    text.textContent = '🟢 Online — Agent Connected';
  } else if (s==='closed') {
    dot.className  = dot.className.replace(/bg-\S+/,'bg-slate-500').replace('animate-pulse','');
    text.className = text.className.replace(/text-\S+-\d+/g,'text-slate-400');
    text.textContent = '🔒 Session Ended';
  } else {
    if (!dot.className.includes('animate-pulse')) dot.className += ' animate-pulse';
    dot.className  = dot.className.replace(/bg-\S+/,'bg-amber-400');
    text.className = text.className.replace(/text-\S+-\d+/g,'text-amber-300');
    text.textContent = '⏳ Waiting for agent…';
  }
}

async function sendMsg() {
  const input = document.getElementById('msg-input');
  const msg = input.value.trim();
  if (!msg) return;
  input.value = '';
  input.style.height = '40px';

  const r = await fetch('{{ route("chat.student.send") }}', {
    method:'POST',
    headers:{'Content-Type':'application/json','X-CSRF-TOKEN':CSRF},
    body: JSON.stringify({message:msg})
  });
  const d = await r.json();
  if (d.ok) poll();
}

async function poll() {
  try {
    const r = await fetch('{{ route("chat.student.poll") }}?since='+lastTs);
    const d = await r.json();
    d.messages.forEach(m => { if(m.ts>lastTs) appendMsg(m); });
    if (d.messages.length) lastTs = d.messages[d.messages.length-1].ts;
    setStatus(d.status);
    if (d.status==='closed') {
      clearInterval(pollInterval);
      location.reload();
    }
  } catch(e) {}
}

async function endChat() {
  if (!confirm('End this chat session?')) return;
  await fetch('{{ route("chat.student.close") }}', {
    method:'POST', headers:{'X-CSRF-TOKEN':CSRF}
  });
  location.reload();
}

async function newChat() {
  await fetch('{{ route("chat.student.close") }}', {
    method:'POST', headers:{'X-CSRF-TOKEN':CSRF}
  });
  location.href = '{{ route("chat.student") }}';
}

pollInterval = setInterval(poll, 3000);
</script>
@endpush
