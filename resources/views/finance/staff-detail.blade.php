<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>{{ $staff->name }} — Salary Detail</title>
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Outfit:wght@500;600;700;800;900&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<script>
  tailwind.config = { darkMode: 'class', theme: { extend: { fontFamily: { sans: ['"Plus Jakarta Sans"', 'sans-serif'], outfit: ['"Outfit"', 'sans-serif'] } } } }
  document.documentElement.classList.remove('dark');
</script>
<style>
  body { font-family: 'Plus Jakarta Sans', sans-serif; background: #050b18; color: #e2e8f0; min-height: 100vh; }
  .font-outfit { font-family: 'Outfit', sans-serif; }
  .glass { background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.08); border-radius: 1.25rem; }
  .glass-light { background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.10); border-radius: 0.875rem; }
  .grad-emerald { background: linear-gradient(135deg, #059669, #0d9488); }
  .grad-amber { background: linear-gradient(135deg, #d97706, #b45309); }
  .badge { display:inline-flex; align-items:center; gap:0.25rem; padding:0.2rem 0.6rem; border-radius:9999px; font-size:0.68rem; font-weight:700; text-transform:uppercase; }
  .badge-green  { background:rgba(5,150,105,0.2);  color:#34d399; border:1px solid rgba(5,150,105,0.4); }
  .badge-amber  { background:rgba(217,119,6,0.2);  color:#fbbf24; border:1px solid rgba(217,119,6,0.4); }
  .badge-rose   { background:rgba(225,29,72,0.2);  color:#fb7185; border:1px solid rgba(225,29,72,0.4); }
  .fin-table th { padding:0.75rem 1rem; font-size:0.65rem; font-weight:800; text-transform:uppercase; letter-spacing:0.1em; color:#6ee7b7; background:rgba(5,150,105,0.1); border-bottom:1px solid rgba(5,150,105,0.2); }
  .fin-table td { padding:0.875rem 1rem; font-size:0.75rem; border-bottom:1px solid rgba(255,255,255,0.04); color:#cbd5e1; }
  .fin-table tr:hover td { background:rgba(255,255,255,0.02); }
  .fin-inp { width:100%; padding:0.65rem 1rem; background:rgba(255,255,255,0.05); border:1px solid rgba(255,255,255,0.12); border-radius:0.875rem; color:#e2e8f0; font-size:0.78rem; font-family:'Plus Jakarta Sans',sans-serif; }
  .fin-inp:focus { outline:none; border-color:#059669; box-shadow:0 0 0 3px rgba(5,150,105,0.15); }
  .fin-inp::placeholder { color:#475569; }
  .fin-inp option { background:#1e293b; }
  .orb { position:absolute; border-radius:9999px; filter:blur(80px); pointer-events:none; opacity:0.25; }
  ::-webkit-scrollbar { width:5px; } ::-webkit-scrollbar-thumb { background:rgba(5,150,105,0.4); border-radius:9999px; }
  .fin-modal { position:fixed; inset:0; z-index:9999; background:rgba(0,0,0,0.85); backdrop-filter:blur(12px); display:none; align-items:center; justify-content:center; padding:1rem; }
  .fin-modal.open { display:flex; }
  .fin-modal-box { background:#0f1f2e; border:1px solid rgba(255,255,255,0.12); border-radius:1.5rem; max-width:460px; width:100%; padding:2rem; position:relative; box-shadow:0 40px 80px rgba(0,0,0,0.6); }
</style>
</head>
<body class="relative">
<div class="orb w-72 h-72 bg-emerald-500 top-0 left-10 -z-0"></div>
<div class="orb w-56 h-56 bg-amber-500 bottom-20 right-10 -z-0"></div>

<div class="relative z-10 max-w-5xl mx-auto px-4 py-6 space-y-6">

  {{-- Back nav --}}
  <div class="flex items-center justify-between">
    <a href="{{ route('finance.dashboard') }}" class="flex items-center gap-2 text-xs font-bold text-emerald-400 hover:text-emerald-300 transition">
      <i class="fa-solid fa-arrow-left"></i> Finance Vault
    </a>
    <span class="text-xs font-bold text-amber-400 bg-amber-500/10 px-3 py-1 rounded-xl border border-amber-500/25">
      <i class="fa-solid fa-calculator"></i> ₹1,000 / Hour
    </span>
  </div>

  {{-- Flash --}}
  @foreach(['success'=>'emerald','error'=>'rose'] as $key => $color)
  @if(session($key))
  <div class="p-4 rounded-2xl bg-{{ $color }}-500/10 border border-{{ $color }}-500/30 text-{{ $color }}-300 text-sm font-semibold flex items-center gap-3">
    <i class="fa-solid fa-circle-{{ $key === 'error' ? 'exclamation' : 'check' }} text-{{ $color }}-400 text-lg"></i>
    {{ session($key) }}
  </div>
  @endif
  @endforeach

  {{-- STAFF PROFILE CARD --}}
  <div class="glass rounded-3xl p-6 relative overflow-hidden"
       style="background:linear-gradient(135deg,rgba(5,150,105,0.15) 0%,rgba(13,148,136,0.06) 60%,rgba(217,119,6,0.05) 100%);border:1px solid rgba(5,150,105,0.3);">
    <div class="absolute -right-10 -top-10 w-44 h-44 rounded-full bg-emerald-500/10 blur-3xl"></div>
    <div class="flex items-center justify-between flex-wrap gap-4 relative">
      <div class="flex items-center gap-5">
        <div class="w-20 h-20 rounded-2xl grad-emerald flex items-center justify-center text-white text-3xl font-black shadow-xl shadow-emerald-500/30 flex-shrink-0">
          {{ strtoupper(substr($staff->name, 0, 1)) }}
        </div>
        <div>
          <h2 class="font-outfit font-black text-white text-2xl">{{ $staff->name }}</h2>
          <p class="text-slate-400 text-sm">{{ $staff->email }}</p>
          <p class="text-slate-500 text-xs mt-1">{{ $staff->position ?? 'Staff Member' }} &nbsp;·&nbsp; Joined {{ $staff->joining_date ? $staff->joining_date->format('M Y') : ($staff->created_at->format('M Y')) }}</p>
          <div class="flex items-center gap-3 mt-2 text-[11px]">
            <span class="text-slate-400"><i class="fa-solid fa-building-columns text-emerald-500 mr-1"></i>{{ $staff->bank_name ?? 'Bank not set' }}</span>
            <span class="text-slate-600">·</span>
            <span class="text-slate-400 font-mono">A/C: {{ $staff->bank_account ?? 'Not set' }}</span>
            @if($staff->bank_ifsc)
            <span class="text-slate-600">·</span>
            <span class="text-slate-400 font-mono">IFSC: {{ $staff->bank_ifsc }}</span>
            @endif
          </div>
        </div>
      </div>
      <div class="text-right">
        <span class="text-slate-400 text-[10px] font-bold uppercase tracking-wider block">This Month Earned</span>
        <h3 class="font-outfit font-black text-4xl text-emerald-400 mt-1">₹{{ number_format($currentMonthEarned) }}</h3>
        <p class="text-teal-400 text-xs font-bold mt-1"><i class="fa-solid fa-clock"></i> {{ $currentMonthHours }} hrs tracked</p>
        <p class="text-slate-500 text-[10px] mt-0.5">Total Ever Paid: <strong class="text-rose-400">₹{{ number_format($totalPaidEver) }}</strong></p>
      </div>
    </div>
  </div>

  {{-- MONTHLY BREAKDOWN --}}
  @if($monthStats->isNotEmpty())
  <div class="glass p-5 rounded-2xl">
    <h2 class="font-outfit font-black text-white text-base flex items-center gap-2 mb-4">
      <i class="fa-solid fa-chart-bar text-emerald-400"></i> Monthly Work Breakdown
    </h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
      @foreach($monthStats->take(6) as $key => $ms)
      <div class="glass-light p-4 rounded-xl border border-white/[0.06] hover:border-emerald-500/30 transition">
        <p class="text-[10px] font-black text-emerald-400 uppercase tracking-widest">{{ $ms['label'] }}</p>
        <p class="font-outfit font-black text-white text-xl mt-1">₹{{ number_format($ms['earned']) }}</p>
        <div class="flex items-center justify-between mt-2 text-[10px] text-slate-400">
          <span><i class="fa-solid fa-clock text-sky-400"></i> {{ $ms['hours'] }} hrs</span>
          <span><i class="fa-solid fa-calendar-check text-teal-400"></i> {{ $ms['days'] }} days</span>
          <span><i class="fa-solid fa-layer-group text-violet-400"></i> {{ $ms['sessions'] }} sessions</span>
        </div>
      </div>
      @endforeach
    </div>
  </div>
  @endif

  {{-- PAY SALARY FORM --}}
  <div class="glass p-6 rounded-2xl">
    <div class="flex items-center gap-3 border-b border-white/[0.06] pb-4 mb-5">
      <div class="w-11 h-11 rounded-xl grad-amber flex items-center justify-center text-white text-lg shadow-md shadow-amber-500/25">
        <i class="fa-solid fa-money-bill-wave"></i>
      </div>
      <div>
        <h3 class="font-outfit font-black text-white text-lg">Process Salary Payment</h3>
        <p class="text-slate-400 text-xs">Deducted from Main Finance Bank · PIN required to authorize</p>
      </div>
    </div>

    <form id="salaryForm" class="space-y-4" onsubmit="requirePinThenSubmit(event, this)">
      @csrf
      <input type="hidden" name="_action_url" value="{{ route('finance.pay', $staff) }}">

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider mb-1.5">Period Label *</label>
          <input type="text" name="period_label" required value="{{ old('period_label', now()->format('F Y') . ' Salary') }}" class="fin-inp">
        </div>
        <div>
          <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider mb-1.5">Hours Worked *</label>
          <input type="number" step="0.1" min="0" name="hours_worked" id="hoursInput"
                 value="{{ old('hours_worked', $currentMonthHours) }}" required oninput="recalcAmount()" class="fin-inp font-bold">
        </div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider mb-1.5">Salary Amount (₹) *</label>
          <div class="relative">
            <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-rose-400 font-bold text-sm">−₹</span>
            <input type="number" step="0.01" min="1" name="amount" id="amountInput"
                   value="{{ old('amount', $currentMonthEarned) }}" required class="fin-inp pl-10 font-bold">
          </div>
        </div>
        <div>
          <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider mb-1.5">Payment Status *</label>
          <select name="status" class="fin-inp text-xs">
            <option value="completed">Completed (Deducts from Main Bank)</option>
            <option value="approved">Approved</option>
            <option value="pending">Pending</option>
          </select>
        </div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider mb-1.5">Bank Account #</label>
          <input type="text" name="bank_account" value="{{ old('bank_account', $staff->bank_account) }}" placeholder="9876543210" class="fin-inp font-mono text-xs">
        </div>
        <div>
          <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider mb-1.5">Bank Name</label>
          <input type="text" name="bank_name" value="{{ old('bank_name', $staff->bank_name) }}" placeholder="HDFC Bank" class="fin-inp text-xs">
        </div>
      </div>

      <div class="flex items-center gap-2">
        <input type="checkbox" name="auto_approve" value="1" checked id="autoApproveChk" class="w-4 h-4 accent-amber-500">
        <label for="autoApproveChk" class="text-xs text-slate-300 font-semibold cursor-pointer">
          Auto-Approve & Send Salary Email Notification
        </label>
      </div>

      <div>
        <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider mb-1.5">Notes (Optional)</label>
        <textarea name="notes" rows="2" placeholder="e.g. Performance bonus included" class="fin-inp"></textarea>
      </div>

      <div class="p-3.5 rounded-2xl bg-amber-500/10 border border-amber-500/25 flex items-center gap-3">
        <i class="fa-solid fa-shield-halved text-amber-400 text-lg flex-shrink-0"></i>
        <p class="text-xs text-amber-300 font-semibold">PIN verification required before payment is processed</p>
      </div>

      <button type="submit" class="w-full py-3.5 rounded-2xl font-bold text-white grad-amber shadow-lg shadow-amber-500/25 hover:opacity-90 transition text-sm">
        <i class="fa-solid fa-lock-open"></i> Authorize & Execute Salary Payment
      </button>
    </form>
  </div>

  {{-- PAYMENT HISTORY --}}
  @if($payments->isNotEmpty())
  <div class="glass rounded-2xl overflow-hidden">
    <div class="p-5 border-b border-white/[0.06]">
      <h3 class="font-outfit font-black text-white text-base flex items-center gap-2">
        <i class="fa-solid fa-receipt text-emerald-400"></i> Payment Records
      </h3>
    </div>
    <div class="overflow-x-auto">
      <table class="fin-table w-full text-left">
        <thead>
          <tr>
            <th>Period</th>
            <th>Date</th>
            <th>Hours</th>
            <th>Amount</th>
            <th>Bank</th>
            <th>Paid By</th>
            <th>Mail</th>
            <th>Status</th>
            <th class="text-right">Action</th>
          </tr>
        </thead>
        <tbody>
          @foreach($payments as $p)
          <tr>
            <td class="font-bold text-slate-200">{{ $p->period_label }}</td>
            <td class="text-[11px]">
              <div>{{ $p->created_at->format('d M Y') }}</div>
              @if($p->paid_at)<div class="text-emerald-400/70 text-[10px]">Paid: {{ $p->paid_at->format('d M, h:i A') }}</div>@endif
            </td>
            <td><span class="font-outfit font-black text-sky-400">{{ $p->hours_worked }}</span><span class="text-slate-500 text-xs ml-1">hrs</span></td>
            <td><span class="font-outfit font-black text-rose-400">−₹{{ number_format($p->amount, 2) }}</span></td>
            <td class="text-[11px] text-slate-400 font-mono">{{ $p->bank_name ?? '—' }}</td>
            <td class="text-[11px] text-slate-400">{{ $p->paidBy?->name ?? '—' }}</td>
            <td>
              @if($p->mail_sent)
                <span class="badge badge-green"><i class="fa-solid fa-envelope-circle-check"></i> Sent</span>
              @else
                <span class="badge" style="background:rgba(100,116,139,0.2);color:#94a3b8;border:1px solid rgba(100,116,139,0.4);">—</span>
              @endif
            </td>
            <td>
              @php $bs=['paid'=>'badge-green','pending'=>'badge-amber','failed'=>'badge-rose']; @endphp
              <span class="badge {{ $bs[$p->payment_status] ?? 'badge-gray' }}">{{ ucfirst($p->payment_status) }}</span>
            </td>
            <td class="text-right">
              @if($p->payment_status === 'pending')
              <form method="POST" action="{{ route('finance.payments.mark-paid', $p) }}" class="inline">
                @csrf
                <button type="submit" class="px-3 py-1.5 rounded-xl text-[10px] font-bold text-white grad-emerald shadow-sm hover:opacity-90 transition">
                  <i class="fa-solid fa-check"></i> Mark Paid
                </button>
              </form>
              @else
              <span class="text-[10px] text-emerald-500 font-bold flex items-center gap-1 justify-end">
                <i class="fa-solid fa-circle-check"></i> Paid
              </span>
              @endif
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
  @endif

</div>

{{-- ACTION PIN MODAL --}}
<div id="actionPinModal" class="fin-modal">
  <div class="fin-modal-box" style="text-align:center;">
    <button onclick="fClose('actionPinModal')" class="absolute top-4 right-4 text-slate-400 hover:text-white"><i class="fa-solid fa-xmark text-lg"></i></button>
    <div class="w-16 h-16 rounded-2xl grad-amber flex items-center justify-center text-white text-2xl mx-auto shadow-lg shadow-amber-500/30 mb-4">
      <i class="fa-solid fa-shield-halved"></i>
    </div>
    <h3 class="font-outfit font-black text-white text-xl mb-1">Authorize Payment</h3>
    <p class="text-slate-400 text-xs mb-5">Enter your security PIN to confirm this salary payment</p>
    <div class="space-y-4">
      <input type="password" id="actionPinInput" maxlength="6" placeholder="Enter PIN"
             class="fin-inp text-center text-2xl tracking-[0.5em] font-mono text-amber-400">
      <p id="actionPinError" class="text-rose-400 text-xs hidden"></p>
      <button onclick="submitActionPin()" class="w-full py-3 rounded-2xl font-bold text-white grad-amber shadow-lg hover:opacity-90 transition text-sm">
        <i class="fa-solid fa-lock-open"></i> Authorize & Process
      </button>
    </div>
  </div>
</div>

<script>
  function fClose(id) {
    const m = document.getElementById(id);
    if (m) m.classList.remove('open');
  }

  function recalcAmount() {
    const hrs = parseFloat(document.getElementById('hoursInput').value) || 0;
    document.getElementById('amountInput').value = Math.round(hrs * 1000);
  }

  let _pendingFormEl = null;

  function requirePinThenSubmit(event, formEl) {
    event.preventDefault();
    _pendingFormEl = formEl;
    document.getElementById('actionPinInput').value = '';
    document.getElementById('actionPinError').classList.add('hidden');
    const m = document.getElementById('actionPinModal');
    if (m) m.classList.add('open');
    setTimeout(() => document.getElementById('actionPinInput').focus(), 100);
  }

  async function submitActionPin() {
    const pin = document.getElementById('actionPinInput').value.trim();
    const errEl = document.getElementById('actionPinError');
    errEl.classList.add('hidden');

    if (!pin) { errEl.textContent = 'Please enter your PIN.'; errEl.classList.remove('hidden'); return; }

    try {
      const res = await fetch("{{ route('finance.verify-action-pin') }}", {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': "{{ csrf_token() }}", 'Content-Type': 'application/json', 'Accept': 'application/json' },
        body: JSON.stringify({ pin: pin })
      });
      const data = await res.json();

      if (data.success) {
        fClose('actionPinModal');
        if (_pendingFormEl) {
          const hidden = _pendingFormEl.querySelector('[name="_action_url"]');
          if (hidden) _pendingFormEl.action = hidden.value;
          _pendingFormEl.submit();
        }
      } else {
        errEl.textContent = data.message || 'Incorrect PIN.';
        errEl.classList.remove('hidden');
        document.getElementById('actionPinInput').value = '';
        document.getElementById('actionPinInput').focus();
      }
    } catch (err) {
      errEl.textContent = 'Server error. Please try again.';
      errEl.classList.remove('hidden');
    }
  }

  document.getElementById('actionPinInput')?.addEventListener('keydown', e => {
    if (e.key === 'Enter') { e.preventDefault(); submitActionPin(); }
  });
</script>

@include('partials.global-loader')
</body>
</html>
