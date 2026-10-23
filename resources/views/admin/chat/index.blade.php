@extends('admin.layout')
@section('title','Live Chat')
@section('page-title','Live Chat')

@section('content')
<div class="space-y-5">

  {{-- Header --}}
  <div class="glass rounded-2xl p-5 flex items-center gap-4"
       style="background:linear-gradient(135deg,rgba(5,150,105,0.12) 0%,rgba(16,185,129,0.06) 100%);">
    <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center text-white shadow-lg shadow-emerald-500/30">
      <i class="fa-solid fa-comments text-lg"></i>
    </div>
    <div>
      <h2 class="font-extrabold text-slate-800 dark:text-slate-100 text-base font-outfit">Live Chat Sessions</h2>
      <p class="text-slate-500 dark:text-slate-400 text-xs mt-0.5">Respond to students in real-time</p>
    </div>
    <div class="ml-auto flex gap-3">
      <div class="text-center">
        @php
          $waitingCount = \App\Models\ChatMessage::where('chat_status','waiting')->distinct('session_id')->count('session_id');
          $activeCount  = \App\Models\ChatMessage::where('chat_status','active')->distinct('session_id')->count('session_id');
        @endphp
        <p class="text-xl font-black text-amber-500 font-outfit">{{ $waitingCount }}</p>
        <p class="text-[9px] font-bold text-slate-400 uppercase tracking-wider">Waiting</p>
      </div>
      <div class="text-center">
        <p class="text-xl font-black text-emerald-500 font-outfit">{{ $activeCount }}</p>
        <p class="text-[9px] font-bold text-slate-400 uppercase tracking-wider">Active</p>
      </div>
    </div>
  </div>

  {{-- Sessions List --}}
  <div class="glass rounded-2xl overflow-hidden">
    <div class="px-6 py-4 border-b border-slate-100 dark:border-white/5">
      <h3 class="font-extrabold text-slate-800 dark:text-slate-100 text-sm font-outfit">
        <i class="fa-solid fa-inbox text-emerald-500 mr-2"></i>Chat Sessions
      </h3>
    </div>
    @if($sessions->isEmpty())
    <div class="py-16 text-center">
      <div class="w-14 h-14 rounded-2xl bg-emerald-50 dark:bg-emerald-950/20 flex items-center justify-center mx-auto mb-3">
        <i class="fa-solid fa-comments text-2xl text-emerald-300 dark:text-emerald-700"></i>
      </div>
      <p class="text-slate-400 text-sm font-semibold">No chat sessions yet</p>
      <p class="text-slate-400 text-xs mt-1">Students can start a chat from the Help Center</p>
    </div>
    @else
    <div class="divide-y divide-slate-100 dark:divide-white/[0.04]">
      @foreach($sessions as $s)
      <a href="{{ route('admin.chat.show', $s->session_id) }}"
         class="flex items-center gap-4 px-6 py-4 hover:bg-emerald-50/30 dark:hover:bg-white/[0.02] transition group
                {{ $s->chat_status === 'waiting' ? 'bg-amber-50/40 dark:bg-amber-950/10' : '' }}">
        <div class="w-10 h-10 rounded-xl flex items-center justify-center text-white font-bold text-sm flex-shrink-0
            {{ $s->chat_status === 'waiting' ? 'bg-gradient-to-br from-amber-400 to-orange-500' :
               ($s->chat_status === 'active'  ? 'bg-gradient-to-br from-emerald-500 to-teal-600' :
                                                'bg-slate-400') }}">
          {{ strtoupper(substr($s->user->name ?? 'U', 0, 1)) }}
        </div>
        <div class="flex-1 min-w-0">
          <div class="flex items-center gap-2">
            <p class="font-bold text-slate-700 dark:text-slate-200 text-sm">{{ $s->user->name ?? 'Unknown' }}</p>
            @if($s->unread > 0)
            <span class="text-[9px] font-black bg-rose-500 text-white px-1.5 py-0.5 rounded-full">{{ $s->unread }} new</span>
            @endif
          </div>
          <p class="text-[10px] text-slate-400 mt-0.5">{{ $s->user->email ?? '' }}</p>
        </div>
        <div class="flex flex-col items-end gap-1.5">
          <span class="badge {{ $s->chat_status === 'waiting' ? 'badge-amber' : ($s->chat_status === 'active' ? 'badge-green' : 'badge-gray') }}">
            {{ ucfirst($s->chat_status) }}
          </span>
          <span class="text-[10px] text-slate-400">{{ \Carbon\Carbon::parse($s->last_at)->diffForHumans() }}</span>
        </div>
        <i class="fa-solid fa-chevron-right text-slate-300 dark:text-slate-600 group-hover:text-emerald-500 transition text-xs ml-2"></i>
      </a>
      @endforeach
    </div>
    @if($sessions->hasPages())
    <div class="px-6 py-4 border-t border-slate-100 dark:border-white/5">
      {{ $sessions->links() }}
    </div>
    @endif
    @endif
  </div>

</div>
@endsection

@push('scripts')
<script>
  // Auto-refresh sessions list every 10s
  setInterval(() => window.location.reload(), 10000);
</script>
@endpush
