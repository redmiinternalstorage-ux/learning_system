@extends('college.layout')
@section('title','Dashboard')
@section('page-title','College Dashboard')

@section('content')
<div class="space-y-5">

    {{-- Welcome bar --}}
    <div class="rounded-3xl p-6 text-white relative overflow-hidden" style="background:linear-gradient(135deg,#4f46e5,#7c3aed)">
        <div class="absolute right-0 bottom-0 opacity-10 text-[180px] leading-none hidden md:block">
            <i class="fa-solid fa-building-columns"></i>
        </div>
        <div class="relative z-10">
            <span class="bg-white/20 text-white text-[10px] font-bold px-3 py-1 rounded-full uppercase tracking-wider">College Admin Panel</span>
            <h2 class="text-2xl font-black mt-3 font-outfit">{{ $college->college_name }}</h2>
            <p class="text-indigo-200 text-sm mt-1">{{ $college->contact_person ?? 'College Administrator' }} • Manage your students and course access</p>
        </div>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        @foreach([
            ['val'=>$stats['total'],    'label'=>'Total Students', 'icon'=>'fa-users',         'color'=>'text-indigo-600', 'bg'=>'bg-indigo-50'],
            ['val'=>$stats['approved'], 'label'=>'Approved',       'icon'=>'fa-circle-check',  'color'=>'text-emerald-600','bg'=>'bg-emerald-50'],
            ['val'=>$stats['pending'],  'label'=>'Pending',        'icon'=>'fa-clock',         'color'=>'text-amber-600', 'bg'=>'bg-amber-50'],
            ['val'=>$stats['rejected'], 'label'=>'Rejected',       'icon'=>'fa-circle-xmark',  'color'=>'text-rose-600',  'bg'=>'bg-rose-50'],
        ] as $s)
        <div class="glass rounded-2xl p-5 flex items-center gap-4">
            <div class="w-11 h-11 rounded-xl {{ $s['bg'] }} flex items-center justify-center flex-shrink-0">
                <i class="fa-solid {{ $s['icon'] }} {{ $s['color'] }} text-lg"></i>
            </div>
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">{{ $s['label'] }}</p>
                <p class="text-2xl font-black text-slate-800 font-outfit">{{ $s['val'] }}</p>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Quick Actions --}}
    <div class="glass rounded-2xl p-5">
        <h3 class="font-extrabold text-slate-800 text-sm mb-4 font-outfit">Quick Actions</h3>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('college.students') }}" class="btn-primary text-xs">
                <i class="fa-solid fa-users"></i> Manage Students
            </a>
            <button onclick="document.getElementById('add-student-modal').classList.remove('hidden')"
                    class="btn-primary text-xs" style="background:linear-gradient(135deg,#059669,#0d9488)">
                <i class="fa-solid fa-user-plus"></i> Add Student
            </button>
            <a href="{{ route('college.profile') }}" class="btn-secondary text-xs">
                <i class="fa-solid fa-building"></i> College Profile
            </a>
        </div>
    </div>

    {{-- Recent Students --}}
    @if($recentStudents->count())
    <div class="glass rounded-2xl overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="font-extrabold text-slate-800 text-sm font-outfit">Recent Students</h3>
                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mt-0.5">Latest registrations</p>
            </div>
            <a href="{{ route('college.students') }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 transition">
                View All →
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="admin-table w-full">
                <thead>
                    <tr>
                        <th class="text-left">Student</th>
                        <th class="text-left">Email</th>
                        <th class="text-left">Department</th>
                        <th class="text-left">Status</th>
                        <th class="text-right">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentStudents as $s)
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
                        <td>
                            @if($s->status === 'approved') <span class="badge badge-green">Approved</span>
                            @elseif($s->status === 'pending') <span class="badge badge-amber">Pending</span>
                            @else <span class="badge badge-rose">Rejected</span>
                            @endif
                        </td>
                        <td class="text-right">
                            <a href="{{ route('college.students.edit', $s) }}" class="text-[11px] font-bold text-indigo-600 hover:text-indigo-800 transition">Edit →</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @else
    <div class="glass rounded-2xl p-10 text-center">
        <i class="fa-solid fa-users text-4xl text-slate-200 mb-4 block"></i>
        <p class="font-bold text-slate-600 text-sm">No students yet</p>
        <p class="text-slate-400 text-xs mt-1 mb-4">Add your first student or share the campus registration link</p>
        <button onclick="document.getElementById('add-student-modal').classList.remove('hidden')" class="btn-primary text-xs">
            <i class="fa-solid fa-user-plus"></i> Add First Student
        </button>
    </div>
    @endif
</div>

{{-- Add Student Modal --}}
<div id="add-student-modal" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl shadow-2xl w-full max-w-lg p-6 space-y-4" onclick="event.stopPropagation()">
        <div class="flex items-center justify-between">
            <h3 class="font-extrabold text-slate-800 text-base font-outfit">Add New Student</h3>
            <button onclick="document.getElementById('add-student-modal').classList.add('hidden')" class="text-slate-400 hover:text-slate-700">
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
                    <input type="text" name="department" class="inp text-xs" placeholder="e.g. CS, ECE">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-1">Semester</label>
                    <input type="text" name="semester" class="inp text-xs" placeholder="e.g. 3rd Sem">
                </div>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-600 mb-1">Initial Password *</label>
                <input type="text" name="password" required class="inp text-xs" placeholder="Min 6 characters" value="college@123">
                <p class="text-[9px] text-slate-400 mt-1">Student can change this after first login</p>
            </div>
            <div class="flex gap-2 pt-2">
                <button type="submit" class="btn-primary flex-1 text-xs">
                    <i class="fa-solid fa-user-plus"></i> Add & Approve Student
                </button>
                <button type="button" onclick="document.getElementById('add-student-modal').classList.add('hidden')"
                        class="btn-secondary text-xs px-4">Cancel</button>
            </div>
        </form>
    </div>
</div>
@endsection
