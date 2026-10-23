@extends('admin.layout')
@section('title','Staff Management')
@section('page-title','Staff Management')

@section('content')
<div class="space-y-5">

  {{-- Stats --}}
  <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
    <div class="glass rounded-2xl p-5 stat-lift">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-fuchsia-50 dark:bg-fuchsia-950/20 flex items-center justify-center text-fuchsia-500 text-lg">
          <i class="fa-solid fa-user-tie"></i>
        </div>
        <div>
          <p class="text-2xl font-black text-fuchsia-500 font-outfit">{{ $stats['total'] }}</p>
          <p class="text-slate-500 dark:text-slate-400 text-[10px] font-bold uppercase tracking-wider">Total Staff</p>
        </div>
      </div>
    </div>
    <div class="glass rounded-2xl p-5 stat-lift">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-emerald-50 dark:bg-emerald-950/20 flex items-center justify-center text-emerald-500 text-lg">
          <i class="fa-solid fa-circle-check"></i>
        </div>
        <div>
          <p class="text-2xl font-black text-emerald-500 font-outfit">{{ $stats['active'] }}</p>
          <p class="text-slate-500 dark:text-slate-400 text-[10px] font-bold uppercase tracking-wider">Active</p>
        </div>
      </div>
    </div>
    <div class="glass rounded-2xl p-5 stat-lift">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-rose-50 dark:bg-rose-950/20 flex items-center justify-center text-rose-500 text-lg">
          <i class="fa-solid fa-ban"></i>
        </div>
        <div>
          <p class="text-2xl font-black text-rose-500 font-outfit">{{ $stats['blocked'] }}</p>
          <p class="text-slate-500 dark:text-slate-400 text-[10px] font-bold uppercase tracking-wider">Blocked</p>
        </div>
      </div>
    </div>
    <div class="glass rounded-2xl p-5 stat-lift">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-amber-50 dark:bg-amber-950/20 flex items-center justify-center text-amber-500 text-lg">
          <i class="fa-solid fa-indian-rupee-sign"></i>
        </div>
        <div>
          <p class="text-2xl font-black text-amber-500 font-outfit">{{ number_format($stats['total_salary'], 0) }}</p>
          <p class="text-slate-500 dark:text-slate-400 text-[10px] font-bold uppercase tracking-wider">Monthly Payroll</p>
        </div>
      </div>
    </div>
  </div>

  {{-- Search + Add --}}
  <div class="flex items-center gap-3 flex-wrap">
    <form method="GET" class="flex gap-2 flex-1 min-w-48">
      <input type="text" name="search" value="{{ request('search') }}"
             placeholder="Search by name, email, position…" class="inp text-xs flex-1">
      <button type="submit" class="btn-primary text-xs px-4 py-2">
        <i class="fa-solid fa-magnifying-glass"></i>
      </button>
      @if(request('search'))
      <a href="{{ route('admin.staff.index') }}" class="btn-secondary text-xs px-3 py-2">
        <i class="fa-solid fa-xmark"></i>
      </a>
      @endif
    </form>
    <a href="{{ route('admin.staff.create') }}" class="btn-primary text-xs">
      <i class="fa-solid fa-user-plus"></i> Add Staff Member
    </a>
  </div>

  {{-- Info note --}}
  <div class="p-4 rounded-2xl bg-sky-50 dark:bg-sky-950/20 border border-sky-200 dark:border-sky-900/30 text-sky-700 dark:text-sky-300 text-xs flex items-start gap-3">
    <i class="fa-solid fa-circle-info mt-0.5 flex-shrink-0"></i>
    <div>
      <strong>Staff Admins</strong> can access the admin panel for support tickets and live chat.
      Only the <strong>Main Admin</strong> can manage staff accounts and salary details.
      Staff login using their email &amp; password at <strong>/login</strong>.
    </div>
  </div>

  {{-- Table --}}
  <div class="glass rounded-2xl overflow-hidden">
    @if($staff->isEmpty())
    <div class="py-16 text-center">
      <div class="w-14 h-14 rounded-2xl bg-fuchsia-50 dark:bg-fuchsia-950/20 flex items-center justify-center mx-auto mb-3">
        <i class="fa-solid fa-user-tie text-2xl text-fuchsia-300 dark:text-fuchsia-700"></i>
      </div>
      <p class="text-slate-400 text-sm font-semibold">No staff members yet</p>
      <a href="{{ route('admin.staff.create') }}" class="inline-flex items-center gap-2 mt-4 btn-primary text-xs px-5 py-2">
        <i class="fa-solid fa-user-plus"></i> Add Your First Staff Member
      </a>
    </div>
    @else
    <div class="overflow-x-auto">
      <table class="w-full admin-table">
        <thead>
          <tr>
            <th class="text-left">Staff Member</th>
            <th class="text-left">Position</th>
            <th class="text-left">Salary</th>
            <th class="text-left">Joined</th>
            <th class="text-left">Status</th>
            <th class="text-left">Actions</th>
          </tr>
        </thead>
        <tbody>
          @foreach($staff as $s)
          <tr>
            <td>
              <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-fuchsia-400 to-purple-600 flex items-center justify-center text-white font-bold text-sm flex-shrink-0">
                  {{ strtoupper(substr($s->name, 0, 1)) }}
                </div>
                <div>
                  <p class="font-bold text-slate-700 dark:text-slate-200 text-xs">{{ $s->name }}</p>
                  <p class="text-[10px] text-slate-400">{{ $s->email }}</p>
                </div>
              </div>
            </td>
            <td>
              <span class="text-xs text-slate-600 dark:text-slate-300 font-medium">
                {{ $s->position ?? '—' }}
              </span>
            </td>
            <td>
              @if($s->salary)
              <div>
                <p class="text-xs font-bold text-slate-700 dark:text-slate-200">
                  {{ $s->salary_currency }} {{ number_format($s->salary, 0) }}
                </p>
                <p class="text-[10px] text-slate-400">/ {{ $s->salary_cycle }}</p>
              </div>
              @else
              <span class="text-[10px] text-slate-400">Not set</span>
              @endif
            </td>
            <td class="text-[10px] text-slate-400">
              {{ $s->joining_date ? $s->joining_date->format('d M Y') : '—' }}
            </td>
            <td>
              <span class="badge {{ $s->is_blocked ? 'badge-rose' : 'badge-green' }}">
                {{ $s->is_blocked ? 'Blocked' : 'Active' }}
              </span>
            </td>
            <td>
              <div class="flex items-center gap-1.5 flex-wrap">
                <a href="{{ route('admin.staff.show', $s) }}"
                   class="flex items-center gap-1 px-2.5 py-1.5 rounded-xl text-[10px] font-bold bg-slate-100 dark:bg-white/5 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-white/10 transition border border-slate-200 dark:border-white/10">
                  <i class="fa-solid fa-eye text-[9px]"></i> View
                </a>
                <a href="{{ route('admin.staff.edit', $s) }}" class="flex items-center gap-1 px-2.5 py-1.5 rounded-xl text-[10px] font-bold bg-fuchsia-50 dark:bg-fuchsia-950/20 text-fuchsia-600 dark:text-fuchsia-400 hover:bg-fuchsia-100 transition border border-fuchsia-200 dark:border-fuchsia-900/30">
                  <i class="fa-solid fa-pen text-[9px]"></i> Edit
                </a>
                <form method="POST" action="{{ route('admin.staff.toggle-block', $s) }}">
                  @csrf
                  <button type="submit"
                    class="flex items-center gap-1 px-2.5 py-1.5 rounded-xl text-[10px] font-bold border transition
                      {{ $s->is_blocked
                        ? 'bg-emerald-50 dark:bg-emerald-950/20 text-emerald-600 dark:text-emerald-400 border-emerald-200 dark:border-emerald-900/30'
                        : 'bg-amber-50 dark:bg-amber-950/20 text-amber-600 dark:text-amber-400 border-amber-200 dark:border-amber-900/30' }}">
                    <i class="fa-solid {{ $s->is_blocked ? 'fa-lock-open' : 'fa-ban' }} text-[9px]"></i>
                    {{ $s->is_blocked ? 'Unblock' : 'Block' }}
                  </button>
                </form>
                <form method="POST" action="{{ route('admin.staff.destroy', $s) }}"
                      onsubmit="return confirm('Delete {{ addslashes($s->name) }}? This cannot be undone.')">
                  @csrf @method('DELETE')
                  <button type="submit"
                    class="flex items-center gap-1 px-2.5 py-1.5 rounded-xl text-[10px] font-bold bg-rose-50 dark:bg-rose-950/20 text-rose-600 dark:text-rose-400 border border-rose-200 dark:border-rose-900/30 hover:bg-rose-100 transition">
                    <i class="fa-solid fa-trash text-[9px]"></i>
                  </button>
                </form>
              </div>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
    @if($staff->hasPages())
    <div class="px-6 py-4 border-t border-slate-100 dark:border-white/5">
      {{ $staff->links() }}
    </div>
    @endif
    @endif
  </div>

</div>
@endsection
