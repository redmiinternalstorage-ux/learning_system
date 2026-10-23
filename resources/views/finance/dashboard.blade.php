<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Finance Vault — Learning Curve</title>
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Outfit:wght@500;600;700;800;900&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<script>
  tailwind.config = { darkMode: 'class', theme: { extend: { fontFamily: { sans: ['"Plus Jakarta Sans"', 'sans-serif'], outfit: ['"Outfit"', 'sans-serif'] } } } }
  document.documentElement.classList.remove('dark');
</script>
<style>
  :root { color-scheme: dark; }
  body { font-family: 'Plus Jakarta Sans', sans-serif; background: #050b18; color: #e2e8f0; min-height: 100vh; }
  .font-outfit { font-family: 'Outfit', sans-serif; }

  /* Glass cards */
  .glass { background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.08); border-radius: 1.25rem; backdrop-filter: blur(12px); }
  .glass-light { background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.10); border-radius: 1rem; }

  /* Gradients */
  .grad-emerald { background: linear-gradient(135deg, #059669 0%, #0d9488 100%); }
  .grad-amber   { background: linear-gradient(135deg, #d97706 0%, #b45309 100%); }
  .grad-rose    { background: linear-gradient(135deg, #e11d48 0%, #be123c 100%); }
  .grad-sky     { background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%); }
  .grad-violet  { background: linear-gradient(135deg, #7c3aed 0%, #6d28d9 100%); }
  .grad-teal    { background: linear-gradient(135deg, #0d9488 0%, #0f766e 100%); }

  /* Colored glow cards */
  .card-emerald { background: linear-gradient(135deg, rgba(5,150,105,0.15) 0%, rgba(13,148,136,0.08) 100%); border: 1px solid rgba(5,150,105,0.3); }
  .card-rose    { background: linear-gradient(135deg, rgba(225,29,72,0.15) 0%, rgba(190,18,60,0.08) 100%); border: 1px solid rgba(225,29,72,0.3); }
  .card-amber   { background: linear-gradient(135deg, rgba(217,119,6,0.15) 0%, rgba(180,83,9,0.08) 100%); border: 1px solid rgba(217,119,6,0.3); }
  .card-sky     { background: linear-gradient(135deg, rgba(2,132,199,0.15) 0%, rgba(3,105,161,0.08) 100%); border: 1px solid rgba(2,132,199,0.3); }
  .card-violet  { background: linear-gradient(135deg, rgba(124,58,237,0.15) 0%, rgba(109,40,217,0.08) 100%); border: 1px solid rgba(124,58,237,0.3); }

  /* Badge chips */
  .badge { display:inline-flex; align-items:center; gap:0.25rem; padding:0.2rem 0.65rem; border-radius:9999px; font-size:0.68rem; font-weight:700; text-transform:uppercase; letter-spacing:0.05em; }
  .badge-green  { background:rgba(5,150,105,0.2);  color:#34d399; border:1px solid rgba(5,150,105,0.4); }
  .badge-amber  { background:rgba(217,119,6,0.2);  color:#fbbf24; border:1px solid rgba(217,119,6,0.4); }
  .badge-rose   { background:rgba(225,29,72,0.2);  color:#fb7185; border:1px solid rgba(225,29,72,0.4); }
  .badge-sky    { background:rgba(2,132,199,0.2);  color:#38bdf8; border:1px solid rgba(2,132,199,0.4); }
  .badge-violet { background:rgba(124,58,237,0.2); color:#a78bfa; border:1px solid rgba(124,58,237,0.4); }
  .badge-gray   { background:rgba(100,116,139,0.2);color:#94a3b8; border:1px solid rgba(100,116,139,0.4); }

  /* Nav buttons */
  .nav-btn { display:inline-flex; align-items:center; gap:0.4rem; padding:0.45rem 0.9rem; border-radius:0.75rem; font-size:0.72rem; font-weight:700; transition:all 0.2s; cursor:pointer; border:none; }

  /* Table */
  .fin-table th { padding:0.75rem 1rem; font-size:0.65rem; font-weight:800; text-transform:uppercase; letter-spacing:0.1em; color:#6ee7b7; background:rgba(5,150,105,0.1); border-bottom:1px solid rgba(5,150,105,0.2); }
  .fin-table td { padding:0.875rem 1rem; font-size:0.75rem; border-bottom:1px solid rgba(255,255,255,0.04); color:#cbd5e1; }
  .fin-table tr:hover td { background:rgba(255,255,255,0.02); }
  .fin-table tr:last-child td { border-bottom:none; }

  /* Input */
  .fin-inp { width:100%; padding:0.65rem 1rem; background:rgba(255,255,255,0.05); border:1px solid rgba(255,255,255,0.12); border-radius:0.875rem; color:#e2e8f0; font-size:0.78rem; font-family:'Plus Jakarta Sans',sans-serif; transition:border 0.2s; }
  .fin-inp:focus { outline:none; border-color:#059669; box-shadow:0 0 0 3px rgba(5,150,105,0.15); }
  .fin-inp::placeholder { color:#475569; }
  .fin-inp option { background:#1e293b; color:#e2e8f0; }

  /* Scrollbar */
  ::-webkit-scrollbar { width:5px; height:5px; }
  ::-webkit-scrollbar-track { background:transparent; }
  ::-webkit-scrollbar-thumb { background:rgba(5,150,105,0.4); border-radius:9999px; }
  ::-webkit-scrollbar-thumb:hover { background:#059669; }

  /* Glow orbs */
  .orb { position:absolute; border-radius:9999px; filter:blur(80px); pointer-events:none; opacity:0.35; }

  /* Animated number counter */
  @keyframes countUp { from { opacity:0; transform:translateY(8px); } to { opacity:1; transform:translateY(0); } }
  .count-anim { animation: countUp 0.5s ease forwards; }

  /* Modal */
  .fin-modal { position:fixed; inset:0; z-index:9999; background:rgba(0,0,0,0.85); backdrop-filter:blur(12px); display:none; align-items:center; justify-content:center; padding:1rem; }
  .fin-modal.open { display:flex; }
  .fin-modal-box { background:#0f1f2e; border:1px solid rgba(255,255,255,0.12); border-radius:1.5rem; max-width:520px; width:100%; padding:2rem; position:relative; box-shadow:0 40px 80px rgba(0,0,0,0.6); max-height:92vh; overflow-y:auto; }

  /* Pulse dot */
  @keyframes pulse { 0%,100%{opacity:1;} 50%{opacity:0.4;} }
  .pulse-dot { animation:pulse 2s infinite; }

  /* Transaction mark stamp */
  .mark-stamp { font-size:0.65rem; color:#fbbf24; background:rgba(217,119,6,0.1); border:1px solid rgba(217,119,6,0.25); border-radius:0.5rem; padding:0.2rem 0.5rem; display:inline-block; margin-top:3px; }
</style>
</head>
<body>

{{-- Background orbs --}}
<div class="orb w-96 h-96 bg-emerald-500 top-0 left-20 -z-0"></div>
<div class="orb w-64 h-64 bg-teal-600 bottom-20 right-10 -z-0"></div>
<div class="orb w-48 h-48 bg-violet-600 top-40 right-1/3 -z-0"></div>

<div class="relative z-10 max-w-[1400px] mx-auto px-4 py-6 space-y-6">

  {{-- ══════════ TOPBAR ══════════ --}}
  <div class="glass p-4 rounded-2xl flex items-center justify-between flex-wrap gap-3">
    <div class="flex items-center gap-3.5">
      <div class="w-11 h-11 rounded-2xl grad-emerald flex items-center justify-center text-white text-lg shadow-lg shadow-emerald-500/30 flex-shrink-0">
        <i class="fa-solid fa-vault"></i>
      </div>
      <div>
        <div class="flex items-center gap-2">
          <h1 class="font-outfit font-black text-white text-xl">Finance Vault</h1>
          <span class="badge badge-green"><span class="pulse-dot w-1.5 h-1.5 rounded-full bg-emerald-400 inline-block"></span> Live</span>
        </div>
        <p class="text-slate-400 text-[10px] mt-0.5"><i class="fa-regular fa-calendar text-emerald-500"></i> {{ now()->format('l, d F Y') }} &nbsp;·&nbsp; <i class="fa-solid fa-clock-rotate-left text-sky-400"></i> ₹1,000/hr rate</p>
      </div>
    </div>
    <div class="flex items-center gap-2 flex-wrap">
      <button onclick="fModal('exportModal')" class="nav-btn bg-violet-500/15 border border-violet-500/30 text-violet-300 hover:bg-violet-500/25">
        <i class="fa-solid fa-vault text-violet-400"></i> Export Vault
      </button>
      @if($pendingCount > 0)
      <form method="POST" action="{{ route('finance.auto-approve-all') }}" onsubmit="return confirm('Auto-approve all {{ $pendingCount }} pending transactions?')">
        @csrf
        <button class="nav-btn bg-amber-500/15 border border-amber-500/30 text-amber-300 hover:bg-amber-500/25">
          <i class="fa-solid fa-bolt text-amber-400"></i> Auto-Approve ({{ $pendingCount }})
        </button>
      </form>
      @endif
      <button onclick="fModal('transferModal')" class="nav-btn grad-sky text-white shadow-md shadow-sky-500/20">
        <i class="fa-solid fa-right-left"></i> Transfer
      </button>
      <button onclick="fModal('clientPaymentModal')" class="nav-btn grad-emerald text-white shadow-md shadow-emerald-500/20">
        <i class="fa-solid fa-plus-circle"></i> Income
      </button>
      <a href="{{ route('finance.history') }}" class="nav-btn bg-white/5 border border-white/10 text-slate-300 hover:bg-white/10">
        <i class="fa-solid fa-list-check text-emerald-400"></i> Full History
      </a>
      <a href="{{ route('admin.switch-department') }}" class="nav-btn bg-amber-500/10 border border-amber-500/25 text-amber-300 hover:bg-amber-500/20">
        <i class="fa-solid fa-arrows-rotate text-amber-400"></i> Switch
      </a>
      @if(Auth::user()->isAdmin())
      <a href="{{ route('admin.panel-select') }}" class="nav-btn bg-violet-500/10 border border-violet-500/25 text-violet-300 hover:bg-violet-500/20">
        <i class="fa-solid fa-crown text-violet-400"></i> Admin
      </a>
      @endif
      <form method="POST" action="{{ route('admin.logout') }}" class="inline">
        @csrf
        <button class="nav-btn bg-rose-500/10 border border-rose-500/25 text-rose-300 hover:bg-rose-500/20">
          <i class="fa-solid fa-right-from-bracket text-rose-400"></i> Logout
        </button>
      </form>
    </div>
  </div>

  {{-- Flash --}}
  @foreach(['success'=>'emerald','error'=>'rose','info'=>'sky','warning'=>'amber'] as $key => $color)
  @if(session($key))
  <div class="p-4 rounded-2xl bg-{{ $color }}-500/10 border border-{{ $color }}-500/30 text-{{ $color }}-300 text-sm font-semibold flex items-center justify-between">
    <div class="flex items-center gap-3">
      <i class="fa-solid fa-circle-{{ $key === 'error' ? 'exclamation' : 'check' }} text-{{ $color }}-400 text-lg"></i>
      <span>{{ session($key) }}</span>
    </div>
    <button onclick="this.parentElement.remove()" class="text-{{ $color }}-400 hover:text-{{ $color }}-200"><i class="fa-solid fa-xmark"></i></button>
  </div>
  @endif
  @endforeach

  {{-- ══════════ MAIN BALANCE + STATS ══════════ --}}
  <div class="grid grid-cols-1 lg:grid-cols-12 gap-5">

    {{-- VAULT BALANCE CARD --}}
    <div class="lg:col-span-5 glass relative overflow-hidden rounded-3xl p-6 flex flex-col justify-between min-h-[240px]"
         style="background:linear-gradient(135deg,rgba(5,150,105,0.18) 0%,rgba(13,148,136,0.08) 60%,rgba(124,58,237,0.06) 100%);border:1px solid rgba(5,150,105,0.3);">
      {{-- Decorative circles --}}
      <div class="absolute -right-12 -top-12 w-48 h-48 rounded-full bg-emerald-500/10 blur-2xl"></div>
      <div class="absolute -left-8 -bottom-8 w-36 h-36 rounded-full bg-teal-500/10 blur-2xl"></div>

      <div class="relative">
        <div class="flex items-center justify-between mb-1">
          <div class="flex items-center gap-2 text-emerald-400 text-xs font-black uppercase tracking-widest">
            <i class="fa-solid fa-vault"></i> Main Bank Vault
          </div>
          <span class="badge badge-green"><i class="fa-solid fa-shield-halved"></i> Secured</span>
        </div>
        <p class="text-slate-400 text-[10px]">Primary Organization Reserve · ACC-MAIN-1001</p>
      </div>

      <div class="relative mt-4">
        <p class="text-slate-400 text-[10px] font-bold uppercase tracking-wider mb-2">Available Balance</p>
        @if($pinUnlocked || session('finance_pin_verified'))
        <div class="flex items-end gap-3 flex-wrap">
          <h2 class="font-outfit font-black text-5xl text-white leading-none count-anim">
            <span class="text-emerald-400">₹</span>{{ number_format($availableBalance, 2) }}
          </h2>
          <button onclick="lockBalance()" class="text-slate-400 hover:text-emerald-400 transition mb-1" title="Lock Balance">
            <i class="fa-solid fa-eye text-lg"></i>
          </button>
        </div>
        <p class="text-emerald-400 text-xs font-semibold mt-2 flex items-center gap-1.5">
          <i class="fa-solid fa-circle-check"></i> Live · Auto-updating balance
        </p>
        @else
        <div class="flex items-center gap-4">
          <h2 class="font-outfit font-black text-4xl text-slate-600 tracking-widest select-none">••••••••</h2>
          <button onclick="fModal('pinModal')" class="flex items-center gap-2 px-4 py-2 rounded-xl bg-emerald-500/20 hover:bg-emerald-500/30 text-emerald-300 text-xs font-bold border border-emerald-500/30 transition">
            <i class="fa-solid fa-lock text-emerald-400"></i> Unlock Vault
          </button>
        </div>
        <p class="text-amber-400/80 text-xs font-semibold mt-2 flex items-center gap-1.5">
          <i class="fa-solid fa-shield-halved"></i> PIN required to view balance
        </p>
        @endif
      </div>

      <div class="relative mt-5 pt-4 border-t border-white/10 grid grid-cols-2 gap-4">
        <div>
          <span class="text-slate-400 text-[10px] font-bold uppercase tracking-wider block">Total Income</span>
          <span class="font-outfit font-black text-lg text-emerald-400 mt-0.5 flex items-center gap-1">
            + ₹{{ number_format($totalIncome, 0) }}
          </span>
        </div>
        <div>
          <span class="text-slate-400 text-[10px] font-bold uppercase tracking-wider block">Total Expenses</span>
          <span class="font-outfit font-black text-lg text-rose-400 mt-0.5 flex items-center gap-1">
            − ₹{{ number_format($totalExpenses, 0) }}
          </span>
        </div>
      </div>
    </div>

    {{-- STATS 2x2 GRID --}}
    <div class="lg:col-span-7 grid grid-cols-2 sm:grid-cols-2 gap-4">

      {{-- Income --}}
      <div class="glass card-emerald rounded-2xl p-5 flex flex-col justify-between">
        <div class="flex items-center justify-between">
          <div class="w-10 h-10 rounded-xl grad-emerald flex items-center justify-center text-white shadow-md shadow-emerald-500/25">
            <i class="fa-solid fa-arrow-trend-up text-sm"></i>
          </div>
          <span class="badge badge-green">+ Income</span>
        </div>
        <div class="mt-4">
          <p class="text-slate-400 text-[10px] font-bold uppercase tracking-wider">Total Revenue</p>
          <h3 class="font-outfit font-black text-2xl text-emerald-400 mt-1">₹{{ number_format($totalIncome, 0) }}</h3>
          <p class="text-slate-500 text-[10px] mt-0.5">Client fees & deposits</p>
        </div>
      </div>

      {{-- Expenses --}}
      <div class="glass card-rose rounded-2xl p-5 flex flex-col justify-between">
        <div class="flex items-center justify-between">
          <div class="w-10 h-10 rounded-xl grad-rose flex items-center justify-center text-white shadow-md shadow-rose-500/25">
            <i class="fa-solid fa-arrow-trend-down text-sm"></i>
          </div>
          <span class="badge badge-rose">− Expense</span>
        </div>
        <div class="mt-4">
          <p class="text-slate-400 text-[10px] font-bold uppercase tracking-wider">Total Expenses</p>
          <h3 class="font-outfit font-black text-2xl text-rose-400 mt-1">₹{{ number_format($totalExpenses, 0) }}</h3>
          <p class="text-slate-500 text-[10px] mt-0.5">Salaries & payouts</p>
        </div>
      </div>

      {{-- Pending --}}
      <div class="glass card-amber rounded-2xl p-5 flex flex-col justify-between">
        <div class="flex items-center justify-between">
          <div class="w-10 h-10 rounded-xl grad-amber flex items-center justify-center text-white shadow-md shadow-amber-500/25">
            <i class="fa-solid fa-hourglass-half text-sm"></i>
          </div>
          <span class="badge badge-amber">{{ $pendingCount }} Pending</span>
        </div>
        <div class="mt-4">
          <p class="text-slate-400 text-[10px] font-bold uppercase tracking-wider">Pending Approvals</p>
          <h3 class="font-outfit font-black text-2xl text-amber-400 mt-1">₹{{ number_format($pendingAmount, 0) }}</h3>
          <p class="text-slate-500 text-[10px] mt-0.5">{{ $pendingCount }} transactions waiting</p>
        </div>
      </div>

      {{-- Departments --}}
      <div class="glass card-violet rounded-2xl p-5 flex flex-col justify-between">
        <div class="flex items-center justify-between">
          <div class="w-10 h-10 rounded-xl grad-violet flex items-center justify-center text-white shadow-md shadow-violet-500/25">
            <i class="fa-solid fa-sitemap text-sm"></i>
          </div>
          <span class="badge badge-violet">{{ $accounts->count() }} Accounts</span>
        </div>
        <div class="mt-4">
          <p class="text-slate-400 text-[10px] font-bold uppercase tracking-wider">Departments</p>
          <h3 class="font-outfit font-black text-2xl text-violet-300 mt-1">{{ $accounts->where('type','department')->count() }} Depts</h3>
          <p class="text-slate-500 text-[10px] mt-0.5">HR, IT, Marketing & Ops</p>
        </div>
      </div>
    </div>
  </div>

  {{-- ══════════ DEPARTMENT ACCOUNTS STRIP ══════════ --}}
  <div class="glass p-5 rounded-2xl">
    <div class="flex items-center justify-between mb-4">
      <h2 class="font-outfit font-black text-white text-base flex items-center gap-2">
        <i class="fa-solid fa-building text-amber-400"></i> Department Accounts & Balances
      </h2>
      <button onclick="fModal('transferModal')" class="text-xs font-bold text-emerald-400 hover:text-emerald-300 flex items-center gap-1 transition">
        <i class="fa-solid fa-paper-plane"></i> Transfer Funds
      </button>
    </div>
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-7 gap-3">
      @php $acctColors = ['bank'=>'emerald','department'=>'sky','client'=>'violet','staff'=>'amber']; @endphp
      @foreach($accounts as $acc)
      @php $c = $acctColors[$acc->type] ?? 'slate'; @endphp
      <div class="glass-light p-3 rounded-xl hover:border-{{ $c }}-500/40 transition group">
        <div class="w-7 h-7 rounded-lg bg-{{ $c }}-500/20 flex items-center justify-center text-{{ $c }}-400 text-[11px] mb-2">
          <i class="fa-solid {{ $acc->type === 'bank' ? 'fa-building-columns' : ($acc->type === 'staff' ? 'fa-user-tie' : ($acc->type === 'client' ? 'fa-handshake' : 'fa-briefcase')) }}"></i>
        </div>
        <p class="text-[10px] font-black text-slate-300 uppercase tracking-wide truncate">{{ $acc->name }}</p>
        <p class="font-outfit font-black text-sm mt-1 {{ $acc->balance > 0 ? 'text-emerald-400' : 'text-slate-500' }}">
          ₹{{ number_format($acc->balance, 0) }}
        </p>
        <p class="text-[9px] text-slate-600 font-mono mt-0.5 truncate">{{ $acc->account_number }}</p>
      </div>
      @endforeach
    </div>
  </div>

  {{-- ══════════ STAFF SALARY ══════════ --}}
  <div class="glass rounded-2xl overflow-hidden">
    <div class="p-5 flex items-center justify-between border-b border-white/[0.06] flex-wrap gap-3">
      <div>
        <h2 class="font-outfit font-black text-white text-base flex items-center gap-2">
          <i class="fa-solid fa-users-gear text-amber-400"></i> Staff Salary Management
        </h2>
        <p class="text-slate-400 text-[10px] mt-0.5">Auto-calculated from work log · ₹1,000/hr · PIN required for payments</p>
      </div>
      <span class="badge badge-amber"><i class="fa-solid fa-calculator"></i> {{ $staff->count() }} Staff</span>
    </div>

    @if($staff->isEmpty())
    <div class="py-16 text-center text-slate-500 text-sm">No staff members registered.</div>
    @else
    <div class="overflow-x-auto">
      <table class="fin-table w-full text-left">
        <thead>
          <tr>
            <th>Staff Member</th>
            <th>Bank Details</th>
            <th>Hrs (Month)</th>
            <th>Rate</th>
            <th>Earned</th>
            <th>Due</th>
            <th>Last Paid</th>
            <th class="text-right">Action</th>
          </tr>
        </thead>
        <tbody>
          @foreach($staff as $s)
          <tr>
            <td>
              <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl grad-emerald flex items-center justify-center text-white font-black text-sm shadow-md shadow-emerald-500/20 flex-shrink-0">
                  {{ strtoupper(substr($s->name, 0, 1)) }}
                </div>
                <div>
                  <a href="{{ route('finance.staff', $s) }}" class="font-bold text-white hover:text-emerald-400 transition block">{{ $s->name }}</a>
                  <p class="text-[10px] text-slate-500">{{ $s->email }}</p>
                </div>
              </div>
            </td>
            <td class="font-mono text-[11px]">
              @if($s->bank_account)
                <p class="font-bold text-slate-200">{{ $s->bank_name ?? 'Bank' }}</p>
                <p class="text-[10px] text-slate-500">A/C: {{ $s->bank_account }}</p>
              @else
                <span class="text-slate-600 italic text-xs">Not set</span>
              @endif
            </td>
            <td><span class="font-outfit font-black text-sky-400 text-sm">{{ $s->month_hours }}</span><span class="text-slate-500 text-xs ml-1">hrs</span></td>
            <td class="font-bold text-slate-400 text-xs">₹1,000</td>
            <td><span class="font-outfit font-black text-emerald-400">₹{{ number_format($s->month_earned) }}</span></td>
            <td>
              @if($s->pending_amount > 0)
                <span class="badge badge-rose"><i class="fa-solid fa-clock"></i> ₹{{ number_format($s->pending_amount) }}</span>
              @else
                <span class="badge badge-green"><i class="fa-solid fa-check"></i> Clear</span>
              @endif
            </td>
            <td class="text-[11px] text-slate-400">{{ $s->last_payment ? $s->last_payment->created_at->format('d M Y') : 'Never' }}</td>
            <td class="text-right">
              <button onclick="openPayStaffModal({{ json_encode(['id'=>$s->id,'name'=>$s->name,'email'=>$s->email,'hours'=>$s->month_hours,'earned'=>$s->month_earned,'pending'=>$s->pending_amount,'bank_account'=>$s->bank_account,'bank_name'=>$s->bank_name]) }})"
                      class="px-3.5 py-1.5 rounded-xl text-xs font-bold text-white grad-amber shadow-md shadow-amber-500/20 hover:opacity-90 transition inline-flex items-center gap-1.5">
                <i class="fa-solid fa-money-bill-wave"></i> Pay
              </button>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
    @endif
  </div>

  {{-- ══════════ TRANSACTION HISTORY ══════════ --}}
  <div class="glass rounded-2xl overflow-hidden">
    <div class="p-5 flex items-center justify-between flex-wrap gap-4 border-b border-white/[0.06]">
      <div>
        <h2 class="font-outfit font-black text-white text-base flex items-center gap-2">
          <i class="fa-solid fa-receipt text-emerald-400"></i> Transaction Ledger
        </h2>
        <p class="text-slate-400 text-[10px] mt-0.5">Live log — click status to update, mark icon to stamp</p>
      </div>
      <form method="GET" action="{{ route('finance.dashboard') }}" class="flex items-center gap-2 flex-wrap">
        <div class="relative">
          <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
          <input type="text" name="q" value="{{ request('q') }}" placeholder="Search transactions..."
                 class="fin-inp pl-8 w-48 py-1.5 text-xs">
        </div>
        <select name="status" onchange="this.form.submit()" class="fin-inp px-3 py-1.5 w-32 text-xs">
          <option value="all">All Statuses</option>
          <option value="pending"   {{ request('status') === 'pending'   ? 'selected' : '' }}>Pending</option>
          <option value="approved"  {{ request('status') === 'approved'  ? 'selected' : '' }}>Approved</option>
          <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
          <option value="failed"    {{ request('status') === 'failed'    ? 'selected' : '' }}>Failed</option>
        </select>
        <select name="type" onchange="this.form.submit()" class="fin-inp px-3 py-1.5 w-36 text-xs">
          <option value="all">All Types</option>
          <option value="salary"         {{ request('type') === 'salary'         ? 'selected' : '' }}>Salary</option>
          <option value="transfer"       {{ request('type') === 'transfer'       ? 'selected' : '' }}>Transfer</option>
          <option value="client_payment" {{ request('type') === 'client_payment' ? 'selected' : '' }}>Client Income</option>
          <option value="deposit"        {{ request('type') === 'deposit'        ? 'selected' : '' }}>Deposit</option>
        </select>
        @if(request()->hasAny(['q','status','type']))
        <a href="{{ route('finance.dashboard') }}" class="px-3 py-1.5 text-xs font-bold text-rose-400 bg-rose-500/10 rounded-xl border border-rose-500/25 hover:bg-rose-500/20 transition">
          <i class="fa-solid fa-xmark"></i>
        </a>
        @endif
      </form>
    </div>

    @if($transactions->isEmpty())
    <div class="py-16 text-center text-slate-500">No transactions found.</div>
    @else
    <div class="overflow-x-auto">
      <table class="fin-table w-full text-left">
        <thead>
          <tr>
            <th>Code</th>
            <th>Date & Time</th>
            <th>Type</th>
            <th>Sender → Receiver</th>
            <th>Amount</th>
            <th>Notes / Mark</th>
            <th>Status</th>
            <th class="text-right">Actions</th>
          </tr>
        </thead>
        <tbody>
          @foreach($transactions as $t)
          <tr>
            <td class="font-mono font-bold text-amber-400 text-[11px]">
              {{ $t->transaction_code }}
              @if($t->auto_approved)<span class="text-[9px] text-amber-400/70 block"><i class="fa-solid fa-bolt"></i> Auto</span>@endif
            </td>
            <td class="text-[11px]">
              <div class="font-bold text-slate-200">{{ $t->created_at->format('d M Y') }}</div>
              <div class="text-slate-500 text-[10px]">{{ $t->created_at->format('h:i A') }}</div>
            </td>
            <td>
              <span class="px-2 py-0.5 rounded-lg text-[10px] font-bold uppercase bg-white/[0.04] border border-white/[0.08] text-slate-300">
                {{ str_replace('_', ' ', $t->transaction_type) }}
              </span>
            </td>
            <td class="text-[11px]">
              <div class="font-semibold text-slate-200 max-w-[160px] truncate">{{ $t->sender_name }}</div>
              <div class="text-slate-500 text-[10px] max-w-[160px] truncate">→ {{ $t->receiver_name }}</div>
            </td>
            <td>
              @if($t->flow_type === 'income')
                <span class="font-outfit font-black text-emerald-400">+ ₹{{ number_format($t->amount, 2) }}</span>
              @elseif($t->flow_type === 'expense')
                <span class="font-outfit font-black text-rose-400">− ₹{{ number_format($t->amount, 2) }}</span>
              @else
                <span class="font-outfit font-black text-sky-400">⇄ ₹{{ number_format($t->amount, 2) }}</span>
              @endif
            </td>
            <td class="text-[10px] text-slate-500 max-w-[140px]">
              <p class="truncate" title="{{ $t->notes }}">{{ $t->notes ? Str::limit($t->notes, 30) : '—' }}</p>
              <button onclick="openMarkModal({{ $t->id }}, '{{ route('finance.transactions.mark', $t) }}')"
                      class="mt-1 text-[9px] text-amber-400/70 hover:text-amber-400 transition flex items-center gap-1">
                <i class="fa-solid fa-stamp"></i> Stamp Note
              </button>
            </td>
            <td>
              @php
                $badges = ['completed'=>'badge-green','approved'=>'badge-sky','pending'=>'badge-amber','failed'=>'badge-rose'];
                $icons  = ['completed'=>'fa-check','approved'=>'fa-thumbs-up','pending'=>'fa-clock','failed'=>'fa-xmark'];
              @endphp
              <span class="badge {{ $badges[$t->status] ?? 'badge-gray' }}">
                <i class="fa-solid {{ $icons[$t->status] ?? 'fa-circle' }}"></i>
                {{ ucfirst($t->status) }}
              </span>
            </td>
            <td class="text-right">
              <form method="POST" action="{{ route('finance.transactions.status', $t) }}" class="inline">
                @csrf
                <select name="status" onchange="this.form.submit()"
                        class="px-2 py-1 text-[10px] bg-white/5 border border-white/10 rounded-lg text-slate-200 focus:outline-none focus:border-emerald-500">
                  <option value="pending"   {{ $t->status === 'pending'   ? 'selected' : '' }}>Pending</option>
                  <option value="approved"  {{ $t->status === 'approved'  ? 'selected' : '' }}>Approved</option>
                  <option value="completed" {{ $t->status === 'completed' ? 'selected' : '' }}>Completed</option>
                  <option value="failed"    {{ $t->status === 'failed'    ? 'selected' : '' }}>Failed</option>
                </select>
              </form>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
    <div class="p-4 border-t border-white/[0.06]">
      {{ $transactions->links() }}
    </div>
    @endif
  </div>

</div>{{-- end max-w --}}


{{-- ══════════ MODALS ══════════ --}}

{{-- PIN UNLOCK MODAL --}}
<div id="pinModal" class="fin-modal">
  <div class="fin-modal-box" style="max-width:400px;text-align:center;">
    <button onclick="fClose('pinModal')" class="absolute top-4 right-4 text-slate-400 hover:text-white"><i class="fa-solid fa-xmark text-lg"></i></button>
    <div class="w-16 h-16 rounded-2xl grad-emerald flex items-center justify-center text-white text-2xl mx-auto shadow-lg shadow-emerald-500/30 mb-4">
      <i class="fa-solid fa-vault"></i>
    </div>
    <h3 class="font-outfit font-black text-white text-xl mb-1">Unlock Vault Balance</h3>
    <p class="text-slate-400 text-xs mb-5">Enter your security PIN to reveal the live bank balance</p>
    <form method="POST" action="{{ route('finance.verify-pin') }}" class="space-y-4">
      @csrf
      <input type="password" name="pin" maxlength="6" autofocus required placeholder="Enter PIN"
             class="fin-inp text-center text-2xl tracking-[0.5em] font-mono text-emerald-400">
      <button type="submit" class="w-full py-3 rounded-2xl font-bold text-white grad-emerald shadow-lg shadow-emerald-500/25 hover:opacity-90 transition text-sm">
        <i class="fa-solid fa-key"></i> Verify & Reveal
      </button>
    </form>
  </div>
</div>

{{-- ACTION PIN MODAL (before payment/transfer) --}}
<div id="actionPinModal" class="fin-modal">
  <div class="fin-modal-box" style="max-width:400px;text-align:center;">
    <button onclick="fClose('actionPinModal')" class="absolute top-4 right-4 text-slate-400 hover:text-white"><i class="fa-solid fa-xmark text-lg"></i></button>
    <div class="w-16 h-16 rounded-2xl grad-amber flex items-center justify-center text-white text-2xl mx-auto shadow-lg shadow-amber-500/30 mb-4">
      <i class="fa-solid fa-shield-halved"></i>
    </div>
    <h3 class="font-outfit font-black text-white text-xl mb-1">Confirm with PIN</h3>
    <p class="text-slate-400 text-xs mb-5">Enter your security PIN to authorize this financial action</p>
    <div class="space-y-4">
      <input type="password" id="actionPinInput" maxlength="6" placeholder="Enter PIN"
             class="fin-inp text-center text-2xl tracking-[0.5em] font-mono text-amber-400">
      <p id="actionPinError" class="text-rose-400 text-xs hidden"></p>
      <button onclick="submitActionPin()" class="w-full py-3 rounded-2xl font-bold text-white grad-amber shadow-lg shadow-amber-500/25 hover:opacity-90 transition text-sm">
        <i class="fa-solid fa-lock-open"></i> Authorize Action
      </button>
    </div>
  </div>
</div>

{{-- MARK/STAMP MODAL --}}
<div id="markModal" class="fin-modal">
  <div class="fin-modal-box" style="max-width:420px;">
    <button onclick="fClose('markModal')" class="absolute top-4 right-4 text-slate-400 hover:text-white"><i class="fa-solid fa-xmark text-lg"></i></button>
    <div class="flex items-center gap-3 mb-5">
      <div class="w-11 h-11 rounded-xl grad-amber flex items-center justify-center text-white"><i class="fa-solid fa-stamp"></i></div>
      <div>
        <h3 class="font-outfit font-black text-white">Stamp / Mark Transaction</h3>
        <p class="text-slate-400 text-xs">Add a note stamp to this transaction record</p>
      </div>
    </div>
    <form id="markForm" method="POST" action="" class="space-y-4">
      @csrf
      <div>
        <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider mb-1.5">Mark / Note</label>
        <input type="text" name="mark_note" required maxlength="200" placeholder="e.g. Verified by Finance Head, Disputed — under review"
               class="fin-inp">
      </div>
      <button type="submit" class="w-full py-3 rounded-2xl font-bold text-white grad-amber shadow-lg hover:opacity-90 transition text-sm">
        <i class="fa-solid fa-stamp"></i> Apply Mark Stamp
      </button>
    </form>
  </div>
</div>

{{-- EXPORT VAULT MODAL --}}
<div id="exportModal" class="fin-modal">
  <div class="fin-modal-box" style="max-width:480px;">
    <button onclick="fClose('exportModal')" class="absolute top-4 right-4 text-slate-400 hover:text-white"><i class="fa-solid fa-xmark text-lg"></i></button>
    <div class="flex items-center gap-3 mb-4">
      <div class="w-12 h-12 rounded-2xl grad-violet flex items-center justify-center text-white text-xl shadow-lg shadow-violet-500/30"><i class="fa-solid fa-vault"></i></div>
      <div>
        <h3 class="font-outfit font-black text-white text-lg">Export Finance Vault</h3>
        <span class="badge badge-violet text-[10px]"><i class="fa-solid fa-shield-halved"></i> Authorized Recipient Only</span>
      </div>
    </div>
    <div class="p-4 rounded-2xl bg-white/[0.03] border border-white/10 mb-5 space-y-1.5 text-[11px] text-slate-400">
      <p class="flex items-center gap-2"><i class="fa-solid fa-envelope text-violet-400"></i> <strong class="text-slate-300">To:</strong> akpatel2029@gmail.com <span class="badge badge-violet text-[9px]">Fixed</span></p>
      <p class="flex items-center gap-2"><i class="fa-solid fa-file-pdf text-rose-400"></i> <strong class="text-slate-300">Format:</strong> PDF Data Sheet — All accounts, transactions & payroll</p>
      <p class="flex items-center gap-2"><i class="fa-solid fa-lock text-amber-400"></i> <strong class="text-slate-300">Security:</strong> Confidential — sent to authorized admin only</p>
    </div>
    <button onclick="sendExportSheet()" id="exportBtn"
            class="w-full py-3 rounded-2xl font-bold text-white grad-violet shadow-lg shadow-violet-500/20 hover:opacity-90 transition text-sm flex items-center justify-center gap-2">
      <i class="fa-solid fa-paper-plane"></i> Export & Send to akpatel2029@gmail.com
    </button>
    <p id="exportResult" class="text-center text-xs mt-3 hidden"></p>
  </div>
</div>

{{-- TRANSFER MODAL --}}
<div id="transferModal" class="fin-modal">
  <div class="fin-modal-box">
    <button onclick="fClose('transferModal')" class="absolute top-4 right-4 text-slate-400 hover:text-white"><i class="fa-solid fa-xmark text-lg"></i></button>
    <div class="flex items-center gap-3 border-b border-white/10 pb-4 mb-5">
      <div class="w-10 h-10 rounded-xl grad-sky flex items-center justify-center text-white"><i class="fa-solid fa-right-left"></i></div>
      <div>
        <h3 class="font-outfit font-black text-white text-lg">Money Transfer</h3>
        <p class="text-slate-400 text-xs">Move funds between accounts — PIN required</p>
      </div>
    </div>
    <form id="transferForm" class="space-y-4" onsubmit="requirePinThen(event, this)">
      @csrf
      <input type="hidden" name="_action_url" value="{{ route('finance.transfer') }}">
      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider mb-1.5">From Account</label>
          <select name="from_account_id" required class="fin-inp text-xs">
            @foreach($accounts as $a)
            <option value="{{ $a->id }}">{{ $a->name }} (₹{{ number_format($a->balance, 0) }})</option>
            @endforeach
          </select>
        </div>
        <div>
          <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider mb-1.5">To Account</label>
          <select name="to_account_id" required class="fin-inp text-xs">
            @foreach($accounts as $a)
            <option value="{{ $a->id }}" {{ $loop->index === 1 ? 'selected' : '' }}>{{ $a->name }} (₹{{ number_format($a->balance, 0) }})</option>
            @endforeach
          </select>
        </div>
      </div>
      <div>
        <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider mb-1.5">Amount (₹)</label>
        <div class="relative">
          <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-sky-400 font-bold text-sm">₹</span>
          <input type="number" name="amount" step="0.01" min="1" required placeholder="5000" class="fin-inp pl-8">
        </div>
      </div>
      <div>
        <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider mb-1.5">Status</label>
        <select name="status" class="fin-inp text-xs">
          <option value="completed">Completed (Instant)</option>
          <option value="approved">Approved</option>
          <option value="pending">Pending</option>
        </select>
      </div>
      <div class="flex items-center gap-2">
        <input type="checkbox" name="auto_approve" value="1" checked class="w-4 h-4 accent-sky-500">
        <label class="text-xs text-slate-300 font-semibold">Auto-Approve & Instant Balance Update</label>
      </div>
      <div>
        <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider mb-1.5">Notes (Optional)</label>
        <textarea name="notes" rows="2" placeholder="Purpose of transfer..." class="fin-inp"></textarea>
      </div>
      <button type="submit" class="w-full py-3 rounded-2xl font-bold text-white grad-sky shadow-lg hover:opacity-90 transition text-sm">
        <i class="fa-solid fa-paper-plane"></i> Execute Transfer
      </button>
    </form>
  </div>
</div>

{{-- CLIENT PAYMENT MODAL --}}
<div id="clientPaymentModal" class="fin-modal">
  <div class="fin-modal-box">
    <button onclick="fClose('clientPaymentModal')" class="absolute top-4 right-4 text-slate-400 hover:text-white"><i class="fa-solid fa-xmark text-lg"></i></button>
    <div class="flex items-center gap-3 border-b border-white/10 pb-4 mb-5">
      <div class="w-10 h-10 rounded-xl grad-emerald flex items-center justify-center text-white"><i class="fa-solid fa-plus-circle"></i></div>
      <div>
        <h3 class="font-outfit font-black text-white text-lg">Record Client Income</h3>
        <p class="text-slate-400 text-xs">Add incoming course fee or client revenue — PIN required</p>
      </div>
    </div>
    <form id="clientForm" class="space-y-4" onsubmit="requirePinThen(event, this)">
      @csrf
      <input type="hidden" name="_action_url" value="{{ route('finance.client-payment') }}">
      <div>
        <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider mb-1.5">Client / Student Name *</label>
        <input type="text" name="client_name" required placeholder="John Doe" class="fin-inp">
      </div>
      <div>
        <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider mb-1.5">Client Email</label>
        <input type="email" name="client_email" placeholder="john@example.com" class="fin-inp">
      </div>
      <div>
        <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider mb-1.5">Income Amount (₹)</label>
        <div class="relative">
          <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-emerald-400 font-bold text-sm">+ ₹</span>
          <input type="number" name="amount" step="0.01" min="1" required placeholder="15000" class="fin-inp pl-12">
        </div>
      </div>
      <div>
        <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider mb-1.5">Status</label>
        <select name="status" class="fin-inp text-xs">
          <option value="completed">Completed (Adds to Balance)</option>
          <option value="approved">Approved</option>
          <option value="pending">Pending</option>
        </select>
      </div>
      <div class="flex items-center gap-2">
        <input type="checkbox" name="auto_approve" value="1" checked class="w-4 h-4 accent-emerald-500">
        <label class="text-xs text-slate-300 font-semibold">Auto-Approve & Update Bank Balance</label>
      </div>
      <div>
        <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider mb-1.5">Notes</label>
        <input type="text" name="notes" placeholder="e.g. Full Stack Course Fee" class="fin-inp">
      </div>
      <button type="submit" class="w-full py-3 rounded-2xl font-bold text-white grad-emerald shadow-lg hover:opacity-90 transition text-sm">
        <i class="fa-solid fa-circle-check"></i> Record Income Payment
      </button>
    </form>
  </div>
</div>

{{-- PAY STAFF SALARY MODAL --}}
<div id="payStaffModal" class="fin-modal">
  <div class="fin-modal-box">
    <button onclick="fClose('payStaffModal')" class="absolute top-4 right-4 text-slate-400 hover:text-white"><i class="fa-solid fa-xmark text-lg"></i></button>
    <div class="flex items-center gap-3 border-b border-white/10 pb-4 mb-5">
      <div class="w-10 h-10 rounded-xl grad-amber flex items-center justify-center text-white"><i class="fa-solid fa-money-bill-wave"></i></div>
      <div>
        <h3 class="font-outfit font-black text-white text-lg" id="staffModalTitle">Pay Staff Salary</h3>
        <p class="text-slate-400 text-xs">₹1,000/hr · Deducted from Main Bank · PIN required</p>
      </div>
    </div>

    <form id="payStaffForm" class="space-y-4" onsubmit="requirePinThen(event, this)">
      @csrf
      <input type="hidden" name="_action_url" id="payStaffActionUrl" value="">

      <div class="p-3.5 rounded-2xl bg-white/[0.04] border border-white/10 space-y-1 text-xs">
        <p class="text-slate-400">Staff: <strong id="staffModalName" class="text-white"></strong></p>
        <p class="text-slate-400">Month Hours: <strong id="staffModalHours" class="text-sky-400"></strong></p>
        <p class="text-slate-400">Earned: <strong id="staffModalEarned" class="text-emerald-400"></strong></p>
      </div>

      <div>
        <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider mb-1.5">Period Label</label>
        <input type="text" name="period_label" required value="{{ now()->format('F Y') }} Salary" class="fin-inp">
      </div>

      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider mb-1.5">Hours Worked</label>
          <input type="number" step="0.1" min="0" name="hours_worked" id="staffModalHoursInput" required oninput="calcSalary()" class="fin-inp font-bold">
        </div>
        <div>
          <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider mb-1.5">Amount (₹)</label>
          <div class="relative">
            <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-rose-400 font-bold">−₹</span>
            <input type="number" step="0.01" min="1" name="amount" id="staffModalAmountInput" required class="fin-inp pl-9 font-bold">
          </div>
        </div>
      </div>

      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider mb-1.5">Bank Account #</label>
          <input type="text" name="bank_account" id="staffModalBankAcc" placeholder="Account No." class="fin-inp font-mono text-xs">
        </div>
        <div>
          <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider mb-1.5">Bank Name</label>
          <input type="text" name="bank_name" id="staffModalBankName" placeholder="HDFC Bank" class="fin-inp text-xs">
        </div>
      </div>

      <div>
        <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider mb-1.5">Status</label>
        <select name="status" class="fin-inp text-xs">
          <option value="completed">Completed (Deducts from Main Bank)</option>
          <option value="approved">Approved</option>
          <option value="pending">Pending</option>
        </select>
      </div>

      <div class="flex items-center gap-2">
        <input type="checkbox" name="auto_approve" value="1" checked class="w-4 h-4 accent-amber-500">
        <label class="text-xs text-slate-300 font-semibold">Auto-Approve & Send Salary Email</label>
      </div>

      <div>
        <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider mb-1.5">Notes</label>
        <input type="text" name="notes" placeholder="e.g. Monthly salary payout" class="fin-inp text-xs">
      </div>

      <button type="submit" class="w-full py-3 rounded-2xl font-bold text-white grad-amber shadow-lg shadow-amber-500/20 hover:opacity-90 transition text-sm">
        <i class="fa-solid fa-paper-plane"></i> Process Salary Payment
      </button>
    </form>
  </div>
</div>


{{-- ══════════ SCRIPTS ══════════ --}}
<script>
  // Modal open/close
  function fModal(id) {
    const m = document.getElementById(id);
    if (m) { m.classList.add('open'); }
  }
  function fClose(id) {
    const m = document.getElementById(id);
    if (m) { m.classList.remove('open'); }
  }

  // Close on backdrop click
  document.querySelectorAll('.fin-modal').forEach(m => {
    m.addEventListener('click', e => { if (e.target === m) m.classList.remove('open'); });
  });

  // Open PIN modal
  function openPinModal() { fModal('pinModal'); }

  // Lock balance
  function lockBalance() {
    fetch("{{ route('finance.verify-pin') }}", {
      method: 'POST',
      headers: { 'X-CSRF-TOKEN': "{{ csrf_token() }}", 'Content-Type': 'application/json', 'Accept': 'application/json' },
      body: JSON.stringify({ pin: 'lock' })
    }).then(() => window.location.reload());
  }

  // Open pay staff modal
  function openPayStaffModal(s) {
    document.getElementById('staffModalTitle').innerText = 'Pay: ' + s.name;
    document.getElementById('staffModalName').innerText = s.name + ' (' + s.email + ')';
    document.getElementById('staffModalHours').innerText = s.hours + ' hrs';
    document.getElementById('staffModalEarned').innerText = '₹' + (s.earned || 0).toLocaleString();
    document.getElementById('staffModalHoursInput').value = s.hours;
    document.getElementById('staffModalAmountInput').value = s.pending > 0 ? s.pending : s.earned;
    document.getElementById('staffModalBankAcc').value = s.bank_account || '';
    document.getElementById('staffModalBankName').value = s.bank_name || '';
    document.getElementById('payStaffActionUrl').value = '/finance/staff/' + s.id + '/pay';
    fModal('payStaffModal');
  }

  function calcSalary() {
    const hrs = parseFloat(document.getElementById('staffModalHoursInput').value) || 0;
    document.getElementById('staffModalAmountInput').value = Math.round(hrs * 1000);
  }

  // Open mark/stamp modal
  function openMarkModal(txnId, url) {
    document.getElementById('markForm').action = url;
    fModal('markModal');
  }

  // ── PIN-before-action system ──────────────────────────────────────────────
  let _pendingFormEl = null;
  let _pendingFormUrl = null;

  function requirePinThen(event, formEl) {
    event.preventDefault();
    _pendingFormEl  = formEl;
    _pendingFormUrl = formEl.querySelector('[name="_action_url"]')?.value || formEl.action;
    document.getElementById('actionPinInput').value = '';
    document.getElementById('actionPinError').classList.add('hidden');
    fModal('actionPinModal');
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
        // Now actually submit the form to real URL
        if (_pendingFormEl) {
          const hiddenUrl = _pendingFormEl.querySelector('[name="_action_url"]');
          if (hiddenUrl) { _pendingFormEl.action = hiddenUrl.value; }
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

  // Allow Enter key in PIN input
  document.getElementById('actionPinInput')?.addEventListener('keydown', e => {
    if (e.key === 'Enter') { e.preventDefault(); submitActionPin(); }
  });

  // ── Export vault sheet ──────────────────────────────────────────────────
  async function sendExportSheet() {
    const btn = document.getElementById('exportBtn');
    const res = document.getElementById('exportResult');
    btn.disabled = true;
    btn.innerHTML = '<i class="fa-solid fa-spinner animate-spin"></i> Exporting...';
    res.classList.add('hidden');

    try {
      const r = await fetch("{{ route('finance.export-sheet') }}", {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': "{{ csrf_token() }}", 'Accept': 'application/json', 'Content-Type': 'application/json' }
      });
      const d = await r.json();
      res.textContent = d.message;
      res.className = 'text-center text-xs mt-3 ' + (d.ok ? 'text-emerald-400' : 'text-rose-400');
      res.classList.remove('hidden');
    } catch (err) {
      res.textContent = '❌ Export request failed.';
      res.className = 'text-center text-xs mt-3 text-rose-400';
      res.classList.remove('hidden');
    } finally {
      btn.disabled = false;
      btn.innerHTML = '<i class="fa-solid fa-paper-plane"></i> Export & Send to akpatel2029@gmail.com';
    }
  }

  // Escape key closes modals
  document.addEventListener('keydown', e => {
    if (e.key === 'Escape') {
      document.querySelectorAll('.fin-modal.open').forEach(m => m.classList.remove('open'));
    }
  });
</script>

@include('partials.global-loader')
</body>
</html>
