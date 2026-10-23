@extends('layouts.app')

@section('title', 'Progress Tracker & Learning Analytics')
@section('page-title', 'Progress Tracker')

@section('content')
<div class="space-y-6">

    {{-- Top Hero Header --}}
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-emerald-600 via-teal-600 to-emerald-800 p-6 lg:p-8 text-white shadow-xl">
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="space-y-2">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 backdrop-blur-md border border-white/20 text-[11px] font-bold uppercase tracking-wider text-emerald-100">
                    <span class="w-2 h-2 rounded-full bg-emerald-300 animate-pulse"></span>
                    Real-time Learning Analytics
                </div>
                <h1 class="text-2xl lg:text-3xl font-extrabold font-outfit">Your Learning Journey, {{ $user->name }}</h1>
                <p class="text-emerald-100/90 text-xs sm:text-sm max-w-xl">
                    Track your course progress, assignment scores, learning streak, and official certificates in one place.
                </p>
            </div>
            <div class="flex items-center gap-3 flex-wrap">
                <a href="{{ route('certificates.demo') }}"
                   class="px-4 py-2.5 rounded-2xl bg-white text-emerald-800 hover:bg-emerald-50 font-bold text-xs shadow-lg transition flex items-center gap-2">
                    <i class="fa-solid fa-award text-amber-500"></i> Demo Certificates
                </a>
                <a href="{{ route('courses.public') }}"
                   class="px-4 py-2.5 rounded-2xl bg-white/10 hover:bg-white/20 border border-white/20 text-white font-bold text-xs backdrop-blur-md transition flex items-center gap-2">
                    <i class="fa-solid fa-compass"></i> Explore Courses
                </a>
            </div>
        </div>
    </div>

    {{-- Key Stat Cards Grid --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">

        <div class="p-5 rounded-3xl bg-white border border-slate-200/80 shadow-sm space-y-2 card-lift">
            <div class="flex items-center justify-between">
                <span class="text-slate-400 text-[11px] font-bold uppercase tracking-wider">Total Enrolled</span>
                <div class="w-9 h-9 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-sm">
                    <i class="fa-solid fa-book-open"></i>
                </div>
            </div>
            <p class="text-2xl font-black text-slate-900 font-outfit">{{ $totalEnrolled }}</p>
            <p class="text-slate-500 text-[11px]">Active enrolled courses</p>
        </div>

        <div class="p-5 rounded-3xl bg-white border border-slate-200/80 shadow-sm space-y-2 card-lift">
            <div class="flex items-center justify-between">
                <span class="text-slate-400 text-[11px] font-bold uppercase tracking-wider">Completed</span>
                <div class="w-9 h-9 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-sm">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
            </div>
            <p class="text-2xl font-black text-slate-900 font-outfit">{{ $completedCourses }}</p>
            <p class="text-slate-500 text-[11px]">Courses 100% completed</p>
        </div>

        <div class="p-5 rounded-3xl bg-white border border-slate-200/80 shadow-sm space-y-2 card-lift">
            <div class="flex items-center justify-between">
                <span class="text-slate-400 text-[11px] font-bold uppercase tracking-wider">Hours Learned</span>
                <div class="w-9 h-9 rounded-2xl bg-teal-50 text-teal-600 flex items-center justify-center font-bold text-sm">
                    <i class="fa-solid fa-clock"></i>
                </div>
            </div>
            <p class="text-2xl font-black text-slate-900 font-outfit">{{ $earnedHours }} <span class="text-xs text-slate-400 font-normal">/ {{ $totalHours }} hrs</span></p>
            <p class="text-slate-500 text-[11px]">Based on course completion</p>
        </div>

        <div class="p-5 rounded-3xl bg-white border border-slate-200/80 shadow-sm space-y-2 card-lift">
            <div class="flex items-center justify-between">
                <span class="text-slate-400 text-[11px] font-bold uppercase tracking-wider">Points Earned</span>
                <div class="w-9 h-9 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold text-sm">
                    <i class="fa-solid fa-star"></i>
                </div>
            </div>
            <p class="text-2xl font-black text-slate-900 font-outfit">{{ $totalPointsEarned }}</p>
            <p class="text-slate-500 text-[11px]">From graded assignments</p>
        </div>

    </div>

    {{-- Course Progress Breakdown --}}
    <div class="p-6 rounded-3xl bg-white border border-slate-200/80 shadow-sm space-y-5">
        <div class="flex items-center justify-between border-b border-slate-100 pb-4">
            <div>
                <h2 class="font-extrabold text-slate-900 text-base font-outfit">Enrolled Courses Progress</h2>
                <p class="text-slate-500 text-xs mt-0.5">Real-time status calculated from evaluated assignments</p>
            </div>
            <a href="{{ route('courses.mine') }}" class="text-xs font-bold text-emerald-600 hover:text-emerald-700 flex items-center gap-1">
                View All <i class="fa-solid fa-arrow-right text-[10px]"></i>
            </a>
        </div>

        <div class="space-y-4">
            @forelse($courses as $course)
                @php $prog = $course->pivot->progress ?? 0; @endphp
                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/60 hover:border-emerald-200 transition space-y-3">
                    <div class="flex items-center justify-between gap-4">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center text-white font-bold text-sm flex-shrink-0">
                                <i class="fa-solid fa-graduation-cap"></i>
                            </div>
                            <div class="min-w-0">
                                <h3 class="font-bold text-slate-900 text-xs sm:text-sm truncate">{{ $course->title }}</h3>
                                <p class="text-slate-400 text-[10px]">Instructor: {{ $course->instructor_name }} &bull; {{ $course->duration_hours }} Hours</p>
                            </div>
                        </div>

                        <div class="text-right flex-shrink-0">
                            <span class="font-black text-sm font-outfit {{ $prog >= 100 ? 'text-emerald-600' : 'text-slate-700' }}">{{ $prog }}%</span>
                            @if($prog >= 100)
                                <a href="{{ route('certificates.download', $course) }}" class="block text-[10px] font-extrabold text-emerald-600 hover:underline mt-0.5">
                                    <i class="fa-solid fa-award"></i> Certificate Ready
                                </a>
                            @endif
                        </div>
                    </div>

                    {{-- Progress Bar --}}
                    <div class="w-full bg-slate-200 rounded-full h-2 overflow-hidden">
                        <div class="h-full bg-gradient-to-r from-emerald-500 to-teal-500 rounded-full transition-all duration-500" style="width: {{ $prog }}%"></div>
                    </div>
                </div>
            @empty
                <div class="py-12 text-center text-slate-400">
                    <i class="fa-solid fa-book-open text-3xl mb-2 text-slate-300"></i>
                    <p class="text-xs font-semibold">You are not enrolled in any courses yet.</p>
                    <a href="{{ route('courses.public') }}" class="mt-3 inline-block px-4 py-2 rounded-xl bg-emerald-600 text-white font-bold text-xs hover:bg-emerald-700 transition">
                        Browse Course Catalog
                    </a>
                </div>
            @endforelse
        </div>
    </div>

    {{-- Milestones & Achievements Grid --}}
    <div class="p-6 rounded-3xl bg-white border border-slate-200/80 shadow-sm space-y-5">
        <div>
            <h2 class="font-extrabold text-slate-900 text-base font-outfit">Milestones & Achievements</h2>
            <p class="text-slate-500 text-xs mt-0.5">Unlock badges as you complete tasks and earn certificates</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
            @foreach($badges as $b)
                <div class="p-4 rounded-2xl border transition flex items-center gap-3.5 {{ $b['unlocked'] ? 'bg-emerald-50/60 border-emerald-200 text-slate-900' : 'bg-slate-50 border-slate-200/60 text-slate-400 opacity-60' }}">
                    <div class="w-11 h-11 rounded-2xl flex items-center justify-center font-bold text-base flex-shrink-0 shadow-sm {{ $b['unlocked'] ? 'bg-emerald-600 text-white' : 'bg-slate-300 text-slate-500' }}">
                        <i class="fa-solid {{ $b['icon'] }}"></i>
                    </div>
                    <div>
                        <h4 class="font-extrabold text-xs font-outfit leading-tight flex items-center gap-1.5">
                            {{ $b['title'] }}
                            @if($b['unlocked'])
                                <i class="fa-solid fa-circle-check text-emerald-600 text-[11px]"></i>
                            @endif
                        </h4>
                        <p class="text-[10px] text-slate-500 mt-0.5 leading-snug">{{ $b['desc'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

</div>
@endsection
