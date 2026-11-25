@extends('admin.layout')
@section('title','Campus Domains')
@section('page-title','College Email Domains 🏫')

@section('content')
<div class="space-y-5">

    {{-- Add Domain --}}
    <div class="glass rounded-2xl p-5">
        <h3 class="font-extrabold text-slate-900 text-sm mb-4">Add Allowed College Domain</h3>
        <form method="POST" action="{{ route('admin.campus.domains.store') }}" class="flex flex-wrap gap-3 items-end">
            @csrf
            <div class="flex-1 min-w-[200px]">
                <label class="block text-xs font-bold text-slate-500 mb-1.5">Email Domain <span class="text-slate-400">(e.g. iit.ac.in)</span></label>
                <div class="relative">
                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs font-bold">@</span>
                    <input type="text" name="domain" placeholder="college.ac.in" class="inp pl-7" required pattern="[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}">
                </div>
                @error('domain')<p class="text-rose-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div class="flex-1 min-w-[200px]">
                <label class="block text-xs font-bold text-slate-500 mb-1.5">College Name</label>
                <input type="text" name="college_name" placeholder="IIT Bombay" class="inp" required>
                @error('college_name')<p class="text-rose-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <button type="submit" class="btn-primary h-10 px-6 text-xs whitespace-nowrap">
                <i class="fa-solid fa-plus"></i> Add Domain
            </button>
        </form>
        <p class="text-xs text-slate-400 mt-2">Students with emails from these domains can register for the Campus Learning Program.</p>
    </div>

    {{-- Domains table --}}
    <div class="glass rounded-2xl overflow-hidden">
        <table class="w-full admin-table">
            <thead>
                <tr>
                    <th class="text-left">#</th>
                    <th class="text-left">Domain</th>
                    <th class="text-left">College Name</th>
                    <th class="text-center hidden sm:table-cell">Students</th>
                    <th class="text-center">Status</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($domains as $d)
                <tr>
                    <td class="text-xs text-slate-400">{{ $d->id }}</td>
                    <td>
                        <span class="font-bold text-emerald-700 font-mono text-xs bg-emerald-50 px-2.5 py-1 rounded-lg">@{{ $d->domain }}</span>
                    </td>
                    <td class="font-semibold text-slate-900 text-xs">{{ $d->college_name }}</td>
                    <td class="text-center hidden sm:table-cell">
                        @php $count = \App\Models\CampusStudent::where('college_email','like','%@'.$d->domain)->count(); @endphp
                        <span class="text-xs font-black text-emerald-700 bg-emerald-100 px-2 py-0.5 rounded-full">{{ $count }}</span>
                    </td>
                    <td class="text-center">
                        <span class="badge {{ $d->is_active ? 'badge-green' : 'badge-rose' }}">{{ $d->is_active ? 'Active' : 'Disabled' }}</span>
                    </td>
                    <td>
                        <div class="flex items-center justify-center gap-1.5">
                            <form method="POST" action="{{ route('admin.campus.domains.toggle', $d) }}">
                                @csrf
                                <button type="submit" class="w-8 h-8 rounded-xl {{ $d->is_active ? 'bg-amber-100 hover:bg-amber-200' : 'bg-emerald-100 hover:bg-emerald-200' }} flex items-center justify-center transition" title="{{ $d->is_active ? 'Disable' : 'Enable' }}">
                                    <i class="fa-solid {{ $d->is_active ? 'fa-ban text-amber-600' : 'fa-check text-emerald-600' }} text-xs"></i>
                                </button>
                            </form>
                            <form method="POST" action="{{ route('admin.campus.domains.delete', $d) }}" onsubmit="return confirm('Delete @{{ $d->domain }}?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="w-8 h-8 rounded-xl bg-slate-100 hover:bg-red-100 flex items-center justify-center transition" title="Delete">
                                    <i class="fa-solid fa-trash text-slate-400 hover:text-red-500 text-xs"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center text-slate-400 text-sm py-10">No domains added yet. Add your first college domain above.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div>{{ $domains->links() }}</div>
</div>
@endsection
