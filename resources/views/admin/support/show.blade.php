@extends('admin.layout')
@section('title','Ticket #'.$ticket->id)
@section('page-title','Ticket #'.$ticket->id)

@section('content')
@php $sm = $ticket->getStatusMeta(); $pm = $ticket->getPriorityMeta(); $cm = $ticket->getCategoryMeta(); @endphp

<div class="space-y-5 max-w-4xl">

  {{-- Back + Meta --}}
  <div class="flex items-center gap-3 flex-wrap">
    <a href="{{ route('admin.support.index') }}" class="btn-secondary text-xs px-4 py-2">
      <i class="fa-solid fa-arrow-left"></i> All Tickets
    </a>
    <span class="badge {{ $sm['badge'] }}"><i class="fa-solid {{ $sm['icon'] }} text-[8px]"></i> {{ $sm['label'] }}</span>
    <span class="text-[10px] font-bold" style="color:{{ $pm['color'] }}">
      <i class="fa-solid fa-flag text-[10px]"></i> {{ $pm['label'] }}
    </span>
    <span class="text-[10px] text-slate-400">
      <i class="fa-solid {{ $cm['icon'] }} mr-1" style="color:{{ $cm['color'] }}"></i>{{ $cm['label'] }}
    </span>
    <span class="ml-auto text-[10px] text-slate-400">Opened {{ $ticket->created_at->format('d M Y, h:i A') }}</span>
  </div>

  {{-- Ticket Detail --}}
  <div class="glass rounded-2xl overflow-hidden">
    <div class="px-6 py-4 border-b border-slate-100 dark:border-white/5 flex items-center gap-3">
      <div class="w-9 h-9 rounded-xl flex items-center justify-center text-white text-xs font-bold bg-gradient-to-br from-purple-400 to-fuchsia-600">
        {{ strtoupper(substr($ticket->user->name ?? 'U', 0, 1)) }}
      </div>
      <div>
        <p class="font-extrabold text-slate-800 dark:text-slate-100 text-sm">{{ $ticket->subject }}</p>
        <p class="text-[10px] text-slate-400">by {{ $ticket->user->name ?? 'Unknown' }} · {{ $ticket->user->email ?? '' }}</p>
      </div>
    </div>
    <div class="p-6">
      <p class="text-sm text-slate-600 dark:text-slate-300 leading-relaxed whitespace-pre-wrap">{{ $ticket->message }}</p>
    </div>
  </div>

  {{-- Reply Thread --}}
  @if($ticket->replies->isNotEmpty())
  <div class="glass rounded-2xl overflow-hidden">
    <div class="px-6 py-3 border-b border-slate-100 dark:border-white/5">
      <h4 class="font-extrabold text-slate-700 dark:text-slate-300 text-xs uppercase tracking-wider">
        <i class="fa-solid fa-comments text-purple-400 mr-2"></i>{{ $ticket->replies->count() }} {{ Str::plural('Reply', $ticket->replies->count()) }}
      </h4>
    </div>
    <div class="divide-y divide-slate-100 dark:divide-white/[0.04]">
      @foreach($ticket->replies as $r)
      <div class="p-5 flex gap-4 {{ $r->is_admin ? 'bg-purple-50/40 dark:bg-purple-950/10' : '' }}">
        <div class="w-8 h-8 rounded-xl flex items-center justify-center text-[11px] font-black flex-shrink-0
            {{ $r->is_admin ? 'bg-gradient-to-br from-purple-500 to-fuchsia-600 text-white' : 'bg-slate-200 dark:bg-white/10 text-slate-600 dark:text-slate-300' }}">
          {{ strtoupper(substr($r->user->name ?? 'A', 0, 1)) }}
        </div>
        <div class="flex-1">
          <div class="flex items-center gap-2 mb-1.5 flex-wrap">
            <span class="font-bold text-xs text-slate-700 dark:text-slate-200">{{ $r->user->name ?? 'Unknown' }}</span>
            @if($r->is_admin)
            <span class="text-[9px] bg-purple-100 dark:bg-purple-900/40 text-purple-600 dark:text-purple-300 px-2 py-0.5 rounded-full font-black uppercase">Admin</span>
            @endif
            @if($r->mail_sent)
            <span class="text-[9px] bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 dark:text-emerald-400 px-2 py-0.5 rounded-full font-semibold border border-emerald-200 dark:border-emerald-800">
              <i class="fa-solid fa-envelope-circle-check text-[8px]"></i> Mail Sent
            </span>
            @endif
            <span class="ml-auto text-[10px] text-slate-400">{{ $r->created_at->format('d M Y, h:i A') }}</span>
          </div>
          <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed whitespace-pre-wrap">{{ $r->message }}</p>
        </div>
      </div>
      @endforeach
    </div>
  </div>
  @endif

  {{-- Reply Form --}}
  <div class="glass rounded-2xl p-6">
    <h4 class="font-extrabold text-slate-800 dark:text-slate-100 text-sm font-outfit mb-4">
      <i class="fa-solid fa-reply text-purple-500 mr-2"></i>Reply to Ticket
    </h4>
    <form method="POST" action="{{ route('admin.support.reply', $ticket) }}" class="space-y-4">
      @csrf
      <div>
        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Your Reply <span class="text-rose-400">*</span></label>
        <textarea name="message" rows="5" required maxlength="5000"
                  placeholder="Type your reply here… The reply will appear on the web and optionally sent via email."
                  class="inp text-sm resize-none leading-relaxed">{{ old('message') }}</textarea>
        @error('message')<p class="text-rose-500 text-xs mt-1">{{ $message }}</p>@enderror
      </div>

      <div class="grid sm:grid-cols-2 gap-4">
        <div>
          <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Update Status</label>
          <select name="new_status" class="inp text-sm">
            @foreach(\App\Models\SupportTicket::$statusMeta as $k => $v)
            <option value="{{ $k }}" {{ $ticket->status===$k?'selected':'' }}>{{ $v['label'] }}</option>
            @endforeach
          </select>
        </div>
        <div class="flex items-end">
          <label class="flex items-center gap-3 cursor-pointer p-3 rounded-xl border border-slate-200 dark:border-white/10 hover:bg-slate-50 dark:hover:bg-white/5 transition w-full">
            <input type="checkbox" name="send_email" value="1" checked
                   class="w-4 h-4 rounded accent-purple-600">
            <div>
              <p class="font-bold text-slate-700 dark:text-slate-200 text-xs">Send via Email</p>
              <p class="text-[10px] text-slate-400">Notify {{ $ticket->user->email ?? 'user' }} by email</p>
            </div>
          </label>
        </div>
      </div>

      <div class="flex items-center gap-3 pt-1">
        <button type="submit" class="btn-primary text-xs">
          <i class="fa-solid fa-paper-plane"></i> Post Reply
        </button>
        <p class="text-[10px] text-slate-400">
          <i class="fa-solid fa-circle-info mr-1"></i>
          Reply is always posted on web. Check the box to also send email.
        </p>
      </div>
    </form>
  </div>

  {{-- Quick status + Delete --}}
  @if(Auth::user()->isAdmin())
  <div class="glass rounded-2xl p-4 flex flex-wrap items-center gap-3">
    <form method="POST" action="{{ route('admin.support.status', $ticket) }}" class="flex items-center gap-2">
      @csrf
      <select name="status" class="inp text-xs w-36">
        @foreach(\App\Models\SupportTicket::$statusMeta as $k => $v)
        <option value="{{ $k }}" {{ $ticket->status===$k?'selected':'' }}>{{ $v['label'] }}</option>
        @endforeach
      </select>
      <button type="submit" class="btn-secondary text-xs px-3 py-2">
        <i class="fa-solid fa-floppy-disk"></i> Save Status
      </button>
    </form>
    <form method="POST" action="{{ route('admin.support.destroy', $ticket) }}" class="ml-auto"
          onsubmit="return confirm('Delete this ticket permanently?')">
      @csrf @method('DELETE')
      <button type="submit" class="flex items-center gap-1.5 px-3 py-2 rounded-xl bg-rose-50 dark:bg-rose-950/20 text-rose-600 dark:text-rose-400 text-xs font-bold border border-rose-200 dark:border-rose-900/30 hover:bg-rose-100 dark:hover:bg-rose-950/40 transition">
        <i class="fa-solid fa-trash"></i> Delete Ticket
      </button>
    </form>
  </div>
  @endif

</div>
@endsection
