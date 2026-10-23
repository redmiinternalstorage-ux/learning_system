<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Tickets — Staff Portal</title>
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&family=Outfit:wght@600;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<script>tailwind.config={darkMode:'class',theme:{extend:{fontFamily:{sans:['"Plus Jakarta Sans"'],outfit:['"Outfit"']}}}}</script>
<style>
body{font-family:'Plus Jakarta Sans',sans-serif;background:linear-gradient(160deg,#060018,#040012,#020008);}
.font-outfit{font-family:'Outfit',sans-serif;}
.glass{background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.09);}
.inp{width:100%;padding:.6rem .875rem;border-radius:.75rem;border:1px solid rgba(255,255,255,.1);background:rgba(255,255,255,.05);color:#f1f5f9;font-size:.8rem;outline:none;transition:all .2s;}
.inp:focus{border-color:#6366f1;box-shadow:0 0 0 3px rgba(99,102,241,.15);}
.inp::placeholder{color:rgba(148,163,184,.5);}
select.inp option{background:#1e293b;}
.badge{display:inline-flex;align-items:center;gap:.25rem;padding:.15rem .55rem;border-radius:9999px;font-size:.65rem;font-weight:700;text-transform:uppercase;}
.badge-amber{background:rgba(245,158,11,.15);color:#fbbf24;border:1px solid rgba(245,158,11,.25);}
.badge-blue{background:rgba(99,102,241,.15);color:#a5b4fc;border:1px solid rgba(99,102,241,.25);}
.badge-green{background:rgba(16,185,129,.15);color:#34d399;border:1px solid rgba(16,185,129,.25);}
.badge-gray{background:rgba(100,116,139,.15);color:#94a3b8;border:1px solid rgba(100,116,139,.2);}
.badge-rose{background:rgba(225,29,72,.15);color:#fb7185;border:1px solid rgba(225,29,72,.25);}
</style>
</head>
<body class="min-h-screen text-white p-4 md:p-6">
<div class="max-w-5xl mx-auto space-y-5">

  <div class="flex items-center gap-3">
    <a href="{{ route('staff.dashboard') }}" class="flex items-center gap-2 text-xs font-bold text-slate-400 hover:text-white transition">
      <i class="fa-solid fa-arrow-left"></i> Dashboard
    </a>
    <h1 class="font-extrabold text-white text-lg font-outfit ml-2">Support Tickets</h1>
  </div>

  @if(session('success'))
  <div class="p-3.5 glass rounded-2xl text-emerald-300 text-xs font-semibold flex items-center gap-2" style="border-color:rgba(16,185,129,.25)">
    <i class="fa-solid fa-circle-check text-emerald-400"></i> {{ session('success') }}
  </div>
  @endif

  {{-- Stats --}}
  <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
    @foreach([
      ['Open',$stats['open'],'badge-amber','fa-circle-dot'],
      ['In Progress',$stats['in_progress'],'badge-blue','fa-spinner'],
      ['Resolved',$stats['resolved'],'badge-green','fa-circle-check'],
      ['Unread',$stats['unread'],'badge-rose','fa-bell'],
    ] as [$label,$val,$badge,$icon])
    <div class="glass rounded-2xl p-4 text-center">
      <p class="text-xl font-black text-white font-outfit">{{ $val }}</p>
      <span class="badge {{ $badge }} mt-1"><i class="fa-solid {{ $icon }} text-[8px]"></i> {{ $label }}</span>
    </div>
    @endforeach
  </div>

  {{-- Filter --}}
  <form method="GET" class="flex gap-3 flex-wrap">
    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search tickets…" class="inp flex-1 min-w-40">
    <select name="status" onchange="this.form.submit()" class="inp w-36">
      <option value="">All Status</option>
      @foreach(\App\Models\SupportTicket::$statusMeta as $k=>$v)
      <option value="{{ $k }}" {{ request('status')===$k?'selected':'' }}>{{ $v['label'] }}</option>
      @endforeach
    </select>
    <button type="submit" class="px-4 py-2 rounded-xl text-xs font-bold text-white" style="background:linear-gradient(135deg,#6366f1,#7c3aed)">
      <i class="fa-solid fa-magnifying-glass"></i>
    </button>
  </form>

  {{-- Table --}}
  <div class="glass rounded-2xl overflow-hidden">
    @if($tickets->isEmpty())
    <div class="py-14 text-center text-slate-500 text-sm">
      <i class="fa-solid fa-ticket text-2xl opacity-30 mb-2 block"></i> No tickets found
    </div>
    @else
    <div class="overflow-x-auto">
      <table class="w-full">
        <thead class="border-b border-white/[0.07]">
          <tr>
            @foreach(['#','User','Subject','Category','Status','Date','Action'] as $h)
            <th class="px-4 py-3 text-left text-[9px] font-black text-slate-500 uppercase tracking-wider">{{ $h }}</th>
            @endforeach
          </tr>
        </thead>
        <tbody>
          @foreach($tickets as $t)
          @php $sm=$t->getStatusMeta();$cm=$t->getCategoryMeta(); @endphp
          <tr class="border-b border-white/[0.03] last:border-0 hover:bg-white/[0.02] transition {{ !$t->admin_read?'bg-indigo-950/20':'' }}">
            <td class="px-4 py-3 text-xs text-slate-500">
              #{{ $t->id }}
              @if(!$t->admin_read)<span class="ml-1 inline-block w-1.5 h-1.5 rounded-full bg-rose-500 align-middle"></span>@endif
            </td>
            <td class="px-4 py-3">
              <p class="text-xs font-bold text-white">{{ $t->user->name??'—' }}</p>
              <p class="text-[9px] text-slate-500">{{ $t->user->email??'' }}</p>
            </td>
            <td class="px-4 py-3 text-xs text-slate-300 max-w-[180px] truncate">{{ $t->subject }}</td>
            <td class="px-4 py-3 text-[10px] text-slate-400">
              <i class="fa-solid {{ $cm['icon'] }} mr-1" style="color:{{ $cm['color'] }}"></i>{{ $cm['label'] }}
            </td>
            <td class="px-4 py-3"><span class="badge {{ $sm['badge'] }}">{{ $sm['label'] }}</span></td>
            <td class="px-4 py-3 text-[10px] text-slate-500 whitespace-nowrap">{{ $t->created_at->format('d M y') }}</td>
            <td class="px-4 py-3">
              <a href="{{ route('staff.tickets.show',$t) }}"
                 class="px-3 py-1.5 rounded-xl text-[10px] font-bold text-white"
                 style="background:linear-gradient(135deg,#6366f1,#7c3aed)">
                <i class="fa-solid fa-eye"></i> View
              </a>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
    @if($tickets->hasPages())
    <div class="px-5 py-3 border-t border-white/[0.06]">{{ $tickets->links() }}</div>
    @endif
    @endif
  </div>

</div>
@include('partials.global-loader')
</body>
</html>
