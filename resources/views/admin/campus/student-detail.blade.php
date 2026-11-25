@extends('admin.layout')
@section('title',$student->name)
@section('page-title','Campus Student Detail')

@section('content')
<div class="space-y-5 max-w-3xl">
    <div class="glass rounded-2xl p-6">
        <div class="flex items-start justify-between gap-4 mb-6">
            <div class="flex items-start gap-4">
                <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-emerald-400 to-teal-600 flex items-center justify-center text-white text-2xl font-black shadow-lg">
                    {{ strtoupper(substr($student->name, 0, 1)) }}
                </div>
                <div>
                    <h2 class="font-extrabold text-slate-900 text-lg">{{ $student->name }}</h2>
                    <p class="text-sm text-slate-500">{{ $student->college_email }}</p>
                    <span class="inline-block mt-1 px-3 py-1 rounded-full text-xs font-black
                        {{ $student->status === 'approved' ? 'bg-emerald-100 text-emerald-700' : ($student->status === 'rejected' ? 'bg-rose-100 text-rose-700' : 'bg-amber-100 text-amber-700') }}">
                        {{ ucfirst($student->status) }}
                    </span>
                </div>
            </div>
            <div class="flex gap-2 flex-shrink-0">
                @if($student->status !== 'approved')
                <form method="POST" action="{{ route('admin.campus.students.approve', $student) }}">
                    @csrf
                    <button type="submit" class="btn-primary text-xs px-4">✅ Approve</button>
                </form>
                @endif
            </div>
        </div>

        <div class="grid sm:grid-cols-2 gap-3 text-xs">
            @foreach([
                ['College', $student->college_name ?? '-'],
                ['Domain', '@'.$student->getCollegeDomain()],
                ['Student ID', $student->student_id ?? '-'],
                ['Department', $student->department ?? '-'],
                ['Semester', $student->semester ?? '-'],
                ['Phone', $student->phone ?? '-'],
                ['Email Verified', $student->email_verified ? 'Yes ✅' : 'No ❌'],
                ['Registered', $student->created_at->format('d M Y, h:i A')],
                ['Approved', $student->approved_at ? $student->approved_at->format('d M Y') : '-'],
                ['Approved By', $student->approvedBy?->name ?? '-'],
            ] as [$label, $val])
            <div class="flex justify-between p-3 rounded-xl bg-slate-50 dark:bg-slate-800/30">
                <span class="text-slate-500">{{ $label }}</span>
                <span class="font-bold text-slate-800 dark:text-slate-200">{{ $val }}</span>
            </div>
            @endforeach
        </div>

        @if($student->rejection_reason)
        <div class="mt-4 p-3 rounded-xl bg-rose-50 border border-rose-200 text-xs text-rose-700">
            <strong>Rejection Reason:</strong> {{ $student->rejection_reason }}
        </div>
        @endif
    </div>

    {{-- Enrollments --}}
    <div class="glass rounded-2xl p-5">
        <h3 class="font-extrabold text-slate-900 text-sm mb-4">Course Enrollments ({{ $enrollments->count() }})</h3>
        @forelse($enrollments as $e)
        <div class="flex items-center gap-4 p-3 rounded-xl hover:bg-slate-50 transition mb-2">
            <div class="flex-1">
                <p class="text-xs font-bold text-slate-900">{{ $e->course->title }}</p>
                <p class="text-[10px] text-slate-500">{{ $e->course->category }} • Enrolled {{ $e->created_at->diffForHumans() }}</p>
                <div class="flex items-center gap-2 mt-1.5">
                    <div class="flex-1 bg-slate-100 rounded-full h-1.5">
                        <div class="bg-gradient-to-r from-emerald-500 to-teal-500 rounded-full h-1.5" style="width:{{ $e->progress }}%"></div>
                    </div>
                    <span class="text-[10px] font-black text-emerald-600">{{ $e->progress }}%</span>
                </div>
            </div>
            @if($e->progress >= 100)
            <span class="text-[10px] font-black text-emerald-600 bg-emerald-100 px-2 py-0.5 rounded-full flex-shrink-0">✅ Done</span>
            @endif
        </div>
        @empty
        <p class="text-xs text-slate-500 text-center py-4">No courses enrolled</p>
        @endforelse
    </div>

    <div class="flex gap-3">
        <a href="{{ route('admin.campus.students') }}" class="btn-secondary text-xs">← Back to Students</a>
        <form method="POST" action="{{ route('admin.campus.students.delete', $student) }}" onsubmit="return confirm('Delete this student?')">
            @csrf @method('DELETE')
            <button type="submit" class="btn-primary text-xs bg-rose-600 hover:bg-rose-700 from-rose-600 to-rose-700">Delete Student</button>
        </form>
    </div>
</div>
@endsection
