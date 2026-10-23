@extends('admin.layout')
@section('title', 'Send Mail')
@section('page-title', 'Send Mail')

@push('styles')
<style>
  .recipient-chip {
    display:inline-flex;align-items:center;gap:6px;
    padding:4px 10px 4px 8px;border-radius:999px;
    font-size:11px;font-weight:700;cursor:pointer;
    background:#ecfdf5;border:1.5px solid #a7f3d0;color:#047857;
    transition:all 0.15s;user-select:none;
  }
  .recipient-chip.selected{background:linear-gradient(135deg,#059669,#10b981);border-color:transparent;color:#fff}
  .recipient-chip:hover:not(.selected){background:#d1fae5;border-color:#6ee7b7}
  .char-count{font-size:11px;color:#94a3b8;text-align:right;margin-top:4px}
  .char-count.warn{color:#f59e0b}
  .char-count.over{color:#ef4444}
  .role-btn{cursor:pointer;padding:6px 14px;border-radius:10px;font-size:11px;font-weight:700;border:1.5px solid;transition:all .15s}
  .role-btn:hover{opacity:.85}

  /* Modal send icon spin */
  @keyframes spin-send{from{transform:rotate(0deg) translateX(2px)}to{transform:rotate(360deg) translateX(2px)}}
  .icon-spin{animation:spin-send 1s linear infinite}

  /* Count number pop */
  @keyframes numPop{0%{transform:scale(1)}50%{transform:scale(1.25)}100%{transform:scale(1)}}
  .num-pop{animation:numPop 0.2s ease}
</style>
@endpush

@section('content')
<div class="space-y-6">

  {{-- Hero --}}
  <div class="relative rounded-3xl overflow-hidden p-7 shadow-lg shadow-emerald-900/10"
       style="background:linear-gradient(135deg,#059669 0%,#047857 45%,#065f46 100%)">
    <div class="absolute inset-0 pointer-events-none">
      <div class="absolute top-0 right-0 w-80 h-80 bg-emerald-400/20 rounded-full blur-3xl -translate-y-1/2 translate-x-1/3"></div>
      <div class="absolute bottom-0 left-0 w-60 h-60 bg-teal-400/15 rounded-full blur-3xl translate-y-1/2 -translate-x-1/4"></div>
    </div>
    <div class="relative z-10 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
      <div>
        <p class="text-emerald-200 text-[10px] font-black uppercase tracking-widest mb-2">Admin Panel</p>
        <h2 class="text-white text-2xl font-black font-outfit">📨 Send Direct Mail</h2>
        <p class="text-emerald-100/80 text-sm mt-1.5">Send a custom email directly to one or multiple users</p>
      </div>
      <a href="{{ route('admin.mail-logs') }}"
         class="flex items-center gap-2 px-4 py-2.5 rounded-2xl bg-white/10 hover:bg-white/20 border border-white/20 text-white text-xs font-bold transition">
        <i class="fa-solid fa-clock-rotate-left text-emerald-200"></i> Mail Logs
      </a>
    </div>
  </div>

  <form id="mailForm">
    @csrf
    <div class="grid lg:grid-cols-3 gap-5">

      {{-- LEFT: Recipient Selector --}}
      <div class="lg:col-span-1 space-y-4">
        <div class="glass rounded-2xl p-5">
          <h3 class="font-extrabold text-slate-800 text-sm font-outfit mb-1">
            <i class="fa-solid fa-users text-emerald-600 mr-1.5"></i> Select Recipients
          </h3>
          <p class="text-slate-400 text-[10px] mb-4">Click users or use quick-select buttons</p>

          <div class="flex flex-wrap gap-2 mb-4">
            <button type="button" onclick="selectByRole('all')"    class="role-btn" style="border-color:#059669;color:#059669">All Users</button>
            <button type="button" onclick="selectByRole('student')" class="role-btn" style="border-color:#0d9488;color:#0d9488">Students</button>
            <button type="button" onclick="selectByRole('instructor')" class="role-btn" style="border-color:#4f46e5;color:#4f46e5">Instructors</button>
            <button type="button" onclick="selectByRole('none')"   class="role-btn" style="border-color:#94a3b8;color:#94a3b8">Clear</button>
          </div>

          <input type="text" id="userSearch" placeholder="🔍 Search users…" oninput="filterUsers()" class="inp text-xs mb-3">

          <div id="userList" class="space-y-1.5 max-h-[360px] overflow-y-auto pr-1">
            @foreach($users as $u)
            <div class="user-item flex items-center gap-2.5 p-2.5 rounded-xl cursor-pointer border border-transparent hover:border-emerald-200 hover:bg-emerald-50/50 transition select-none"
                 data-id="{{ $u->id }}" data-role="{{ $u->role }}"
                 data-name="{{ strtolower($u->name) }}" data-email="{{ strtolower($u->email) }}"
                 onclick="toggleUser(this,{{ $u->id }},'{{ addslashes($u->name) }}','{{ $u->role }}')">
              <div class="w-8 h-8 rounded-lg flex items-center justify-center text-white text-xs font-bold font-outfit flex-shrink-0
                @if($u->role==='admin') bg-gradient-to-br from-rose-500 to-pink-600
                @elseif($u->role==='instructor') bg-gradient-to-br from-violet-500 to-purple-600
                @else bg-gradient-to-br from-emerald-500 to-teal-600 @endif">
                {{ strtoupper(substr($u->name,0,1)) }}
              </div>
              <div class="flex-1 min-w-0">
                <p class="text-xs font-bold text-slate-700 truncate">{{ $u->name }}</p>
                <p class="text-[10px] text-slate-400 truncate">{{ $u->email }}</p>
              </div>
              <span class="text-[9px] font-bold uppercase
                @if($u->role==='admin') text-rose-500
                @elseif($u->role==='instructor') text-violet-500
                @else text-emerald-600 @endif">{{ $u->role }}</span>
              <div class="w-4 h-4 rounded-full border-2 border-slate-300 flex-shrink-0 check-circle transition"></div>
            </div>
            @endforeach
          </div>
        </div>

        {{-- Selected chips --}}
        <div class="glass rounded-2xl p-4">
          <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-3">
            <i class="fa-solid fa-check-circle text-emerald-500 mr-1"></i>
            Selected: <span id="selectedCount" class="text-emerald-700 font-black">0</span>
          </p>
          <div id="selectedChips" class="flex flex-wrap gap-1.5 min-h-[32px]">
            <p id="noSelection" class="text-slate-400 text-xs italic">No recipients selected</p>
          </div>
        </div>
      </div>

      {{-- RIGHT: Compose --}}
      <div class="lg:col-span-2 space-y-4">
        <div class="glass rounded-2xl p-6 space-y-5">
          <h3 class="font-extrabold text-slate-800 text-sm font-outfit mb-1">
            <i class="fa-solid fa-pen-to-square text-emerald-600 mr-1.5"></i> Compose Message
          </h3>

          <div>
            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Subject <span class="text-rose-400">*</span></label>
            <input type="text" name="subject" placeholder="e.g. Important Update Regarding Your Account"
              class="inp text-sm" maxlength="150" oninput="updateChar('subject','subjectCount',150)">
            <div class="char-count" id="subjectCount">0 / 150</div>
          </div>

          <div>
            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Message <span class="text-rose-400">*</span></label>
            <textarea name="message" rows="10"
              placeholder="Write your message here…&#10;&#10;You can use multiple paragraphs."
              class="inp text-sm resize-none leading-relaxed" maxlength="5000"
              oninput="updateChar('message','messageCount',5000)"></textarea>
            <div class="char-count" id="messageCount">0 / 5000</div>
          </div>

          <div class="pt-1">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-3">
              <i class="fa-solid fa-link text-emerald-600 mr-1"></i> Optional CTA Button
            </p>
            <div class="grid sm:grid-cols-2 gap-3">
              <div>
                <label class="block text-[10px] text-slate-400 mb-1.5">Button Label</label>
                <input type="text" name="cta_label" placeholder="e.g. Visit Dashboard" maxlength="60" class="inp text-xs">
              </div>
              <div>
                <label class="block text-[10px] text-slate-400 mb-1.5">Button URL</label>
                <input type="url" name="cta_url" placeholder="https://…" maxlength="300" class="inp text-xs">
              </div>
            </div>
          </div>
        </div>

        {{-- Live preview --}}
        <div class="glass rounded-2xl p-5">
          <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-3">
            <i class="fa-solid fa-eye text-emerald-500 mr-1"></i> Live Preview
          </p>
          <div class="rounded-xl border border-slate-200 p-4 bg-slate-50/70 space-y-2">
            <p class="text-[10px] text-slate-400 font-semibold">To: <span id="previewTo" class="text-emerald-700 font-bold">—</span></p>
            <p class="text-[10px] text-slate-400 font-semibold">Subject: <span id="previewSubject" class="text-slate-700 font-bold">—</span></p>
            <div class="border-t border-slate-200 pt-2 mt-2">
              <p id="previewBody" class="text-xs text-slate-500 whitespace-pre-wrap max-h-24 overflow-hidden leading-relaxed italic">Your message will appear here…</p>
            </div>
          </div>
        </div>

        {{-- Submit row --}}
        <div class="flex items-center justify-between gap-4">
          <p class="text-xs text-slate-400">
            <i class="fa-solid fa-info-circle text-emerald-500 mr-1"></i>
            Mail will be logged in Mail Logs after sending.
          </p>
          <div class="flex gap-3">
            <a href="{{ route('admin.mail-logs') }}" class="btn-secondary px-5 py-2.5 text-xs">
              <i class="fa-solid fa-xmark"></i> Cancel
            </a>
            <button type="button" id="sendBtn" onclick="startSend()" class="btn-primary px-6 py-2.5 text-xs" disabled>
              <i class="fa-solid fa-paper-plane"></i>
              Send Mail <span id="recipientCountBtn"></span>
            </button>
          </div>
        </div>
      </div>

    </div>
  </form>
</div>

{{-- ══════════ LIVE PROGRESS MODAL ══════════ --}}
<div id="progressModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 hidden">
  <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-md"></div>
  <div class="relative z-10 w-full max-w-md glass rounded-3xl p-7 shadow-2xl border border-emerald-100">

    {{-- Sending state --}}
    <div id="statesSending">
      <div class="text-center mb-5">
        <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center mx-auto mb-4 shadow-lg shadow-emerald-500/20">
          <i class="fa-solid fa-paper-plane text-white text-xl icon-spin"></i>
        </div>
        <h3 class="font-black text-slate-800 text-lg font-outfit">Sending Mails…</h3>
        <p class="text-slate-400 text-xs mt-1">Please wait, do not close this window</p>
      </div>

      {{-- Big live counter --}}
      <div class="text-center my-4">
        <div class="inline-flex items-baseline gap-3">
          <span id="sentCount" class="text-6xl font-black font-outfit text-emerald-600 tabular-nums leading-none">0</span>
          <span class="text-2xl font-black text-slate-300">of</span>
          <span id="totalCount" class="text-6xl font-black font-outfit text-slate-700 tabular-nums leading-none">0</span>
        </div>
        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-2">Emails Sent</p>
      </div>

      {{-- Progress bar --}}
      <div class="h-3 rounded-full bg-slate-100 overflow-hidden mb-2">
        <div id="progressBar" class="h-full rounded-full bg-gradient-to-r from-emerald-500 to-teal-500 transition-all duration-500" style="width:0%"></div>
      </div>
      <div class="flex justify-between text-[10px] font-bold text-slate-400 mb-4">
        <span id="progressPct">0%</span>
        <span id="progressStatus">Preparing…</span>
      </div>

      {{-- Live feed --}}
      <div id="liveFeed" class="space-y-1.5 max-h-28 overflow-y-auto pr-1 rounded-xl bg-slate-50 p-3"></div>
    </div>

    {{-- Done state --}}
    <div id="statesDone" class="hidden">
      <div class="text-center mb-5">
        <div id="doneIcon" class="w-16 h-16 rounded-2xl flex items-center justify-center mx-auto mb-4 text-2xl text-white shadow-lg"></div>
        <h3 id="doneTitle" class="font-black text-slate-800 text-lg font-outfit"></h3>
        <p id="doneSub" class="text-slate-400 text-xs mt-1 leading-relaxed"></p>
      </div>

      <div class="grid grid-cols-3 gap-3 mb-5">
        <div class="text-center p-3 rounded-2xl bg-emerald-50 border border-emerald-100">
          <p id="doneTotal" class="text-2xl font-black font-outfit text-emerald-700">0</p>
          <p class="text-[9px] font-bold text-slate-400 uppercase tracking-wide mt-0.5">Total</p>
        </div>
        <div class="text-center p-3 rounded-2xl bg-emerald-50 border border-emerald-200">
          <p id="doneSent" class="text-2xl font-black font-outfit text-emerald-600">0</p>
          <p class="text-[9px] font-bold text-slate-400 uppercase tracking-wide mt-0.5">✅ Sent</p>
        </div>
        <div class="text-center p-3 rounded-2xl bg-rose-50 border border-rose-100">
          <p id="doneFailed" class="text-2xl font-black font-outfit text-rose-600">0</p>
          <p class="text-[9px] font-bold text-slate-400 uppercase tracking-wide mt-0.5">❌ Failed</p>
        </div>
      </div>

      <div class="flex gap-3">
        <a href="{{ route('admin.mail-logs') }}" class="flex-1 btn-secondary py-2.5 text-xs text-center">
          <i class="fa-solid fa-clock-rotate-left mr-1"></i> View Logs
        </a>
        <button onclick="closeModal()" class="flex-1 btn-primary py-2.5 text-xs">
          <i class="fa-solid fa-plus mr-1"></i> Send Another
        </button>
      </div>
    </div>

  </div>
</div>

@endsection

@push('scripts')
<script>
const selectedIds   = new Set();
const selectedNames = {};

/* ── Toggle single user ── */
function toggleUser(el, id, name, role) {
  if (selectedIds.has(id)) {
    selectedIds.delete(id);
    delete selectedNames[id];
    el.classList.remove('bg-emerald-50/80','border-emerald-300');
    const cc = el.querySelector('.check-circle');
    cc.style.background = ''; cc.style.borderColor = '';
  } else {
    selectedIds.add(id);
    selectedNames[id] = { name, role };
    el.classList.add('bg-emerald-50/80','border-emerald-300');
    const cc = el.querySelector('.check-circle');
    cc.style.background = 'linear-gradient(135deg,#059669,#10b981)';
    cc.style.borderColor = 'transparent';
  }
  updateUI();
}

/* ── Quick role select ── */
function selectByRole(role) {
  document.querySelectorAll('.user-item').forEach(el => {
    const elRole = el.dataset.role;
    const id     = parseInt(el.dataset.id);
    const name   = el.querySelector('p').textContent.trim();
    const should = role==='all' ? true : role==='none' ? false : (role===elRole);
    if (should) {
      selectedIds.add(id);
      selectedNames[id] = { name, role: elRole };
      el.classList.add('bg-emerald-50/80','border-emerald-300');
      const cc = el.querySelector('.check-circle');
      cc.style.background = 'linear-gradient(135deg,#059669,#10b981)';
      cc.style.borderColor = 'transparent';
    } else {
      selectedIds.delete(id);
      delete selectedNames[id];
      el.classList.remove('bg-emerald-50/80','border-emerald-300');
      const cc = el.querySelector('.check-circle');
      cc.style.background = ''; cc.style.borderColor = '';
    }
  });
  updateUI();
}

/* ── Update UI ── */
function updateUI() {
  const count = selectedIds.size;
  document.getElementById('selectedCount').textContent = count;
  document.getElementById('recipientCountBtn').textContent = count > 0 ? `(${count})` : '';
  document.getElementById('sendBtn').disabled = (count === 0);

  const chipsEl = document.getElementById('selectedChips');
  chipsEl.innerHTML = '';
  if (count === 0) {
    chipsEl.innerHTML = '<p class="text-slate-400 text-xs italic">No recipients selected</p>';
    document.getElementById('previewTo').textContent = '—';
    return;
  }
  Object.entries(selectedNames).slice(0,8).forEach(([id,{name,role}]) => {
    const chip = document.createElement('span');
    chip.className = 'recipient-chip selected';
    chip.innerHTML = `${name} <i class="fa-solid fa-xmark text-[9px]"></i>`;
    chip.onclick = () => {
      const el = document.querySelector(`.user-item[data-id="${id}"]`);
      if (el) toggleUser(el, parseInt(id), name, role);
    };
    chipsEl.appendChild(chip);
  });
  if (count > 8) {
    const m = document.createElement('span');
    m.className = 'recipient-chip';
    m.textContent = `+${count-8} more`;
    chipsEl.appendChild(m);
  }
  const names = Object.values(selectedNames).slice(0,3).map(x=>x.name).join(', ');
  document.getElementById('previewTo').textContent = count > 3 ? `${names} +${count-3} more` : names;
}

/* ── Search ── */
function filterUsers() {
  const q = document.getElementById('userSearch').value.toLowerCase();
  document.querySelectorAll('.user-item').forEach(el => {
    el.style.display = (el.dataset.name.includes(q)||el.dataset.email.includes(q)) ? '' : 'none';
  });
}

/* ── Char counter + preview ── */
function updateChar(n, cid, max) {
  const val = document.querySelector(`[name="${n}"]`).value.length;
  const el  = document.getElementById(cid);
  el.textContent = `${val} / ${max}`;
  el.className   = 'char-count'+(val>=max?' over':val>max*.9?' warn':'');
  if (n==='subject') document.getElementById('previewSubject').textContent = document.querySelector('[name="subject"]').value||'—';
  if (n==='message') document.getElementById('previewBody').textContent    = document.querySelector('[name="message"]').value||'Your message will appear here…';
}
document.addEventListener('DOMContentLoaded',()=>{updateChar('subject','subjectCount',150);updateChar('message','messageCount',5000);});

/* ════════════════════════════════════════
   AJAX SEND + LIVE PROGRESS
════════════════════════════════════════ */
function startSend() {
  const total   = selectedIds.size;
  const subject = document.querySelector('[name="subject"]').value.trim();
  const message = document.querySelector('[name="message"]').value.trim();
  if (!total)   return;
  if (!subject) { alert('Please enter a Subject.'); return; }
  if (!message) { alert('Please enter a Message.'); return; }

  // Show modal
  const modal = document.getElementById('progressModal');
  modal.classList.remove('hidden');
  document.getElementById('statesSending').classList.remove('hidden');
  document.getElementById('statesDone').classList.add('hidden');
  document.getElementById('sentCount').textContent  = '0';
  document.getElementById('totalCount').textContent = total;
  document.getElementById('progressBar').style.width = '0%';
  document.getElementById('progressPct').textContent  = '0%';
  document.getElementById('progressStatus').textContent = 'Sending…';
  document.getElementById('liveFeed').innerHTML = '';

  // Build form data
  const fd = new FormData();
  fd.append('_token', '{{ csrf_token() }}');
  fd.append('subject', subject);
  fd.append('message', message);
  fd.append('cta_label', document.querySelector('[name="cta_label"]').value||'');
  fd.append('cta_url',   document.querySelector('[name="cta_url"]').value||'');
  selectedIds.forEach(id => fd.append('recipients[]', id));

  // Fake tick animation while SMTP runs
  let fakeCount = 0;
  const tick = setInterval(() => {
    if (fakeCount < total - 1) {
      fakeCount++;
      animateTo(fakeCount, total);
      addFeedRow(null, fakeCount); // placeholder
    }
  }, Math.max(400, 2200 / total));

  fetch('{{ route("admin.mail-send") }}', {
    method: 'POST',
    headers: { 'Accept': 'application/json' },
    body: fd,
  })
  .then(r => r.json())
  .then(data => {
    clearInterval(tick);
    // Replace feed with real results
    const feed = document.getElementById('liveFeed');
    feed.innerHTML = '';
    (data.results||[]).forEach(r => addFeedRow(r));
    animateTo(data.total, data.total);
    document.getElementById('progressStatus').textContent = 'Complete!';
    setTimeout(() => showDone(data), 500);
  })
  .catch(err => {
    clearInterval(tick);
    document.getElementById('progressStatus').textContent = 'Error occurred';
    showDone({ total, sent:0, failed:total, results:[] });
  });
}

function animateTo(current, total) {
  const el  = document.getElementById('sentCount');
  el.classList.remove('num-pop');
  void el.offsetWidth; // reflow
  el.classList.add('num-pop');
  el.textContent = current;
  const pct = total > 0 ? Math.round((current/total)*100) : 0;
  document.getElementById('progressBar').style.width = pct+'%';
  document.getElementById('progressPct').textContent  = pct+'%';
}

function addFeedRow(r, fakeIndex) {
  const feed = document.getElementById('liveFeed');
  const row  = document.createElement('div');
  row.className = 'flex items-center gap-2 text-[11px]';
  if (!r) {
    row.innerHTML = `<span class="w-4 h-4 rounded-full bg-emerald-400 flex items-center justify-center text-white text-[8px] flex-shrink-0"><i class="fa-solid fa-spin fa-circle-notch"></i></span>
                     <span class="text-slate-500">Sending #${fakeIndex}…</span>`;
  } else {
    row.innerHTML = r.status==='ok'
      ? `<span class="w-4 h-4 rounded-full bg-emerald-500 flex items-center justify-center text-white text-[8px] flex-shrink-0"><i class="fa-solid fa-check"></i></span>
         <span class="text-slate-600 truncate"><strong>${r.name}</strong> — ${r.email}</span>`
      : `<span class="w-4 h-4 rounded-full bg-rose-500 flex items-center justify-center text-white text-[8px] flex-shrink-0"><i class="fa-solid fa-xmark"></i></span>
         <span class="text-slate-600 truncate"><strong>${r.name}</strong> <span class="text-rose-400">${r.error||'failed'}</span></span>`;
  }
  feed.appendChild(row);
  feed.scrollTop = feed.scrollHeight;
}

function showDone(data) {
  document.getElementById('statesSending').classList.add('hidden');
  document.getElementById('statesDone').classList.remove('hidden');
  document.getElementById('doneTotal').textContent  = data.total;
  document.getElementById('doneSent').textContent   = data.sent;
  document.getElementById('doneFailed').textContent = data.failed;
  const ok = data.failed === 0;
  const icon = document.getElementById('doneIcon');
  icon.className = 'w-16 h-16 rounded-2xl flex items-center justify-center mx-auto mb-4 text-2xl text-white shadow-lg '
    + (ok ? 'bg-gradient-to-br from-emerald-500 to-teal-600 shadow-emerald-500/30'
           : 'bg-gradient-to-br from-amber-500 to-orange-600 shadow-amber-500/30');
  icon.innerHTML = ok ? '<i class="fa-solid fa-circle-check"></i>' : '<i class="fa-solid fa-triangle-exclamation"></i>';
  document.getElementById('doneTitle').textContent = ok ? '🎉 All Mails Sent!' : `⚠️ Sent with ${data.failed} failure${data.failed>1?'s':''}`;
  document.getElementById('doneSub').textContent   = ok
    ? `Successfully delivered to all ${data.total} recipient${data.total>1?'s':''}.`
    : `${data.sent} delivered, ${data.failed} failed. Check Mail Logs for details.`;
}

function closeModal() {
  document.getElementById('progressModal').classList.add('hidden');
  document.querySelector('[name="subject"]').value   = '';
  document.querySelector('[name="message"]').value   = '';
  document.querySelector('[name="cta_label"]').value = '';
  document.querySelector('[name="cta_url"]').value   = '';
  selectByRole('none');
  updateChar('subject','subjectCount',150);
  updateChar('message','messageCount',5000);
}
</script>
@endpush
