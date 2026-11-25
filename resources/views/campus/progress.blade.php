@extends('campus.layout')
@section('title','My Progress')
@section('page-title','My Learning Progress 📊')

@section('content')
<div class="space-y-5">
    @php
        $completed  = $enrollments->where('progress', 100)->count();
        $inProgress = $enrollments->where('progress', '>', 0)->where('progress', '<', 100)->count();
        $notStarted = $enrollments->where('progress', 0)->count();
        $avgProgress= $enrollments->count() ? round($enrollments->avg('progress')) : 0;
    @endphp

    {{-- Stats --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        @foreach([
            ['Avg Progress', $avgProgress.'%', 'fa-chart-line', 'emerald'],
            ['Completed', $completed, 'fa-circle-check', 'teal'],
            ['In Progress', $inProgress, 'fa-spinner', 'blue'],
            ['Not Started', $notStarted, 'fa-hourglass', 'amber'],
        ] as [$label, $val, $icon, $color])
        <div class="glass-card p-4 text-center">
            <div class="w-10 h-10 rounded-xl bg-{{ $color }}-100 flex items-center justify-center mx-auto mb-2">
                <i class="fa-solid {{ $icon }} text-{{ $color }}-600"></i>
            </div>
            <p class="text-2xl font-black text-slate-900 dark:text-white font-outfit">{{ $val }}</p>
            <p class="text-xs font-bold text-slate-500 mt-0.5">{{ $label }}</p>
        </div>
        @endforeach
    </div>

    {{-- Course list with progress --}}
    <div class="glass-card p-5">
        <h3 class="font-extrabold text-slate-900 dark:text-white text-sm mb-4">All Enrolled Courses</h3>
        @forelse($enrollments as $e)
        <div class="mb-4 pb-4 border-b border-slate-100 dark:border-slate-700/50 last:border-0 last:mb-0 last:pb-0">
            <div class="flex items-center justify-between mb-2">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-emerald-400 to-teal-500 flex items-center justify-center text-white text-sm font-black flex-shrink-0">
                        {{ strtoupper(substr($e->course->title, 0, 1)) }}
                    </div>
                    <div>
                        <p class="text-xs font-bold text-slate-900 dark:text-white">{{ $e->course->title }}</p>
                        <p class="text-[10px] text-slate-500">{{ $e->course->category }} • Started {{ $e->created_at->diffForHumans() }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    @if($e->progress >= 100)
                    <span class="px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-700 text-[9px] font-black">✅ Completed</span>
                    @elseif($e->progress > 0)
                    <span class="px-2 py-0.5 rounded-full bg-blue-100 text-blue-700 text-[9px] font-black">🔄 In Progress</span>
                    @else
                    <span class="px-2 py-0.5 rounded-full bg-slate-100 text-slate-600 text-[9px] font-black">⏳ Not Started</span>
                    @endif
                    <span class="text-sm font-black text-emerald-600 font-outfit">{{ $e->progress }}%</span>
                </div>
            </div>
            <div class="w-full bg-slate-100 dark:bg-slate-700 rounded-full h-2">
                <div class="bg-gradient-to-r from-emerald-500 to-teal-500 rounded-full h-2 transition-all" style="width:{{ $e->progress }}%"></div>
            </div>
            @if($e->completed_at)
            <p class="text-[10px] text-slate-400 mt-1">Completed {{ $e->completed_at->format('d M Y') }}</p>
            @endif
        </div>
        @empty
        <div class="text-center py-10">
            <i class="fa-solid fa-chart-line text-4xl text-slate-200 mb-3"></i>
            <p class="text-sm font-bold text-slate-500">No courses enrolled yet</p>
            <a href="{{ route('campus.courses') }}" class="btn-green mt-3 inline-flex text-xs">Browse Courses</a>
        </div>
        @endforelse
    </div>
</div>
@endsection
