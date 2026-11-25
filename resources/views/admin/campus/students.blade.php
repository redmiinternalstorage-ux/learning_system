@extends('admin.layout')
@section('title','Campus Students')
@section('page-title','Campus Students')

@section('content')
<div class="space-y-5">
    {{-- Filters --}}
    <div class="glass rounded-2xl p-4">
        <form method="GET" class="flex flex-wrap gap-3 items-center">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search name, email, college..." class="inp flex-1 min-w-[200px] h-10 text-xs">
            <select name="status" class="inp w-36 h-10 text-xs font-bold">
                <option value="">All Status</option>
                @foreach(['pending'=>'Pending','approved'=>'Approved','rejected'=>'Rejected'] as $v=>$l)
                <option value="{{ $v }}" {{ request('status')===$v ? 'selected' : '' }}>{{ $l }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn-primary h-10 px-5 text-xs">Filter</button>
            @if(request()->hasAny(['search','status']))
            <a href="{{ route('admin.campus.students') }}" class="btn-secondary h-10 px-4 text-xs flex items-center">Clear</a>
            @endif
        </form>
    </div>

    <div class="glass rounded-2xl overflow-hidden">
        <table class="w-full admin-table">
            <thead>
                <tr>
                    <th class="text-left">Student</th>
                    <th class="text-left hidden md:table-cell">College</th>
                    <th class="text-left hidden lg:table-cell">Department</th>
                    <th class="text-center">Status</th>
                    <th class="text-left hidden sm:table-cell">Registered</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($students as $s)
                <tr>
                    <td>
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-emerald-400 to-teal-500 text-white flex items-center justify-center font-black text-xs flex-shrink-0">
                                {{ strtoupper(substr($s->name, 0, 1)) }}
                            </div>
                            <div>
                                <p class="text-xs font-bold text-slate-900">{{ $s->name }}</p>
                                <p class="text-[10px] text-slate-500">{{ $s->college_email }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="hidden md:table-cell text-xs text-slate-600">{{ $s->college_name ?? '-' }}</td>
                    <td class="hidden lg:table-cell text-xs text-slate-600">{{ $s->department ?? '-' }}</td>
                    <td class="text-center">
                        <span class="badge {{ $s->status === 'approved' ? 'badge-green' : ($s->status === 'rejected' ? 'badge-rose' : 'badge-amber') }}">
                            {{ ucfirst($s->status) }}
                        </span>
                    </td>
                    <td class="text-xs text-slate-500 hidden sm:table-cell">{{ $s->created_at->format('d M Y') }}</td>
                    <td>
                        <div class="flex items-center justify-center gap-1.5">
                            <a href="{{ route('admin.campus.students.show', $s) }}" class="w-8 h-8 rounded-xl bg-slate-100 hover:bg-slate-200 flex items-center justify-center transition" title="View">
                                <i class="fa-solid fa-eye text-slate-600 text-xs"></i>
                            </a>
                            @if($s->status !== 'approved')
                            <form method="POST" action="{{ route('admin.campus.students.approve', $s) }}">
                                @csrf
                                <button type="submit" class="w-8 h-8 rounded-xl bg-emerald-100 hover:bg-emerald-200 flex items-center justify-center transition" title="Approve">
                                    <i class="fa-solid fa-check text-emerald-600 text-xs"></i>
                                </button>
                            </form>
                            @endif
                            @if($s->status !== 'rejected')
                            <button onclick="openReject({{ $s->id }})" class="w-8 h-8 rounded-xl bg-rose-100 hover:bg-rose-200 flex items-center justify-center transition" title="Reject">
                                <i class="fa-solid fa-xmark text-rose-600 text-xs"></i>
                            </button>
                            @endif
                            <form method="POST" action="{{ route('admin.campus.students.delete', $s) }}" onsubmit="return confirm('Delete {{ $s->name }}?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="w-8 h-8 rounded-xl bg-slate-100 hover:bg-red-100 flex items-center justify-center transition" title="Delete">
                                    <i class="fa-solid fa-trash text-slate-400 hover:text-red-500 text-xs"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center text-slate-400 text-sm py-10">No campus students found</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div>{{ $students->links() }}</div>
</div>

{{-- Reject Modal --}}
<div id="reject-modal" class="fixed inset-0 z-50 hidden bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl p-6 max-w-md w-full shadow-2xl">
        <h3 class="font-extrabold text-slate-900 text-base mb-4">Reject Application</h3>
        <form id="reject-form" method="POST">
            @csrf
            <label class="block text-xs font-bold text-slate-500 mb-2">Reason (optional)</label>
            <textarea name="reason" class="inp" rows="3" placeholder="Reason for rejection..."></textarea>
            <div class="flex gap-3 mt-4">
                <button type="submit" class="btn-primary flex-1 text-sm bg-rose-600 hover:bg-rose-700 from-rose-600 to-rose-700">Reject</button>
                <button type="button" onclick="closeReject()" class="btn-secondary flex-1 text-sm">Cancel</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function openReject(id) {
    document.getElementById('reject-form').action = `/admin/campus/students/${id}/reject`;
    document.getElementById('reject-modal').classList.remove('hidden');
}
function closeReject() {
    document.getElementById('reject-modal').classList.add('hidden');
}
</script>
@endpush
@endsection
