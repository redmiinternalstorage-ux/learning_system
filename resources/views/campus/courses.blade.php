@extends('campus.layout')
@section('title','Browse Courses')
@section('page-title','Browse Courses 📚')

@section('content')
<div class="space-y-5">
    <div class="glass-card p-4">
        <p class="text-xs text-slate-500 font-semibold">🎓 All courses are <strong class="text-emerald-700">FREE</strong> for verified campus students</p>
    </div>

    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
        @foreach($courses as $course)
        @php $isEnrolled = $enrolledIds->contains($course->id); @endphp
        <div class="glass-card p-5 flex flex-col hover:border-emerald-300 transition-colors">
            <div class="flex items-start justify-between mb-3">
                <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-emerald-400 to-teal-500 flex items-center justify-center text-white text-lg font-black shadow-md shadow-emerald-500/20">
                    {{ strtoupper(substr($course->title, 0, 1)) }}
                </div>
                <div class="flex flex-col items-end gap-1">
                    <span class="px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-700 text-[9px] font-black">FREE</span>
                    <span class="px-2 py-0.5 rounded-full bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 text-[9px] font-bold">{{ ucfirst($course->level) }}</span>
                </div>
            </div>
            <h3 class="font-extrabold text-slate-900 dark:text-white text-sm leading-tight mb-1">{{ $course->title }}</h3>
            <p class="text-[11px] text-slate-500 leading-relaxed flex-1">{{ $course->short_description ?? Str::limit($course->description, 80) }}</p>
            <div class="mt-3 pt-3 border-t border-slate-100 dark:border-slate-700/50 flex items-center justify-between">
                <div class="flex items-center gap-3 text-[10px] text-slate-400">
                    <span><i class="fa-solid fa-clock mr-1"></i>{{ $course->duration_hours }}h</span>
                    <span><i class="fa-solid fa-tag mr-1"></i>{{ $course->category }}</span>
                </div>
                @if($isEnrolled)
                <a href="{{ route('campus.course-detail', $course) }}" class="btn-green text-[10px] px-3 py-1.5">
                    <i class="fa-solid fa-play"></i> Continue
                </a>
                @else
                <form method="POST" action="{{ route('campus.enroll', $course) }}">
                    @csrf
                    <button type="submit" class="btn-green text-[10px] px-3 py-1.5">
                        <i class="fa-solid fa-plus"></i> Enroll Free
                    </button>
                </form>
                @endif
            </div>
        </div>
        @endforeach
    </div>
    <div>{{ $courses->links() }}</div>
</div>
@endsection
