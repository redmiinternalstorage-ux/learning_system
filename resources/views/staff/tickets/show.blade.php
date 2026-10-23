<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Ticket #{{ $ticket->id }} — Staff Portal</title>
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&family=Outfit:wght@600;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<script>tailwind.config={darkMode:'class',theme:{extend:{fontFamily:{sans:['"Plus Jakarta Sans"'],outfit:['"Outfit"']}}}}</script>
<style>
body{font-family:'Plus Jakarta Sans',sans-serif;background:linear-gradient(160deg,#060018,#040012,#020008);}
.glass{background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.09);}
.inp{width:100%;padding:.65rem .875rem;border-radius:.75rem;border:1px solid rgba(255,255,255,.1);background:rgba(255,255,255,.05);color:#f1f5f9;font-size:.82rem;outline:none;transition:all .2s;}
.inp:focus{border-color:#6366f1;box-shadow:0 0 0 3px rgba(99,102,241,.15);}
.inp::placeholder{color:rgba(148,163,184,.5);}
select.inp option{background:#1e293b;}
textarea.inp{resize:vertical;}
.badge{display:inline-flex;align-items:center;gap:.25rem;padding:.15rem .55rem;border-radius:9999px;font-size:.65rem;font-weight:700;text-transform:uppercase;}
.badge-amber{background:rgba(245,158,11,.15);color:#fbbf24;border:1px solid rgba(245,158,11,.25);}
.badge-blue{background:rgba(99,102,241,.15);color:#a5b4fc;border:1px solid rgba(99,102,241,.25);}
.badge-green{background:rgba(16,185,129,.15);color:#34d399;border:1px solid rgba(16,185,129,.25);}
.badge-gray{background:rgba(100,116,139,.15);color:#94a3b8;border:1px solid rgba(100,116,139,.2);}
</style>
</head>
<body class="min-h-screen text-white p-4 md:p-6">
@php $sm=$ticket->getStatusMeta();$pm=$ticket->getPriorityMeta();$cm=$ticket->getCategoryMeta(); @endphp
<div class="max-w-3xl mx-auto space-y-5">

  <div class="flex items-center gap-3 flex-wrap">
    <a href="{{ route('staff.tickets.index') }}" class="text-xs font-bold text-slate-400 hover:text-white">
      <i class="fa-solid fa-arrow-left"></i> Tickets
    </a>
    <span class="text-slate-600">·</span>
    <span class="badge {{ $sm['badge'] }}">{{ $sm['label'] }}</span>
    <span class="text-[10px] font-bold" style="color:{{ $pm['color'] }}">{{ $pm['label'] }}</span>

    <button onclick="openTransferModal()" class="ml-auto px-3 py-1.5 rounded-xl bg-amber-500/20 text-amber-300 text-xs font-bold border border-amber-500/30 hover:bg-amber-500/30 transition">
      <i class="fa-solid fa-arrow-right-arrow-left"></i> Transfer Ticket
    </button>
  </div>

  {{-- Transfer Ticket Modal --}}
  <div id="transfer-ticket-backdrop" class="fixed inset-0 bg-slate-900/70 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="glass rounded-3xl max-w-md w-full p-6 shadow-2xl border border-white/10 relative">
      <button onclick="closeTransferModal()" class="absolute top-5 right-5 text-slate-400 hover:text-white w-8 h-8 rounded-full flex items-center justify-center bg-white/5">
        <i class="fa-solid fa-xmark text-sm"></i>
      </button>

      <div class="flex items-center gap-3 mb-4">
        <div class="w-10 h-10 rounded-2xl bg-amber-500/20 text-amber-400 flex items-center justify-center font-bold text-lg border border-amber-500/30">
          <i class="fa-solid fa-arrow-right-arrow-left"></i>
        </div>
        <div>
          <h3 class="font-extrabold text-white text-base font-outfit">Transfer Ticket #{{ $ticket->id }}</h3>
          <p class="text-slate-400 text-[10px]">Reassign ticket to another staff member</p>
        </div>
      </div>

      <form method="POST" action="{{ route('staff.tickets.transfer', $ticket) }}" class="space-y-4">
        @csrf
        <div>
          <label class="block text-xs font-bold text-slate-300 mb-1">Select Target Staff / Admin</label>
          <select name="target_user_id" required class="inp w-full text-xs">
            <option value="">-- Choose Staff Member --</option>
            @php $staffList = \App\Models\User::whereIn('role', ['admin', 'staff'])->where('is_blocked', false)->get(); @endphp
            @foreach($staffList as $st)
              <option value="{{ $st->id }}">{{ $st->name }} ({{ ucfirst($st->role) }} - {{ $st->position ?? 'Staff' }})</option>
            @endforeach
          </select>
        </div>

        <div>
          <label class="block text-xs font-bold text-slate-300 mb-1">Transfer Note / Instructions</label>
          <textarea name="transfer_note" rows="3" placeholder="Reason for transferring ticket..."
                    class="inp w-full text-xs"></textarea>
        </div>

        <div class="flex justify-end gap-2 pt-2">
          <button type="button" onclick="closeTransferModal()" class="px-4 py-2 rounded-xl text-xs font-bold text-slate-400 bg-white/5 hover:bg-white/10">
            Cancel
          </button>
          <button type="submit" class="px-4 py-2 rounded-xl text-xs font-extrabold text-white bg-amber-600 hover:bg-amber-500 shadow-md">
            <i class="fa-solid fa-paper-plane mr-1"></i> Transfer Ticket
          </button>
        </div>
      </form>
    </div>
  </div>

  <script>
    function openTransferModal() { document.getElementById('transfer-ticket-backdrop').classList.remove('hidden'); }
    function closeTransferModal() { document.getElementById('transfer-ticket-backdrop').classList.add('hidden'); }
  </script>

  @if(session('success'))
  <div class="p-3.5 glass rounded-2xl text-emerald-300 text-xs font-semibold flex items-center gap-2" style="border-color:rgba(16,185,129,.25)">
    <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
  </div>
  @endif

  {{-- Original Message --}}
  <div class="glass rounded-2xl overflow-hidden">
    <div class="px-6 py-4 border-b border-white/[0.06]" style="background:{{ $cm['color'] }}18">
      <div class="flex items-start gap-3">
        <div class="w-10 h-10 rounded-xl flex items-center justify-center text-white text-sm flex-shrink-0" style="background:{{ $cm['color'] }}">
          <i class="fa-solid {{ $cm['icon'] }}"></i>
        </div>
        <div>
          <h2 class="font-extrabold text-white text-base">{{ $ticket->subject }}</h2>
          <p class="text-slate-400 text-[10px] mt-1">{{ $ticket->user->name }} · {{ $ticket->user->email }}</p>
        </div>
      </div>
    </div>
    <div class="px-6 py-5">
      <p class="text-slate-300 text-sm leading-relaxed whitespace-pre-wrap">{{ $ticket->message }}</p>
    </div>
  </div>

  {{-- Reply Thread --}}
  @if($ticket->replies->isNotEmpty())
  <div class="space-y-3">
    @foreach($ticket->replies as $r)
    <div class="glass rounded-2xl p-4 {{ $r->is_admin ? 'ml-4' : 'mr-4' }}"
         style="{{ $r->is_admin ? 'border-color:rgba(99,102,241,.25);background:rgba(99,102,241,.07)' : '' }}">
      <div class="flex items-center gap-2 mb-2.5">
        <div class="w-7 h-7 rounded-xl flex items-center justify-center text-[10px] font-black
            {{ $r->is_admin ? 'bg-gradient-to-br from-indigo-500 to-purple-600 text-white' : 'bg-slate-700 text-slate-300' }}">
          {{ $r->is_admin ? 'S' : strtoupper(substr($r->user->name??'U',0,1)) }}
        </div>
        <div class="flex-1">
          <span class="text-white text-xs font-bold">{{ $r->is_admin ? 'Staff ('.$r->user->name.')' : $r->user->name }}</span>
          @if($r->is_admin)<span class="ml-1 badge badge-blue">Staff Reply</span>@endif
          @if($r->mail_sent)<span class="ml-1 text-[9px] text-emerald-400"><i class="fa-solid fa-envelope mr-0.5"></i>Emailed</span>@endif
        </div>
        <span class="text-[10px] text-slate-500">{{ $r->created_at->diffForHumans() }}</span>
      </div>
      <p class="text-slate-300 text-sm leading-relaxed whitespace-pre-wrap">{{ $r->message }}</p>
    </div>
    @endforeach
  </div>
  @endif

  {{-- Reply Form --}}
  <div class="glass rounded-2xl p-5">
    <h4 class="font-extrabold text-white text-sm font-outfit mb-4 flex items-center gap-2">
      <i class="fa-solid fa-reply text-indigo-400"></i> Reply to User
    </h4>
    <form method="POST" action="{{ route('staff.tickets.reply',$ticket) }}" class="space-y-4">
      @csrf
      <textarea name="message" rows="4" required maxlength="5000"
                placeholder="Write your reply here…" class="inp">{{ old('message') }}</textarea>
      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="block text-[9px] font-black text-slate-500 uppercase tracking-wider mb-1.5">Update Status</label>
          <select name="new_status" class="inp text-sm">
            @foreach(\App\Models\SupportTicket::$statusMeta as $k=>$v)
            <option value="{{ $k }}" {{ $ticket->status===$k?'selected':'' }}>{{ $v['label'] }}</option>
            @endforeach
          </select>
        </div>
        <div class="flex items-end">
          <label class="flex items-center gap-2.5 cursor-pointer p-3 glass rounded-xl w-full hover:bg-white/[0.04] transition">
            <input type="checkbox" name="send_email" value="1" checked class="w-3.5 h-3.5 rounded accent-indigo-500">
            <div>
              <p class="text-white text-xs font-bold">Email User</p>
              <p class="text-[9px] text-slate-500">Notify via email</p>
            </div>
          </label>
        </div>
      </div>
      <button type="submit"
              class="w-full py-3 rounded-2xl font-extrabold text-sm text-white transition hover:-translate-y-0.5"
              style="background:linear-gradient(135deg,#6366f1,#7c3aed)">
        <i class="fa-solid fa-paper-plane mr-2"></i> Send Reply
      </button>
    </form>
  </div>

</div>
@include('partials.global-loader')
</body>
</html>
