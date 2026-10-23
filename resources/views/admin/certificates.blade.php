@extends('admin.layout')

@section('title', 'Issued Certificates')
@section('page-title', 'Certificates Management')

@section('content')
<div class="space-y-6">

    {{-- Stats Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="glass p-5 rounded-2xl stat-lift flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl shadow-sm border border-amber-100">
                <i class="fa-solid fa-award"></i>
            </div>
            <div>
                <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-widest">Issued Certificates</p>
                <p class="text-2xl font-black text-slate-900 font-outfit mt-0.5">{{ number_format($totalIssued) }}</p>
            </div>
        </div>

        <div class="glass p-5 rounded-2xl stat-lift flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-xl shadow-sm border border-indigo-100">
                <i class="fa-solid fa-user-graduate"></i>
            </div>
            <div>
                <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-widest">Unique Recipients</p>
                <p class="text-2xl font-black text-slate-900 font-outfit mt-0.5">{{ number_format($uniqueStudents) }}</p>
            </div>
        </div>

        <div class="glass p-5 rounded-2xl stat-lift flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl shadow-sm border border-emerald-100">
                <i class="fa-solid fa-envelope-circle-check"></i>
            </div>
            <div>
                <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-widest">Emails Delivered</p>
                <p class="text-2xl font-black text-slate-900 font-outfit mt-0.5">{{ number_format($emailsDelivered) }}</p>
            </div>
        </div>

        <div class="glass p-5 rounded-2xl stat-lift flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center text-xl shadow-sm border border-rose-100">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
            <div>
                <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-widest">Failed / Pending</p>
                <p class="text-2xl font-black text-slate-900 font-outfit mt-0.5">{{ number_format($emailsFailed) }}</p>
            </div>
        </div>
    </div>

    {{-- Filter & Table Card --}}
    <div class="glass rounded-3xl overflow-hidden shadow-sm">
        
        {{-- Search & Filter Bar --}}
        <div class="p-5 border-b border-slate-100 bg-slate-50/50">
            <form method="GET" action="{{ route('admin.certificates') }}" class="flex flex-wrap items-center justify-between gap-3">
                <div class="flex flex-wrap items-center gap-3 flex-1 min-w-[280px]">
                    <div class="relative flex-1 min-w-[200px]">
                        <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                        <input type="text" name="search" value="{{ request('search') }}"
                               placeholder="Search student name, email, or course..."
                               class="inp pl-9 text-xs">
                    </div>

                    <select name="course_id" class="inp text-xs max-w-[220px]">
                        <option value="">All Courses</option>
                        @foreach($courses as $c)
                            <option value="{{ $c->id }}" {{ request('course_id') == $c->id ? 'selected' : '' }}>
                                {{ $c->title }}
                            </option>
                        @endforeach
                    </select>

                    <button type="submit" class="btn-primary text-xs py-2 px-4">
                        <i class="fa-solid fa-filter"></i> Filter
                    </button>
                    @if(request()->anyFilled(['search', 'course_id']))
                        <a href="{{ route('admin.certificates') }}" class="btn-secondary text-xs py-2 px-4">
                            <i class="fa-solid fa-xmark"></i> Clear
                        </a>
                    @endif
                </div>

                <div class="text-xs font-bold text-slate-400">
                    Showing {{ $certificates->firstItem() ?? 0 }} - {{ $certificates->lastItem() ?? 0 }} of {{ $certificates->total() }} records
                </div>
            </form>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="w-full admin-table text-left border-collapse">
                <thead>
                    <tr>
                        <th>Student Details</th>
                        <th>Course Information</th>
                        <th>Completion Date</th>
                        <th>Certificate Mail Status</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($certificates as $cert)
                    <tr class="hover:bg-slate-50/80 transition duration-150">
                        {{-- Student --}}
                        <td>
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-indigo-500 to-purple-600 text-white font-extrabold text-xs flex items-center justify-center font-outfit shadow-sm">
                                    {{ strtoupper(substr($cert->user_name, 0, 1)) }}
                                </div>
                                <div>
                                    <p class="font-bold text-slate-900 text-xs">{{ $cert->user_name }}</p>
                                    <p class="text-slate-400 text-[11px]">{{ $cert->user_email }}</p>
                                </div>
                            </div>
                        </td>

                        {{-- Course --}}
                        <td>
                            <div>
                                <span class="text-[9px] font-extrabold uppercase tracking-wider text-emerald-700 bg-emerald-50 border border-emerald-100 px-2 py-0.5 rounded-full inline-block mb-1">
                                    {{ $cert->course_category }}
                                </span>
                                <p class="font-bold text-slate-800 text-xs leading-snug">{{ $cert->course_title }}</p>
                                <p class="text-slate-400 text-[10px] mt-0.5">Instructor: {{ $cert->instructor_name }}</p>
                            </div>
                        </td>

                        {{-- Completion Date --}}
                        <td>
                            <div class="flex items-center gap-1.5 text-xs text-slate-600 font-medium">
                                <i class="fa-solid fa-calendar-check text-emerald-500"></i>
                                <span>{{ \Carbon\Carbon::parse($cert->completion_date)->format('d M Y') }}</span>
                            </div>
                        </td>

                        {{-- Mail Status --}}
                        <td>
                            @if($cert->mail_status === 'sent')
                                <div class="space-y-1">
                                    <span class="badge badge-emerald flex items-center gap-1 w-max">
                                        <i class="fa-solid fa-circle-check text-[10px]"></i> Email Sent
                                    </span>
                                    <p class="text-[10px] text-slate-400 font-medium">{{ $cert->mail_sent_at }}</p>
                                </div>
                            @elseif($cert->mail_status === 'failed')
                                <div class="space-y-1">
                                    <span class="badge badge-rose flex items-center gap-1 w-max" title="{{ $cert->mail_error }}">
                                        <i class="fa-solid fa-circle-xmark text-[10px]"></i> Delivery Failed
                                    </span>
                                    <p class="text-[10px] text-rose-500 truncate max-w-[180px]" title="{{ $cert->mail_error }}">
                                        {{ $cert->mail_error }}
                                    </p>
                                </div>
                            @else
                                <span class="badge badge-gray flex items-center gap-1 w-max">
                                    <i class="fa-solid fa-clock text-[10px]"></i> Not Dispatched
                                </span>
                            @endif
                        </td>

                        {{-- Actions --}}
                        <td class="text-right">
                            <div class="flex items-center justify-end gap-1.5">
                                {{-- View PDF --}}
                                <a href="{{ route('admin.certificates.view', [$cert->user_id, $cert->course_id]) }}" target="_blank"
                                   class="px-2.5 py-1.5 rounded-lg bg-indigo-50 hover:bg-indigo-100 text-indigo-600 border border-indigo-200 text-xs font-bold transition flex items-center gap-1"
                                   title="Preview PDF Certificate">
                                    <i class="fa-solid fa-eye text-[10px]"></i> View
                                </a>

                                {{-- Download PDF --}}
                                <a href="{{ route('admin.certificates.download', [$cert->user_id, $cert->course_id]) }}"
                                   class="px-2.5 py-1.5 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200 text-xs font-bold transition flex items-center gap-1"
                                   title="Download PDF Certificate">
                                    <i class="fa-solid fa-download text-[10px]"></i> PDF
                                </a>

                                {{-- Resend Email --}}
                                <form method="POST" action="{{ route('admin.certificates.resend-email', [$cert->user_id, $cert->course_id]) }}" class="inline">
                                    @csrf
                                    <button type="submit" onclick="return confirm('Send certificate email to {{ addslashes($cert->user_name) }} ({{ addslashes($cert->user_email) }})?')"
                                            class="px-2.5 py-1.5 rounded-lg bg-amber-50 hover:bg-amber-100 text-amber-700 border border-amber-200 text-xs font-bold transition flex items-center gap-1"
                                            title="Send or Resend Certificate Email">
                                        <i class="fa-solid fa-paper-plane text-[10px]"></i> Send Mail
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-12 text-center text-slate-400">
                            <div class="w-16 h-16 rounded-2xl bg-slate-100 flex items-center justify-center mx-auto mb-3 text-slate-300">
                                <i class="fa-solid fa-award text-3xl"></i>
                            </div>
                            <p class="font-bold text-slate-600 text-sm">No issued certificates found</p>
                            <p class="text-xs text-slate-400 mt-1">Students will appear here once they complete all tasks in a course.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($certificates->hasPages())
        <div class="p-4 border-t border-slate-100">
            {{ $certificates->links() }}
        </div>
        @endif

    </div>

</div>
@endsection
