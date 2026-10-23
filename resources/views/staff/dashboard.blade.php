<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Staff Portal — {{ $user->name }}</title>
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Outfit:wght@400;600;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<script>
tailwind.config={darkMode:'class',theme:{extend:{fontFamily:{sans:['"Plus Jakarta Sans"'],outfit:['"Outfit"']}}}}
if(localStorage.getItem('color-theme')==='dark'||(!('color-theme' in localStorage)&&window.matchMedia('(prefers-color-scheme:dark)').matches))document.documentElement.classList.add('dark');
</script>
<style>
  body{font-family:'Plus Jakarta Sans',sans-serif;background:linear-gradient(160deg,#060018 0%,#040012 50%,#020008 100%);}
  .font-outfit{font-family:'Outfit',sans-serif;}
  .glass{background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.09);backdrop-filter:blur(16px);}
  .glass-amber{background:rgba(217,119,6,0.08);border:1px solid rgba(245,158,11,0.2);}
  .glass-sky{background:rgba(14,165,233,0.08);border:1px solid rgba(56,189,248,0.2);}
  .glass-emerald{background:rgba(16,185,129,0.08);border:1px solid rgba(52,211,153,0.2);}
  .glass-rose{background:rgba(225,29,72,0.08);border:1px solid rgba(251,113,133,0.2);}
  @keyframes fadeUp{from{opacity:0;transform:translateY(14px)}to{opacity:1;transform:translateY(0)}}
  .au{animation:fadeUp .35s cubic-bezier(.16,1,.3,1) forwards;}
  .au2{animation:fadeUp .35s .07s cubic-bezier(.16,1,.3,1) both;}
  .au3{animation:fadeUp .35s .14s cubic-bezier(.16,1,.3,1) both;}
  .au4{animation:fadeUp .35s .21s cubic-bezier(.16,1,.3,1) both;}
  .nav-item{display:flex;align-items:center;gap:.625rem;padding:.55rem .875rem;border-radius:.75rem;font-size:.72rem;font-weight:700;color:rgba(148,163,184,.8);transition:all .2s;text-decoration:none;}
  .nav-item:hover{color:#e2e8f0;background:rgba(255,255,255,.06);}
  .nav-item.active{color:#a5b4fc;background:linear-gradient(90deg,rgba(99,102,241,.22),rgba(99,102,241,.04));border-left:3px solid #6366f1;padding-left:calc(.875rem - 3px);}
  .badge{display:inline-flex;align-items:center;gap:.3rem;padding:.2rem .6rem;border-radius:9999px;font-size:.65rem;font-weight:700;text-transform:uppercase;letter-spacing:.04em;}
  .badge-amber{background:rgba(245,158,11,.15);color:#fbbf24;border:1px solid rgba(245,158,11,.25);}
  .badge-blue{background:rgba(99,102,241,.15);color:#a5b4fc;border:1px solid rgba(99,102,241,.25);}
  .badge-green{background:rgba(16,185,129,.15);color:#34d399;border:1px solid rgba(16,185,129,.25);}
  .badge-rose{background:rgba(225,29,72,.15);color:#fb7185;border:1px solid rgba(225,29,72,.25);}
  .badge-gray{background:rgba(100,116,139,.15);color:#94a3b8;border:1px solid rgba(100,116,139,.2);}
</style>
</head>
<body class="min-h-screen text-white">

<div class="flex min-h-screen">

  {{-- SIDEBAR --}}
  <aside class="w-56 glass border-r border-white/[0.07] fixed top-0 left-0 h-full flex flex-col z-40 hidden lg:flex">
    {{-- Logo --}}
    <div class="px-4 py-5 border-b border-white/[0.07]">
      <div class="flex items-center gap-2.5">
        <div class="w-9 h-9 rounded-xl flex items-center justify-center" style="background:linear-gradient(135deg,#6366f1,#7c3aed)">
          <i class="fa-solid fa-star text-white text-sm"></i>
        </div>
        <div>
          <p class="font-extrabold text-white text-xs font-outfit">Staff Portal</p>
          <p class="text-indigo-400 text-[9px] font-bold uppercase tracking-widest mt-0.5">Learning Curve</p>
        </div>
      </div>
    </div>

    {{-- User badge --}}
    <div class="mx-3 mt-4 p-3 glass rounded-xl">
      <div class="flex items-center gap-2.5">
        <div class="w-8 h-8 rounded-xl bg-gradient-to-br from-sky-400 to-indigo-600 flex items-center justify-center font-bold text-sm flex-shrink-0">
          {{ strtoupper(substr($user->name,0,1)) }}
        </div>
        <div class="min-w-0">
          <p class="text-white font-bold text-xs truncate">{{ $user->name }}</p>
          <p class="text-slate-400 text-[9px] truncate">{{ $user->position ?? 'Staff' }}</p>
        </div>
      </div>
    </div>

    {{-- Nav --}}
    <nav class="flex-1 px-3 py-4 space-y-0.5">
      <a href="{{ route('staff.dashboard') }}" class="nav-item active">
        <i class="fa-solid fa-house w-4 text-center"></i> Dashboard
      </a>
      <a href="{{ route('staff.tickets.index') }}" class="nav-item">
        <i class="fa-solid fa-ticket w-4 text-center"></i> Support Tickets
        @if($assignedTickets->count() > 0)
        <span class="ml-auto text-[8px] font-black bg-rose-500 text-white px-1.5 py-0.5 rounded-full">{{ $assignedTickets->count() }}</span>
        @endif
      </a>
      <a href="{{ route('staff.chat.index') }}" class="nav-item">
        <i class="fa-solid fa-comments w-4 text-center"></i> Live Chats
        @if($waitingChats > 0)
        <span class="ml-auto text-[8px] font-black bg-emerald-500 text-white px-1.5 py-0.5 rounded-full">{{ $waitingChats }}</span>
        @endif
      </a>
      <a href="{{ route('team-chat.index') }}" class="nav-item">
        <i class="fa-solid fa-comments text-emerald-400 w-4 text-center"></i> Learning Curve Team Chat
      </a>
      <a href="{{ route('staff.history') }}" class="nav-item">
        <i class="fa-solid fa-clock-rotate-left w-4 text-center"></i> Work History
      </a>
    </nav>

    {{-- Footer --}}
    <div class="px-3 py-4 border-t border-white/[0.07]">
      <form method="POST" action="{{ route('admin.logout') }}">
        @csrf
        <button class="nav-item w-full text-left text-rose-400 hover:text-rose-300 hover:bg-rose-500/10">
          <i class="fa-solid fa-right-from-bracket w-4 text-center"></i> Logout
        </button>
      </form>
    </div>
  </aside>

  {{-- MAIN --}}
  <div class="flex-1 lg:ml-56 p-4 md:p-6 space-y-5">

    {{-- Mobile header --}}
    <div class="flex items-center justify-between lg:hidden mb-2">
      <p class="font-extrabold text-white text-base font-outfit">Staff Portal</p>
      <div class="flex items-center gap-2">
        <a href="{{ route('admin.switch-department') }}" class="px-2.5 py-1 rounded-xl bg-amber-500/20 text-amber-300 text-[10px] font-bold border border-amber-500/30">
          <i class="fa-solid fa-arrows-rotate"></i> Switch Dept
        </a>
        <form method="POST" action="{{ route('admin.logout') }}">@csrf
          <button class="text-rose-400 text-xs font-bold"><i class="fa-solid fa-right-from-bracket"></i></button>
        </form>
      </div>
    </div>

    {{-- Desktop header topbar --}}
    <div class="hidden lg:flex items-center justify-between pb-3 border-b border-white/[0.07]">
      <div>
        <h1 class="text-white font-extrabold text-lg font-outfit">Staff Portal Dashboard</h1>
        <p class="text-slate-400 text-xs">Logged in as <span class="text-sky-400 font-semibold">{{ $user->name }}</span> ({{ $user->email }})</p>
      </div>
      <div class="flex items-center gap-3">
        <a href="{{ route('admin.switch-department') }}" class="flex items-center gap-2 px-3.5 py-2 rounded-xl bg-amber-500/10 hover:bg-amber-500/20 text-amber-300 border border-amber-500/30 text-xs font-bold transition">
          <i class="fa-solid fa-arrows-rotate"></i> Switch Department
        </a>
      </div>
    </div>

    {{-- Flash --}}
    @if(session('welcome'))
    <div class="au p-3.5 glass-sky rounded-2xl text-sky-200 text-xs font-semibold flex items-center gap-2">
      <i class="fa-solid fa-star text-sky-400"></i> {{ session('welcome') }}
    </div>
    @endif
    @if(session('success'))
    <div class="au p-3.5 glass-emerald rounded-2xl text-emerald-200 text-xs font-semibold flex items-center gap-2">
      <i class="fa-solid fa-circle-check text-emerald-400"></i> {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div class="au p-3.5 glass-rose rounded-2xl text-rose-200 text-xs font-semibold flex items-center gap-2">
      <i class="fa-solid fa-circle-exclamation text-rose-400"></i> {{ session('error') }}
    </div>
    @endif

    {{-- Daily Attendance Card (Replaces Hourly Timer) --}}
    <div class="au glass rounded-3xl p-6" style="background:linear-gradient(135deg,rgba(16,185,129,.12),rgba(13,148,136,.08));">
      <div class="flex items-center justify-between mb-4 flex-wrap gap-3">
        <div class="flex items-center gap-3">
          <div class="w-11 h-11 rounded-2xl flex items-center justify-center bg-gradient-to-br from-emerald-500 to-teal-600 shadow-lg shadow-emerald-500/20 text-white text-xl">
            <i class="fa-solid fa-calendar-check"></i>
          </div>
          <div>
            <h2 class="font-extrabold text-white text-base font-outfit">Daily Attendance System</h2>
            <p class="text-slate-400 text-[10px]">Today: {{ today()->format('l, d F Y') }} — Rate: ₹1,000 / Present Day</p>
          </div>
        </div>

        <div id="live-badge" class="flex items-center gap-1.5 px-3 py-1.5 rounded-full text-[10px] font-black uppercase tracking-wider
            {{ $todayLog ? 'badge-green' : 'badge-amber' }}">
          <span class="w-2 h-2 rounded-full {{ $todayLog ? 'bg-emerald-400' : 'bg-amber-400 animate-pulse' }}"></span>
          {{ $todayLog ? 'Present Today' : 'Attendance Unmarked' }}
        </div>
      </div>

      <div class="p-4 rounded-2xl bg-white/5 border border-white/10 mb-5 flex items-center justify-between flex-wrap gap-3">
        <div>
          <p class="text-xs text-slate-300 font-bold">Current Date Status</p>
          @if($todayLog)
            <p class="text-emerald-400 font-extrabold text-sm mt-0.5 font-outfit">
              <i class="fa-solid fa-circle-check mr-1"></i> Present Marked at {{ $todayLog->marked_at?->format('h:i A') ?? $todayLog->created_at->format('h:i A') }}
            </p>
            <p class="text-slate-400 text-[10px] mt-0.5">Day payout (+₹1,000) credited to monthly balance.</p>
          @else
            <p class="text-amber-400 font-extrabold text-sm mt-0.5 font-outfit">
              <i class="fa-solid fa-clock mr-1"></i> Attendance not marked for today
            </p>
            <p class="text-slate-400 text-[10px] mt-0.5">Click "Mark Present" below to record attendance and earn ₹1,000 for today.</p>
          @endif
        </div>

        <div>
          @if(!$todayLog)
          <form method="POST" action="{{ route('staff.attendance.mark-present') }}">
            @csrf
            <button type="submit"
                    class="px-6 py-3 rounded-2xl font-extrabold text-sm text-white transition-all hover:scale-105 active:scale-95 shadow-lg shadow-emerald-500/25"
                    style="background:linear-gradient(135deg,#10b981,#059669)">
              <i class="fa-solid fa-user-check mr-1.5"></i> Mark Present Today
            </button>
          </form>
          @else
          <button disabled class="px-5 py-2.5 rounded-2xl font-bold text-xs text-emerald-300 bg-emerald-500/20 border border-emerald-500/40 cursor-not-allowed">
            <i class="fa-solid fa-check-double mr-1"></i> Attendance Done Today
          </button>
          @endif
        </div>
      </div>
    </div>

    {{-- Day-Wise Stats Grid --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 au2">
      <div class="glass rounded-2xl p-4 text-center">
        <p class="text-2xl font-black text-sky-400 font-outfit">{{ $todayLog ? '1 Day' : '0 Day' }}</p>
        <p class="text-slate-500 text-[9px] font-bold uppercase tracking-wider mt-1">Today Count</p>
        <p class="text-sky-400/60 text-[9px] mt-0.5">₹{{ $todayLog ? '1,000' : '0' }}</p>
      </div>
      <div class="glass rounded-2xl p-4 text-center">
        <p class="text-2xl font-black text-indigo-400 font-outfit">{{ $presentDaysMonth }} Days</p>
        <p class="text-slate-500 text-[9px] font-bold uppercase tracking-wider mt-1">Present This Month</p>
        <p class="text-indigo-400/60 text-[9px] mt-0.5">₹{{ number_format($earnedMonth) }}</p>
      </div>
      <div class="glass rounded-2xl p-4 text-center">
        <p class="text-2xl font-black text-amber-400 font-outfit">₹{{ number_format($earnedMonth) }}</p>
        <p class="text-slate-500 text-[9px] font-bold uppercase tracking-wider mt-1">Earned This Month</p>
        <p class="text-amber-400/60 text-[9px] mt-0.5">@₹1,000 / Day</p>
      </div>
      <div class="glass rounded-2xl p-4 text-center">
        <p class="text-2xl font-black text-emerald-400 font-outfit">₹{{ number_format($totalPaid) }}</p>
        <p class="text-slate-500 text-[9px] font-bold uppercase tracking-wider mt-1">Total Received</p>
        <p class="text-emerald-400/60 text-[9px] mt-0.5">Finance Payouts</p>
      </div>
    </div>

    {{-- Bank Details & Day-Wise Salary Calculator --}}
    <div class="grid lg:grid-cols-2 gap-5 au3">

      {{-- Bank Details Card --}}
      <div class="glass rounded-2xl p-5 border border-white/[0.09]">
        <div class="flex items-center justify-between mb-4 pb-3 border-b border-white/[0.07]">
          <h3 class="font-extrabold text-white text-sm font-outfit flex items-center gap-2">
            <i class="fa-solid fa-building-columns text-emerald-400"></i> Staff Bank Details
          </h3>
          <span class="badge {{ $user->bank_account ? 'badge-green' : 'badge-amber' }}">
            {{ $user->bank_account ? 'Verified' : 'Pending Details' }}
          </span>
        </div>

        <form method="POST" action="{{ route('staff.bank-details.update') }}" class="space-y-3">
          @csrf
          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Bank Name</label>
              <input type="text" name="bank_name" value="{{ old('bank_name', $user->bank_name) }}" required placeholder="e.g. HDFC Bank"
                     class="w-full px-3 py-2 rounded-xl bg-white/5 border border-white/10 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-emerald-500">
            </div>
            <div>
              <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Account Holder Name</label>
              <input type="text" name="account_holder" value="{{ old('account_holder', $user->account_holder ?? $user->name) }}" required placeholder="Full Name on Account"
                     class="w-full px-3 py-2 rounded-xl bg-white/5 border border-white/10 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-emerald-500">
            </div>
          </div>

          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Account Number</label>
              <input type="text" name="bank_account" value="{{ old('bank_account', $user->bank_account) }}" required placeholder="Account Number"
                     class="w-full px-3 py-2 rounded-xl bg-white/5 border border-white/10 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-emerald-500">
            </div>
            <div>
              <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">IFSC Code</label>
              <input type="text" name="bank_ifsc" value="{{ old('bank_ifsc', $user->bank_ifsc) }}" required placeholder="HDFC0001234"
                     class="w-full px-3 py-2 rounded-xl bg-white/5 border border-white/10 text-xs text-white uppercase placeholder-slate-500 focus:outline-none focus:border-emerald-500">
            </div>
          </div>

          <div class="pt-1 flex justify-end">
            <button type="submit" class="px-4 py-2 rounded-xl text-xs font-extrabold text-white bg-emerald-600 hover:bg-emerald-500 transition shadow-md shadow-emerald-600/20">
              <i class="fa-solid fa-floppy-disk mr-1"></i> Update Bank Details
            </button>
          </div>
        </form>
      </div>

      {{-- Day-Wise Salary Calculator & Summary Card --}}
      <div class="glass rounded-2xl p-5 border border-white/[0.09] flex flex-col justify-between">
        <div>
          <div class="flex items-center justify-between mb-4 pb-3 border-b border-white/[0.07]">
            <h3 class="font-extrabold text-white text-sm font-outfit flex items-center gap-2">
              <i class="fa-solid fa-calculator text-amber-400"></i> Day-Wise Payout Breakdown
            </h3>
            <span class="text-[10px] font-bold text-slate-400">Attendance Basis</span>
          </div>

          <div class="space-y-2.5 text-xs">
            <div class="flex justify-between items-center py-1.5 border-b border-white/5">
              <span class="text-slate-400">Daily Payout Rate:</span>
              <span class="font-bold text-white font-outfit">₹1,000 / Present Day</span>
            </div>
            <div class="flex justify-between items-center py-1.5 border-b border-white/5">
              <span class="text-slate-400">Present Days (This Month):</span>
              <span class="font-bold text-sky-400 font-outfit">{{ $presentDaysMonth }} Days</span>
            </div>
            <div class="flex justify-between items-center py-1.5 border-b border-white/5">
              <span class="text-slate-400">Total Calculated Earnings:</span>
              <span class="font-bold text-amber-400 font-outfit">₹{{ number_format($earnedMonth) }}</span>
            </div>
            <div class="flex justify-between items-center py-1.5 border-b border-white/5">
              <span class="text-slate-400">Total Salary Received (Finance):</span>
              <span class="font-bold text-emerald-400 font-outfit">₹{{ number_format($totalPaid) }}</span>
            </div>
          </div>
        </div>

        <div class="mt-4 p-3 rounded-xl bg-amber-500/10 border border-amber-500/20 flex items-center justify-between">
          <div>
            <p class="text-[10px] font-bold text-amber-300 uppercase tracking-wider">Remaining Payout Balance</p>
            <p class="text-slate-400 text-[10px]">Calculated unpaid present days</p>
          </div>
          <p class="text-lg font-black text-amber-400 font-outfit">₹{{ number_format(max(0, $earnedMonth - $totalPaid)) }}</p>
        </div>
      </div>

    </div>

    {{-- My Assigned Tasks Section --}}
    <div class="glass rounded-2xl overflow-hidden au3">
      <div class="px-5 py-3.5 border-b border-white/[0.07] flex items-center justify-between">
        <h3 class="font-extrabold text-white text-sm font-outfit flex items-center gap-2">
          <i class="fa-solid fa-list-check text-sky-400"></i> My Assigned Tasks
        </h3>
        <span class="badge badge-blue">{{ $assignedTasks->count() }} Tasks Assigned</span>
      </div>
      @forelse($assignedTasks as $task)
      <div class="px-5 py-3.5 border-b border-white/[0.04] last:border-0 flex items-center justify-between gap-3">
        <div class="flex items-center gap-3 min-w-0">
          <div class="w-8 h-8 rounded-xl bg-gradient-to-br from-sky-500 to-indigo-600 flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
            <i class="fa-solid fa-list-check"></i>
          </div>
          <div class="min-w-0">
            <p class="text-white text-xs font-bold truncate">{{ $task->title }}</p>
            <p class="text-slate-400 text-[10px] truncate">Course: {{ $task->course->title ?? 'General' }} · {{ $task->points }} Points</p>
          </div>
        </div>
        <div class="text-right flex-shrink-0">
          @if($task->due_date)
            <span class="badge badge-amber text-[9px]">
              <i class="fa-regular fa-calendar mr-1"></i> {{ \Carbon\Carbon::parse($task->due_date)->format('d M Y') }}
            </span>
          @else
            <span class="badge badge-gray text-[9px]">No Due Date</span>
          @endif
        </div>
      </div>
      @empty
      <div class="px-5 py-6 text-center text-slate-500 text-xs">
        <i class="fa-solid fa-check-circle text-2xl text-sky-500/40 mb-2 block"></i>
        No tasks assigned to you currently.
      </div>
      @endforelse
    </div>

    {{-- Two-column layout --}}
    <div class="grid lg:grid-cols-2 gap-5 au3">

      {{-- Open Tickets --}}
      <div class="glass rounded-2xl overflow-hidden">
        <div class="px-5 py-3.5 border-b border-white/[0.07] flex items-center justify-between">
          <h3 class="font-extrabold text-white text-sm font-outfit flex items-center gap-2">
            <i class="fa-solid fa-ticket text-indigo-400"></i> Open Tickets
          </h3>
          <a href="{{ route('staff.tickets.index') }}" class="text-[10px] text-indigo-300 hover:text-indigo-200 font-bold">View all →</a>
        </div>
        @forelse($assignedTickets as $t)
        @php $sm = $t->getStatusMeta(); $cm = $t->getCategoryMeta(); @endphp
        <a href="{{ route('staff.tickets.show', $t) }}"
           class="flex items-center gap-3 px-5 py-3 border-b border-white/[0.04] last:border-0 hover:bg-white/[0.03] transition">
          <div class="w-8 h-8 rounded-xl flex items-center justify-center text-white text-xs flex-shrink-0"
               style="background:{{ $cm['color'] }}">
            <i class="fa-solid {{ $cm['icon'] }}"></i>
          </div>
          <div class="flex-1 min-w-0">
            <p class="text-white text-xs font-bold truncate">{{ $t->subject }}</p>
            <p class="text-slate-500 text-[9px]">#{{ $t->id }} · {{ $t->user->name ?? '—' }} · {{ $t->created_at->diffForHumans() }}</p>
          </div>
          <span class="badge {{ $sm['badge'] }}">{{ $sm['label'] }}</span>
        </a>
        @empty
        <div class="px-5 py-8 text-center text-slate-500 text-xs">
          <i class="fa-solid fa-circle-check text-2xl text-emerald-500/40 mb-2 block"></i>
          All clear! No open tickets.
        </div>
        @endforelse
      </div>

      {{-- Payment History --}}
      <div class="glass rounded-2xl overflow-hidden">
        <div class="px-5 py-3.5 border-b border-white/[0.07] flex items-center justify-between">
          <h3 class="font-extrabold text-white text-sm font-outfit flex items-center gap-2">
            <i class="fa-solid fa-money-bill-wave text-amber-400"></i> Salary Payments
          </h3>
        </div>
        @forelse($payments as $p)
        <div class="px-5 py-3 border-b border-white/[0.04] last:border-0 flex items-center gap-3">
          <div class="w-9 h-9 rounded-xl flex items-center justify-center flex-shrink-0
              {{ $p->payment_status==='paid' ? 'glass-emerald' : 'glass-amber' }}">
            <i class="fa-solid {{ $p->payment_status==='paid' ? 'fa-check text-emerald-400' : 'fa-clock text-amber-400' }} text-xs"></i>
          </div>
          <div class="flex-1 min-w-0">
            <p class="text-white text-xs font-bold">{{ $p->period_label }}</p>
            <p class="text-slate-500 text-[9px]">{{ $p->hours_worked }}h · {{ $p->created_at->format('d M Y') }}</p>
          </div>
          <div class="text-right">
            <p class="font-extrabold {{ $p->payment_status==='paid' ? 'text-emerald-400' : 'text-amber-400' }} text-sm font-outfit">₹{{ number_format($p->amount,0) }}</p>
            <span class="badge {{ $p->payment_status==='paid' ? 'badge-green' : 'badge-amber' }}">{{ ucfirst($p->payment_status) }}</span>
          </div>
        </div>
        @empty
        <div class="px-5 py-8 text-center text-slate-500 text-xs">
          <i class="fa-solid fa-indian-rupee-sign text-2xl text-amber-500/40 mb-2 block"></i>
          No payments yet. Keep working!
        </div>
        @endforelse
      </div>

    </div>

    {{-- Recent Work Logs --}}
    @if($logs->isNotEmpty())
    <div class="glass rounded-2xl overflow-hidden au4">
      <div class="px-5 py-3.5 border-b border-white/[0.07] flex items-center justify-between">
        <h3 class="font-extrabold text-white text-sm font-outfit flex items-center gap-2">
          <i class="fa-solid fa-clock text-sky-400"></i> Recent Work Sessions
        </h3>
        <a href="{{ route('staff.history') }}" class="text-[10px] text-sky-300 hover:text-sky-200 font-bold">Full history →</a>
      </div>
      <div class="overflow-x-auto">
        <table class="w-full">
          <thead>
            <tr class="border-b border-white/[0.06]">
              <th class="px-5 py-2.5 text-left text-[9px] font-black text-slate-500 uppercase tracking-wider">Date</th>
              <th class="px-5 py-2.5 text-left text-[9px] font-black text-slate-500 uppercase tracking-wider">Clock In</th>
              <th class="px-5 py-2.5 text-left text-[9px] font-black text-slate-500 uppercase tracking-wider">Clock Out</th>
              <th class="px-5 py-2.5 text-left text-[9px] font-black text-slate-500 uppercase tracking-wider">Duration</th>
              <th class="px-5 py-2.5 text-right text-[9px] font-black text-slate-500 uppercase tracking-wider">Earned</th>
            </tr>
          </thead>
          <tbody>
            @foreach($logs->take(8) as $l)
            <tr class="border-b border-white/[0.03] last:border-0 hover:bg-white/[0.02] transition">
              <td class="px-5 py-3 text-xs text-slate-300">{{ $l->work_date->format('d M Y') }}</td>
              <td class="px-5 py-3 text-xs text-slate-400">{{ $l->clock_in?->format('h:i A') ?? '—' }}</td>
              <td class="px-5 py-3 text-xs text-slate-400">{{ $l->clock_out?->format('h:i A') ?? '<span class="text-emerald-400 font-bold">Active</span>' }}</td>
              <td class="px-5 py-3">
                <span class="text-xs font-bold {{ $l->clock_out ? 'text-sky-400' : 'text-emerald-400' }}">
                  {{ $l->clock_out ? $l->duration_formatted : 'Running' }}
                </span>
              </td>
              <td class="px-5 py-3 text-right text-xs font-bold text-amber-400">
                ₹{{ number_format(round($l->duration_hours * 1000, 0)) }}
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
    @endif

  </div>
</div>

<script>
const CSRF = '{{ csrf_token() }}';
const RATE_PER_SEC = 1000 / 3600; // ₹1,000/hour

const display = document.getElementById('timer-display');
const clockInIso = display?.dataset?.clockin;
let timerInt;

if (clockInIso) {
  const start = new Date(clockInIso);
  timerInt = setInterval(() => {
    const secs = Math.floor((Date.now() - start) / 1000);
    display.textContent = fmt(secs);
    const earned = document.getElementById('earned-live');
    if (earned) earned.textContent = '₹' + Math.floor(secs * RATE_PER_SEC).toLocaleString();
  }, 1000);
}

function fmt(s) {
  return [Math.floor(s/3600), Math.floor(s%3600/60), s%60].map(n=>String(n).padStart(2,'0')).join(':');
}

async function clockIn() {
  const r = await fetch('{{ route("staff.clock-in") }}', {
    method:'POST', headers:{'Content-Type':'application/json','X-CSRF-TOKEN':CSRF},
    body:JSON.stringify({})
  });
  const d = await r.json();
  if (d.ok) location.reload();
  else alert(d.message);
}

async function clockOut() {
  if (!confirm('Clock out and save session?')) return;
  const r = await fetch('{{ route("staff.clock-out") }}', {
    method:'POST', headers:{'Content-Type':'application/json','X-CSRF-TOKEN':CSRF},
    body:JSON.stringify({})
  });
  const d = await r.json();
  if (d.ok) {
    const earned = Math.floor(d.seconds / 3600 * 1000);
    alert(`Session saved!\nDuration: ${d.duration}\nEarned: ₹${earned.toLocaleString()}`);
    location.reload();
  } else alert(d.message);
}
</script>
@include('partials.global-loader')
</body>
</html>
