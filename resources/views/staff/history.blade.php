<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Work History — Staff Portal</title>
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&family=Outfit:wght@600;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<script>tailwind.config={darkMode:'class',theme:{extend:{fontFamily:{sans:['"Plus Jakarta Sans"'],outfit:['"Outfit"']}}}}</script>
<style>
body{font-family:'Plus Jakarta Sans',sans-serif;background:linear-gradient(160deg,#060018,#040012,#020008);}
.glass{background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.09);}
</style>
</head>
<body class="min-h-screen text-white p-4 md:p-6">
<div class="max-w-4xl mx-auto space-y-5">

  <div class="flex items-center gap-3">
    <a href="{{ route('staff.dashboard') }}" class="text-xs font-bold text-slate-400 hover:text-white">
      <i class="fa-solid fa-arrow-left"></i> Dashboard
    </a>
    <h1 class="font-extrabold text-white text-lg font-outfit ml-2">Work History</h1>
  </div>

  {{-- Summary --}}
  @php
    $totalH = round($logs->sum('duration_seconds')/3600,2);
    $totalE = round($totalH*1000,0);
  @endphp
  <div class="glass rounded-2xl p-5 flex gap-6 flex-wrap" style="background:linear-gradient(135deg,rgba(99,102,241,.1),rgba(124,58,237,.06));">
    <div>
      <p class="text-2xl font-black text-indigo-400 font-outfit">{{ $totalH }}h</p>
      <p class="text-slate-500 text-[9px] uppercase tracking-wider font-bold">Total Hours (shown)</p>
    </div>
    <div>
      <p class="text-2xl font-black text-amber-400 font-outfit">₹{{ number_format($totalE) }}</p>
      <p class="text-slate-500 text-[9px] uppercase tracking-wider font-bold">Total Earned</p>
    </div>
    <div class="ml-auto text-right">
      <p class="text-white text-xs font-semibold">Rate: <span class="text-amber-400 font-black">₹1,000/hour</span></p>
      <p class="text-slate-500 text-[9px] mt-0.5">{{ $logs->total() }} sessions total</p>
    </div>
  </div>

  <div class="glass rounded-2xl overflow-hidden">
    <div class="overflow-x-auto">
      <table class="w-full">
        <thead class="border-b border-white/[0.07]">
          <tr>
            @foreach(['Date','Clock In','Clock Out','Duration','Earned'] as $h)
            <th class="px-5 py-3 text-left text-[9px] font-black text-slate-500 uppercase tracking-wider">{{ $h }}</th>
            @endforeach
          </tr>
        </thead>
        <tbody>
          @forelse($logs as $l)
          <tr class="border-b border-white/[0.03] last:border-0 hover:bg-white/[0.02] transition">
            <td class="px-5 py-3 text-xs text-slate-300">{{ $l->work_date->format('d M Y') }}</td>
            <td class="px-5 py-3 text-xs text-slate-400">{{ $l->clock_in?->format('h:i A')??'—' }}</td>
            <td class="px-5 py-3 text-xs text-slate-400">{{ $l->clock_out?->format('h:i A')??'—' }}</td>
            <td class="px-5 py-3"><span class="text-xs font-bold text-sky-400">{{ $l->duration_formatted }}</span></td>
            <td class="px-5 py-3"><span class="text-xs font-black text-amber-400">₹{{ number_format(round($l->duration_hours*1000)) }}</span></td>
          </tr>
          @empty
          <tr><td colspan="5" class="py-12 text-center text-slate-500 text-sm">No work sessions yet.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    @if($logs->hasPages())
    <div class="px-5 py-3 border-t border-white/[0.06]">{{ $logs->links() }}</div>
    @endif
  </div>

</div>
@include('partials.global-loader')
</body>
</html>
