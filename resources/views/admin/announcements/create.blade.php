@extends('admin.layout')
@section('title', 'Create Announcement')
@section('page-title', 'Create Announcement')

@section('content')
<div class="max-w-2xl">
    <a href="{{ route('admin.announcements') }}"
       class="inline-flex items-center gap-2 text-slate-400 hover:text-purple-600 dark:hover:text-purple-400 text-xs font-bold mb-5 transition">
        <i class="fa-solid fa-arrow-left text-[10px]"></i> Back to Announcements
    </a>

    <div class="glass rounded-3xl p-7">

        {{-- Header --}}
        <div class="flex items-center gap-3 mb-6 pb-5 border-b border-slate-100 dark:border-white/5">
            <div class="w-11 h-11 rounded-2xl bg-gradient-to-br from-amber-500 to-orange-600 flex items-center justify-center text-white text-base shadow-lg shadow-amber-500/25">
                <i class="fa-solid fa-bullhorn"></i>
            </div>
            <div>
                <h2 class="font-extrabold text-slate-800 dark:text-slate-100 text-sm font-outfit">New Announcement</h2>
                <p class="text-slate-400 text-xs mt-0.5">Broadcast a message to all users</p>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.announcements.store') }}" class="space-y-4">
            @csrf

            <div class="space-y-1.5">
                <label class="block text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Title *</label>
                <input type="text" name="title" value="{{ old('title') }}" required class="inp"
                       placeholder="Announcement title...">
                @error('title')<p class="text-rose-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="space-y-1.5">
                <label class="block text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Body *</label>
                <textarea name="body" rows="5" class="inp resize-none"
                          placeholder="Announcement content...">{{ old('body') }}</textarea>
                @error('body')<p class="text-rose-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <label class="flex items-center gap-3 p-4 rounded-2xl border border-slate-200 dark:border-white/8 cursor-pointer hover:bg-slate-50 dark:hover:bg-white/[0.02] transition">
                <div class="relative">
                    <input type="checkbox" name="is_active" id="is_active" value="1" checked class="sr-only peer">
                    <div class="w-11 h-6 bg-slate-200 dark:bg-slate-700 rounded-full peer-checked:bg-gradient-to-r peer-checked:from-emerald-500 peer-checked:to-teal-600 transition-all"></div>
                    <div class="absolute top-0.5 left-0.5 w-5 h-5 bg-white rounded-full shadow transition-all peer-checked:translate-x-5"></div>
                </div>
                <div>
                    <p class="text-xs font-bold text-slate-700 dark:text-slate-200">Active (visible to users)</p>
                    <p class="text-[10px] text-slate-400 mt-0.5">Toggle off to hide this announcement</p>
                </div>
            </label>

            <div class="flex gap-3 pt-2">
                <button type="submit" class="btn-primary flex-1">
                    <i class="fa-solid fa-bullhorn"></i> Create Announcement
                </button>
                <a href="{{ route('admin.announcements') }}" class="btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
