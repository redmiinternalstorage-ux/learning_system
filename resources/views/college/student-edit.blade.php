@extends('college.layout')
@section('title','Edit Student')
@section('page-title','Edit Student')

@section('content')
<div class="max-w-lg">
    <a href="{{ route('college.students') }}" class="inline-flex items-center gap-2 text-slate-400 hover:text-slate-700 text-xs font-bold mb-5 transition">
        <i class="fa-solid fa-arrow-left text-[10px]"></i> Back to Students
    </a>

    <div class="glass rounded-3xl p-6 space-y-5">
        <div class="flex items-center gap-4 pb-5 border-b border-slate-100">
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-indigo-500 to-violet-600 text-white flex items-center justify-center font-black text-xl font-outfit">
                {{ strtoupper(substr($student->name,0,1)) }}
            </div>
            <div>
                <p class="font-extrabold text-slate-800 text-sm">{{ $student->name }}</p>
                <p class="text-slate-400 text-xs">{{ $student->college_email }}</p>
            </div>
        </div>

        <form method="POST" action="{{ route('college.students.update', $student) }}" class="space-y-4">
            @csrf @method('PUT')
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-1.5">Full Name</label>
                    <input type="text" name="name" value="{{ old('name', $student->name) }}" required class="inp text-xs">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-1.5">Student ID</label>
                    <input type="text" name="student_id" value="{{ old('student_id', $student->student_id) }}" class="inp text-xs">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-1.5">Department</label>
                    <input type="text" name="department" value="{{ old('department', $student->department) }}" class="inp text-xs">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-1.5">Semester</label>
                    <input type="text" name="semester" value="{{ old('semester', $student->semester) }}" class="inp text-xs">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-1.5">Phone</label>
                    <input type="text" name="phone" value="{{ old('phone', $student->phone) }}" class="inp text-xs">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-1.5">Status</label>
                    <select name="status" class="inp text-xs font-bold">
                        <option value="approved" {{ $student->status==='approved' ? 'selected':'' }}>Approved</option>
                        <option value="pending"  {{ $student->status==='pending'  ? 'selected':'' }}>Pending</option>
                        <option value="rejected" {{ $student->status==='rejected' ? 'selected':'' }}>Rejected</option>
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-600 mb-1.5">New Password <span class="text-slate-400 font-normal">(leave blank to keep current)</span></label>
                <input type="password" name="password" class="inp text-xs" placeholder="Min 6 characters">
            </div>
            @if($errors->any())
            <div class="p-3 rounded-xl bg-rose-50 border border-rose-200 text-rose-600 text-xs">
                @foreach($errors->all() as $e)<p>{{ $e }}</p>@endforeach
            </div>
            @endif
            <div class="flex gap-2 pt-2">
                <button type="submit" class="btn-primary flex-1 text-xs"><i class="fa-solid fa-save"></i> Save Changes</button>
                <a href="{{ route('college.students') }}" class="btn-secondary text-xs px-4">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
