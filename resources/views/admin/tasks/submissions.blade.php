@extends('admin.layout')
@section('title', 'Submissions')
@section('page-title', 'Submissions')

@section('content')
<div class="space-y-5">

    {{-- Back + Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.courses.tasks.index', $task->course_id) }}"
               class="w-9 h-9 rounded-xl border border-slate-200 flex items-center justify-center text-slate-400 hover:text-emerald-600 hover:border-emerald-300 transition">
                <i class="fa-solid fa-arrow-left text-xs"></i>
            </a>
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Task Submissions</p>
                <h2 class="font-extrabold text-slate-800 text-sm font-outfit mt-0.5">{{ $task->title }}</h2>
            </div>
        </div>
    </div>

    {{-- Task Summary --}}
    <div class="grid grid-cols-3 gap-4">
        @php
        $totalSubs   = $submissions->count();
        $gradedSubs  = $submissions->where('status','graded')->count();
        $pendingSubs = $submissions->where('status','pending')->count();
        @endphp
        <div class="glass rounded-2xl p-4 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center text-white text-sm flex-shrink-0 shadow-md shadow-emerald-500/20">
                <i class="fa-solid fa-paper-plane"></i>
            </div>
            <div>
                <p class="text-xl font-black text-slate-800 font-outfit">{{ $totalSubs }}</p>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Total</p>
            </div>
        </div>
        <div class="glass rounded-2xl p-4 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center text-white text-sm flex-shrink-0 shadow-md shadow-emerald-500/20">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <div>
                <p class="text-xl font-black text-slate-800 font-outfit">{{ $gradedSubs }}</p>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Graded</p>
            </div>
        </div>
        <div class="glass rounded-2xl p-4 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-amber-500 to-orange-600 flex items-center justify-center text-white text-sm flex-shrink-0 shadow-md shadow-amber-500/20">
                <i class="fa-solid fa-clock"></i>
            </div>
            <div>
                <p class="text-xl font-black text-slate-800 font-outfit">{{ $pendingSubs }}</p>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Pending</p>
            </div>
        </div>
    </div>

    {{-- Submissions Table --}}
    <div class="glass rounded-3xl overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <h3 class="font-extrabold text-slate-800 text-sm font-outfit">Submissions</h3>
            <span class="badge badge-emerald">{{ $task->points }} pts max</span>
        </div>

        <div class="overflow-x-auto">
            <table class="admin-table w-full">
                <thead>
                    <tr>
                        <th class="text-left">Student</th>
                        <th class="text-left">Submitted</th>
                        <th class="text-left">Status</th>
                        <th class="text-left">Grade</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($submissions as $sub)
                    <tr id="row-{{ $sub->id }}">
                        <td>
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center text-white font-bold text-xs font-outfit flex-shrink-0">
                                    {{ strtoupper(substr($sub->user->name ?? '?', 0, 1)) }}
                                </div>
                                <div>
                                    <p class="font-bold text-slate-800 text-xs">{{ $sub->user->name ?? 'Deleted User' }}</p>
                                    <p class="text-slate-400 text-[10px]">{{ $sub->user->email ?? '' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="text-slate-400 text-xs font-medium">
                            {{ $sub->submitted_at ? \Carbon\Carbon::parse($sub->submitted_at)->format('d M Y, H:i') : '—' }}
                        </td>
                        <td>
                            @if($sub->status === 'graded')
                                <span class="badge badge-green"><i class="fa-solid fa-check text-[8px]"></i> Graded</span>
                            @else
                                <span class="badge badge-amber"><i class="fa-solid fa-clock text-[8px]"></i> Pending</span>
                            @endif
                        </td>
                        <td>
                            @if($sub->grade !== null)
                                <span class="font-black text-slate-800 font-outfit text-sm">{{ $sub->grade }}</span>
                                <span class="text-slate-400 text-xs">/{{ $task->points }}</span>
                            @else
                                <span class="text-slate-300 text-xs">—</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <button onclick="toggleGrade({{ $sub->id }})"
                                    class="px-3 py-1.5 rounded-xl bg-emerald-50 text-emerald-700 text-[11px] font-bold hover:bg-emerald-100 transition">
                                {{ $sub->status === 'graded' ? 'Re-grade' : 'Grade' }}
                            </button>
                        </td>
                    </tr>

                    {{-- Grade Form Row (hidden) --}}
                    <tr id="grade-row-{{ $sub->id }}" class="hidden">
                        <td colspan="5" class="px-6 py-5 bg-emerald-50/40 border-t border-emerald-100">
                            @if($sub->submission_text)
                            <div class="mb-4 p-4 rounded-2xl bg-white border border-slate-200">
                                <p class="text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-2">Student's Submission</p>
                                <p class="text-xs text-slate-600 leading-relaxed">{{ $sub->submission_text }}</p>
                            </div>
                            @endif
                            @if($sub->file_path)
                            <div class="mb-4 p-3.5 rounded-2xl bg-white border border-slate-200 flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl bg-emerald-50 flex items-center justify-center text-emerald-600 flex-shrink-0">
                                    <i class="fa-solid fa-paperclip text-sm"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs font-bold text-slate-700">Attached file</p>
                                    <p class="text-[10px] text-slate-400 truncate">{{ basename($sub->file_path) }}</p>
                                </div>
                                <a href="{{ Storage::url($sub->file_path) }}" target="_blank"
                                   class="text-[11px] font-bold text-emerald-600 hover:underline">Download</a>
                            </div>
                            @endif

                            <form method="POST" action="{{ route('admin.submissions.grade', $sub) }}"
                                  class="flex flex-col sm:flex-row items-end gap-3">
                                @csrf
                                <div>
                                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">
                                        Grade (max {{ $task->points }})
                                    </label>
                                    <input type="number" name="grade" value="{{ $sub->grade }}"
                                           min="0" max="{{ $task->points }}" required
                                           class="inp w-28">
                                </div>
                                <div class="flex-1">
                                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Feedback</label>
                                    <input type="text" name="feedback" value="{{ $sub->feedback }}"
                                           placeholder="Optional feedback for student..."
                                           class="inp">
                                </div>
                                <button type="submit" class="btn-primary flex-shrink-0">
                                    <i class="fa-solid fa-check"></i> Submit Grade
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-14 text-center">
                            <i class="fa-solid fa-inbox text-3xl text-slate-200 mb-3 block"></i>
                            <p class="text-slate-400 text-xs">No submissions yet.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script>
function toggleGrade(id) {
    const row = document.getElementById('grade-row-' + id);
    row.classList.toggle('hidden');
}
</script>
@endpush
@endsection
