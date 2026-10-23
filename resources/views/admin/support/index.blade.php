@extends('admin.layout')
@section('title','Help Tickets')
@section('page-title','Help Tickets')

@section('content')
<div class="space-y-5">

  {{-- Stats --}}
  <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
    @php
      $cards = [
        ['label'=>'Open',        'val'=>$stats['open'],        'icon'=>'fa-circle-dot',   'color'=>'text-amber-600',  'bg'=>'bg-amber-50'],
        ['label'=>'In Progress', 'val'=>$stats['in_progress'], 'icon'=>'fa-spinner',      'color'=>'text-sky-600',    'bg'=>'bg-sky-50'],
        ['label'=>'Resolved',    'val'=>$stats['resolved'],    'icon'=>'fa-circle-check', 'color'=>'text-emerald-600','bg'=>'bg-emerald-50'],
        ['label'=>'Unread',      'val'=>$stats['unread'],      'icon'=>'fa-bell',         'color'=>'text-rose-600',   'bg'=>'bg-rose-50'],
      ];
    @endphp
    @foreach($cards as $c)
    <div class="glass rounded-2xl p-5 stat-lift {{ $c['bg'] }} border border-transparent">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl {{ $c['bg'] }} flex items-center justify-center {{ $c['color'] }} text-lg border border-current/20">
          <i class="fa-solid {{ $c['icon'] }}"></i>
        </div>
        <div>
          <p class="text-2xl font-black {{ $c['color'] }} font-outfit">{{ $c['val'] }}</p>
          <p class="text-slate-500 text-[10px] font-bold uppercase tracking-wider">{{ $c['label'] }}</p>
        </div>
      </div>
    </div>
    @endforeach
  </div>

  {{-- Filters --}}
  <div class="glass rounded-2xl p-4">
    <form method="GET" class="flex flex-wrap gap-3 items-end">
      <div class="flex-1 min-w-36">
        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Search</label>
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Subject, name, email…" class="inp text-xs">
      </div>
      <div class="w-32">
        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Status</label>
        <select name="status" class="inp text-xs">
          <option value="">All</option>
          @foreach(\App\Models\SupportTicket::$statusMeta as $k => $v)
          <option value="{{ $k }}" {{ request('status')===$k?'selected':'' }}>{{ $v['label'] }}</option>
          @endforeach
        </select>
      </div>
      <div class="w-28">
        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Priority</label>
        <select name="priority" class="inp text-xs">
          <option value="">All</option>
          @foreach(\App\Models\SupportTicket::$priorityMeta as $k => $v)
          <option value="{{ $k }}" {{ request('priority')===$k?'selected':'' }}>{{ $v['label'] }}</option>
          @endforeach
        </select>
      </div>
      <div class="w-32">
        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Category</label>
        <select name="category" class="inp text-xs">
          <option value="">All</option>
          @foreach(\App\Models\SupportTicket::$categories as $k => $v)
          <option value="{{ $k }}" {{ request('category')===$k?'selected':'' }}>{{ $v['label'] }}</option>
          @endforeach
        </select>
      </div>
      <button type="submit" class="btn-primary text-xs h-[38px] px-5">
        <i class="fa-solid fa-magnifying-glass"></i> Filter
      </button>
      @if(request()->hasAny(['search','status','priority','category']))
      <a href="{{ route('admin.support.index') }}" class="btn-secondary text-xs h-[38px] px-4">
        <i class="fa-solid fa-xmark"></i> Clear
      </a>
      @endif
    </form>
  </div>

  {{-- Tickets Table --}}
  <div class="glass rounded-2xl overflow-hidden">
    <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
      <h3 class="font-extrabold text-slate-800 text-sm font-outfit">
        <i class="fa-solid fa-ticket text-emerald-600 mr-2"></i>Support Tickets
        <span class="ml-2 text-xs font-normal text-slate-400">({{ $tickets->total() }} total)</span>
      </h3>
    </div>
    @if($tickets->isEmpty())
    <div class="py-16 text-center">
      <div class="w-14 h-14 rounded-2xl bg-emerald-50 flex items-center justify-center mx-auto mb-3">
        <i class="fa-solid fa-ticket text-2xl text-emerald-400"></i>
      </div>
      <p class="text-slate-500 text-sm font-semibold">No tickets found</p>
    </div>
    @else
    <div class="overflow-x-auto">
      <table class="w-full admin-table">
        <thead>
          <tr>
            <th class="text-left">#</th>
            <th class="text-left">User</th>
            <th class="text-left">Subject</th>
            <th class="text-left">Category</th>
            <th class="text-left">Status</th>
            <th class="text-left">Priority</th>
            <th class="text-left">Date</th>
            <th class="text-left">Action</th>
          </tr>
        </thead>
        <tbody>
          @foreach($tickets as $t)
          @php $sm = $t->getStatusMeta(); $pm = $t->getPriorityMeta(); $cm = $t->getCategoryMeta(); @endphp
          <tr class="{{ !$t->admin_read ? 'bg-emerald-50/60 font-medium' : '' }}">
            <td class="text-slate-400 text-xs">
              #{{ $t->id }}
              @if(!$t->admin_read)
              <span class="block w-2 h-2 rounded-full bg-rose-500 inline-block ml-1"></span>
              @endif
            </td>
            <td>
              <div>
                <p class="font-bold text-slate-700 text-xs">{{ $t->user->name ?? '—' }}</p>
                <p class="text-[10px] text-slate-400">{{ $t->user->email ?? '' }}</p>
              </div>
            </td>
            <td>
              <p class="font-semibold text-slate-700 text-xs max-w-[200px] truncate">{{ $t->subject }}</p>
              @if($t->lastReply)
              <p class="text-[10px] text-slate-400 mt-0.5">{{ $t->replies->count() }} {{ Str::plural('reply',$t->replies->count()) }}</p>
              @endif
            </td>
            <td>
              <span class="inline-flex items-center gap-1.5 text-[10px] font-semibold text-slate-600">
                <i class="fa-solid {{ $cm['icon'] }} text-[11px]" style="color:{{ $cm['color'] }}"></i>
                {{ $cm['label'] }}
              </span>
            </td>
            <td>
              <span class="badge {{ $sm['badge'] }}">
                <i class="fa-solid {{ $sm['icon'] }} text-[8px]"></i> {{ $sm['label'] }}
              </span>
            </td>
            <td>
              <span class="text-[10px] font-bold" style="color:{{ $pm['color'] }}">{{ $pm['label'] }}</span>
            </td>
            <td class="text-[10px] text-slate-400 whitespace-nowrap">{{ $t->created_at->format('d M y') }}</td>
            <td>
              <a href="{{ route('admin.support.show', $t) }}" class="btn-primary px-3 py-1.5 text-[10px]">
                <i class="fa-solid fa-eye"></i> View
              </a>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
    @if($tickets->hasPages())
    <div class="px-6 py-4 border-t border-slate-100">
      {{ $tickets->links() }}
    </div>
    @endif
    @endif
  </div>
</div>
@endsection
