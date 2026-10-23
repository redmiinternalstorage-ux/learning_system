<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Transaction Ledger — Finance Vault</title>
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
  .badge { display:inline-flex; align-items:center; gap:0.25rem; padding:0.2rem 0.6rem; border-radius:9999px; font-size:0.68rem; font-weight:700; text-transform:uppercase; letter-spacing:0.05em; }
  .badge-green  { background:rgba(5,150,105,0.2);  color:#34d399; border:1px solid rgba(5,150,105,0.4); }
  .badge-amber  { background:rgba(217,119,6,0.2);  color:#fbbf24; border:1px solid rgba(217,119,6,0.4); }
  .badge-rose   { background:rgba(225,29,72,0.2);  color:#fb7185; border:1px solid rgba(225,29,72,0.4); }
  .badge-sky    { background:rgba(2,132,199,0.2);  color:#38bdf8; border:1px solid rgba(2,132,199,0.4); }
  .badge-gray   { background:rgba(100,116,139,0.2);color:#94a3b8; border:1px solid rgba(100,116,139,0.4); }
  .fin-table th { padding:0.75rem 1rem; font-size:0.65rem; font-weight:800; text-transform:uppercase; letter-spacing:0.1em; color:#6ee7b7; background:rgba(5,150,105,0.1); border-bottom:1px solid rgba(5,150,105,0.2); }
  .fin-table td { padding:0.875rem 1rem; font-size:0.75rem; border-bottom:1px solid rgba(255,255,255,0.04); color:#cbd5e1; }
  .fin-table tr:hover td { background:rgba(255,255,255,0.02); }
  .fin-inp { width:100%; padding:0.6rem 0.875rem; background:rgba(255,255,255,0.05); border:1px solid rgba(255,255,255,0.12); border-radius:0.75rem; color:#e2e8f0; font-size:0.75rem; font-family:'Plus Jakarta Sans',sans-serif; }
  .fin-inp:focus { outline:none; border-color:#059669; }
  .fin-inp option { background:#1e293b; }
  .orb { position:absolute; border-radius:9999px; filter:blur(80px); pointer-events:none; opacity:0.25; }
  ::-webkit-scrollbar { width:5px; } ::-webkit-scrollbar-thumb { background:rgba(5,150,105,0.4); border-radius:9999px; }
  @media print {
    body { background:#fff !important; color:#000 !important; }
    .no-print { display:none !important; }
    .glass, .glass-light { background:#f8fafc !important; border:1px solid #e2e8f0 !important; }
    .fin-table th { background:#f0fdf4 !important; color:#047857 !important; }
  }
</style>
</head>
<body class="relative">
<div class="orb w-80 h-80 bg-emerald-500 top-0 left-0 -z-0"></div>
<div class="orb w-60 h-60 bg-teal-600 bottom-10 right-10 -z-0"></div>

<div class="relative z-10 max-w-[1400px] mx-auto px-4 py-6 space-y-6">

  {{-- Header --}}
  <div class="glass p-4 rounded-2xl flex items-center justify-between flex-wrap gap-3 no-print">
    <div class="flex items-center gap-3.5">
      <a href="{{ route('finance.dashboard') }}"
         class="w-10 h-10 rounded-xl bg-emerald-500/15 border border-emerald-500/30 flex items-center justify-center text-emerald-400 hover:bg-emerald-500/25 transition">
        <i class="fa-solid fa-arrow-left text-sm"></i>
      </a>
      <div>
        <h1 class="font-outfit font-black text-white text-xl flex items-center gap-2">
          <i class="fa-solid fa-receipt text-emerald-400"></i> Transaction Ledger
        </h1>
        <p class="text-slate-400 text-[10px] mt-0.5">Complete audit log · {{ $transactions->total() }} total transactions</p>
      </div>
    </div>
    <div class="flex items-center gap-2">
      <button onclick="window.print()" class="flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-bold bg-white/5 border border-white/10 text-slate-300 hover:bg-white/10 transition">
        <i class="fa-solid fa-print text-emerald-400"></i> Print / Export
      </button>
      <a href="{{ route('finance.dashboard') }}" class="flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold text-white grad-emerald shadow-md shadow-emerald-500/20">
        <i class="fa-solid fa-vault"></i> Dashboard
      </a>
    </div>
  </div>

  {{-- Flash --}}
  @if(session('success'))
  <div class="p-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-300 text-sm font-semibold flex items-center gap-3">
    <i class="fa-solid fa-circle-check text-emerald-400 text-lg"></i> {{ session('success') }}
  </div>
  @endif

  {{-- Summary Bar --}}
  <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
    @php
      $income   = $transactions->where('flow_type','income')->sum('amount');
      $expenses = $transactions->where('flow_type','expense')->sum('amount');
      $pending  = $transactions->where('status','pending')->count();
      $completed= $transactions->where('status','completed')->count();
    @endphp
    @foreach([
      ['label'=>'Page Income','val'=>'+ ₹'.number_format($income,0),'col'=>'text-emerald-400','icon'=>'fa-arrow-trend-up','bg'=>'bg-emerald-500/10 border-emerald-500/25'],
      ['label'=>'Page Expenses','val'=>'− ₹'.number_format($expenses,0),'col'=>'text-rose-400','icon'=>'fa-arrow-trend-down','bg'=>'bg-rose-500/10 border-rose-500/25'],
      ['label'=>'Pending on Page','val'=>$pending.' txns','col'=>'text-amber-400','icon'=>'fa-hourglass-half','bg'=>'bg-amber-500/10 border-amber-500/25'],
      ['label'=>'Completed on Page','val'=>$completed.' txns','col'=>'text-sky-400','icon'=>'fa-circle-check','bg'=>'bg-sky-500/10 border-sky-500/25'],
    ] as $s)
    <div class="glass-light p-4 rounded-2xl {{ $s['bg'] }} border flex items-center gap-3">
      <div class="w-9 h-9 rounded-xl {{ $s['bg'] }} border flex items-center justify-center text-sm {{ $s['col'] }}">
        <i class="fa-solid {{ $s['icon'] }}"></i>
      </div>
      <div>
        <p class="text-[9px] font-bold text-slate-400 uppercase tracking-wider">{{ $s['label'] }}</p>
        <p class="font-outfit font-black {{ $s['col'] }} text-sm mt-0.5">{{ $s['val'] }}</p>
      </div>
    </div>
    @endforeach
  </div>

  {{-- Filters --}}
  <div class="glass p-5 rounded-2xl no-print">
    <form method="GET" action="{{ route('finance.history') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
      <div class="relative lg:col-span-2">
        <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Search code, sender, receiver..."
               class="fin-inp pl-9">
      </div>
      <div>
        <select name="status" onchange="this.form.submit()" class="fin-inp">
          <option value="all" {{ request('status','all')==='all'?'selected':'' }}>All Statuses</option>
          <option value="pending"   {{ request('status')==='pending'  ?'selected':'' }}>Pending</option>
          <option value="approved"  {{ request('status')==='approved' ?'selected':'' }}>Approved</option>
          <option value="completed" {{ request('status')==='completed'?'selected':'' }}>Completed</option>
          <option value="failed"    {{ request('status')==='failed'   ?'selected':'' }}>Failed</option>
        </select>
      </div>
      <div>
        <select name="type" onchange="this.form.submit()" class="fin-inp">
          <option value="all" {{ request('type','all')==='all'?'selected':'' }}>All Types</option>
          <option value="salary"         {{ request('type')==='salary'        ?'selected':'' }}>Staff Salary</option>
          <option value="transfer"       {{ request('type')==='transfer'      ?'selected':'' }}>Transfer</option>
          <option value="client_payment" {{ request('type')==='client_payment'?'selected':'' }}>Client Fee</option>
          <option value="deposit"        {{ request('type')==='deposit'       ?'selected':'' }}>Deposit</option>
        </select>
      </div>
      <div class="flex gap-2">
        <button type="submit" class="flex-1 py-2 rounded-xl text-xs font-bold text-white grad-emerald shadow-sm">Apply</button>
        @if(request()->hasAny(['q','status','type']))
        <a href="{{ route('finance.history') }}" class="px-3 py-2 rounded-xl text-xs font-bold text-rose-400 bg-rose-500/10 border border-rose-500/25 hover:bg-rose-500/20">Clear</a>
        @endif
      </div>
    </form>
  </div>

  {{-- Transactions Table --}}
  <div class="glass rounded-2xl overflow-hidden">
    @if($transactions->isEmpty())
    <div class="py-20 text-center text-slate-500">No transactions match the current filters.</div>
    @else
    <div class="overflow-x-auto">
      <table class="fin-table w-full text-left">
        <thead>
          <tr>
            <th>Code</th>
            <th>Date & Time</th>
            <th>Category</th>
            <th>Sender</th>
            <th>Receiver</th>
            <th>Amount</th>
            <th>Notes</th>
            <th>Status</th>
            <th class="no-print text-right">Update</th>
          </tr>
        </thead>
        <tbody>
          @foreach($transactions as $t)
          <tr>
            <td class="font-mono font-bold text-amber-400 text-[11px]">
              {{ $t->transaction_code }}
              @if($t->auto_approved)<span class="text-[9px] text-amber-400/60 block"><i class="fa-solid fa-bolt"></i> Auto</span>@endif
            </td>
            <td class="text-[11px]">
              <div class="font-bold text-slate-200">{{ $t->created_at->format('d M Y') }}</div>
              <div class="text-slate-500 text-[10px]">{{ $t->created_at->format('h:i:s A') }}</div>
            </td>
            <td>
              <span class="px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wide bg-white/[0.04] border border-white/[0.08] text-slate-300">
                {{ str_replace('_', ' ', $t->transaction_type) }}
              </span>
            </td>
            <td class="font-semibold text-slate-200 text-[11px] max-w-[120px]">
              <span class="truncate block" title="{{ $t->sender_name }}">{{ $t->sender_name }}</span>
            </td>
            <td class="font-semibold text-slate-200 text-[11px] max-w-[120px]">
              <span class="truncate block" title="{{ $t->receiver_name }}">{{ $t->receiver_name }}</span>
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
            <td class="text-[11px] text-slate-500 max-w-[150px]">
              <span class="truncate block" title="{{ $t->notes }}">{{ $t->notes ?? '—' }}</span>
            </td>
            <td>
              @php $statusBadges = ['completed'=>'badge-green','approved'=>'badge-sky','pending'=>'badge-amber','failed'=>'badge-rose']; @endphp
              <span class="badge {{ $statusBadges[$t->status] ?? 'badge-gray' }}">
                {{ ucfirst($t->status) }}
              </span>
            </td>
            <td class="text-right no-print">
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
    <div class="p-4 border-t border-white/[0.06] no-print">
      {{ $transactions->links() }}
    </div>
    @endif
  </div>

</div>
@include('partials.global-loader')
</body>
</html>
