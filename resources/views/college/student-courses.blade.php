@extends('college.layout')
@section('title','Student Courses')
@section('page-title','Student Course Access')

@section('content')
<div class="max-w-2xl space-y-5">
    <a href="{{ route('college.students') }}" class="inline-flex items-center gap-2 text-slate-400 hover:text-slate-700 text-xs font-bold transition">
        <i class="fa-solid fa-arrow-left text-[10px]"></i> Back to Students
    </a>

    <div class="glass rounded-2xl p-5">
        <div class="flex items-center gap-3 mb-1">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-500 to-violet-600 text-white flex items-center justify-center font-bold text-sm">
                {{ strtoupper(substr($student->name,0,1)) }}
            </div>
            <div>
                <p class="font-extrabold text-slate-800 text-sm">{{ $student->name }}</p>
                <p class="text-slate-400 text-xs">{{ $student->college_email }} • {{ $student->department ?? 'No dept' }}</p>
            </div>
        </div>
    </div>

    {{-- Enroll in a course --}}
    @if($allCourses->whereNotIn('id', $enrolledIds)->count())
    <div class="glass rounded-2xl p-5">
        <h3 class="font-extrabold text-slate-800 text-sm mb-3 font-outfit">Enroll in New Course</h3>
        <form method="POST" action="{{ route('college.students.courses.enroll', $student) }}" class="flex gap-2">
            @csrf
            <select name="course_id" class="inp flex-1 text-xs" required>
                <option value="">Select course...</option>
                @foreach($allCourses->whereNotIn('id', $enrolledIds) as $c)
                <option value="{{ $c->id }}">{{ $c->title }} ({{ $c->category }})</option>
                @endforeach
            </select>
            <button type="submit" class="btn-primary text-xs px-5 whitespace-nowrap">
                <i class="fa-solid fa-plus"></i> Enroll
            </button>
        </form>
    </div>
    @endif

    {{-- Current enrollments --}}
    <div class="glass rounded-2xl overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100">
            <h3 class="font-extrabold text-slate-800 text-sm font-outfit">Enrolled Courses ({{ $enrollments->count() }})</h3>
        </div>
        @forelse($enrollments as $e)
        <div class="px-5 py-4 border-b border-slate-100 last:border-0 flex items-center justify-between gap-3">
            <div>
                <p class="font-bold text-slate-800 text-xs">{{ $e->course->title }}</p>
                <p class="text-[10px] text-slate-400 mt-0.5">{{ $e->course->category }} • Progress: {{ $e->progress }}%</p>
                <div class="w-32 h-1.5 bg-slate-100 rounded-full mt-1.5 overflow-hidden">
                    <div class="h-full rounded-full bg-indigo-500" style="width:{{ $e->progress }}%"></div>
                </div>
            </div>
            <form method="POST" action="{{ route('college.students.courses.unenroll', $student) }}">
                @csrf
                <input type="hidden" name="course_id" value="{{ $e->course_id }}">
                <button type="submit" onclick="return confirm('Remove enrollment?')"
                        class="text-[11px] font-bold text-rose-500 hover:text-rose-700 transition">Remove</button>
            </form>
        </div>
        @empty
        <div class="px-5 py-10 text-center text-slate-400 text-xs">
            <i class="fa-solid fa-book text-2xl mb-2 block text-slate-200"></i>
            Not enrolled in any courses yet.
        </div>
        @endforelse
    </div>
</div>
@endsection
