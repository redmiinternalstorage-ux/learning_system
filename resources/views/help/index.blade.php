@extends('layouts.app')
@section('title','Help Center')
@section('page-title','Help Center')

@push('styles')
<style>
.hc-tab { transition: all .2s; cursor: pointer; }
.hc-tab.active { background: linear-gradient(135deg, #059669, #0d9488); color: white; box-shadow: 0 4px 14px rgba(5,150,105,.25); }
</style>
@endpush

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

  {{-- Hero Banner --}}
  <div class="relative rounded-3xl overflow-hidden p-6 sm:p-8 bg-gradient-to-r from-emerald-600 to-teal-500 text-white shadow-sm">
    <div class="absolute -right-10 -top-10 w-48 h-48 rounded-full bg-white/10 blur-3xl pointer-events-none"></div>
    <div class="absolute -left-5 -bottom-5 w-32 h-32 rounded-full bg-teal-300/10 blur-2xl pointer-events-none"></div>
    <div class="relative z-10">
      <div class="flex items-start justify-between gap-4 flex-wrap">
        <div>
          <div class="flex items-center gap-3 mb-3">
            <div class="w-12 h-12 rounded-2xl bg-white/20 border border-white/25 flex items-center justify-center">
              <i class="fa-solid fa-headset text-white text-lg"></i>
            </div>
            <div>
              <h1 class="text-white text-2xl font-black font-outfit">Help Center</h1>
              <p class="text-emerald-100/80 text-xs mt-0.5">We're here to help — 24/7 support</p>
            </div>
          </div>
          <p class="text-emerald-50/90 text-sm max-w-md">Submit a ticket or start a live chat with our support team. We respond quickly.</p>
        </div>
        <a href="{{ route('chat.student') }}"
           class="flex items-center gap-2.5 px-5 py-2.5 rounded-2xl font-bold text-sm bg-white/20 hover:bg-white/30 border border-white/25 text-white transition-all whitespace-nowrap">
          <span class="w-2 h-2 rounded-full bg-emerald-300 animate-pulse"></span>
          <i class="fa-solid fa-comments"></i> Live Chat
        </a>
      </div>

      {{-- Stats --}}
      @php
        $open     = $tickets->where('status','open')->count();
        $total    = $tickets->total();
        $resolved = $tickets->where('status','resolved')->count();
      @endphp
      <div class="grid grid-cols-3 gap-3 mt-5">
        <div class="rounded-2xl p-3 text-center bg-white/10 border border-white/15">
          <p class="text-xl font-black text-white font-outfit">{{ $total }}</p>
          <p class="text-emerald-100/70 text-[9px] font-bold uppercase tracking-wider">Total</p>
        </div>
        <div class="rounded-2xl p-3 text-center bg-amber-400/20 border border-amber-300/25">
          <p class="text-xl font-black text-amber-200 font-outfit">{{ $open }}</p>
          <p class="text-amber-100/70 text-[9px] font-bold uppercase tracking-wider">Open</p>
        </div>
        <div class="rounded-2xl p-3 text-center bg-white/10 border border-white/15">
          <p class="text-xl font-black text-white font-outfit">{{ $resolved }}</p>
          <p class="text-emerald-100/70 text-[9px] font-bold uppercase tracking-wider">Resolved</p>
        </div>
      </div>
    </div>
  </div>

  {{-- Tabs --}}
  <div class="flex gap-2">
    <button onclick="switchTab('new')" id="tab-new"
            class="hc-tab active flex items-center gap-2 px-5 py-2.5 rounded-2xl text-sm font-bold border border-transparent">
      <i class="fa-solid fa-plus"></i> New Ticket
    </button>
    <button onclick="switchTab('list')" id="tab-list"
            class="hc-tab flex items-center gap-2 px-5 py-2.5 rounded-2xl text-sm font-bold bg-white border border-slate-200 text-slate-600">
      <i class="fa-solid fa-list"></i> My Tickets
      @php $newReply = $tickets->filter(fn($t) => !$t->user_read && $t->admin_replied)->count(); @endphp
      @if($newReply > 0)
      <span class="bg-rose-500 text-white text-[9px] font-black px-1.5 py-0.5 rounded-full">{{ $newReply }}</span>
      @endif
    </button>
  </div>

  {{-- New Ticket Form --}}
  <div id="pane-new">
    <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-sm">
      <div class="flex items-center gap-3 mb-5">
        <div class="w-10 h-10 rounded-xl bg-emerald-50 border border-emerald-100 flex items-center justify-center">
          <i class="fa-solid fa-ticket text-emerald-600"></i>
        </div>
        <div>
          <h3 class="font-extrabold text-slate-800 text-sm font-outfit">Submit Support Ticket</h3>
          <p class="text-slate-400 text-[10px]">We'll reply on web and by email</p>
        </div>
      </div>

      @if(session('success'))
      <div class="mb-4 p-3.5 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs font-semibold flex items-center gap-2">
        <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
      </div>
      @endif

      <form method="POST" action="{{ route('help.store') }}" class="space-y-4">
        @csrf
        <div class="grid sm:grid-cols-2 gap-4">
          <div>
            <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-1.5">Category <span class="text-rose-400">*</span></label>
            <select name="category" class="inp" required>
              @foreach(\App\Models\SupportTicket::$categories as $k => $cat)
              <option value="{{ $k }}" {{ old('category')===$k?'selected':'' }}>{{ $cat['label'] }}</option>
              @endforeach
            </select>
            @error('category')<p class="text-rose-500 text-xs mt-1">{{ $message }}</p>@enderror
          </div>
          <div>
            <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-1.5">Priority <span class="text-rose-400">*</span></label>
            <select name="priority" class="inp" required>
              <option value="low">🟢 Low</option>
              <option value="medium" selected>🟡 Medium</option>
              <option value="high">🔴 High</option>
              <option value="urgent">🚨 Urgent</option>
            </select>
          </div>
        </div>
        <div>
          <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-1.5">Subject <span class="text-rose-400">*</span></label>
          <input type="text" name="subject" value="{{ old('subject') }}" maxlength="200"
                 placeholder="Brief description of your issue…" class="inp" required>
          @error('subject')<p class="text-rose-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
          <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-1.5">Details <span class="text-rose-400">*</span></label>
          <textarea name="message" rows="5" maxlength="3000" required
                    placeholder="Explain your issue in detail…"
                    class="inp resize-none leading-relaxed">{{ old('message') }}</textarea>
          @error('message')<p class="text-rose-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>
        <div class="flex items-center justify-between pt-1 flex-wrap gap-3">
          <p class="text-xs text-slate-400 flex items-center gap-1.5">
            <i class="fa-solid fa-envelope text-emerald-500"></i>
            Confirmation sent to <strong>{{ Auth::user()->email }}</strong>
          </p>
          <button type="submit"
            class="flex items-center gap-2 px-6 py-2.5 rounded-2xl text-white text-sm font-bold bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 shadow-md shadow-emerald-500/20 transition-all">
            <i class="fa-solid fa-paper-plane"></i> Submit Ticket
          </button>
        </div>
      </form>
    </div>

    {{-- Quick Help Cards --}}
    <div class="grid sm:grid-cols-3 gap-4 mt-4">
      @foreach([
        ['icon'=>'fa-comments','from'=>'from-emerald-500','to'=>'to-teal-600','title'=>'Live Chat','desc'=>'Instant answers from our support team','link'=>route('chat.student'),'btn'=>'Start Chat'],
        ['icon'=>'fa-book-open','from'=>'from-teal-500','to'=>'to-emerald-600','title'=>'Browse Courses','desc'=>'Explore our learning materials','link'=>route('courses.public'),'btn'=>'Explore'],
        ['icon'=>'fa-user-gear','from'=>'from-emerald-600','to'=>'to-teal-500','title'=>'My Profile','desc'=>'Update your account settings','link'=>route('profile'),'btn'=>'Go to Profile'],
      ] as $card)
      <div class="bg-white border border-slate-100 rounded-2xl p-4 flex flex-col gap-3 hover:border-emerald-200 hover:shadow-sm transition">
        <div class="w-9 h-9 rounded-xl bg-gradient-to-br {{ $card['from'] }} {{ $card['to'] }} flex items-center justify-center">
          <i class="fa-solid {{ $card['icon'] }} text-white text-sm"></i>
        </div>
        <div>
          <p class="font-bold text-slate-800 text-sm">{{ $card['title'] }}</p>
          <p class="text-xs text-slate-400 mt-0.5">{{ $card['desc'] }}</p>
        </div>
        <a href="{{ $card['link'] }}" class="text-xs font-bold text-emerald-600 hover:text-emerald-700 mt-auto flex items-center gap-1 transition">
          {{ $card['btn'] }} <i class="fa-solid fa-arrow-right text-[10px]"></i>
        </a>
      </div>
      @endforeach
    </div>
  </div>

  {{-- My Tickets List --}}
  <div id="pane-list" class="hidden">
    <div class="bg-white border border-slate-200 rounded-3xl overflow-hidden shadow-sm">

      {{-- Filter bar --}}
      <div class="px-5 py-3.5 border-b border-slate-100 flex items-center gap-3">
        <h3 class="font-extrabold text-slate-800 text-sm font-outfit flex-1">
          My Tickets <span class="text-slate-400 font-normal text-xs">({{ $tickets->total() }})</span>
        </h3>
        <form method="GET" class="flex gap-2">
          <input type="hidden" name="tab" value="list">
          <select name="status" onchange="this.form.submit()" class="inp text-xs w-32 py-1.5">
            <option value="">All Status</option>
            @foreach(\App\Models\SupportTicket::$statusMeta as $k => $v)
            <option value="{{ $k }}" {{ request('status')===$k?'selected':'' }}>{{ $v['label'] }}</option>
            @endforeach
          </select>
        </form>
      </div>

      @forelse($tickets as $t)
      @php $sm = $t->getStatusMeta(); $pm = $t->getPriorityMeta(); $cm = $t->getCategoryMeta(); @endphp
      <a href="{{ route('help.ticket', $t) }}"
         class="flex items-center gap-4 px-5 py-4 border-b border-slate-100 hover:bg-emerald-50/40 transition last:border-b-0
                {{ !$t->user_read && $t->admin_replied ? 'bg-emerald-50/60' : '' }}">
        <div class="w-10 h-10 rounded-xl flex items-center justify-center text-white text-sm flex-shrink-0"
             style="background:{{ $cm['color'] }}">
          <i class="fa-solid {{ $cm['icon'] }}"></i>
        </div>
        <div class="flex-1 min-w-0">
          <div class="flex items-center gap-2 flex-wrap">
            <p class="text-sm font-bold text-slate-800 truncate">{{ $t->subject }}</p>
            @if(!$t->user_read && $t->admin_replied)
            <span class="text-[9px] font-black bg-emerald-500 text-white px-2 py-0.5 rounded-full animate-pulse">NEW REPLY</span>
            @endif
          </div>
          <p class="text-[10px] text-slate-400 mt-0.5 flex items-center gap-2 flex-wrap">
            <span>#{{ $t->id }}</span>
            <span>·</span>
            <span>{{ $t->created_at->diffForHumans() }}</span>
            <span>·</span>
            <span>{{ $cm['label'] }}</span>
            <span>·</span>
            <span>{{ $t->replies->count() }} {{ Str::plural('reply', $t->replies->count()) }}</span>
          </p>
        </div>
        <div class="flex flex-col items-end gap-1.5 flex-shrink-0">
          <span class="badge {{ $sm['badge'] }}">
            <i class="fa-solid {{ $sm['icon'] }} text-[8px]"></i> {{ $sm['label'] }}
          </span>
          <span class="text-[9px] font-bold" style="color:{{ $pm['color'] }}">{{ $pm['label'] }}</span>
        </div>
      </a>
      @empty
      <div class="px-6 py-14 text-center">
        <div class="w-16 h-16 rounded-2xl bg-emerald-50 border border-emerald-100 flex items-center justify-center mx-auto mb-4">
          <i class="fa-solid fa-ticket text-2xl text-emerald-300"></i>
        </div>
        <p class="text-slate-500 text-sm font-semibold">No tickets yet</p>
        <p class="text-slate-400 text-xs mt-1">Submit your first support request using the form</p>
        <button onclick="switchTab('new')" class="mt-4 text-xs font-bold text-emerald-600 hover:underline">
          Create Ticket →
        </button>
      </div>
      @endforelse

      @if($tickets->hasPages())
      <div class="px-5 py-3.5 border-t border-slate-100">
        {{ $tickets->appends(['tab'=>'list'])->links() }}
      </div>
      @endif
    </div>
  </div>

</div>
@endsection

@push('scripts')
<script>
function switchTab(tab) {
  const isNew = tab === 'new';
  document.getElementById('pane-new').classList.toggle('hidden', !isNew);
  document.getElementById('pane-list').classList.toggle('hidden', isNew);

  const tabNew  = document.getElementById('tab-new');
  const tabList = document.getElementById('tab-list');

  if (isNew) {
    tabNew.classList.add('active');
    tabNew.classList.remove('bg-white','border-slate-200','text-slate-600');
    tabList.classList.remove('active');
    tabList.className = 'hc-tab flex items-center gap-2 px-5 py-2.5 rounded-2xl text-sm font-bold bg-white border border-slate-200 text-slate-600';
  } else {
    tabList.classList.add('active');
    tabList.classList.remove('bg-white','border-slate-200','text-slate-600');
    tabNew.classList.remove('active');
    tabNew.className = 'hc-tab flex items-center gap-2 px-5 py-2.5 rounded-2xl text-sm font-bold bg-white border border-slate-200 text-slate-600';
  }
}

@if(request('tab') === 'list' || session('show_tickets'))
document.addEventListener('DOMContentLoaded', () => switchTab('list'));
@endif
@if(session('success'))
document.addEventListener('DOMContentLoaded', () => switchTab('list'));
@endif
</script>
@endpush
