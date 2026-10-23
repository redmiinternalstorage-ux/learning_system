@extends('layouts.app')
@section('title', $task->title)
@section('page-title', 'Assignment')

@section('content')
<div class="max-w-2xl space-y-5">

    {{-- Back --}}
    <a href="{{ route('tasks.index') }}"
       class="inline-flex items-center gap-2 text-slate-400 hover:text-emerald-600 text-xs font-bold transition">
        <i class="fa-solid fa-arrow-left text-[10px]"></i> Back to Assignments
    </a>

    {{-- Task header card --}}
    <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
        {{-- Top gradient strip --}}
        <div class="h-2 bg-gradient-to-r from-emerald-500 via-teal-500 to-green-400"></div>

        <div class="p-6">
            <div class="flex items-start justify-between gap-4 mb-4">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center text-white text-base shadow-md shadow-emerald-500/20 flex-shrink-0">
                        <i class="fa-solid fa-file-pen"></i>
                    </div>
                    <div>
                        <h1 class="font-extrabold text-slate-800 text-lg font-outfit leading-tight">{{ $task->title }}</h1>
                        <a href="{{ route('courses.show', $task->course) }}"
                           class="text-emerald-600 text-xs font-bold hover:underline mt-1 inline-flex items-center gap-1">
                            <i class="fa-solid fa-book-open text-[9px]"></i> {{ $task->course->title }}
                        </a>
                    </div>
                </div>
                <div class="flex flex-col items-end gap-1.5 flex-shrink-0">
                    <span class="px-3 py-1.5 rounded-xl bg-emerald-50 text-emerald-700 text-xs font-bold border border-emerald-100">
                        <i class="fa-solid fa-star text-[9px] mr-1 text-amber-500"></i>{{ $task->points }} pts
                    </span>
                    @if($task->due_date)
                    <span class="text-[11px] font-semibold text-slate-400 flex items-center gap-1">
                        <i class="fa-regular fa-calendar text-[9px]"></i>
                        Due: {{ \Carbon\Carbon::parse($task->due_date)->format('d M Y') }}
                    </span>
                    @endif
                </div>
            </div>

            @if($task->description)
            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100">
                <p class="text-slate-600 text-sm leading-relaxed">{{ $task->description }}</p>
            </div>
            @endif
        </div>
    </div>

    {{-- Submission result --}}
    @if($submission)
    <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl
                    @if($submission->status === 'graded') bg-gradient-to-br from-emerald-400 to-teal-600
                    @else bg-gradient-to-br from-blue-400 to-indigo-600 @endif
                    flex items-center justify-center text-white text-sm shadow-md">
                    @if($submission->status === 'graded')
                        <i class="fa-solid fa-star"></i>
                    @else
                        <i class="fa-solid fa-check"></i>
                    @endif
                </div>
                <h2 class="font-extrabold text-slate-800 text-sm font-outfit">Your Submission</h2>
            </div>
            @if($submission->status === 'graded')
                <span class="px-3 py-1 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs font-bold flex items-center gap-1">
                    <i class="fa-solid fa-circle-check"></i> Graded
                </span>
            @else
                <span class="px-3 py-1 rounded-xl bg-blue-50 border border-blue-200 text-blue-700 text-xs font-bold flex items-center gap-1">
                    <i class="fa-solid fa-clock"></i> Pending Review
                </span>
            @endif
        </div>

        <div class="p-6 space-y-4">
            @if($submission->submission_text)
            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 text-sm text-slate-600 leading-relaxed">
                {{ $submission->submission_text }}
            </div>
            @endif

            @if($submission->status === 'graded')
            <div class="flex items-center gap-4 p-4 rounded-2xl bg-gradient-to-r from-emerald-50 to-teal-50 border border-emerald-200">
                <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-emerald-400 to-teal-600 flex items-center justify-center text-white text-base shadow-md flex-shrink-0">
                    <i class="fa-solid fa-star"></i>
                </div>
                <div>
                    <p class="font-extrabold text-emerald-700 text-base font-outfit">{{ $submission->grade }} / {{ $task->points }} points</p>
                    @if($submission->feedback)
                    <p class="text-emerald-600 text-xs mt-1 leading-relaxed">{{ $submission->feedback }}</p>
                    @endif
                </div>
            </div>
            @else
            <div class="flex items-center gap-3 p-4 rounded-2xl bg-blue-50 border border-blue-200 text-blue-700 text-sm font-semibold">
                <i class="fa-solid fa-circle-check text-blue-500 flex-shrink-0"></i>
                Submitted successfully — awaiting review from instructor.
            </div>
            @endif
        </div>
    </div>
    @endif

    {{-- Submit / Update form --}}
    @if(!$submission || $submission->status !== 'graded')
    <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="h-1.5 bg-gradient-to-r from-emerald-500 to-teal-500"></div>
        <div class="p-6">
            <h2 class="font-extrabold text-slate-800 text-sm font-outfit mb-4 flex items-center gap-2">
                <i class="fa-solid fa-paper-plane text-emerald-500"></i>
                {{ $submission ? 'Update Submission' : 'Submit Assignment' }}
            </h2>

            <form method="POST" action="{{ route('tasks.submit', $task) }}" enctype="multipart/form-data" class="space-y-4">
                @csrf

                @if($errors->any())
                <div class="p-3.5 rounded-2xl bg-rose-50 border border-rose-200 text-rose-700 text-xs font-semibold">
                    @foreach($errors->all() as $e)
                    <p class="flex items-center gap-1.5"><i class="fa-solid fa-circle-exclamation"></i> {{ $e }}</p>
                    @endforeach
                </div>
                @endif

                <div class="space-y-1.5">
                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider">Your Answer *</label>
                    <textarea name="submission_text" rows="7" required
                              class="w-full px-4 py-3 rounded-2xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 bg-white resize-none transition placeholder-slate-300"
                              placeholder="Write your detailed answer here...">{{ old('submission_text', $submission?->submission_text) }}</textarea>
                </div>

                <button type="submit"
                        class="w-full py-3.5 rounded-2xl bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 text-white font-bold text-sm shadow-md shadow-emerald-500/20 transition flex items-center justify-center gap-2">
                    <i class="fa-solid fa-paper-plane"></i>
                    {{ $submission ? 'Update Submission' : 'Submit Assignment' }}
                </button>
            </form>
        </div>
    </div>
    @endif

    {{-- 🏆 Certificate banner — shown when ALL course tasks are graded --}}
    @if($courseCompleted)
    <div class="bg-white rounded-3xl border border-violet-200 shadow-lg overflow-hidden">
        {{-- Gradient top --}}
        <div class="h-2 bg-gradient-to-r from-indigo-500 via-violet-500 to-purple-600"></div>
        <div class="p-6">
            <div class="flex items-center gap-5">
                <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-indigo-500 to-violet-600 flex items-center justify-center text-white text-3xl shadow-lg shadow-indigo-500/30 flex-shrink-0">
                    🏆
                </div>
                <div class="flex-1 min-w-0">
                    <p class="font-extrabold text-slate-800 text-base font-outfit leading-tight">
                        Course Completed! You've earned a Certificate 🎉
                    </p>
                    <p class="text-slate-400 text-xs mt-1">
                        You have successfully passed all assignments for <strong class="text-indigo-600">{{ $task->course->title }}</strong>.
                        Your certificate has also been sent to your registered email.
                    </p>
                </div>
            </div>
            <div class="flex gap-3 mt-5">
                <a href="{{ route('certificates.download', $task->course) }}"
                   class="flex-1 py-3 rounded-2xl bg-gradient-to-r from-indigo-500 to-violet-600 hover:from-indigo-600 hover:to-violet-700 text-white font-bold text-sm shadow-md shadow-indigo-500/25 transition flex items-center justify-center gap-2">
                    <i class="fa-solid fa-download"></i> Download Certificate
                </a>
                <a href="{{ route('certificates.view', $task->course) }}"
                   target="_blank"
                   class="flex-1 py-3 rounded-2xl border-2 border-indigo-200 hover:border-indigo-400 text-indigo-600 font-bold text-sm transition flex items-center justify-center gap-2 hover:bg-indigo-50">
                    <i class="fa-solid fa-eye"></i> Preview Certificate
                </a>
            </div>
        </div>
    </div>
    @endif

</div>
@endsection
