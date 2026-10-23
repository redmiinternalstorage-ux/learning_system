@extends('admin.layout')
@section('title', 'Announcements')
@section('page-title', 'Announcements')

@section('content')
<div class="space-y-5">

    {{-- Toolbar --}}
    <div class="flex items-center justify-between">
        <p class="text-slate-400 text-[10px] font-bold uppercase tracking-widest">{{ $announcements->total() }} total</p>
        <a href="{{ route('admin.announcements.create') }}" class="btn-primary">
            <i class="fa-solid fa-plus"></i> Create New
        </a>
    </div>

    {{-- Table --}}
    <div class="glass rounded-3xl overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 dark:border-white/5 flex items-center justify-between">
            <div>
                <h3 class="font-extrabold text-slate-800 dark:text-slate-100 text-sm font-outfit">All Announcements</h3>
                <p class="text-slate-400 text-[10px] font-bold uppercase tracking-widest mt-0.5">Platform-wide notices</p>
            </div>
            <div class="w-10 h-10 rounded-2xl bg-gradient-to-br from-amber-500 to-orange-600 flex items-center justify-center text-white shadow-md shadow-amber-500/25">
                <i class="fa-solid fa-bullhorn text-sm"></i>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="admin-table w-full">
                <thead>
                    <tr>
                        <th class="text-left">Title</th>
                        <th class="text-left">Preview</th>
                        <th class="text-left">Status</th>
                        <th class="text-left">Created</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($announcements as $ann)
                    <tr>
                        <td>
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-xl bg-gradient-to-br from-amber-400 to-orange-500 flex items-center justify-center text-white flex-shrink-0">
                                    <i class="fa-solid fa-bullhorn text-[10px]"></i>
                                </div>
                                <p class="font-bold text-slate-800 dark:text-slate-200 text-xs">{{ $ann->title }}</p>
                            </div>
                        </td>
                        <td class="text-slate-500 dark:text-slate-400 font-medium max-w-xs truncate">{{ Str::limit($ann->body, 60) }}</td>
                        <td>
                            @if($ann->is_active)
                                <span class="badge badge-green"><i class="fa-solid fa-circle text-[6px]"></i> Active</span>
                            @else
                                <span class="badge badge-gray">Inactive</span>
                            @endif
                        </td>
                        <td class="text-slate-400 font-medium">{{ $ann->created_at->format('d M Y') }}</td>
                        <td>
                            <div class="flex items-center justify-end gap-3">
                                <form method="POST" action="{{ route('admin.announcements.toggle', $ann) }}" class="inline">
                                    @csrf
                                    <button type="submit"
                                            class="text-[11px] font-bold transition {{ $ann->is_active ? 'text-orange-500 hover:text-orange-700' : 'text-emerald-600 hover:text-emerald-800' }}">
                                        {{ $ann->is_active ? 'Deactivate' : 'Activate' }}
                                    </button>
                                </form>
                                <a href="{{ route('admin.announcements.edit', $ann) }}"
                                   class="text-[11px] font-bold text-amber-600 dark:text-amber-400 hover:text-amber-800 transition flex items-center gap-1">
                                    <i class="fa-solid fa-pen text-[9px]"></i> Edit
                                </a>
                                <form method="POST" action="{{ route('admin.announcements.destroy', $ann) }}"
                                      onsubmit="return confirm('Delete this announcement?')" class="inline">
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
                        <td colspan="5" class="py-14 text-center">
                            <i class="fa-solid fa-bullhorn text-3xl text-slate-200 dark:text-slate-700 mb-3 block"></i>
                            <p class="text-slate-400 text-xs">No announcements yet.
                                <a href="{{ route('admin.announcements.create') }}" class="text-purple-500 font-bold hover:underline">Create one!</a>
                            </p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($announcements->hasPages())
        <div class="px-6 py-4 border-t border-slate-100 dark:border-white/5 flex items-center justify-between text-[11px] font-bold text-slate-400 bg-slate-50/30 dark:bg-white/[0.01]">
            <span>{{ $announcements->firstItem() }}–{{ $announcements->lastItem() }} of {{ $announcements->total() }}</span>
            <div class="flex gap-2">
                @if($announcements->onFirstPage())
                    <span class="px-3 py-1.5 rounded-xl border border-slate-100 dark:border-white/5 text-slate-300 cursor-not-allowed">← Prev</span>
                @else
                    <a href="{{ $announcements->previousPageUrl() }}" class="px-3 py-1.5 rounded-xl border border-slate-200 dark:border-white/10 text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-white/5 transition">← Prev</a>
                @endif
                @if($announcements->hasMorePages())
                    <a href="{{ $announcements->nextPageUrl() }}" class="px-3 py-1.5 rounded-xl border border-slate-200 dark:border-white/10 text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-white/5 transition">Next →</a>
                @else
                    <span class="px-3 py-1.5 rounded-xl border border-slate-100 dark:border-white/5 text-slate-300 cursor-not-allowed">Next →</span>
                @endif
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
