@extends('admin.layout')
@section('title','Enrollments')
@section('page-title','Enrollments Manager')

@section('content')
<div class="space-y-6">

    {{-- Hero --}}
    <div class="relative rounded-3xl overflow-hidden p-7 sm:p-9"
         style="background:linear-gradient(135deg,#003020 0%,#004d30 40%,#001a10 100%)">
        <div class="absolute inset-0 pointer-events-none">
            <div class="absolute top-0 right-0 w-80 h-80 bg-emerald-500/20 rounded-full blur-3xl -translate-y-1/2 translate-x-1/3"></div>
            <div class="absolute bottom-0 left-0 w-60 h-60 bg-teal-500/10 rounded-full blur-2xl translate-y-1/2 -translate-x-1/4"></div>
        </div>
        <div class="relative z-10 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <p class="text-emerald-300 text-xs font-black uppercase tracking-widest mb-2">Platform Enrollments</p>
                <h2 class="text-white text-2xl sm:text-3xl font-black font-outfit leading-tight">
                    🎓 Enrollment Manager
                </h2>
                <p class="text-emerald-200/60 text-sm mt-1.5">Track student progress & manually assign courses</p>
            </div>
            <div class="flex items-center gap-3 flex-wrap">
                <button onclick="openEnrollModal()" class="flex items-center gap-2 px-5 py-3 rounded-2xl bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 text-white font-bold text-xs shadow-lg shadow-emerald-500/25 transition">
                    <i class="fa-solid fa-user-plus"></i> Manual Enroll Student
                </button>
            </div>
        </div>
    </div>

    {{-- Stats Bar --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        @php
        $stats = [
            ['label'=>'Total Enrollments', 'val'=>$totalEnrollments, 'icon'=>'fa-graduation-cap', 'col'=>'text-white',       'from'=>'from-emerald-500','to'=>'to-teal-600'],
            ['label'=>'Completed (100%)',  'val'=>$completed,        'icon'=>'fa-circle-check',  'col'=>'text-emerald-300', 'from'=>'from-emerald-600','to'=>'to-green-700'],
            ['label'=>'In Progress',       'val'=>$inProgress,       'icon'=>'fa-spinner',       'col'=>'text-amber-300',   'from'=>'from-amber-500',  'to'=>'to-orange-600'],
            ['label'=>'Not Started (0%)',  'val'=>$notStarted,       'icon'=>'fa-hourglass',     'col'=>'text-rose-300',    'from'=>'from-rose-500',   'to'=>'to-pink-600'],
        ];
        @endphp
        @foreach($stats as $s)
        <div class="glass stat-lift rounded-2xl p-4 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br {{ $s['from'] }} {{ $s['to'] }} flex items-center justify-center text-white text-sm shadow-md flex-shrink-0">
                <i class="fa-solid {{ $s['icon'] }}"></i>
            </div>
            <div>
                <p class="text-xl font-black font-outfit text-slate-800 dark:text-slate-100 leading-none">{{ $s['val'] }}</p>
                <p class="text-[9px] font-bold text-slate-400 uppercase tracking-wider mt-1">{{ $s['label'] }}</p>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Filter Bar --}}
    <div class="glass rounded-2xl p-4">
        <form method="GET" action="{{ route('admin.enrollments') }}" class="flex flex-wrap items-end gap-3">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Search</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Student name, email, course title..." class="inp text-xs h-10">
            </div>
            <div class="min-w-[180px]">
                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Course</label>
                <select name="course_id" class="inp text-xs h-10 font-bold">
                    <option value="">All Courses</option>
                    @foreach($courses as $c)
                    <option value="{{ $c->id }}" {{ request('course_id')==$c->id?'selected':'' }}>{{ $c->title }}</option>
                    @endforeach
                </select>
            </div>
            <div class="min-w-[160px]">
                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Progress</label>
                <select name="progress" class="inp text-xs h-10 font-bold">
                    <option value="">All Progress Levels</option>
                    <option value="not_started"  {{ request('progress')==='not_started'?'selected':'' }}>Not Started (0%)</option>
                    <option value="in_progress"  {{ request('progress')==='in_progress'?'selected':'' }}>In Progress (1–99%)</option>
                    <option value="completed"    {{ request('progress')==='completed'?'selected':'' }}>Completed (100%)</option>
                </select>
            </div>
            <div class="flex gap-2 pb-0.5">
                <button type="submit" class="btn-primary h-10 px-5 text-xs bg-gradient-to-r from-emerald-500 to-teal-600">
                    <i class="fa-solid fa-filter"></i> Filter
                </button>
                @if(request()->hasAny(['search','course_id','progress']))
                <a href="{{ route('admin.enrollments') }}" class="btn-secondary h-10 px-4 text-xs flex items-center justify-center">
                    <i class="fa-solid fa-xmark"></i>
                </a>
                @endif
            </div>
        </form>
    </div>

    {{-- Enrollment Table --}}
    <div class="glass rounded-3xl overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 dark:border-white/5 flex items-center justify-between">
            <div>
                <h3 class="font-extrabold text-slate-800 dark:text-slate-100 text-sm font-outfit">Active Course Enrollments</h3>
                <p class="text-slate-400 text-[10px] font-bold uppercase tracking-widest mt-0.5">Showing {{ $enrollments->firstItem()??0 }}–{{ $enrollments->lastItem()??0 }} of {{ $enrollments->total() }} records</p>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full admin-table">
                <thead>
                    <tr>
                        <th class="text-left">Student</th>
                        <th class="text-left">Course Details</th>
                        <th class="text-left">Progress</th>
                        <th class="text-left">Enrolled On</th>
                        <th class="text-right">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($enrollments as $e)
                    <tr class="hover:bg-slate-50/50 dark:hover:bg-white/[0.02] transition">
                        <td>
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-emerald-400 to-teal-600 flex items-center justify-center text-white text-xs font-bold font-outfit flex-shrink-0 shadow-sm">
                                    {{ strtoupper(substr($e->user_name,0,1)) }}
                                </div>
                                <div>
                                    <p class="text-xs font-bold text-slate-800 dark:text-slate-200">{{ $e->user_name }}</p>
                                    <p class="text-[10px] text-slate-400">{{ $e->user_email }}</p>
                                </div>
                            </div>
                        </td>
                        <td>
                            <p class="text-xs font-bold text-slate-800 dark:text-slate-200 max-w-xs truncate">{{ $e->course_title }}</p>
                            @if($e->course_category)
                            <span class="badge badge-purple text-[8px] mt-0.5">{{ $e->course_category }}</span>
                            @endif
                        </td>
                        <td>
                            @php
                                $prog = (int)$e->progress;
                                $col = $prog===100 ? '#10b981' : ($prog>0 ? '#f59e0b' : '#94a3b8');
                            @endphp
                            <div class="flex items-center gap-2 min-w-[120px]">
                                <div class="flex-1 h-2 rounded-full bg-slate-100 dark:bg-white/10 overflow-hidden">
                                    <div class="h-full rounded-full transition-all" style="width:{{ $prog }}%;background:{{ $col }}"></div>
                                </div>
                                <span class="text-[10px] font-bold" style="color:{{ $col }}">{{ $prog }}%</span>
                            </div>
                            @if($prog===100)<span class="badge badge-green text-[8px] mt-1">✓ Completed</span>@endif
                        </td>
                        <td>
                            <p class="text-xs font-semibold text-slate-700 dark:text-slate-300">{{ \Carbon\Carbon::parse($e->enrolled_at)->format('d M Y') }}</p>
                            <p class="text-[10px] text-slate-400">{{ \Carbon\Carbon::parse($e->enrolled_at)->diffForHumans() }}</p>
                        </td>
                        <td class="text-right">
                            <div class="flex items-center justify-end gap-1.5">
                                @if((int)$e->progress >= 100)
                                    <a href="{{ route('admin.certificates.view', [$e->user_id, $e->course_id]) }}" target="_blank"
                                       class="px-2.5 py-1.5 rounded-xl bg-amber-50 dark:bg-amber-950/30 text-amber-700 dark:text-amber-400 text-[10px] font-bold hover:bg-amber-100 transition border border-amber-200/60"
                                       title="View Certificate">
                                        <i class="fa-solid fa-award mr-1"></i> Cert
                                    </a>
                                @endif
                                <form method="POST" action="{{ route('admin.enrollments.unenroll') }}"
                                      onsubmit="return confirm('Remove {{ addslashes($e->user_name) }} from {{ addslashes($e->course_title) }}?')" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <input type="hidden" name="user_id"   value="{{ $e->user_id }}">
                                    <input type="hidden" name="course_id" value="{{ $e->course_id }}">
                                    <button type="submit" class="px-3 py-1.5 rounded-xl bg-rose-50 dark:bg-rose-950/30 text-rose-600 dark:text-rose-400 text-[10px] font-bold hover:bg-rose-100 dark:hover:bg-rose-950/50 transition border border-rose-100 dark:border-rose-900/30">
                                        <i class="fa-solid fa-user-minus mr-1"></i> Unenroll
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-16 text-center text-slate-400 text-xs">
                            <i class="fa-solid fa-graduation-cap text-3xl mb-3 block opacity-30"></i>
                            No enrollments found matching the filters.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($enrollments->hasPages())
        <div class="px-6 py-4 border-t border-slate-100 dark:border-white/5 flex justify-between items-center text-[11px] font-bold text-slate-400">
            <span>Page {{ $enrollments->currentPage() }} of {{ $enrollments->lastPage() }}</span>
            <div class="flex gap-2">
                @if(!$enrollments->onFirstPage())
                    <a href="{{ $enrollments->previousPageUrl() }}" class="px-3 py-1.5 rounded-xl border border-slate-200 dark:border-white/10 text-slate-600 dark:text-slate-300 hover:bg-slate-50 transition">← Prev</a>
                @endif
                @if($enrollments->hasMorePages())
                    <a href="{{ $enrollments->nextPageUrl() }}" class="px-3 py-1.5 rounded-xl border border-slate-200 dark:border-white/10 text-slate-600 dark:text-slate-300 hover:bg-slate-50 transition">Next →</a>
                @endif
            </div>
        </div>
        @endif
    </div>

</div>

{{-- Manual Enroll Modal --}}
<div id="enroll-modal" class="fixed inset-0 bg-slate-950/70 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4" onclick="closeEnrollModalOnBackdrop(event)">
    <div class="glass max-w-lg w-full rounded-3xl p-7 shadow-2xl space-y-5 anim-up" onclick="event.stopPropagation()">
        <div class="flex items-center justify-between border-b border-slate-100 dark:border-white/10 pb-4">
            <div>
                <h3 class="font-extrabold text-slate-800 dark:text-slate-100 text-base font-outfit">Manual Course Enrollment</h3>
                <p class="text-slate-400 text-xs mt-0.5">Assign any student to a course directly</p>
            </div>
            <button onclick="closeEnrollModal()" class="text-slate-400 hover:text-slate-600 dark:hover:text-white">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <form method="POST" action="{{ route('admin.enrollments.store') }}" class="space-y-4">
            @csrf

            <div class="space-y-1">
                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider">Select Student</label>
                <select name="user_id" required class="inp font-bold text-xs">
                    <option value="">Choose a user...</option>
                    @foreach($users as $u)
                    <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->email }})</option>
                    @endforeach
                </select>
            </div>

            <div class="space-y-1">
                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider">Select Course</label>
                <select name="course_id" required class="inp font-bold text-xs">
                    <option value="">Choose a course...</option>
                    @foreach($courses as $c)
                    <option value="{{ $c->id }}">{{ $c->title }}</option>
                    @endforeach
                </select>
            </div>

            <div class="space-y-1">
                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider">Initial Progress (%)</label>
                <input type="number" name="progress" min="0" max="100" value="0" required class="inp text-xs font-bold">
            </div>

            <div class="flex gap-3 pt-3">
                <button type="submit" class="btn-primary flex-1 py-3 text-xs bg-gradient-to-r from-emerald-500 to-teal-600">
                    Enroll Student Now
                </button>
                <button type="button" onclick="closeEnrollModal()" class="btn-secondary px-5 text-xs">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function openEnrollModal() {
        document.getElementById('enroll-modal').classList.remove('hidden');
    }
    function closeEnrollModal() {
        document.getElementById('enroll-modal').classList.add('hidden');
    }
    function closeEnrollModalOnBackdrop(e) {
        if (e.target.id === 'enroll-modal') closeEnrollModal();
    }
</script>
@endpush
@endsection
