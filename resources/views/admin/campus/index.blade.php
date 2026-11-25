@extends('admin.layout')
@section('title','Campus Program')
@section('page-title','Campus Learning Program 🎓')

@section('content')
<div class="space-y-6">

    {{-- Header banner --}}
    <div class="rounded-3xl bg-gradient-to-r from-emerald-600 via-teal-600 to-emerald-700 p-6 text-white relative overflow-hidden">
        <div class="absolute right-0 top-0 w-64 h-full opacity-10">
            <i class="fa-solid fa-graduation-cap text-9xl absolute right-6 top-1/2 -translate-y-1/2"></i>
        </div>
        <div class="relative z-10">
            <h2 class="text-xl font-black">Campus Learning Program</h2>
            <p class="text-emerald-200 text-sm mt-1">Manage college students, domains, and approvals</p>
            <div class="flex items-center gap-3 mt-4 flex-wrap">
                <a href="{{ route('admin.campus.students') }}" class="px-4 py-2 rounded-xl bg-white/20 hover:bg-white/30 text-sm font-bold transition">Students</a>
                <a href="{{ route('admin.campus.domains') }}" class="px-4 py-2 rounded-xl bg-white/20 hover:bg-white/30 text-sm font-bold transition">Domains</a>
                <a href="{{ route('admin.campus.analytics') }}" class="px-4 py-2 rounded-xl bg-white/20 hover:bg-white/30 text-sm font-bold transition">Analytics</a>
                <a href="{{ route('campus.home') }}" target="_blank" class="px-4 py-2 rounded-xl bg-white text-emerald-700 font-black text-sm transition hover:bg-emerald-50">
                    <i class="fa-solid fa-external-link mr-1"></i> Campus Portal
                </a>
            </div>
        </div>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        @foreach([
            ['Total Students', $stats['total'],    'fa-users',          'emerald'],
            ['Pending Approval',$stats['pending'], 'fa-clock',          'amber'],
            ['Approved',        $stats['approved'],'fa-circle-check',   'teal'],
            ['Rejected',        $stats['rejected'],'fa-circle-xmark',   'rose'],
        ] as [$label,$val,$icon,$color])
        <div class="glass rounded-2xl p-4 stat-lift cursor-default">
            <div class="flex items-center justify-between mb-3">
                <div class="w-9 h-9 rounded-xl bg-{{ $color }}-100 flex items-center justify-center">
                    <i class="fa-solid {{ $icon }} text-{{ $color }}-600 text-sm"></i>
                </div>
                <span class="text-2xl font-black text-slate-900 font-outfit">{{ $val }}</span>
            </div>
            <p class="text-xs font-bold text-slate-500">{{ $label }}</p>
        </div>
        @endforeach
    </div>

    <div class="grid lg:grid-cols-2 gap-5">
        {{-- Pending students --}}
        <div class="glass rounded-2xl p-5">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-extrabold text-slate-900 text-sm">Pending Approvals</h3>
                <a href="{{ route('admin.campus.students', ['status'=>'pending']) }}" class="text-xs text-emerald-600 font-bold hover:text-emerald-700">View All →</a>
            </div>
            @php $pending = $recent->where('status','pending')->take(5); @endphp
            @forelse($pending as $s)
            <div class="flex items-center justify-between gap-3 p-3 rounded-xl hover:bg-slate-50 transition mb-2">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-amber-400 to-orange-500 text-white flex items-center justify-center font-black text-sm flex-shrink-0">
                        {{ strtoupper(substr($s->name, 0, 1)) }}
                    </div>
                    <div>
                        <p class="text-xs font-bold text-slate-900">{{ $s->name }}</p>
                        <p class="text-[10px] text-slate-500">{{ $s->college_email }}</p>
                    </div>
                </div>
                <div class="flex gap-1.5 flex-shrink-0">
                    <form method="POST" action="{{ route('admin.campus.students.approve', $s) }}">
                        @csrf
                        <button type="submit" class="px-3 py-1.5 rounded-xl bg-emerald-100 hover:bg-emerald-200 text-emerald-700 text-xs font-bold transition">✅ Approve</button>
                    </form>
                    <a href="{{ route('admin.campus.students.show', $s) }}" class="px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-bold transition">View</a>
                </div>
            </div>
            @empty
            <p class="text-xs text-slate-500 text-center py-4">No pending applications 🎉</p>
            @endforelse
        </div>

        {{-- Recent students --}}
        <div class="glass rounded-2xl p-5">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-extrabold text-slate-900 text-sm">Recent Registrations</h3>
                <a href="{{ route('admin.campus.students') }}" class="text-xs text-emerald-600 font-bold hover:text-emerald-700">View All →</a>
            </div>
            @forelse($recent->take(6) as $s)
            <div class="flex items-center justify-between gap-3 p-3 rounded-xl hover:bg-slate-50 transition mb-1">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-xl bg-gradient-to-br from-emerald-400 to-teal-500 text-white flex items-center justify-center font-black text-xs flex-shrink-0">
                        {{ strtoupper(substr($s->name, 0, 1)) }}
                    </div>
                    <div>
                        <p class="text-xs font-bold text-slate-900">{{ $s->name }}</p>
                        <p class="text-[10px] text-slate-500">{{ $s->created_at->diffForHumans() }}</p>
                    </div>
                </div>
                <span class="px-2 py-0.5 rounded-full text-[9px] font-black
                    {{ $s->status === 'approved' ? 'bg-emerald-100 text-emerald-700' : ($s->status === 'rejected' ? 'bg-rose-100 text-rose-700' : 'bg-amber-100 text-amber-700') }}">
                    {{ ucfirst($s->status) }}
                </span>
            </div>
            @empty
            <p class="text-xs text-slate-500 text-center py-4">No students yet</p>
            @endforelse
        </div>
    </div>

    {{-- Quick links --}}
    <div class="grid sm:grid-cols-3 gap-4">
        <a href="{{ route('admin.campus.students', ['status'=>'pending']) }}" class="glass rounded-2xl p-4 flex items-center gap-3 hover:border-amber-300 transition">
            <div class="w-10 h-10 rounded-xl bg-amber-100 flex items-center justify-center"><i class="fa-solid fa-clock text-amber-600"></i></div>
            <div><p class="font-bold text-slate-900 text-sm">{{ $stats['pending'] }} Pending</p><p class="text-xs text-slate-500">Need your approval</p></div>
        </a>
        <a href="{{ route('admin.campus.domains') }}" class="glass rounded-2xl p-4 flex items-center gap-3 hover:border-teal-300 transition">
            <div class="w-10 h-10 rounded-xl bg-teal-100 flex items-center justify-center"><i class="fa-solid fa-at text-teal-600"></i></div>
            <div><p class="font-bold text-slate-900 text-sm">{{ $stats['domains'] }} Domains</p><p class="text-xs text-slate-500">Allowed college emails</p></div>
        </a>
        <a href="{{ route('admin.campus.analytics') }}" class="glass rounded-2xl p-4 flex items-center gap-3 hover:border-purple-300 transition">
            <div class="w-10 h-10 rounded-xl bg-purple-100 flex items-center justify-center"><i class="fa-solid fa-chart-pie text-purple-600"></i></div>
            <div><p class="font-bold text-slate-900 text-sm">{{ $stats['enrolled'] }} Enrollments</p><p class="text-xs text-slate-500">{{ $stats['completed'] }} completed</p></div>
        </a>
    </div>
</div>
@endsection
