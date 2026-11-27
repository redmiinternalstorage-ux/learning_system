@extends('college.layout')
@section('title','Students')
@section('page-title','Student Management')

@section('content')
<div class="space-y-5">

    {{-- Top bar: search + add --}}
    <div class="glass rounded-2xl p-4 flex flex-col sm:flex-row gap-3 items-stretch sm:items-center justify-between">
        <form method="GET" action="{{ route('college.students') }}" class="flex flex-wrap gap-2 flex-1">
            <div class="relative flex-1 min-w-[180px]">
                <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Name, email, student ID..."
                       class="inp pl-10 text-xs h-9">
            </div>
            <select name="status" class="inp text-xs h-9 w-36 font-bold">
                <option value="">All Statuses</option>
                <option value="approved"  {{ request('status')==='approved'  ? 'selected':'' }}>Approved</option>
                <option value="pending"   {{ request('status')==='pending'   ? 'selected':'' }}>Pending</option>
                <option value="rejected"  {{ request('status')==='rejected'  ? 'selected':'' }}>Rejected</option>
            </select>
            @if($departments->count())
            <select name="department" class="inp text-xs h-9 w-36 font-bold">
                <option value="">All Depts</option>
                @foreach($departments as $d)
                <option value="{{ $d }}" {{ request('department')===$d ? 'selected':'' }}>{{ $d }}</option>
                @endforeach
            </select>
            @endif
            <button type="submit" class="btn-primary h-9 px-4 text-xs">
                <i class="fa-solid fa-filter"></i>
            </button>
            @if(request()->hasAny(['search','status','department']))
            <a href="{{ route('college.students') }}" class="btn-secondary h-9 px-3 text-xs">
                <i class="fa-solid fa-xmark"></i>
            </a>
            @endif
        </form>
        <div class="flex gap-2">
            <button onclick="document.getElementById('add-modal').classList.remove('hidden')"
                    class="btn-primary text-xs h-9 px-4 whitespace-nowrap">
                <i class="fa-solid fa-user-plus"></i> Add Student
            </button>
            <button onclick="document.getElementById('import-modal').classList.remove('hidden')"
                    class="btn-secondary text-xs h-9 px-3 whitespace-nowrap">
                <i class="fa-solid fa-file-arrow-up"></i> Import CSV
            </button>
        </div>
    </div>

    {{-- Table --}}
    <div class="glass rounded-2xl overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="font-extrabold text-slate-800 text-sm font-outfit">Students ({{ $students->total() }})</h3>
                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mt-0.5">{{ $college->college_name }}</p>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="admin-table w-full">
                <thead>
                    <tr>
                        <th class="text-left">Student</th>
                        <th class="text-left">Email</th>
                        <th class="text-left">Department</th>
                        <th class="text-left">Semester</th>
                        <th class="text-left">Status</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($students as $s)
                    <tr>
                        <td>
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-xl bg-gradient-to-br from-indigo-500 to-violet-600 text-white flex items-center justify-center text-xs font-bold flex-shrink-0">
                                    {{ strtoupper(substr($s->name,0,1)) }}
                                </div>
                                <div>
                                    <p class="font-bold text-slate-800 text-xs">{{ $s->name }}</p>
                                    <p class="text-[10px] text-slate-400">{{ $s->student_id ?? '—' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="text-slate-500 text-xs">{{ $s->college_email }}</td>
                        <td class="text-slate-500 text-xs">{{ $s->department ?? '—' }}</td>
                        <td class="text-slate-500 text-xs">{{ $s->semester ?? '—' }}</td>
                        <td>
                            @if($s->status === 'approved') <span class="badge badge-green">Approved</span>
                            @elseif($s->status === 'pending') <span class="badge badge-amber">Pending</span>
                            @else <span class="badge badge-rose">Rejected</span>
                            @endif
                        </td>
                        <td>
                            <div class="flex items-center justify-end gap-2.5">
                                @if($s->status !== 'approved')
                                <form method="POST" action="{{ route('college.students.status', $s) }}" class="inline">
                                    @csrf
                                    <input type="hidden" name="status" value="approved">
                                    <button type="submit" class="text-[11px] font-bold text-emerald-600 hover:text-emerald-800 transition">Approve</button>
                                </form>
                                @endif
                                @if($s->status !== 'rejected')
                                <form method="POST" action="{{ route('college.students.status', $s) }}" class="inline">
                                    @csrf
                                    <input type="hidden" name="status" value="rejected">
                                    <button type="submit" class="text-[11px] font-bold text-amber-600 hover:text-amber-800 transition">Reject</button>
                                </form>
                                @endif
                                <a href="{{ route('college.students.edit', $s) }}" class="text-[11px] font-bold text-indigo-600 hover:text-indigo-800 transition">Edit</a>
                                <a href="{{ route('college.students.courses', $s) }}" class="text-[11px] font-bold text-teal-600 hover:text-teal-800 transition">Courses</a>
                                <form method="POST" action="{{ route('college.students.delete', $s) }}" onsubmit="return confirm('Remove {{ addslashes($s->name) }}?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-[11px] font-bold text-rose-500 hover:text-rose-700 transition">Remove</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-14 text-center">
                            <i class="fa-solid fa-users-slash text-3xl text-slate-200 mb-3 block"></i>
                            <p class="text-slate-400 text-xs">No students found.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($students->hasPages())
        <div class="px-5 py-4 border-t border-slate-100 flex items-center justify-between text-xs font-bold text-slate-500 bg-slate-50">
            <span>{{ $students->firstItem() }}–{{ $students->lastItem() }} of {{ $students->total() }}</span>
            <div class="flex gap-2">
                @if(!$students->onFirstPage())
                <a href="{{ $students->previousPageUrl() }}" class="px-3 py-1.5 rounded-xl border border-slate-200 hover:bg-slate-100 transition">← Prev</a>
                @endif
                @if($students->hasMorePages())
                <a href="{{ $students->nextPageUrl() }}" class="px-3 py-1.5 rounded-xl border border-slate-200 hover:bg-slate-100 transition">Next →</a>
                @endif
            </div>
        </div>
        @endif
    </div>
</div>

{{-- Add Student Modal --}}
<div id="add-modal" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl shadow-2xl w-full max-w-lg p-6 space-y-4">
        <div class="flex items-center justify-between">
            <h3 class="font-extrabold text-slate-800 text-base font-outfit">Add New Student</h3>
            <button onclick="document.getElementById('add-modal').classList.add('hidden')" class="text-slate-400 hover:text-slate-700">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>
        <form method="POST" action="{{ route('college.students.add') }}" class="space-y-3">
            @csrf
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-1">Full Name *</label>
                    <input type="text" name="name" required class="inp text-xs" placeholder="Student name">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-1">Student ID</label>
                    <input type="text" name="student_id" class="inp text-xs" placeholder="Roll / Enroll no.">
                </div>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-600 mb-1">College Email *</label>
                <input type="email" name="college_email" required class="inp text-xs" placeholder="student@college.edu">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-1">Department</label>
                    <input type="text" name="department" class="inp text-xs" placeholder="CS, ECE, MBA...">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-1">Semester</label>
                    <input type="text" name="semester" class="inp text-xs" placeholder="3rd Sem">
                </div>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-600 mb-1">Initial Password *</label>
                <input type="text" name="password" required class="inp text-xs" value="college@123">
                <p class="text-[9px] text-slate-400 mt-1">Student can change this after first login via Campus Portal</p>
            </div>
            <div class="flex gap-2 pt-2">
                <button type="submit" class="btn-primary flex-1 text-xs"><i class="fa-solid fa-user-plus"></i> Add Student</button>
                <button type="button" onclick="document.getElementById('add-modal').classList.add('hidden')" class="btn-secondary text-xs px-4">Cancel</button>
            </div>
        </form>
    </div>
</div>

{{-- Import CSV Modal --}}
<div id="import-modal" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl shadow-2xl w-full max-w-md p-6 space-y-4">
        <div class="flex items-center justify-between">
            <h3 class="font-extrabold text-slate-800 text-base font-outfit">Import Students (CSV)</h3>
            <button onclick="document.getElementById('import-modal').classList.add('hidden')" class="text-slate-400 hover:text-slate-700">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>
        <div class="p-3.5 rounded-2xl bg-indigo-50 border border-indigo-100 text-xs text-indigo-700 space-y-1">
            <p class="font-bold">CSV Format (required headers):</p>
            <p class="font-mono text-[10px]">name, email, student_id, department, semester, phone, password</p>
            <p class="text-indigo-500 mt-1">Password column is optional — defaults to <code class="bg-white px-1 rounded">college@123</code></p>
        </div>
        <form method="POST" action="{{ route('college.students.import') }}" enctype="multipart/form-data" class="space-y-3">
            @csrf
            <div>
                <label class="block text-xs font-bold text-slate-600 mb-1.5">Select CSV File</label>
                <input type="file" name="csv_file" accept=".csv,.txt" required
                       class="block w-full text-xs text-slate-600 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:bg-indigo-50 file:text-indigo-700 file:font-bold hover:file:bg-indigo-100 cursor-pointer">
            </div>
            <div class="flex gap-2">
                <button type="submit" class="btn-primary flex-1 text-xs"><i class="fa-solid fa-file-import"></i> Import</button>
                <button type="button" onclick="document.getElementById('import-modal').classList.add('hidden')" class="btn-secondary text-xs px-4">Cancel</button>
            </div>
        </form>
    </div>
</div>
@endsection
