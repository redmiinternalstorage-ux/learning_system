@extends('admin.layout')
@section('title','Chat — '.$student->name)
@section('page-title','Live Chat')

@push('styles')
<style>
  #chat-box { height: 480px; overflow-y: auto; scroll-behavior: smooth; }
  .bubble-user  { background: rgba(15,23,42,0.08); }
  .dark .bubble-user  { background: rgba(255,255,255,0.07); }
  .bubble-admin { background: linear-gradient(135deg,#7c3aed,#a21caf); }
</style>
@endpush

@section('content')
<div class="max-w-3xl space-y-4">

  {{-- Header bar --}}
  <div class="flex items-center gap-3 flex-wrap">
    <a href="{{ Auth::user()->isStaff() ? route('staff.chat.index') : route('admin.chat.index') }}" class="btn-secondary text-xs px-4 py-2">
      <i class="fa-solid fa-arrow-left"></i> All Chats
    </a>
    <div class="flex items-center gap-3 flex-1">
      <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-emerald-400 to-teal-600 flex items-center justify-center text-white font-bold text-sm">
        {{ strtoupper(substr($student->name, 0, 1)) }}
      </div>
      <div>
        <p class="font-bold text-slate-800 dark:text-slate-100 text-sm">{{ $student->name }}</p>
        <p class="text-[10px] text-slate-400">{{ $student->email }}</p>
      </div>
    </div>

    <span id="status-badge" class="badge {{ $status === 'waiting' ? 'badge-amber' : ($status === 'active' ? 'badge-green' : 'badge-gray') }}">
      {{ ucfirst($status) }}
    </span>

    @if($status !== 'closed')
    <!-- Transfer Chat Button -->
    <button onclick="openTransferModal()" class="flex items-center gap-1.5 px-3 py-2 rounded-xl bg-amber-50 text-amber-700 text-xs font-bold border border-amber-200 hover:bg-amber-100 transition">
      <i class="fa-solid fa-arrow-right-arrow-left"></i> Transfer / Move
    </button>

    <button onclick="closeChat()" class="flex items-center gap-1.5 px-3 py-2 rounded-xl bg-rose-50 dark:bg-rose-950/20 text-rose-600 dark:text-rose-400 text-xs font-bold border border-rose-200 dark:border-rose-900/30 hover:bg-rose-100 transition">
      <i class="fa-solid fa-xmark-circle"></i> Close Chat
    </button>
    @endif
  </div>

  {{-- Transfer Chat Modal --}}
  <div id="transfer-chat-backdrop" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white dark:bg-slate-900 rounded-3xl max-w-md w-full p-6 shadow-2xl border border-slate-200 dark:border-slate-800 relative">
      <button onclick="closeTransferModal()" class="absolute top-5 right-5 text-slate-400 hover:text-slate-600 w-8 h-8 rounded-full flex items-center justify-center bg-slate-100 dark:bg-slate-800">
        <i class="fa-solid fa-xmark text-sm"></i>
      </button>

      <div class="flex items-center gap-3 mb-4">
        <div class="w-10 h-10 rounded-2xl bg-amber-100 text-amber-600 flex items-center justify-center font-bold text-lg">
          <i class="fa-solid fa-arrow-right-arrow-left"></i>
        </div>
        <div>
          <h3 class="font-extrabold text-slate-900 dark:text-white text-base">Move / Transfer Support Chat</h3>
          <p class="text-slate-500 text-[10px]">Reassign conversation with {{ $student->name }}</p>
        </div>
      </div>

      <form method="POST" action="{{ Auth::user()->isStaff() ? route('staff.chat.transfer', $sessionId) : route('admin.chat.transfer', $sessionId) }}" class="space-y-4">
        @csrf
        <div>
          <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Select Target Staff / Admin</label>
          <select name="target_user_id" required class="inp w-full text-xs">
            <option value="">-- Choose Staff Member --</option>
            @foreach($staffList ?? [] as $st)
              <option value="{{ $st->id }}">{{ $st->name }} ({{ ucfirst($st->role) }} - {{ $st->position ?? 'Staff' }})</option>
            @endforeach
          </select>
        </div>

        <div>
          <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Transfer Note / Instructions</label>
          <textarea name="transfer_note" rows="3" placeholder="Reason for transferring chat (e.g. Finance issue, Course inquiry)..."
                    class="inp w-full text-xs"></textarea>
        </div>

        <div class="flex justify-end gap-2 pt-2">
          <button type="button" onclick="closeTransferModal()" class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 bg-slate-100 hover:bg-slate-200">
            Cancel
          </button>
          <button type="submit" class="px-4 py-2 rounded-xl text-xs font-extrabold text-white bg-amber-600 hover:bg-amber-700 shadow-md">
            <i class="fa-solid fa-paper-plane mr-1"></i> Transfer Chat Now
          </button>
        </div>
      </form>
    </div>
  </div>

  {{-- Chat Box --}}
  <div class="glass rounded-2xl overflow-hidden flex flex-col">
    <div id="chat-box" class="p-5 space-y-3 flex-1">
      @foreach($messages as $m)
      <div class="flex {{ $m->is_admin ? 'justify-end' : 'justify-start' }}">
        <div class="max-w-[75%]">
          <p class="text-[9px] font-bold {{ $m->is_admin ? 'text-right text-purple-400' : 'text-left text-slate-400' }} mb-1">
            {{ $m->is_admin ? ($m->admin->name ?? 'Admin') : $student->name }}
            @if($m->assignedTo)
            <span class="text-[8px] font-normal text-amber-500 ml-1">(Assigned to {{ $m->assignedTo->name }})</span>
            @endif
          </p>
          <div class="px-4 py-2.5 rounded-2xl text-sm leading-relaxed {{ $m->is_admin ? 'bubble-admin text-white' : 'bubble-user text-slate-700 dark:text-slate-200' }}
              {{ $m->is_admin ? 'rounded-tr-sm' : 'rounded-tl-sm' }}">
            {{ $m->message }}
          </div>
          <p class="text-[9px] text-slate-400 mt-1 {{ $m->is_admin ? 'text-right' : 'text-left' }}">
            {{ $m->created_at->format('h:i A') }}
          </p>
        </div>
      </div>
      @endforeach
      <div id="anchor"></div>
    </div>

    {{-- Input --}}
    @if($status !== 'closed')
    <div class="px-5 py-4 border-t border-slate-100 dark:border-white/[0.06] flex gap-3">
      <input id="msg-input" type="text" placeholder="Type a reply…"
             class="inp flex-1 text-sm" autocomplete="off"
             onkeydown="if(event.key==='Enter'&&!event.shiftKey){event.preventDefault();sendMsg();}">
      <button onclick="sendMsg()" class="btn-primary px-4">
        <i class="fa-solid fa-paper-plane"></i>
      </button>
    </div>
    @else
    <div class="px-5 py-4 border-t border-slate-100 dark:border-white/[0.06] text-center text-xs text-slate-400">
      <i class="fa-solid fa-lock mr-1"></i> This chat session is closed
    </div>
    @endif
  </div>

</div>
@endsection

@push('scripts')
<script>
  function openTransferModal() {
    document.getElementById('transfer-chat-backdrop').classList.remove('hidden');
  }
  function closeTransferModal() {
    document.getElementById('transfer-chat-backdrop').classList.add('hidden');
  }
</script>

@push('scripts')
<script>
  const sessionId = "{{ $sessionId }}";
  let lastTs = Date.now() - 500; // Start from now to avoid refetching existing messages

  function scrollDown() {
    const box = document.getElementById('chat-box');
    box.scrollTop = box.scrollHeight;
  }
  scrollDown();

  function appendMsg(m) {
    const box = document.getElementById('chat-box');
    const anchor = document.getElementById('anchor');
    const isAdmin = m.is_admin;
    const html = `
      <div class="flex ${isAdmin ? 'justify-end' : 'justify-start'}">
        <div class="max-w-[75%]">
          <p class="text-[9px] font-bold ${isAdmin ? 'text-right text-purple-400' : 'text-left text-slate-400'} mb-1">
            ${isAdmin ? 'You (Admin)' : '{{ $student->name }}'}
          </p>
          <div class="px-4 py-2.5 rounded-2xl text-sm leading-relaxed ${isAdmin ? 'bubble-admin text-white rounded-tr-sm' : 'bubble-user text-slate-700 dark:text-slate-200 rounded-tl-sm'}">
            ${escHtml(m.message)}
          </div>
          <p class="text-[9px] text-slate-400 mt-1 ${isAdmin ? 'text-right' : 'text-left'}">${m.time}</p>
        </div>
      </div>`;
    anchor.insertAdjacentHTML('beforebegin', html);
    scrollDown();
    lastTs = Math.max(lastTs, m.ts);
  }

  function escHtml(t) {
    return t.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
  }

  // Send message
  function sendMsg() {
    const input = document.getElementById('msg-input');
    const msg = input.value.trim();
    if (!msg) return;
    input.value = '';
    fetch(`/admin/chat/${sessionId}/send`, {
      method: 'POST',
      headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}'},
      body: JSON.stringify({ message: msg })
    }).then(r => r.json()).then(d => {
      if (d.ok) poll();
    });
  }

  // Poll for new messages
  function poll() {
    fetch(`/admin/chat/${sessionId}/poll?since=${lastTs}`)
      .then(r => r.json())
      .then(d => {
        d.messages.forEach(m => {
          if (m.ts > lastTs) appendMsg(m);
        });
        if (d.messages.length) lastTs = d.messages[d.messages.length - 1].ts;
      });
  }

  // Close chat
  function closeChat() {
    if (!confirm('Close this chat session?')) return;
    fetch(`/admin/chat/${sessionId}/close`, {
      method: 'POST',
      headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'}
    }).then(() => window.location.reload());
  }

  setInterval(poll, 3000);
</script>
@endpush
