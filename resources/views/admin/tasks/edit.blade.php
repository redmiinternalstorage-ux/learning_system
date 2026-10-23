@extends('admin.layout')
@section('title', 'Edit Task')
@section('page-title', 'Edit Assignment')

@section('content')
<div class="max-w-lg">
    <a href="{{ route('admin.courses.tasks.index', $task->course_id) }}"
       class="inline-flex items-center gap-2 text-slate-400 hover:text-emerald-600 text-xs font-bold mb-5 transition">
        <i class="fa-solid fa-arrow-left text-[10px]"></i> Back to Tasks
    </a>

    <div class="glass rounded-3xl p-7">

        {{-- Header --}}
        <div class="flex items-center gap-3 mb-6 pb-5 border-b border-slate-100">
            <div class="w-11 h-11 rounded-2xl bg-gradient-to-br from-amber-500 to-orange-600 flex items-center justify-center text-white text-base shadow-lg shadow-amber-500/25">
                <i class="fa-solid fa-pen-to-square"></i>
            </div>
            <div>
                <h2 class="font-extrabold text-slate-800 text-sm font-outfit">Edit Assignment</h2>
                <p class="text-slate-400 text-xs mt-0.5 truncate max-w-xs">{{ $task->title }}</p>
            </div>
        </div>

        @if($errors->any())
        <div class="mb-5 p-4 rounded-2xl bg-rose-50 border border-rose-200 space-y-1.5 anim-slide">
            @foreach($errors->all() as $e)
                <p class="text-rose-600 text-xs font-semibold flex items-center gap-2">
                    <i class="fa-solid fa-circle-exclamation"></i> {{ $e }}
                </p>
            @endforeach
        </div>
        @endif

        <form method="POST" action="{{ route('admin.tasks.update', $task) }}" class="space-y-4">
            @csrf @method('PUT')

            <div class="space-y-1.5">
                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider">Task Title *</label>
                <input type="text" name="title" value="{{ old('title', $task->title) }}" required class="inp">
            </div>

            <div class="space-y-1.5">
                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider">Description</label>
                <textarea name="description" rows="4" class="inp resize-none">{{ old('description', $task->description) }}</textarea>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="space-y-1.5">
                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider">Due Date</label>
                    <input type="date" name="due_date"
                           value="{{ old('due_date', $task->due_date ? \Carbon\Carbon::parse($task->due_date)->format('Y-m-d') : '') }}"
                           class="inp">
                </div>
                <div class="space-y-1.5">
                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider">Max Points *</label>
                    <input type="number" name="points" value="{{ old('points', $task->points) }}" min="1" required class="inp">
                </div>
            </div>

            <div class="space-y-1.5">
                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider">Assign To Staff / Main Admin</label>
                <select name="assigned_to" class="inp">
                    <option value="">-- Main Admin / Unassigned --</option>
                    @foreach($staffList as $st)
                        <option value="{{ $st->id }}" {{ old('assigned_to', $task->assigned_to) == $st->id ? 'selected' : '' }}>
                            {{ $st->name }} ({{ ucfirst($st->role) }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit" class="btn-primary flex-1">
                    <i class="fa-solid fa-floppy-disk"></i> Save Changes
                </button>
                <a href="{{ route('admin.courses.tasks.index', $task->course_id) }}" class="btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
