@extends('admin.layout')
@section('title','Staff — '.$staff->name)
@section('page-title','Staff Details')

@section('content')
@php
  use App\Models\StaffWorkLog;
  use App\Models\SalaryPayment;

  $monthSeconds = StaffWorkLog::where('user_id',$staff->id)
      ->whereMonth('work_date',now()->month)->whereYear('work_date',now()->year)
      ->whereNotNull('clock_out')->sum('duration_seconds');
  $monthHours = round($monthSeconds/3600,1);

  $totalSeconds = StaffWorkLog::where('user_id',$staff->id)->whereNotNull('clock_out')->sum('duration_seconds');
  $totalHours = round($totalSeconds/3600,1);

  $lastPayment = SalaryPayment::where('staff_id',$staff->id)->latest()->first();

  $hourlyRate = 0;
  if($staff->salary && $staff->salary_cycle==='monthly') {
      $hourlyRate = round($staff->salary/(22*8),2);
  }
  $earnedThisMonth = round($monthHours*$hourlyRate,0);
@endphp

<div class="max-w-2xl space-y-5">

  <div class="flex items-center gap-3 flex-wrap">
    <a href="{{ route('admin.staff.index') }}" class="btn-secondary text-xs px-4 py-2">
      <i class="fa-solid fa-arrow-left"></i> Back to Staff
    </a>
    <div class="ml-auto flex gap-2">
      <a href="{{ route('admin.staff.edit', $staff) }}" class="btn-primary text-xs px-4">
        <i class="fa-solid fa-pen"></i> Edit
      </a>
      @if(Auth::user()->isAdmin())
      <a href="{{ route('finance.staff', $staff) }}"
         class="flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-bold border transition bg-amber-50 dark:bg-amber-950/20 text-amber-600 dark:text-amber-400 border-amber-200 dark:border-amber-900/30 hover:bg-amber-100">
        <i class="fa-solid fa-building-columns"></i> Finance
      </a>
      @endif
    </div>
  </div>

  {{-- Profile Card --}}
  <div class="glass rounded-2xl overflow-hidden">
    <div class="h-20 w-full" style="background:linear-gradient(135deg,#581c87,#7c3aed,#4f46e5);"></div>
    <div class="px-6 pb-6 -mt-8">
      <div class="flex items-end gap-4 mb-5">
        <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-fuchsia-400 to-purple-600 flex items-center justify-center text-white text-2xl font-extrabold shadow-xl ring-4 ring-white dark:ring-slate-900 flex-shrink-0">
          {{ strtoupper(substr($staff->name,0,1)) }}
        </div>
        <div class="pb-1 flex-1">
          <div class="flex items-center gap-2 flex-wrap">
            <h2 class="font-extrabold text-slate-800 dark:text-slate-100 text-lg font-outfit">{{ $staff->name }}</h2>
            <span class="badge {{ $staff->is_blocked ? 'badge-rose' : 'badge-green' }}">
              {{ $staff->is_blocked ? 'Blocked' : 'Active' }}
            </span>
          </div>
          <p class="text-slate-400 text-xs mt-0.5">{{ $staff->email }}</p>
        </div>
      </div>

      <div class="grid grid-cols-2 gap-4">
        <div class="p-4 rounded-xl bg-slate-50 dark:bg-white/[0.03] border border-slate-100 dark:border-white/[0.06]">
          <p class="text-[9px] font-extrabold text-slate-400 uppercase tracking-widest mb-1">Position</p>
          <p class="font-bold text-slate-700 dark:text-slate-200 text-sm">{{ $staff->position ?? '—' }}</p>
        </div>
        <div class="p-4 rounded-xl bg-slate-50 dark:bg-white/[0.03] border border-slate-100 dark:border-white/[0.06]">
          <p class="text-[9px] font-extrabold text-slate-400 uppercase tracking-widest mb-1">Joining Date</p>
          <p class="font-bold text-slate-700 dark:text-slate-200 text-sm">
            {{ $staff->joining_date ? $staff->joining_date->format('d M Y') : '—' }}
          </p>
          @if($staff->joining_date)
          <p class="text-[10px] text-slate-400 mt-0.5">{{ $staff->joining_date->diffForHumans() }}</p>
          @endif
        </div>
        <div class="p-4 rounded-xl bg-slate-50 dark:bg-white/[0.03] border border-slate-100 dark:border-white/[0.06]">
          <p class="text-[9px] font-extrabold text-slate-400 uppercase tracking-widest mb-1">Total Hours Worked</p>
          <p class="font-bold text-slate-700 dark:text-slate-200 text-sm">{{ $totalHours }}h</p>
        </div>
        <div class="p-4 rounded-xl bg-slate-50 dark:bg-white/[0.03] border border-slate-100 dark:border-white/[0.06]">
          <p class="text-[9px] font-extrabold text-slate-400 uppercase tracking-widest mb-1">Role</p>
          <span class="badge badge-purple">Staff Admin</span>
        </div>
      </div>
    </div>
  </div>

  {{-- Work Stats this month --}}
  <div class="grid grid-cols-3 gap-4">
    <div class="glass rounded-2xl p-4 text-center stat-lift">
      <p class="text-xl font-black text-sky-500 font-outfit">{{ $monthHours }}h</p>
      <p class="text-slate-400 text-[9px] font-bold uppercase tracking-wider mt-1">This Month</p>
    </div>
    <div class="glass rounded-2xl p-4 text-center stat-lift">
      @if($staff->salary)
      <p class="text-xl font-black text-amber-500 font-outfit">{{ $staff->salary_currency }} {{ number_format($earnedThisMonth,0) }}</p>
      @else
      <p class="text-xl font-black text-slate-400 font-outfit">—</p>
      @endif
      <p class="text-slate-400 text-[9px] font-bold uppercase tracking-wider mt-1">Est. Earned</p>
    </div>
    <div class="glass rounded-2xl p-4 text-center stat-lift">
      @if($lastPayment)
      <p class="text-xl font-black text-emerald-500 font-outfit">{{ $lastPayment->currency }} {{ number_format($lastPayment->amount,0) }}</p>
      <p class="text-slate-400 text-[9px] font-bold uppercase tracking-wider mt-1">Last Paid</p>
      @else
      <p class="text-xl font-black text-slate-400 font-outfit">—</p>
      <p class="text-slate-400 text-[9px] font-bold uppercase tracking-wider mt-1">Not Paid Yet</p>
      @endif
    </div>
  </div>

  {{-- Salary Card --}}
  <div class="glass rounded-2xl p-6"
       style="background:linear-gradient(135deg,rgba(217,119,6,0.08),rgba(245,158,11,0.05));border-color:rgba(245,158,11,0.2);">
    <div class="flex items-center justify-between mb-5">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl flex items-center justify-center"
             style="background:linear-gradient(135deg,#d97706,#f59e0b)">
          <i class="fa-solid fa-indian-rupee-sign text-white"></i>
        </div>
        <div>
          <h3 class="font-extrabold text-slate-800 dark:text-slate-100 text-sm font-outfit">Salary</h3>
          <p class="text-slate-400 text-[10px]">Managed by Finance Department</p>
        </div>
      </div>
      <a href="{{ route('admin.staff.edit', $staff) }}" class="btn-secondary text-xs px-3 py-2">
        <i class="fa-solid fa-pen text-[9px]"></i> Update
      </a>
    </div>

    @if($staff->salary)
    <div class="grid grid-cols-3 gap-4">
      <div class="col-span-3 p-4 rounded-xl bg-amber-50 dark:bg-amber-950/20 border border-amber-200 dark:border-amber-900/30 text-center">
        <p class="text-3xl font-black text-amber-600 dark:text-amber-400 font-outfit">
          {{ $staff->salary_currency }} {{ number_format($staff->salary,0) }}
        </p>
        <p class="text-amber-500 text-xs font-bold mt-1 uppercase tracking-wider">Per {{ $staff->salary_cycle }}</p>
      </div>
      @php
        $annual = match($staff->salary_cycle){
          'daily'=>$staff->salary*365,'weekly'=>$staff->salary*52,
          'monthly'=>$staff->salary*12,'yearly'=>$staff->salary,default=>$staff->salary*12};
      @endphp
      <div class="p-3 rounded-xl bg-white dark:bg-white/[0.03] border border-amber-100 dark:border-amber-900/20 text-center">
        <p class="text-[9px] font-extrabold text-slate-400 uppercase tracking-widest">Annual</p>
        <p class="font-extrabold text-slate-700 dark:text-slate-200 text-sm mt-1">{{ $staff->salary_currency }} {{ number_format($annual,0) }}</p>
      </div>
      <div class="p-3 rounded-xl bg-white dark:bg-white/[0.03] border border-amber-100 dark:border-amber-900/20 text-center">
        <p class="text-[9px] font-extrabold text-slate-400 uppercase tracking-widest">Hourly Rate</p>
        <p class="font-extrabold text-slate-700 dark:text-slate-200 text-sm mt-1">{{ $staff->salary_currency }} {{ number_format($hourlyRate,0) }}</p>
      </div>
      <div class="p-3 rounded-xl bg-white dark:bg-white/[0.03] border border-amber-100 dark:border-amber-900/20 text-center">
        <p class="text-[9px] font-extrabold text-slate-400 uppercase tracking-widest">Cycle</p>
        <p class="font-extrabold text-slate-700 dark:text-slate-200 text-sm mt-1">{{ ucfirst($staff->salary_cycle) }}</p>
      </div>
    </div>
    @else
    <div class="text-center py-6">
      <i class="fa-solid fa-circle-exclamation text-amber-400 text-2xl mb-2 block"></i>
      <p class="text-slate-500 dark:text-slate-400 text-sm font-semibold">Salary not set</p>
      <a href="{{ route('admin.staff.edit', $staff) }}" class="inline-flex items-center gap-1.5 mt-3 text-xs font-bold text-amber-600">
        <i class="fa-solid fa-plus-circle"></i> Set Salary
      </a>
    </div>
    @endif
  </div>

  {{-- Notes --}}
  @if($staff->staff_notes)
  <div class="glass rounded-2xl p-5">
    <h4 class="font-extrabold text-slate-700 dark:text-slate-300 text-xs uppercase tracking-wider mb-3">
      <i class="fa-solid fa-sticky-note text-purple-400 mr-2"></i>Notes
    </h4>
    <p class="text-sm text-slate-600 dark:text-slate-300 leading-relaxed whitespace-pre-wrap">{{ $staff->staff_notes }}</p>
  </div>
  @endif

  {{-- Actions --}}
  <div class="glass rounded-2xl p-4 flex items-center gap-3 flex-wrap">
    <form method="POST" action="{{ route('admin.staff.toggle-block', $staff) }}">
      @csrf
      <button type="submit" class="flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-bold border transition
          {{ $staff->is_blocked ? 'bg-emerald-50 dark:bg-emerald-950/20 text-emerald-600 border-emerald-200 dark:border-emerald-900/30' : 'bg-amber-50 dark:bg-amber-950/20 text-amber-600 border-amber-200 dark:border-amber-900/30' }}">
        <i class="fa-solid {{ $staff->is_blocked ? 'fa-lock-open' : 'fa-ban' }}"></i>
        {{ $staff->is_blocked ? 'Unblock' : 'Block' }} Account
      </button>
    </form>
    <form method="POST" action="{{ route('admin.staff.destroy', $staff) }}" class="ml-auto"
          onsubmit="return confirm('Permanently delete this staff account?')">
      @csrf @method('DELETE')
      <button type="submit" class="flex items-center gap-1.5 px-4 py-2 rounded-xl bg-rose-50 dark:bg-rose-950/20 text-rose-600 dark:text-rose-400 text-xs font-bold border border-rose-200 dark:border-rose-900/30 hover:bg-rose-100 transition">
        <i class="fa-solid fa-trash"></i> Delete Account
      </button>
    </form>
  </div>

</div>
@endsection
