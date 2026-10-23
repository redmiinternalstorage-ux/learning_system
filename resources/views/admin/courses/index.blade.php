@extends('admin.layout')
@section('title', 'Courses')
@section('page-title', 'Course Manager')

@section('content')
<div class="space-y-5">

    {{-- Filter & Actions Toolbar --}}
    <div class="glass rounded-2xl p-4">
        <form method="GET" action="{{ route('admin.courses') }}" class="flex flex-col sm:flex-row flex-wrap gap-3 items-stretch sm:items-center">
            <div class="relative flex-1 min-w-[200px]">
                <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Search courses or instructors..."
                       class="inp pl-11 text-xs h-10">
            </div>
            <select name="category" class="inp sm:w-40 font-bold text-xs h-10">
                <option value="">All Categories</option>
                @foreach($categories as $cat)
                <option value="{{ $cat }}" {{ request('category')===$cat ? 'selected' : '' }}>{{ $cat }}</option>
                @endforeach
            </select>
            <select name="status" class="inp sm:w-36 font-bold text-xs h-10">
                <option value="">All Status</option>
                <option value="published" {{ request('status')==='published' ? 'selected' : '' }}>Live</option>
                <option value="draft"     {{ request('status')==='draft'     ? 'selected' : '' }}>Draft</option>
            </select>
            <select name="level" class="inp sm:w-36 font-bold text-xs h-10">
                <option value="">All Levels</option>
                <option value="Beginner"     {{ request('level')==='Beginner'     ? 'selected' : '' }}>Beginner</option>
                <option value="Intermediate" {{ request('level')==='Intermediate' ? 'selected' : '' }}>Intermediate</option>
                <option value="Advanced"     {{ request('level')==='Advanced'     ? 'selected' : '' }}>Advanced</option>
            </select>
            <div class="flex gap-2">
                <button type="submit" class="btn-primary h-10 px-5 text-xs">
                    <i class="fa-solid fa-filter"></i> Filter
                </button>
                @if(request()->hasAny(['search','category','status','level']))
                <a href="{{ route('admin.courses') }}" class="btn-secondary h-10 px-4 text-xs flex items-center justify-center">
                    <i class="fa-solid fa-xmark"></i>
                </a>
                @endif
                <a href="{{ route('admin.courses.create') }}" class="btn-primary h-10 px-4 text-xs whitespace-nowrap bg-gradient-to-r from-emerald-500 to-teal-600">
                    <i class="fa-solid fa-plus"></i> Add Course
                </a>
            </div>
        </form>
    </div>

    {{-- Bulk Action Form Wrapper --}}
    <form id="bulk-course-form" method="POST" action="{{ route('admin.courses.bulk') }}">
        @csrf
        
        {{-- Bulk Action Bar --}}
        <div id="bulk-course-bar" class="glass rounded-2xl p-4 mb-4 hidden flex-wrap items-center justify-between gap-3 border-l-4 border-emerald-500 anim-up">
            <div class="flex items-center gap-2">
                <span class="w-7 h-7 rounded-xl bg-emerald-500/20 text-emerald-400 font-bold text-xs flex items-center justify-center">
                    <span id="selected-course-count">0</span>
                </span>
                <span class="text-xs font-bold text-slate-700 dark:text-slate-200">Courses selected</span>
            </div>
            <div class="flex items-center gap-3">
                <select name="action" class="inp text-xs h-9 font-bold w-48">
                    <option value="">Select Bulk Action...</option>
                    <option value="publish">Publish Live</option>
                    <option value="unpublish">Unpublish (Draft)</option>
                    <option value="delete">Delete Courses</option>
                </select>
                <button type="submit" onclick="return confirm('Apply bulk action to selected courses?')" class="btn-primary h-9 px-4 text-xs bg-gradient-to-r from-emerald-500 to-teal-600">
                    Apply Action
                </button>
            </div>
        </div>

        {{-- Courses Table --}}
        <div class="glass rounded-3xl overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 dark:border-white/5 flex items-center justify-between">
                <div>
                    <h3 class="font-extrabold text-slate-800 dark:text-slate-100 text-sm font-outfit">Course Catalog</h3>
                    <p class="text-slate-400 text-[10px] font-bold uppercase tracking-widest mt-0.5">{{ $courses->total() }} total courses</p>
                </div>
                <div class="w-10 h-10 rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center text-white shadow-md shadow-emerald-500/25">
                    <i class="fa-solid fa-book-open text-sm"></i>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="admin-table w-full">
                    <thead>
                        <tr>
                            <th class="w-10 text-center">
                                <input type="checkbox" id="select-all-courses" onclick="toggleSelectAllCourses(this)" class="rounded text-emerald-600 focus:ring-emerald-500 cursor-pointer">
                            </th>
                            <th class="text-left">Course</th>
                            <th class="text-left">Category</th>
                            <th class="text-left">Instructor</th>
                            <th class="text-left">Metrics</th>
                            <th class="text-left">Price</th>
                            <th class="text-left">Status</th>
                            <th class="text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($courses as $course)
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-white/[0.02] transition">
                            <td class="text-center">
                                <input type="checkbox" name="course_ids[]" value="{{ $course->id }}" onclick="updateCourseBulkState()" class="course-checkbox rounded text-emerald-600 focus:ring-emerald-500 cursor-pointer">
                            </td>
                            <td>
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-emerald-400 to-teal-600 flex items-center justify-center text-white font-bold text-sm flex-shrink-0 shadow-sm font-outfit">
                                        {{ strtoupper(substr($course->title, 0, 1)) }}
                                    </div>
                                    <div class="min-w-0">
                                        <a href="{{ route('admin.courses.edit', $course) }}" class="font-bold text-slate-800 dark:text-slate-200 text-xs hover:text-emerald-500 transition block truncate max-w-xs">
                                            {{ $course->title }}
                                        </a>
                                        <p class="text-slate-400 text-[10px] mt-0.5">{{ $course->level }} · {{ $course->duration_hours }} hrs</p>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge badge-purple text-[9px]">{{ $course->category }}</span>
                            </td>
                            <td class="text-slate-600 dark:text-slate-300 font-medium text-xs">{{ $course->instructor_name }}</td>
                            <td>
                                <div class="flex items-center gap-2">
                                    <span class="badge badge-blue text-[9px]">
                                        <i class="fa-solid fa-graduation-cap"></i> {{ $course->users_count }} enrolled
                                    </span>
                                    <span class="badge badge-amber text-[9px]">
                                        <i class="fa-solid fa-list-check"></i> {{ $course->tasks_count }} tasks
                                    </span>
                                </div>
                            </td>
                            <td>
                                <span class="font-bold text-xs {{ $course->price == 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-700 dark:text-slate-300' }}">
                                    {{ $course->price_display }}
                                </span>
                            </td>
                            <td>
                                @if($course->is_published)
                                    <span class="badge badge-green"><i class="fa-solid fa-circle text-[6px]"></i> Live</span>
                                @else
                                    <span class="badge badge-gray">Draft</span>
                                @endif
                            </td>
                            <td>
                                <div class="flex items-center justify-end gap-3 flex-wrap">
                                    <a href="{{ route('admin.courses.tasks.index', $course) }}"
                                       class="text-[11px] font-bold text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 transition flex items-center gap-1">
                                        <i class="fa-solid fa-file-pen text-[9px]"></i> Tasks
                                    </a>
                                    <a href="{{ route('admin.courses.edit', $course) }}"
                                       class="text-[11px] font-bold text-amber-600 dark:text-amber-400 hover:text-amber-800 transition flex items-center gap-1">
                                        <i class="fa-solid fa-pen text-[9px]"></i> Edit
                                    </a>
                                    <form method="POST" action="{{ route('admin.courses.toggle', $course) }}" class="inline">
                                        @csrf
                                        <button type="submit"
                                                class="text-[11px] font-bold transition {{ $course->is_published ? 'text-orange-500 hover:text-orange-700' : 'text-emerald-600 hover:text-emerald-800' }}">
                                            {{ $course->is_published ? 'Unpublish' : 'Publish' }}
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.courses.delete', $course) }}"
                                          onsubmit="return confirm('Delete course: {{ addslashes($course->title) }}? All tasks and submissions will be deleted.')" class="inline">
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
                            <td colspan="8" class="py-14 text-center">
                                <i class="fa-solid fa-book-open text-3xl text-slate-200 dark:text-slate-700 mb-3 block"></i>
                                <p class="text-slate-400 text-xs">No courses found. <a href="{{ route('admin.courses.create') }}" class="text-purple-500 font-bold hover:underline">Add one!</a></p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($courses->hasPages())
            <div class="px-6 py-4 border-t border-slate-100 dark:border-white/5 flex items-center justify-between text-[11px] font-bold text-slate-400 bg-slate-50/30 dark:bg-white/[0.01]">
                <span>Showing {{ $courses->firstItem() }}–{{ $courses->lastItem() }} of {{ $courses->total() }}</span>
                <div class="flex gap-2">
                    @if(!$courses->onFirstPage())
                        <a href="{{ $courses->previousPageUrl() }}" class="px-3 py-1.5 rounded-xl border border-slate-200 dark:border-white/10 text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-white/5 transition">← Prev</a>
                    @endif
                    @if($courses->hasMorePages())
                        <a href="{{ $courses->nextPageUrl() }}" class="px-3 py-1.5 rounded-xl border border-slate-200 dark:border-white/10 text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-white/5 transition">Next →</a>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </form>
</div>

@push('scripts')
<script>
    function toggleSelectAllCourses(master) {
        const checkboxes = document.querySelectorAll('.course-checkbox');
        checkboxes.forEach(cb => cb.checked = master.checked);
        updateCourseBulkState();
    }
    function updateCourseBulkState() {
        const checked = document.querySelectorAll('.course-checkbox:checked');
        const bar = document.getElementById('bulk-course-bar');
        const count = document.getElementById('selected-course-count');
        if (checked.length > 0) {
            bar.classList.remove('hidden');
            bar.classList.add('flex');
            count.textContent = checked.length;
        } else {
            bar.classList.add('hidden');
            bar.classList.remove('flex');
        }
    }
</script>
@endpush
@endsection
