@extends('layouts.app')
@section('title','Ticket #'.$ticket->id)
@section('page-title','Support Ticket')

@section('content')
@php $sm = $ticket->getStatusMeta(); $pm = $ticket->getPriorityMeta(); $cm = $ticket->getCategoryMeta(); @endphp

<div class="max-w-3xl mx-auto space-y-5">

  {{-- Back + Meta --}}
  <div class="flex items-center gap-3 flex-wrap">
    <a href="{{ route('help.index') }}?tab=list"
       class="flex items-center gap-2 text-xs font-bold text-slate-500 hover:text-emerald-600 transition">
      <i class="fa-solid fa-arrow-left"></i> Help Center
    </a>
    <div class="h-4 w-px bg-slate-200"></div>
    <span class="text-xs text-slate-400">Ticket #{{ $ticket->id }}</span>
    <div class="ml-auto flex items-center gap-2 flex-wrap">
      <span class="badge {{ $sm['badge'] }}">
        <i class="fa-solid {{ $sm['icon'] }} text-[8px]"></i> {{ $sm['label'] }}
      </span>
      <span class="text-[10px] font-bold px-2 py-0.5 rounded-full" style="color:{{ $pm['color'] }};background:{{ $pm['color'] }}22">
        {{ $pm['label'] }} Priority
      </span>
    </div>
  </div>

  {{-- Flash --}}
  @if(session('success'))
  <div class="p-3.5 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs font-semibold flex items-center gap-2">
    <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
  </div>
  @endif

  {{-- Original Ticket --}}
  <div class="bg-white border border-slate-200 rounded-3xl overflow-hidden shadow-sm">
    <div class="px-6 py-4 border-b border-slate-100"
         style="background:linear-gradient(135deg,{{ $cm['color'] }}12,transparent)">
      <div class="flex items-start gap-3">
        <div class="w-10 h-10 rounded-xl flex items-center justify-center text-white text-sm flex-shrink-0 mt-0.5"
             style="background:{{ $cm['color'] }}">
          <i class="fa-solid {{ $cm['icon'] }}"></i>
        </div>
        <div class="flex-1">
          <h2 class="font-extrabold text-slate-800 text-base">{{ $ticket->subject }}</h2>
          <p class="text-[10px] text-slate-400 mt-1 flex items-center gap-2 flex-wrap">
            <span>{{ $cm['label'] }}</span>
            <span>·</span>
            <span>by <strong class="text-slate-600">{{ $ticket->user->name }}</strong></span>
            <span>·</span>
            <span>{{ $ticket->created_at->format('d M Y, h:i A') }}</span>
          </p>
        </div>
      </div>
    </div>
    <div class="px-6 py-5">
      <p class="text-sm text-slate-600 leading-relaxed whitespace-pre-wrap">{{ $ticket->message }}</p>
    </div>
  </div>

  {{-- Conversation Thread --}}
  @if($ticket->replies->isNotEmpty())
  <div class="space-y-3">
    <h3 class="text-xs font-bold text-slate-400 uppercase tracking-widest flex items-center gap-2">
      <i class="fa-solid fa-comments text-emerald-500"></i>
      Conversation ({{ $ticket->replies->count() }} {{ Str::plural('reply', $ticket->replies->count()) }})
    </h3>
    @foreach($ticket->replies as $r)
    <div class="flex gap-3 {{ $r->is_admin ? '' : 'flex-row-reverse' }}">
      <div class="w-8 h-8 rounded-xl flex-shrink-0 flex items-center justify-center text-[11px] font-black
          {{ $r->is_admin ? 'bg-gradient-to-br from-emerald-500 to-teal-600 text-white' : 'bg-emerald-100 text-emerald-600' }}">
        {{ $r->is_admin ? 'S' : strtoupper(substr($r->user->name ?? 'U', 0, 1)) }}
      </div>
      <div class="flex-1 max-w-[82%]">
        <div class="flex items-center gap-2 mb-1.5 {{ $r->is_admin ? '' : 'flex-row-reverse' }}">
          <span class="text-xs font-bold text-slate-700">
            {{ $r->is_admin ? 'Support Team' : 'You' }}
          </span>
          @if($r->is_admin)
          <span class="text-[9px] bg-emerald-100 text-emerald-700 px-2 py-0.5 rounded-full font-black uppercase">Staff</span>
          @endif
          @if($r->mail_sent ?? false)
          <span class="text-[9px] text-emerald-500 flex items-center gap-1">
            <i class="fa-solid fa-envelope-circle-check"></i> Emailed
          </span>
          @endif
          <span class="text-[10px] text-slate-400 {{ $r->is_admin ? 'ml-auto' : 'mr-auto' }}">{{ $r->created_at->diffForHumans() }}</span>
        </div>
        <div class="px-4 py-3 rounded-2xl text-sm leading-relaxed
            {{ $r->is_admin
              ? 'bg-emerald-50 border border-emerald-200 text-emerald-900 rounded-tl-sm'
              : 'bg-slate-100 text-slate-700 rounded-tr-sm' }}">
          <p class="whitespace-pre-wrap">{{ $r->message }}</p>
        </div>
      </div>
    </div>
    @endforeach
  </div>
  @endif

  {{-- Reply Form --}}
  @if(!in_array($ticket->status, ['closed','resolved']))
  <div class="bg-white border border-slate-200 rounded-3xl p-5 shadow-sm">
    <h4 class="font-extrabold text-slate-800 text-sm font-outfit mb-4 flex items-center gap-2">
      <i class="fa-solid fa-reply text-emerald-500"></i> Add a Reply
    </h4>
    <form method="POST" action="{{ route('help.reply', $ticket) }}" class="space-y-3">
      @csrf
      <textarea name="message" rows="4" required maxlength="3000"
                placeholder="Add more details or respond to the support team…"
                class="inp resize-none leading-relaxed">{{ old('message') }}</textarea>
      @error('message')<p class="text-rose-500 text-xs">{{ $message }}</p>@enderror
      <div class="flex items-center justify-between gap-3 flex-wrap">
        <p class="text-xs text-slate-400">
          <i class="fa-solid fa-circle-info text-emerald-400 mr-1"></i>
          Replies are visible to the support team
        </p>
        <button type="submit"
          class="flex items-center gap-2 px-5 py-2.5 rounded-2xl text-white text-sm font-bold bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 shadow-md shadow-emerald-500/20 transition">
          <i class="fa-solid fa-paper-plane"></i> Send Reply
        </button>
      </div>
    </form>
  </div>
  @else
  <div class="bg-slate-50 border border-slate-200 rounded-3xl p-5 text-center">
    <i class="fa-solid {{ $ticket->status === 'resolved' ? 'fa-circle-check text-emerald-500' : 'fa-lock text-slate-400' }} text-xl mb-2 block"></i>
    <p class="text-slate-700 text-sm font-semibold">
      Ticket {{ $ticket->status === 'resolved' ? 'Resolved ✅' : 'Closed' }}
    </p>
    <p class="text-xs text-slate-400 mt-1">
      Need more help?
      <a href="{{ route('help.index') }}" class="text-emerald-600 hover:underline">Open a new ticket</a>
      or
      <a href="{{ route('chat.student') }}" class="text-emerald-600 hover:underline">start a live chat</a>
    </p>
  </div>
  @endif

  {{-- Live Chat CTA --}}
  <div class="rounded-3xl p-5 flex items-center gap-4 justify-between bg-emerald-50 border border-emerald-200 flex-wrap gap-3">
    <div class="flex items-center gap-3">
      <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center">
        <i class="fa-solid fa-comments text-white"></i>
      </div>
      <div>
        <p class="font-bold text-slate-800 text-sm">Need instant help?</p>
        <p class="text-xs text-slate-500">Chat with a support agent right now</p>
      </div>
    </div>
    <a href="{{ route('chat.student') }}"
       class="flex items-center gap-2 px-5 py-2.5 rounded-2xl text-white text-sm font-bold bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 shadow-md transition">
      <i class="fa-solid fa-comments"></i> Start Live Chat
    </a>
  </div>

</div>
@endsection
