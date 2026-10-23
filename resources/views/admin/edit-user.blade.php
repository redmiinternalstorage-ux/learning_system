@extends('admin.layout')
@section('title', 'Edit Profile')
@section('page-title', 'Modify User Profile')

@section('content')
<div class="max-w-lg pt-2">
    <a href="{{ route('admin.users') }}"
       class="inline-flex items-center gap-2 text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 text-xs font-bold mb-6 transition-colors font-outfit">
        <i class="fa-solid fa-arrow-left text-[10px]"></i> Back to Users Directory
    </a>

    <div class="glass-card rounded-3xl p-7 shadow-sm">

        {{-- User Header Details --}}
        <div class="flex items-center gap-4 mb-7 pb-6 border-b border-slate-100 dark:border-slate-800/80">
            <div class="w-14 h-14 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-100 dark:border-emerald-900/30 text-emerald-650 dark:text-emerald-400 flex items-center justify-center font-black text-xl flex-shrink-0 shadow-sm font-outfit">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>
            <div>
                <p class="font-extrabold text-slate-800 dark:text-slate-100 text-base leading-tight font-outfit">{{ $user->name }}</p>
                <p class="text-slate-400 dark:text-slate-500 text-xs font-semibold mt-1">{{ $user->email }}</p>
                <div class="mt-2.5">
                    @if($user->role === 'admin')
                        <span class="px-2.5 py-0.5 rounded-full text-[9px] font-bold uppercase tracking-wider bg-rose-50 dark:bg-rose-950/40 text-rose-700 dark:text-rose-400 border border-rose-100 dark:border-rose-900/30">Admin</span>
                    @elseif($user->role === 'instructor')
                        <span class="px-2.5 py-0.5 rounded-full text-[9px] font-bold uppercase tracking-wider bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-400 border border-emerald-100 dark:border-emerald-900/30">Instructor</span>
                    @else
                        <span class="px-2.5 py-0.5 rounded-full text-[9px] font-bold uppercase tracking-wider bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-400 border border-emerald-100/50 dark:border-emerald-900/30">Student</span>
                    @endif
                </div>
            </div>
        </div>

        {{-- Errors --}}
        @if($errors->any())
        <div class="mb-6 p-4 rounded-2xl bg-rose-50 dark:bg-rose-950/20 border border-rose-100 dark:border-rose-900/30 text-rose-600 dark:text-rose-400 text-xs font-semibold space-y-1.5 animate-slide-in">
            @foreach($errors->all() as $e)
                <p class="flex items-center gap-2">
                    <i class="fa-solid fa-circle-exclamation text-rose-500 text-xs"></i> {{ $e }}
                </p>
            @endforeach
        </div>
        @endif

        <form method="POST" action="{{ route('admin.users.update', $user) }}" class="space-y-5">
            @csrf @method('PUT')

            <div class="space-y-1.5">
                <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Full Name</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                       class="glow-input w-full px-4 py-3 rounded-2xl border border-slate-200 dark:border-slate-800 text-xs text-slate-800 dark:text-slate-100 placeholder-slate-400 focus:outline-none bg-white dark:bg-slate-900/50 transition duration-200 font-medium">
            </div>

            <div class="space-y-1.5">
                <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Email Address</label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                       class="glow-input w-full px-4 py-3 rounded-2xl border border-slate-200 dark:border-slate-800 text-xs text-slate-800 dark:text-slate-100 placeholder-slate-400 focus:outline-none bg-white dark:bg-slate-900/50 transition duration-200 font-medium">
            </div>

            <div class="space-y-1.5">
                <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Account Role</label>
                <select name="role" class="glow-input w-full px-4 py-3 rounded-2xl border border-slate-200 dark:border-slate-800 text-xs focus:outline-none bg-white dark:bg-slate-900/50 text-slate-650 dark:text-slate-300 font-bold transition duration-200">
                    <option value="student"    {{ old('role',$user->role)=='student'    ? 'selected':'' }}>Student</option>
                    <option value="instructor" {{ old('role',$user->role)=='instructor' ? 'selected':'' }}>Instructor</option>
                    <option value="admin"      {{ old('role',$user->role)=='admin'      ? 'selected':'' }}>Admin</option>
                </select>
            </div>

            <div class="space-y-1.5">
                <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                    New Password
                    <span class="text-slate-350 dark:text-slate-500 font-normal normal-case">(leave blank to keep current)</span>
                </label>
                <input type="password" name="password" placeholder="Min. 8 characters"
                       class="glow-input w-full px-4 py-3 rounded-2xl border border-slate-200 dark:border-slate-800 text-xs text-slate-800 dark:text-slate-100 placeholder-slate-400 focus:outline-none bg-white dark:bg-slate-900/50 transition duration-200 font-medium">
            </div>

            <div class="flex gap-3 pt-3">
                <button type="submit"
                        class="flex-1 py-3 rounded-2xl text-xs font-bold text-white bg-gradient-to-r from-rose-500 to-rose-650 hover:from-rose-600 hover:to-rose-700 shadow-md shadow-rose-500/10 hover:shadow-lg transition duration-200 font-outfit">
                    Save Modifications
                </button>
                <a href="{{ route('admin.users') }}"
                   class="px-6 py-3 rounded-2xl border border-slate-200 dark:border-slate-800 text-slate-550 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 text-xs font-bold transition duration-200 font-outfit animate-transition">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
