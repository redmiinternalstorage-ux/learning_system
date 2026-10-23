@extends('layouts.app')

@section('title', 'Student Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div class="space-y-6">

    {{-- Welcome banner --}}
    <div class="relative bg-gradient-to-r from-emerald-600 to-teal-500 rounded-3xl overflow-hidden shadow-sm p-6 sm:p-8 text-white">
        <div class="absolute right-0 bottom-0 opacity-10 transform translate-y-12 translate-x-12 hidden md:block">
            <i class="fa-solid fa-graduation-cap text-[200px]"></i>
        </div>
        <div class="relative z-10 max-w-2xl">
            <span class="bg-white/20 text-white text-xs font-semibold px-3 py-1 rounded-full uppercase tracking-wider">
                @if($user->role === 'instructor') Instructor Console @else Learning Portal @endif
            </span>
            <h2 class="text-2xl sm:text-3xl font-black mt-3">Welcome back, {{ $user->name }}! 👋</h2>
            <p class="text-emerald-50 mt-1.5 text-sm sm:text-base font-medium">
                @if($user->role === 'instructor')
                    Manage your classes, announcements, and grade student submissions from your control panel.
                @else
                    Ready to learn something new today? Continue where you left off or explore new courses.
                @endif
            </p>
        </div>
    </div>

    {{-- Stats Grid --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        {{-- Enrolled Courses --}}
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm hover:shadow-md hover:border-emerald-200 transition duration-200 flex items-center gap-4 group">
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg group-hover:scale-110 transition duration-300">
                <i class="fa-solid fa-book-bookmark"></i>
            </div>
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Courses</p>
                <p class="text-xl font-extrabold text-slate-900 mt-0.5">{{ $stats['enrolled'] }}</p>
            </div>
        </div>

        {{-- Hours Learned --}}
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm hover:shadow-md hover:border-emerald-200 transition duration-200 flex items-center gap-4 group">
            <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-500 flex items-center justify-center text-lg group-hover:scale-110 transition duration-300">
                <i class="fa-solid fa-clock"></i>
            </div>
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Hours</p>
                <p class="text-xl font-extrabold text-slate-900 mt-0.5">{{ $stats['hours'] }}h</p>
            </div>
        </div>

        {{-- Completed --}}
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm hover:shadow-md hover:border-emerald-200 transition duration-200 flex items-center gap-4 group">
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg group-hover:scale-110 transition duration-300">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Completed</p>
                <p class="text-xl font-extrabold text-slate-900 mt-0.5">{{ $stats['completed'] }}</p>
            </div>
        </div>

        {{-- Certificates --}}
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm hover:shadow-md hover:border-emerald-200 transition duration-200 flex items-center gap-4 group">
            <div class="w-12 h-12 rounded-xl bg-rose-50 text-rose-500 flex items-center justify-center text-lg group-hover:scale-110 transition duration-300">
                <i class="fa-solid fa-award"></i>
            </div>
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Certificates</p>
                <p class="text-xl font-extrabold text-slate-900 mt-0.5">{{ $stats['certificates'] }}</p>
            </div>
        </div>
    </div>

    <div class="grid lg:grid-cols-3 gap-6">
        {{-- Enrolled Courses Panel --}}
        <div class="lg:col-span-2 space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="text-base font-extrabold text-gray-900 flex items-center gap-2">
                    <i class="fa-solid fa-play-circle text-emerald-600"></i>
                    @if($user->role === 'instructor') Assigned/Active Courses @else My Active Courses @endif
                </h3>
                @if($user->role !== 'instructor')
                <a href="{{ route('courses.public') }}" class="text-xs font-bold text-emerald-600 hover:text-emerald-700 flex items-center gap-1 transition">
                    Explore New <i class="fa-solid fa-arrow-right-long"></i>
                </a>
                @endif
            </div>

            @if($enrolledCourses->isEmpty())
                <div class="bg-white rounded-2xl border border-gray-200 p-8 text-center shadow-2xs">
                    <div class="w-16 h-16 rounded-2xl bg-gray-50 flex items-center justify-center mx-auto mb-4 text-gray-300">
                        <i class="fa-solid fa-book-open text-2xl"></i>
                    </div>
                    <p class="font-bold text-gray-800 text-sm">No courses found</p>
                    @if($user->role === 'instructor')
                        <p class="text-gray-400 text-xs mt-1">Go to the admin area to create and publish a new course.</p>
                        <a href="{{ route('admin.dashboard') }}" class="mt-4 inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold transition">
                            <i class="fa-solid fa-shield-halved"></i> Go to Admin Panel
                        </a>
                    @else
                        <p class="text-gray-400 text-xs mt-1">Enroll in a course to kickstart your learning journey.</p>
                        <a href="{{ route('courses.public') }}" class="mt-4 inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold transition">
                            <i class="fa-solid fa-compass"></i> Explore Courses
                        </a>
                    @endif
                </div>
            @else
                <div class="grid sm:grid-cols-2 gap-4">
                    @php
                    $catColors = [
                        'Web Development'    => 'from-blue-500 to-indigo-600',
                        'Data Science'       => 'from-green-500 to-teal-600',
                        'Design'             => 'from-pink-500 to-rose-600',
                        'AI & ML'            => 'from-purple-500 to-violet-600',
                        'Mobile Development' => 'from-orange-500 to-amber-600',
                        'Marketing'          => 'from-yellow-500 to-orange-500',
                    ];
                    @endphp
                    @foreach($enrolledCourses as $course)
                        <div class="bg-white rounded-2xl border border-gray-200 shadow-2xs hover:shadow-xs transition duration-200 overflow-hidden flex flex-col group">
                            {{-- Header Gradient --}}
                            <div class="h-2 relative bg-gradient-to-r {{ $catColors[$course->category] ?? 'from-emerald-500 to-teal-600' }}"></div>
                            <div class="p-5 flex-1 flex flex-col">
                                <span class="text-[10px] font-bold text-emerald-600 bg-emerald-50 px-2.5 py-0.5 rounded-full w-max uppercase tracking-wider mb-2">
                                    {{ $course->category }}
                                </span>
                                <h4 class="font-bold text-gray-900 text-sm leading-snug group-hover:text-emerald-600 transition duration-150 line-clamp-1">
                                    {{ $course->title }}
                                </h4>
                                <p class="text-gray-400 text-xs mt-0.5 mb-4">{{ $course->instructor_name }}</p>

                                {{-- Progress --}}
                                <div class="mt-auto space-y-1.5">
                                    <div class="flex justify-between text-[11px] font-semibold">
                                        <span class="text-gray-400">Course Progress</span>
                                        <span class="text-emerald-600">{{ $course->pivot->progress ?? 0 }}%</span>
                                    </div>
                                    <div class="w-full bg-gray-100 rounded-full h-1.5 overflow-hidden">
                                        <div class="bg-gradient-to-r from-emerald-500 to-teal-500 h-full rounded-full transition-all duration-500" style="width: {{ $course->pivot->progress ?? 0 }}%"></div>
                                    </div>
                                </div>

                                {{-- Actions --}}
                                <div class="mt-4 pt-3 border-t border-gray-100 flex items-center justify-between">
                                    <span class="text-xs text-gray-400"><i class="fa-solid fa-clock mr-1"></i>{{ $course->duration_hours }} hrs</span>
                                    <a href="{{ route('courses.show', $course) }}" class="inline-flex items-center gap-1 text-xs font-bold text-emerald-600 hover:text-emerald-700 transition">
                                        Continue <i class="fa-solid fa-arrow-right-long"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Announcements Panel --}}
        <div class="space-y-4">
            <h3 class="text-base font-extrabold text-gray-900 flex items-center gap-2">
                <i class="fa-solid fa-bullhorn text-amber-500"></i>
                Announcements
            </h3>

            <div class="bg-white rounded-2xl border border-gray-200 p-5 shadow-2xs space-y-4">
                @if($announcements->isEmpty())
                    <div class="text-center py-6">
                        <i class="fa-regular fa-bell-slash text-gray-300 text-3xl mb-2 block"></i>
                        <p class="text-gray-400 text-xs font-medium">No announcements posted yet.</p>
                    </div>
                @else
                    <div class="divide-y divide-gray-100">
                        @foreach($announcements as $index => $ann)
                            <div class="py-3.5 {{ $index === 0 ? 'pt-0' : '' }} {{ $index === count($announcements) - 1 ? 'pb-0' : '' }}">
                                <div class="flex items-start justify-between gap-2">
                                    <h4 class="font-bold text-gray-900 text-xs leading-snug line-clamp-2">
                                        {{ $ann->title }}
                                    </h4>
                                    <span class="text-[9px] text-gray-400 bg-gray-50 px-2 py-0.5 rounded-md font-semibold whitespace-nowrap mt-0.5">
                                        {{ $ann->created_at->format('M d') }}
                                    </span>
                                </div>
                                <p class="text-gray-500 text-[11px] leading-relaxed mt-1.5 line-clamp-3">
                                    {{ strip_tags($ann->body) }}
                                </p>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Quick Links Panel --}}
            <h3 class="text-base font-extrabold text-gray-900 flex items-center gap-2 pt-2">
                <i class="fa-solid fa-ellipsis text-gray-400"></i>
                Quick Actions
            </h3>
            <div class="bg-white rounded-2xl border border-gray-200 p-4 shadow-2xs grid grid-cols-2 gap-2 text-xs font-bold text-gray-600">
                <a href="{{ route('profile') }}" class="p-3 rounded-xl bg-gray-50 hover:bg-emerald-50 hover:text-emerald-600 transition flex flex-col gap-2 items-center text-center">
                    <i class="fa-solid fa-user-gear text-lg text-emerald-500"></i>
                    Settings
                </a>
                <a href="{{ route('tasks.index') }}" class="p-3 rounded-xl bg-gray-50 hover:bg-emerald-50 hover:text-emerald-600 transition flex flex-col gap-2 items-center text-center">
                    <i class="fa-solid fa-clipboard-list text-lg text-emerald-500"></i>
                    Assignments
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
