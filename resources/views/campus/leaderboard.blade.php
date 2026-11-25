@extends('campus.layout')
@section('title','Leaderboard')
@section('page-title','Leaderboard 🏆')

@section('content')
<div class="space-y-5">
    <div class="glass-card p-4 flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-amber-100 flex items-center justify-center"><i class="fa-solid fa-trophy text-amber-500 text-lg"></i></div>
        <div>
            <p class="text-sm font-extrabold text-slate-900 dark:text-white">Campus Leaderboard</p>
            <p class="text-xs text-slate-500">Ranked by completed courses across all campus students</p>
        </div>
    </div>

    {{-- Top 3 --}}
    @if($leaders->count() >= 3)
    <div class="grid grid-cols-3 gap-4">
        @foreach([$leaders->get(1), $leaders->get(0), $leaders->get(2)] as $i => $l)
        @if($l)
        @php $rank = $i === 0 ? 2 : ($i === 1 ? 1 : 3); $heights = ['h-28','h-36','h-24']; $colors = ['from-slate-400 to-slate-500','from-amber-400 to-yellow-500','from-orange-400 to-amber-500']; @endphp
        <div class="flex flex-col items-center">
            <div class="w-14 h-14 rounded-2xl bg-gradient-to-br {{ $colors[$i] }} flex items-center justify-center text-white text-xl font-black shadow-lg {{ $l->id === $student->id ? 'ring-4 ring-emerald-400' : '' }}">
                {{ strtoupper(substr($l->name, 0, 1)) }}
            </div>
            <p class="text-xs font-black text-slate-900 dark:text-white mt-2 text-center leading-tight">{{ Str::limit($l->name, 12) }}</p>
            <p class="text-[10px] text-slate-500 text-center">{{ $l->completed_count }} completed</p>
            <div class="{{ $heights[$i] }} w-full mt-2 rounded-t-2xl bg-gradient-to-t {{ $colors[$i] }} flex items-end justify-center pb-2">
                <span class="text-white font-black text-lg">{{ $rank }}</span>
            </div>
        </div>
        @endif
        @endforeach
    </div>
    @endif

    {{-- Full list --}}
    <div class="glass-card overflow-hidden">
        <table class="w-full">
            <thead>
                <tr class="bg-emerald-50 dark:bg-emerald-900/30">
                    <th class="text-left p-4 text-xs font-black text-emerald-700 uppercase tracking-wider">Rank</th>
                    <th class="text-left p-4 text-xs font-black text-emerald-700 uppercase tracking-wider">Student</th>
                    <th class="text-left p-4 text-xs font-black text-emerald-700 uppercase tracking-wider hidden sm:table-cell">College</th>
                    <th class="text-center p-4 text-xs font-black text-emerald-700 uppercase tracking-wider">Completed</th>
                    <th class="text-center p-4 text-xs font-black text-emerald-700 uppercase tracking-wider hidden md:table-cell">Enrolled</th>
                </tr>
            </thead>
            <tbody>
                @foreach($leaders as $idx => $l)
                <tr class="border-t border-slate-100 dark:border-slate-700/50 {{ $l->id === $student->id ? 'bg-emerald-50/60 dark:bg-emerald-900/20' : 'hover:bg-slate-50 dark:hover:bg-slate-800/30' }} transition">
                    <td class="p-4">
                        <span class="w-7 h-7 rounded-lg flex items-center justify-center text-xs font-black
                            {{ $idx === 0 ? 'bg-amber-100 text-amber-600' : ($idx === 1 ? 'bg-slate-100 text-slate-600' : ($idx === 2 ? 'bg-orange-100 text-orange-600' : 'bg-emerald-50 text-emerald-600')) }}">
                            {{ $idx + 1 }}
                        </span>
                    </td>
                    <td class="p-4">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-emerald-400 to-teal-500 flex items-center justify-center text-white text-sm font-black flex-shrink-0">
                                {{ strtoupper(substr($l->name, 0, 1)) }}
                            </div>
                            <div>
                                <p class="text-xs font-bold text-slate-900 dark:text-white">
                                    {{ $l->name }}
                                    @if($l->id === $student->id)<span class="ml-1 text-emerald-600 text-[9px] font-black">(You)</span>@endif
                                </p>
                                @if($l->department)<p class="text-[10px] text-slate-500">{{ $l->department }}</p>@endif
                            </div>
                        </div>
                    </td>
                    <td class="p-4 hidden sm:table-cell">
                        <span class="text-xs text-slate-600 dark:text-slate-300">{{ Str::limit($l->college_name ?? '-', 25) }}</span>
                    </td>
                    <td class="p-4 text-center">
                        <span class="px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-700 text-xs font-black">{{ $l->completed_count }}</span>
                    </td>
                    <td class="p-4 text-center hidden md:table-cell">
                        <span class="text-xs text-slate-500 font-semibold">{{ $l->total_enrollments }}</span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @if($leaders->isEmpty())
        <div class="text-center py-12">
            <i class="fa-solid fa-trophy text-4xl text-slate-200 mb-3"></i>
            <p class="text-sm font-bold text-slate-500">No data yet. Be the first!</p>
        </div>
        @endif
    </div>
</div>
@endsection
