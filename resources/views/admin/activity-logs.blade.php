@extends('admin.layout')
@section('title','Activity Log')
@section('page-title','Activity Log')

@section('content')
<div class="space-y-6">

  {{-- Hero --}}
  <div class="relative rounded-3xl overflow-hidden p-7"
       style="background:linear-gradient(135deg,#0a0020 0%,#1a1050 40%,#050010 100%)">
    <div class="absolute inset-0 pointer-events-none">
      <div class="absolute top-0 right-0 w-72 h-72 bg-indigo-500/20 rounded-full blur-3xl -translate-y-1/2 translate-x-1/3"></div>
    </div>
    <div class="relative z-10 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
      <div>
        <p class="text-indigo-300 text-[10px] font-black uppercase tracking-widest mb-2">Admin Panel</p>
        <h2 class="text-white text-2xl font-black font-outfit">🕐 Activity Log</h2>
        <p class="text-indigo-200/60 text-sm mt-1.5">Complete audit trail of all admin actions</p>
      </div>
      <form method="POST" action="{{ route('admin.activity-logs.clear') }}"
            onsubmit="return confirm('Delete activity logs older than 30 days?')">
        @csrf
        @method('DELETE')
        <button type="submit" class="flex items-center gap-2 px-4 py-2.5 rounded-2xl bg-rose-500/20 hover:bg-rose-500/30 border border-rose-400/30 text-rose-300 text-xs font-bold transition">
          <i class="fa-solid fa-trash-can"></i> Clear Old Logs
        </button>
      </form>
    </div>
  </div>

  {{-- Today summary chips --}}
  @if($todayCounts->isNotEmpty())
  <div class="glass rounded-2xl p-4">
    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-3">Today's Activity</p>
    <div class="flex flex-wrap gap-2">
      @foreach($todayCounts as $action => $cnt)
      @php $m = \App\Models\ActivityLog::$actionMeta[$action] ?? ['icon'=>'fa-dot-circle','color'=>'#64748b','label'=>$action]; @endphp
      <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-[10px] font-bold border"
            style="background:{{ $m['color'] }}15;color:{{ $m['color'] }};border-color:{{ $m['color'] }}30">
        <i class="fa-solid {{ $m['icon'] }} text-[9px]"></i>
        {{ $m['label'] }} <span class="font-black ml-0.5">×{{ $cnt }}</span>
      </span>
      @endforeach
    </div>
  </div>
  @endif

  {{-- Filter --}}
  <div class="glass rounded-2xl p-4">
    <form method="GET" action="{{ route('admin.activity-logs') }}" class="flex flex-wrap items-end gap-3">
      <div class="flex-1 min-w-[150px]">
        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Search</label>
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Action, target, user…" class="inp text-xs h-10">
      </div>
      <div class="min-w-[170px]">
        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Action Type</label>
        <select name="action" class="inp text-xs h-10">
          <option value="">All Actions</option>
          @foreach($actions as $a)
          @php $m = \App\Models\ActivityLog::$actionMeta[$a] ?? ['label'=>$a]; @endphp
          <option value="{{ $a }}" {{ request('action')===$a?'selected':'' }}>{{ $m['label'] }}</option>
          @endforeach
        </select>
      </div>
      <div class="min-w-[130px]">
        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">From</label>
        <input type="date" name="date_from" value="{{ request('date_from') }}" class="inp text-xs h-10">
      </div>
      <div class="min-w-[130px]">
        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">To</label>
        <input type="date" name="date_to" value="{{ request('date_to') }}" class="inp text-xs h-10">
      </div>
      <div class="flex gap-2 pb-0.5">
        <button type="submit" class="btn-primary h-10 px-5 text-xs"><i class="fa-solid fa-magnifying-glass"></i> Filter</button>
        @if(request()->hasAny(['search','action','date_from','date_to']))
        <a href="{{ route('admin.activity-logs') }}" class="btn-secondary h-10 px-4 text-xs"><i class="fa-solid fa-xmark"></i></a>
        @endif
      </div>
    </form>
  </div>

  {{-- Timeline Table --}}
  <div class="glass rounded-3xl overflow-hidden">
    <div class="px-6 py-4 border-b border-slate-100 dark:border-white/5">
      <h3 class="font-extrabold text-slate-800 dark:text-slate-100 text-sm font-outfit">Activity Records</h3>
      <p class="text-slate-400 text-[10px] mt-0.5">{{ $logs->firstItem()??0 }}–{{ $logs->lastItem()??0 }} of {{ $logs->total() }}</p>
    </div>
    <div class="divide-y divide-slate-100/50 dark:divide-white/[0.03]">
      @forelse($logs as $log)
      @php $m = $log->getMeta(); @endphp
      <div class="px-6 py-3.5 flex items-start gap-4 hover:bg-slate-50/50 dark:hover:bg-white/[0.02] transition">
        {{-- Icon --}}
        <div class="w-8 h-8 rounded-xl flex items-center justify-center text-white text-xs flex-shrink-0 shadow-sm mt-0.5"
             style="background:{{ $m['color'] }}">
          <i class="fa-solid {{ $m['icon'] }}"></i>
        </div>
        {{-- Content --}}
        <div class="flex-1 min-w-0">
          <div class="flex items-center gap-2 flex-wrap">
            <span class="text-xs font-bold text-slate-700 dark:text-slate-200">{{ $m['label'] }}</span>
            @if($log->target_label)
            <span class="text-[10px] font-semibold text-slate-500 dark:text-slate-400 truncate max-w-[200px]">
              → {{ $log->target_label }}
            </span>
            @endif
          </div>
          @if($log->description)
          <p class="text-[10px] text-slate-400 mt-0.5">{{ $log->description }}</p>
          @endif
          <div class="flex items-center gap-3 mt-1">
            @if($log->user)
            <span class="text-[9px] font-bold text-purple-500">
              <i class="fa-solid fa-user mr-1"></i>{{ $log->user->name }}
            </span>
            @endif
            @if($log->ip)
            <span class="text-[9px] text-slate-400">
              <i class="fa-solid fa-network-wired mr-1"></i>{{ $log->ip }}
            </span>
            @endif
          </div>
        </div>
        {{-- Time --}}
        <div class="text-right flex-shrink-0">
          <p class="text-[10px] font-semibold text-slate-500 dark:text-slate-400 whitespace-nowrap">
            {{ $log->created_at->format('d M Y') }}
          </p>
          <p class="text-[9px] text-purple-400">{{ $log->created_at->diffForHumans() }}</p>
        </div>
      </div>
      @empty
      <div class="px-6 py-16 text-center text-slate-400 text-xs">
        <i class="fa-solid fa-clock-rotate-left text-2xl mb-3 block opacity-30"></i>
        No activity logs found.
      </div>
      @endforelse
    </div>
    @if($logs->hasPages())
    <div class="px-6 py-4 border-t border-slate-100 dark:border-white/5 flex justify-between items-center">
      <p class="text-[10px] text-slate-400 font-bold">Page {{ $logs->currentPage() }} of {{ $logs->lastPage() }}</p>
      <div class="flex gap-1.5">
        @if(!$logs->onFirstPage())
        <a href="{{ $logs->previousPageUrl() }}" class="w-8 h-8 rounded-xl bg-slate-100 dark:bg-white/5 text-slate-500 flex items-center justify-center hover:bg-indigo-50 hover:text-indigo-600 transition text-xs"><i class="fa-solid fa-chevron-left"></i></a>
        @endif
        @foreach($logs->getUrlRange(max(1,$logs->currentPage()-2),min($logs->lastPage(),$logs->currentPage()+2)) as $page=>$url)
        <a href="{{ $url }}" class="w-8 h-8 rounded-xl flex items-center justify-center text-[11px] font-bold transition {{ $page==$logs->currentPage() ? 'bg-gradient-to-br from-indigo-500 to-purple-600 text-white shadow-md' : 'bg-slate-100 dark:bg-white/5 text-slate-500 hover:bg-indigo-50 hover:text-indigo-600' }}">{{ $page }}</a>
        @endforeach
        @if($logs->hasMorePages())
        <a href="{{ $logs->nextPageUrl() }}" class="w-8 h-8 rounded-xl bg-slate-100 dark:bg-white/5 text-slate-500 flex items-center justify-center hover:bg-indigo-50 hover:text-indigo-600 transition text-xs"><i class="fa-solid fa-chevron-right"></i></a>
        @endif
      </div>
    </div>
    @endif
  </div>
</div>
@endsection
