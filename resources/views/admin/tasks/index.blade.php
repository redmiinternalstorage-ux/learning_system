@extends('admin.layout')
@section('title', 'Tasks')
@section('page-title') Assignments @endsection

@section('content')
<div class="space-y-5">

    {{-- Breadcrumb / Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.courses') }}"
               class="w-9 h-9 rounded-xl border border-slate-200 flex items-center justify-center text-slate-400 hover:text-emerald-600 hover:border-emerald-300 transition">
                <i class="fa-solid fa-arrow-left text-xs"></i>
            </a>
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Course Assignments</p>
                <h2 class="font-extrabold text-slate-800 text-sm font-outfit mt-0.5">{{ $course->title }}</h2>
            </div>
        </div>
        <a href="{{ route('admin.courses.tasks.create', $course) }}" class="btn-primary whitespace-nowrap self-start sm:self-auto">
            <i class="fa-solid fa-plus"></i> Add Task
        </a>
    </div>

    {{-- Course Summary Banner --}}
    <div class="glass rounded-2xl p-5 flex items-center gap-4">
        <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center text-white text-base shadow-lg shadow-emerald-500/20 flex-shrink-0">
            <i class="fa-solid fa-book-open"></i>
        </div>
        <div class="flex-1 min-w-0">
            <p class="font-bold text-slate-800 text-sm truncate">{{ $course->title }}</p>
            <p class="text-slate-400 text-xs mt-0.5">{{ $course->instructor_name }} · {{ $course->level }}</p>
        </div>
        <div class="text-right flex-shrink-0">
            <p class="text-2xl font-black text-slate-800 font-outfit">{{ $tasks->count() }}</p>
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Tasks</p>
        </div>
    </div>

    {{-- Tasks Table --}}
    <div class="glass rounded-3xl overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100">
            <h3 class="font-extrabold text-slate-800 text-sm font-outfit">All Tasks</h3>
        </div>

        <div class="overflow-x-auto">
            <table class="admin-table w-full">
                <thead>
                    <tr>
                        <th class="text-left">Title</th>
                        <th class="text-left">Assigned To</th>
                        <th class="text-left">Due Date</th>
                        <th class="text-left">Points</th>
                        <th class="text-left">Submissions</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tasks as $task)
                    <tr>
                        <td>
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center text-white text-[10px] font-bold flex-shrink-0">
                                    {{ $loop->iteration }}
                                </div>
                                <p class="font-bold text-slate-800 text-xs">{{ $task->title }}</p>
                            </div>
                        </td>
                        <td>
                            @if($task->assignedTo)
                                <span class="badge badge-amber">
                                    <i class="fa-solid fa-user text-[8px]"></i> {{ $task->assignedTo->name }}
                                </span>
                            @else
                                <span class="badge badge-slate">
                                    <i class="fa-solid fa-user-shield text-[8px]"></i> Main Admin
                                </span>
                            @endif
                        </td>
                        <td>
                            @if($task->due_date)
                                <span class="badge badge-amber">
                                    <i class="fa-regular fa-calendar text-[8px]"></i>
                                    {{ \Carbon\Carbon::parse($task->due_date)->format('d M Y') }}
                                </span>
                            @else
                                <span class="text-slate-400 text-xs font-medium">No deadline</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge badge-emerald">{{ $task->points }} pts</span>
                        </td>
                        <td>
                            @php $subCount = $task->submissions()->count(); @endphp
                            <span class="badge {{ $subCount > 0 ? 'badge-green' : 'badge-slate' }}">
                                {{ $subCount }} submitted
                            </span>
                        </td>
                        <td>
                            <div class="flex items-center justify-end gap-3">
                                <a href="{{ route('admin.tasks.submissions', $task) }}"
                                   class="text-[11px] font-bold text-emerald-600 hover:text-emerald-800 transition flex items-center gap-1">
                                    <i class="fa-solid fa-list-check text-[9px]"></i> Submissions
                                </a>
                                <a href="{{ route('admin.tasks.edit', $task) }}"
                                   class="text-[11px] font-bold text-amber-600 hover:text-amber-800 transition flex items-center gap-1">
                                    <i class="fa-solid fa-pen text-[9px]"></i> Edit
                                </a>
                                <form method="POST" action="{{ route('admin.tasks.delete', $task) }}"
                                      onsubmit="return confirm('Delete task: {{ addslashes($task->title) }}?')" class="inline">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                            class="text-[11px] font-bold text-rose-500 hover:text-rose-700 transition flex items-center gap-1">
                                        <i class="fa-solid fa-trash text-[9px]"></i> Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-14 text-center">
                            <i class="fa-solid fa-file-pen text-3xl text-slate-200 mb-3 block"></i>
                            <p class="text-slate-400 text-xs">No tasks yet.
                                <a href="{{ route('admin.courses.tasks.create', $course) }}" class="text-emerald-600 font-bold hover:underline">Add one!</a>
                            </p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
