@extends('campus.layout')
@section('title','Profile')
@section('page-title','My Profile ⚙️')

@section('content')
<div class="max-w-2xl space-y-5">

    {{-- Profile card --}}
    <div class="glass-card p-6">
        <div class="flex items-center gap-4 mb-6">
            <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-emerald-400 to-teal-600 flex items-center justify-center text-white text-2xl font-black shadow-lg shadow-emerald-500/20">
                {{ strtoupper(substr($student->name, 0, 1)) }}
            </div>
            <div>
                <h2 class="font-extrabold text-slate-900 dark:text-white text-lg">{{ $student->name }}</h2>
                <p class="text-sm text-slate-500">{{ $student->college_email }}</p>
                <div class="flex items-center gap-2 mt-1">
                    <span class="px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-700 text-[9px] font-black">✅ Approved</span>
                    @if($student->college_name)<span class="text-[10px] text-slate-500">{{ $student->college_name }}</span>@endif
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('campus.profile.update') }}">
            @csrf
            <div class="grid sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-500 mb-1.5">Full Name</label>
                    <input type="text" name="name" value="{{ $student->name }}" class="inp" required>
                    @error('name')<p class="text-rose-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 mb-1.5">College Email</label>
                    <input type="email" value="{{ $student->college_email }}" class="inp bg-slate-50 cursor-not-allowed" disabled>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 mb-1.5">Student ID / Roll No.</label>
                    <input type="text" name="student_id" value="{{ $student->student_id }}" class="inp" placeholder="Your roll number">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 mb-1.5">Department</label>
                    <input type="text" name="department" value="{{ $student->department }}" class="inp" placeholder="Computer Science">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 mb-1.5">Semester</label>
                    <input type="text" name="semester" value="{{ $student->semester }}" class="inp" placeholder="3rd Semester">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 mb-1.5">Phone</label>
                    <input type="text" name="phone" value="{{ $student->phone }}" class="inp" placeholder="Your phone">
                </div>
            </div>

            <div class="mt-5 pt-5 border-t border-slate-100 dark:border-slate-700/50">
                <h3 class="font-extrabold text-slate-900 dark:text-white text-sm mb-3">Change Password</h3>
                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-1.5">New Password</label>
                        <input type="password" name="password" class="inp" placeholder="Leave blank to keep current">
                        @error('password')<p class="text-rose-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-1.5">Confirm Password</label>
                        <input type="password" name="password_confirmation" class="inp" placeholder="Confirm new password">
                    </div>
                </div>
            </div>

            <button type="submit" class="btn-green mt-5">
                <i class="fa-solid fa-floppy-disk"></i> Save Changes
            </button>
        </form>
    </div>

    {{-- Info --}}
    <div class="glass-card p-5">
        <h3 class="font-extrabold text-slate-900 dark:text-white text-sm mb-3">Account Info</h3>
        <div class="grid sm:grid-cols-2 gap-3 text-xs">
            <div class="flex justify-between p-3 rounded-xl bg-slate-50 dark:bg-slate-800/30"><span class="text-slate-500">Status</span><span class="font-black text-emerald-600">Approved ✅</span></div>
            <div class="flex justify-between p-3 rounded-xl bg-slate-50 dark:bg-slate-800/30"><span class="text-slate-500">Joined</span><span class="font-bold text-slate-700 dark:text-slate-300">{{ $student->created_at->format('d M Y') }}</span></div>
            <div class="flex justify-between p-3 rounded-xl bg-slate-50 dark:bg-slate-800/30"><span class="text-slate-500">Approved</span><span class="font-bold text-slate-700 dark:text-slate-300">{{ $student->approved_at ? $student->approved_at->format('d M Y') : '-' }}</span></div>
            <div class="flex justify-between p-3 rounded-xl bg-slate-50 dark:bg-slate-800/30"><span class="text-slate-500">Domain</span><span class="font-bold text-slate-700 dark:text-slate-300">@{{ $student->getCollegeDomain() }}</span></div>
        </div>
    </div>
</div>
@endsection
