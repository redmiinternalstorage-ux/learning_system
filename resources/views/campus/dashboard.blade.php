@extends('campus.layout')
@section('title','Dashboard')
@section('page-title','Dashboard 🎓')

@section('content')
<div class="space-y-6">

    {{-- Welcome banner --}}
    <div class="rounded-3xl bg-gradient-to-r from-emerald-600 via-teal-600 to-emerald-700 p-6 text-white relative overflow-hidden">
        <div class="absolute top-0 right-0 w-64 h-64 bg-white/5 rounded-full -translate-y-16 translate-x-16"></div>
        <div class="absolute bottom-0 left-0 w-48 h-48 bg-white/5 rounded-full translate-y-12 -translate-x-12"></div>
        <div class="relative z-10">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-emerald-200 text-sm font-semibold">Welcome back,</p>
                    <h2 class="text-2xl font-black mt-0.5">{{ $student->name }} 👋</h2>
                    <p class="text-emerald-200 text-sm mt-1">{{ $student->college_name }} @if($student->department)• {{ $student->department }}@endif</p>
                </div>
                <div class="w-14 h-14 rounded-2xl bg-white/15 backdrop-blur flex items-center justify-center text-2xl font-black">
                    {{ strtoupper(substr($student->name, 0, 1)) }}
                </div>
            </div>
            <div class="mt-4">
                <div class="flex items-center justify-between mb-1.5">
                    <span class="text-xs font-bold text-emerald-200">Overall Progress</span>
                    <span class="text-xs font-black text-white">{{ $totalProgress }}%</span>
                </div>
                <div class="w-full bg-white/20 rounded-full h-2.5">
                    <div class="bg-white rounded-full h-2.5 transition-all" style="width:{{ $totalProgress }}%"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        @php
        $statCards = [
            ['label'=>'Enrolled','value'=>$totalCourses,'icon'=>'fa-book-open','color'=>'emerald'],
            ['label'=>'Completed','value'=>$completed,'icon'=>'fa-circle-check','color'=>'teal'],
            ['label'=>'In Progress','value'=>$inProgress,'icon'=>'fa-spinner','color'=>'blue'],
            ['label'=>'Available','value'=>\App\Models\Course::where('is_published',true)->count(),'icon'=>'fa-compass','color'=>'purple'],
        ];
        @endphp
        @foreach($statCards as $s)
        <div class="glass-card p-4 hover:scale-105 transition-transform cursor-default">
            <div class="flex items-center justify-between mb-3">
                <div class="w-9 h-9 rounded-xl bg-{{ $s['color'] }}-100 flex items-center justify-center">
                    <i class="fa-solid {{ $s['icon'] }} text-{{ $s['color'] }}-600 text-sm"></i>
                </div>
                <span class="text-2xl font-black text-slate-900 dark:text-white font-outfit">{{ $s['value'] }}</span>
            </div>
            <p class="text-xs font-bold text-slate-500 dark:text-slate-400">{{ $s['label'] }}</p>
        </div>
        @endforeach
    </div>

    <div class="grid lg:grid-cols-3 gap-6">
        {{-- My Courses --}}
        <div class="lg:col-span-2 glass-card p-5">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-extrabold text-slate-900 dark:text-white text-sm">My Enrolled Courses</h3>
                <a href="{{ route('campus.courses') }}" class="text-xs text-emerald-600 font-bold hover:text-emerald-700">View All →</a>
            </div>
            @forelse($enrollments->take(5) as $e)
            <a href="{{ route('campus.course-detail', $e->course) }}" class="flex items-center gap-4 p-3 rounded-2xl hover:bg-emerald-50 dark:hover:bg-emerald-900/20 transition group mb-2">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-emerald-400 to-teal-500 flex items-center justify-center text-white text-sm font-black flex-shrink-0">
                    {{ strtoupper(substr($e->course->title, 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-bold text-slate-900 dark:text-white truncate">{{ $e->course->title }}</p>
                    <p class="text-[10px] text-slate-500 mt-0.5">{{ $e->course->category }} • {{ $e->course->level }}</p>
                    <div class="flex items-center gap-2 mt-1.5">
                        <div class="flex-1 bg-slate-100 dark:bg-slate-700 rounded-full h-1.5">
                            <div class="bg-gradient-to-r from-emerald-500 to-teal-500 rounded-full h-1.5" style="width:{{ $e->progress }}%"></div>
                        </div>
                        <span class="text-[10px] font-black text-emerald-600">{{ $e->progress }}%</span>
                    </div>
                </div>
                @if($e->progress >= 100)
                <span class="px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-700 text-[9px] font-black flex-shrink-0">✅ Done</span>
                @endif
            </a>
            @empty
            <div class="text-center py-8">
                <div class="w-14 h-14 rounded-2xl bg-emerald-50 dark:bg-emerald-900/30 flex items-center justify-center mx-auto mb-3">
                    <i class="fa-solid fa-book-open text-emerald-400 text-2xl"></i>
                </div>
                <p class="text-sm font-bold text-slate-500">No courses enrolled yet</p>
                <a href="{{ route('campus.courses') }}" class="btn-green mt-3 text-xs inline-flex">Browse Courses</a>
            </div>
            @endforelse
        </div>

        {{-- Suggested courses --}}
        <div class="glass-card p-5">
            <h3 class="font-extrabold text-slate-900 dark:text-white text-sm mb-4">Suggested for You</h3>
            @forelse($availableCourses as $c)
            <div class="mb-3 p-3 rounded-xl bg-emerald-50/60 dark:bg-emerald-900/20 border border-emerald-100 dark:border-emerald-800/30">
                <p class="text-xs font-bold text-slate-900 dark:text-white leading-tight">{{ $c->title }}</p>
                <p class="text-[10px] text-slate-500 mt-0.5">{{ $c->category }} • {{ $c->level }}</p>
                <div class="flex items-center justify-between mt-2">
                    <span class="text-[9px] font-black text-emerald-700 bg-emerald-100 px-2 py-0.5 rounded-full">FREE</span>
                    <form method="POST" action="{{ route('campus.enroll', $c) }}">
                        @csrf
                        <button type="submit" class="text-[10px] font-black text-emerald-600 hover:text-emerald-800 transition">Enroll →</button>
                    </form>
                </div>
            </div>
            @empty
            <p class="text-xs text-slate-500 text-center py-4">All courses enrolled!</p>
            @endforelse
            <a href="{{ route('campus.courses') }}" class="btn-green w-full mt-2 text-xs">
                <i class="fa-solid fa-compass"></i> Browse All Courses
            </a>
        </div>
    </div>
</div>
@endsection
