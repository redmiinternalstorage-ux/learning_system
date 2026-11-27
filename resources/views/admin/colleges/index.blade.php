@extends('admin.layout')
@section('title','College Accounts')
@section('page-title','College Admin Accounts')

@section('content')
<div class="space-y-5">
    <div class="glass rounded-2xl p-4 flex items-center justify-between gap-3">
        <div>
            <p class="font-bold text-slate-800 text-sm">Registered Colleges</p>
            <p class="text-slate-400 text-xs">Manage college admin accounts and their student access</p>
        </div>
        <a href="{{ route('admin.colleges.create') }}" class="btn-primary text-xs">
            <i class="fa-solid fa-plus"></i> Add College
        </a>
    </div>

    <div class="glass rounded-3xl overflow-hidden border border-emerald-100">
        <div class="overflow-x-auto">
            <table class="admin-table w-full">
                <thead>
                    <tr>
                        <th class="text-left">College</th>
                        <th class="text-left">Email</th>
                        <th class="text-left">Contact</th>
                        <th class="text-left">Students</th>
                        <th class="text-left">Status</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($colleges as $c)
                    <tr>
                        <td>
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-xl bg-gradient-to-br from-indigo-500 to-violet-600 text-white flex items-center justify-center text-xs font-bold">
                                    {{ strtoupper(substr($c->college_name,0,1)) }}
                                </div>
                                <div>
                                    <p class="font-bold text-slate-800 text-xs">{{ $c->college_name }}</p>
                                    <p class="text-[10px] text-slate-400">{{ $c->domain ?? 'No domain' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="text-slate-500 text-xs">{{ $c->email }}</td>
                        <td class="text-slate-500 text-xs">{{ $c->contact_person ?? '—' }}</td>
                        <td><span class="font-bold text-indigo-600 text-xs">{{ $c->students_count }}</span></td>
                        <td>
                            @if($c->status==='active') <span class="badge badge-green">Active</span>
                            @elseif($c->status==='pending') <span class="badge badge-amber">Pending</span>
                            @else <span class="badge badge-rose">Inactive</span>
                            @endif
                        </td>
                        <td>
                            <div class="flex items-center justify-end gap-2.5">
                                <form method="POST" action="{{ route('admin.colleges.toggle', $c) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="text-[11px] font-bold {{ $c->isActive() ? 'text-amber-600 hover:text-amber-800' : 'text-emerald-600 hover:text-emerald-800' }} transition">
                                        {{ $c->isActive() ? 'Deactivate' : 'Activate' }}
                                    </button>
                                </form>
                                <a href="{{ route('admin.colleges.edit', $c) }}" class="text-[11px] font-bold text-teal-700 hover:text-teal-900 transition">Edit</a>
                                <form method="POST" action="{{ route('admin.colleges.destroy', $c) }}" onsubmit="return confirm('Delete {{ addslashes($c->college_name) }}?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-[11px] font-bold text-rose-600 hover:text-rose-800 transition">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-14 text-center">
                            <i class="fa-solid fa-building-columns text-3xl text-slate-200 mb-3 block"></i>
                            <p class="text-slate-400 text-xs">No colleges registered yet.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($colleges->hasPages())
        <div class="px-6 py-4 border-t border-slate-100 flex items-center justify-between text-xs font-bold text-slate-500 bg-slate-50">
            <span>Showing {{ $colleges->firstItem() }}–{{ $colleges->lastItem() }} of {{ $colleges->total() }}</span>
            <div class="flex gap-2">
                @if(!$colleges->onFirstPage())
                    <a href="{{ $colleges->previousPageUrl() }}" class="px-3 py-1.5 rounded-xl border border-slate-200 hover:bg-slate-100 transition">← Prev</a>
                @endif
                @if($colleges->hasMorePages())
                    <a href="{{ $colleges->nextPageUrl() }}" class="px-3 py-1.5 rounded-xl border border-slate-200 hover:bg-slate-100 transition">Next →</a>
                @endif
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
