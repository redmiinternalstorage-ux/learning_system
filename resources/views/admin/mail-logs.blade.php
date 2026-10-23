@extends('admin.layout')
@section('title', 'Mail Logs')
@section('page-title', 'Mail Logs')

@push('styles')
<style>
    /* ── Type card ── */
    .type-card {
        position: relative; overflow: hidden;
        border-radius: 1.25rem; padding: 1.25rem 1.25rem 1rem;
        cursor: pointer; transition: all 0.25s cubic-bezier(0.4,0,0.2,1);
        border: 2px solid transparent;
        background: #ffffff;
    }
    .type-card:hover { transform: translateY(-3px); box-shadow: 0 10px 25px -5px rgba(5, 150, 105, 0.12); }
    .type-card.active { border-color: currentColor; }
    .type-card::before {
        content: ''; position: absolute; top: -30px; right: -30px;
        width: 90px; height: 90px; border-radius: 50%;
        background: currentColor; opacity: 0.08;
    }

    /* ── Progress bar ── */
    .prog-bar { height: 5px; border-radius: 9999px; background: rgba(148,163,184,0.15); overflow: hidden; }
    .prog-fill { height: 100%; border-radius: 9999px; transition: width 0.6s ease; }

    /* ── Log row hover ── */
    .log-row { transition: background 0.15s; }
    .log-row:hover td { background: rgba(5, 150, 105, 0.04) !important; }

    /* ── OTP badge ── */
    .otp-verified   { background:#ecfdf5; color:#047857; border:1px solid #a7f3d0; }
    .otp-unverified { background:#fff7ed; color:#c2410c; border:1px solid #fed7aa; }

    /* ── Filter bar ── */
    .filter-bar { background: #ffffff; border:1px solid #e2e8f0; border-radius:1.25rem; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02); }

    /* ── Donut chart (pure CSS) ── */
    .donut-ring { transition: stroke-dashoffset 0.8s ease; }

    /* ── Pulse dot ── */
    @keyframes pulse-dot { 0%,100%{opacity:1} 50%{opacity:0.4} }
    .pulse-dot { animation: pulse-dot 2s ease-in-out infinite; }

    /* ── Shimmer for empty ── */
    @keyframes shimmer { from{background-position:-200% 0} to{background-position:200% 0} }
    .shimmer-bg {
        background: linear-gradient(90deg, transparent 25%, rgba(16, 185, 129, 0.08) 50%, transparent 75%);
        background-size: 200% 100%;
        animation: shimmer 2.5s infinite;
    }
</style>
@endpush

@section('content')
<div class="space-y-6">

    {{-- ══════════════════════════════════════════════════════════
         HERO BANNER
    ══════════════════════════════════════════════════════════ --}}
    <div class="relative rounded-3xl overflow-hidden p-7 sm:p-9 shadow-lg shadow-emerald-900/10"
         style="background:linear-gradient(135deg,#059669 0%,#047857 40%,#065f46 100%)">
        <div class="absolute inset-0 pointer-events-none">
            <div class="absolute top-0 right-0 w-96 h-96 bg-emerald-400/20 rounded-full blur-3xl -translate-y-1/2 translate-x-1/3"></div>
            <div class="absolute bottom-0 left-0 w-72 h-72 bg-teal-400/15 rounded-full blur-3xl translate-y-1/2 -translate-x-1/4"></div>
            <div class="absolute top-1/2 left-1/3 w-48 h-48 bg-emerald-300/10 rounded-full blur-2xl -translate-y-1/2"></div>
        </div>
        <div class="relative z-10 flex flex-col sm:flex-row sm:items-center justify-between gap-5">
            <div>
                <div class="flex items-center gap-2 mb-2">
                    <span class="pulse-dot inline-block w-2 h-2 rounded-full bg-emerald-300"></span>
                    <p class="text-emerald-200 text-[10px] font-black uppercase tracking-widest">Live Mail Tracker</p>
                </div>
                <h2 class="text-white text-2xl sm:text-3xl font-black font-outfit leading-tight">
                    📬 Mail Activity Centre
                </h2>
                <p class="text-emerald-100/80 text-sm mt-2">
                    Total <span class="text-white font-bold">{{ number_format($overallStats['total']) }}</span> emails ·
                    <span class="text-emerald-200 font-bold">{{ $overallStats['success'] }} delivered</span> ·
                    <span class="text-rose-200 font-bold">{{ $overallStats['failed'] }} failed</span>
                </p>
            </div>
            <div class="flex gap-3 flex-wrap">
                <div class="flex flex-col items-center px-5 py-3 rounded-2xl bg-white/10 border border-white/20 backdrop-blur-sm text-center min-w-[80px]">
                    <span class="text-2xl font-black text-white font-outfit">{{ $overallStats['otp_verified'] }}</span>
                    <span class="text-emerald-200 text-[9px] font-bold uppercase tracking-wider mt-0.5">OTP ✓</span>
                </div>
                <div class="flex flex-col items-center px-5 py-3 rounded-2xl bg-white/10 border border-white/20 backdrop-blur-sm text-center min-w-[80px]">
                    <span class="text-2xl font-black text-white font-outfit">{{ $overallStats['otp_unverified'] }}</span>
                    <span class="text-amber-200 text-[9px] font-bold uppercase tracking-wider mt-0.5">OTP ✗</span>
                </div>
                @php
                    $deliveryRate = $overallStats['total'] > 0
                        ? round(($overallStats['success'] / $overallStats['total']) * 100, 1)
                        : 100;
                @endphp
                <div class="flex flex-col items-center px-5 py-3 rounded-2xl bg-white/10 border border-white/20 backdrop-blur-sm text-center min-w-[80px]">
                    <span class="text-2xl font-black font-outfit {{ $deliveryRate >= 90 ? 'text-emerald-200' : ($deliveryRate >= 70 ? 'text-amber-200' : 'text-rose-200') }}">{{ $deliveryRate }}%</span>
                    <span class="text-emerald-100 text-[9px] font-bold uppercase tracking-wider mt-0.5">Delivery</span>
                </div>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════
         TYPE CARDS (per-type stat cards)
    ══════════════════════════════════════════════════════════ --}}
    @php
    $typeColors = [
        'welcome'               => ['from'=>'from-emerald-500','to'=>'to-teal-500',    'hex'=>'#10b981'],
        'otp'                   => ['from'=>'from-teal-500',   'to'=>'to-emerald-600', 'hex'=>'#0d9488'],
        'otp_resend'            => ['from'=>'from-emerald-600','to'=>'to-emerald-800', 'hex'=>'#059669'],
        'password_changed'      => ['from'=>'from-amber-500',  'to'=>'to-yellow-500',  'hex'=>'#d97706'],
        'account_blocked'       => ['from'=>'from-rose-500',   'to'=>'to-red-600',     'hex'=>'#e11d48'],
        'account_unblocked'     => ['from'=>'from-sky-500',    'to'=>'to-cyan-500',    'hex'=>'#0284c7'],
        'account_deleted'       => ['from'=>'from-red-600',    'to'=>'to-rose-700',    'hex'=>'#dc2626'],
        'auto_password_changed' => ['from'=>'from-orange-500', 'to'=>'to-amber-600',   'hex'=>'#ea580c'],
        'admin_direct'          => ['from'=>'from-emerald-500', 'to'=>'to-teal-600',   'hex'=>'#059669'],
    ];
    $activeType = request('type');
    @endphp

    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3">
        {{-- ALL card --}}
        <a href="{{ route('admin.mail-logs') }}" class="type-card glass {{ !$activeType ? 'active ring-2 ring-emerald-500/50' : '' }}" style="color:#059669">
            <div class="flex items-start justify-between mb-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center text-white text-sm shadow-md">
                    <i class="fa-solid fa-layer-group"></i>
                </div>
                <span class="text-xl font-black font-outfit text-slate-800">{{ $overallStats['total'] }}</span>
            </div>
            <p class="text-xs font-bold text-slate-700">All Emails</p>
            <div class="mt-2 prog-bar">
                <div class="prog-fill bg-gradient-to-r from-emerald-500 to-teal-600" style="width:100%"></div>
            </div>
        </a>

        @foreach(\App\Models\MailLog::$typesMeta as $typeKey => $meta)
        @php
            $tc    = $typeColors[$typeKey] ?? ['from'=>'from-slate-400','to'=>'to-slate-500','text'=>'text-slate-600','bg'=>'bg-slate-50','hex'=>'#64748b'];
            $count = $typeCounts[$typeKey] ?? null;
            $total = $count ? $count->total : 0;
            $ok    = $count ? $count->sent_ok : 0;
            $fail  = $count ? $count->failed : 0;
            $pct   = $total > 0 ? round(($ok / $total) * 100) : 0;
        @endphp
        <a href="{{ route('admin.mail-logs', ['type' => $typeKey]) }}"
           class="type-card glass {{ $activeType === $typeKey ? 'active ring-2' : '' }}"
           style="color:{{ $tc['hex'] }}; {{ $activeType === $typeKey ? 'ring-color:'. $tc['hex'] : '' }}">
            <div class="flex items-start justify-between mb-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br {{ $tc['from'] }} {{ $tc['to'] }} flex items-center justify-center text-white text-sm shadow-md">
                    <i class="fa-solid {{ $meta['icon'] }}"></i>
                </div>
                <span class="text-xl font-black font-outfit text-slate-800">{{ $total }}</span>
            </div>
            <p class="text-[11px] font-bold text-slate-700 leading-snug">{{ $meta['label'] }}</p>
            <div class="mt-2 flex items-center gap-2">
                <div class="flex-1 prog-bar">
                    <div class="prog-fill" style="width:{{ $pct }}%; background:{{ $tc['hex'] }}"></div>
                </div>
                <span class="text-[9px] font-bold text-slate-400">{{ $pct }}%</span>
            </div>
            @if($fail > 0)
            <p class="text-[9px] font-bold text-rose-500 mt-1 flex items-center gap-1">
                <i class="fa-solid fa-circle-xmark"></i> {{ $fail }} failed
            </p>
            @endif
        </a>
        @endforeach
    </div>

    {{-- ══════════════════════════════════════════════════════════
         OTP SPECIAL STATS
    ══════════════════════════════════════════════════════════ --}}
    @php
        $otpTotal       = ($typeCounts['otp']->total ?? 0) + ($typeCounts['otp_resend']->total ?? 0);
        $otpVerified    = $overallStats['otp_verified'];
        $otpUnverified  = $overallStats['otp_unverified'];
        $otpVerifyRate  = $otpTotal > 0 ? round(($otpVerified / $otpTotal) * 100) : 0;
    @endphp
    @if($otpTotal > 0)
    <div class="glass rounded-3xl p-6">
        <div class="flex items-center gap-3 mb-5">
            <div class="w-10 h-10 rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center text-white shadow-md">
                <i class="fa-solid fa-key text-sm"></i>
            </div>
            <div>
                <h3 class="font-extrabold text-slate-800 text-sm font-outfit">OTP Deep Stats</h3>
                <p class="text-slate-400 text-[10px]">Verification success tracking for password resets</p>
            </div>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            <div class="text-center p-4 rounded-2xl bg-emerald-50/60 border border-emerald-100">
                <p class="text-2xl font-black font-outfit text-emerald-700">{{ $otpTotal }}</p>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wide mt-1">Total OTPs Sent</p>
            </div>
            <div class="text-center p-4 rounded-2xl bg-emerald-50 border border-emerald-200">
                <p class="text-2xl font-black font-outfit text-emerald-600">{{ $otpVerified }}</p>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wide mt-1">✅ Verified</p>
            </div>
            <div class="text-center p-4 rounded-2xl bg-orange-50 border border-orange-100">
                <p class="text-2xl font-black font-outfit text-orange-600">{{ $otpUnverified }}</p>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wide mt-1">⏳ Not Verified</p>
            </div>
            <div class="text-center p-4 rounded-2xl bg-teal-50 border border-teal-100">
                <div class="relative inline-flex items-center justify-center mb-1">
                    <svg class="w-14 h-14 -rotate-90" viewBox="0 0 36 36">
                        <circle cx="18" cy="18" r="15.9" fill="none" stroke="rgba(148,163,184,0.2)" stroke-width="3.5"/>
                        <circle class="donut-ring" cx="18" cy="18" r="15.9" fill="none"
                            stroke="#059669" stroke-width="3.5" stroke-linecap="round"
                            stroke-dasharray="{{ $otpVerifyRate }} {{ 100 - $otpVerifyRate }}"
                            stroke-dashoffset="25"/>
                    </svg>
                    <span class="absolute text-[11px] font-black text-emerald-700 font-outfit">{{ $otpVerifyRate }}%</span>
                </div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wide">Verify Rate</p>
            </div>
        </div>
    </div>
    @endif

    {{-- ══════════════════════════════════════════════════════════
         FILTER BAR
    ══════════════════════════════════════════════════════════ --}}
    <div class="filter-bar p-4">
        <form method="GET" action="{{ route('admin.mail-logs') }}" class="flex flex-wrap items-end gap-3">
            {{-- Search --}}
            <div class="flex-1 min-w-[160px]">
                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">
                    <i class="fa-solid fa-magnifying-glass mr-1"></i>Search
                </label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Email or name…"
                    class="inp text-xs h-10">
            </div>
            {{-- Type --}}
            <div class="min-w-[150px]">
                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">
                    <i class="fa-solid fa-tag mr-1"></i>Mail Type
                </label>
                <select name="type" class="inp text-xs h-10">
                    <option value="">All Types</option>
                    @foreach(\App\Models\MailLog::$typesMeta as $k => $m)
                    <option value="{{ $k }}" {{ request('type') === $k ? 'selected' : '' }}>{{ $m['label'] }}</option>
                    @endforeach
                </select>
            </div>
            {{-- Status --}}
            <div class="min-w-[130px]">
                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">
                    <i class="fa-solid fa-filter mr-1"></i>Status
                </label>
                <select name="status" class="inp text-xs h-10">
                    <option value="">All</option>
                    <option value="success" {{ request('status')==='success' ? 'selected' : '' }}>✅ Delivered</option>
                    <option value="failed"  {{ request('status')==='failed'  ? 'selected' : '' }}>❌ Failed</option>
                </select>
            </div>
            {{-- Date From --}}
            <div class="min-w-[140px]">
                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">
                    <i class="fa-solid fa-calendar-days mr-1"></i>From
                </label>
                <input type="date" name="date_from" value="{{ request('date_from') }}" class="inp text-xs h-10">
            </div>
            {{-- Date To --}}
            <div class="min-w-[140px]">
                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">
                    <i class="fa-solid fa-calendar-check mr-1"></i>To
                </label>
                <input type="date" name="date_to" value="{{ request('date_to') }}" class="inp text-xs h-10">
            </div>
            {{-- Buttons --}}
            <div class="flex gap-2 pb-0.5">
                <button type="submit" class="btn-primary h-10 px-5 text-xs">
                    <i class="fa-solid fa-magnifying-glass"></i> Filter
                </button>
                @if(request()->hasAny(['search','type','status','date_from','date_to']))
                <a href="{{ route('admin.mail-logs') }}" class="btn-secondary h-10 px-4 text-xs">
                    <i class="fa-solid fa-xmark"></i> Clear
                </a>
                @endif
            </div>
        </form>
    </div>

    {{-- ══════════════════════════════════════════════════════════
         LOG TABLE
    ══════════════════════════════════════════════════════════ --}}
    @php $senderEmail = config('mail.from.address'); @endphp
    <div class="glass rounded-3xl overflow-hidden">
        {{-- Table header --}}
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between flex-wrap gap-3">
            <div>
                <h3 class="font-extrabold text-slate-800 text-sm font-outfit">
                    📋 Mail Records
                    @if(request()->hasAny(['search','type','status','date_from','date_to']))
                        <span class="text-xs font-normal text-emerald-600 ml-2">(filtered)</span>
                    @endif
                </h3>
                <p class="text-slate-400 text-[10px] mt-0.5">
                    Showing {{ $logs->firstItem() ?? 0 }}–{{ $logs->lastItem() ?? 0 }} of {{ $logs->total() }} records
                </p>
            </div>
            {{-- Active filter badges --}}
            <div class="flex gap-2 flex-wrap">
                @if(request()->filled('type'))
                    @php $tm = \App\Models\MailLog::$typesMeta[request('type')] ?? null; @endphp
                    @if($tm)
                    <span class="badge" style="background:{{ $typeColors[request('type')]['hex'] }}22;color:{{ $typeColors[request('type')]['hex'] }};border-color:{{ $typeColors[request('type')]['hex'] }}44">
                        <i class="fa-solid {{ $tm['icon'] }}"></i> {{ $tm['label'] }}
                    </span>
                    @endif
                @endif
                @if(request()->filled('status'))
                <span class="badge {{ request('status')==='success' ? 'badge-green' : 'badge-rose' }}">
                    {{ request('status')==='success' ? '✅ Delivered' : '❌ Failed' }}
                </span>
                @endif
            </div>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="w-full admin-table">
                <thead>
                    <tr>
                        <th class="text-left w-8">#</th>
                        <th class="text-left">Mail Type</th>
                        <th class="text-left">From</th>
                        <th class="text-left">To</th>
                        <th class="text-left">Status</th>
                        <th class="text-left">Sent At</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                    @php
                        $meta = $log->getMeta();
                        $tc2  = $typeColors[$log->mail_type] ?? ['from'=>'from-slate-400','to'=>'to-slate-500','hex'=>'#64748b'];
                    @endphp
                    <tr class="log-row">

                        {{-- ID --}}
                        <td>
                            <span class="text-slate-400 text-[10px] font-mono">#{{ $log->id }}</span>
                        </td>

                        {{-- Mail Type --}}
                        <td>
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-xl bg-gradient-to-br {{ $tc2['from'] }} {{ $tc2['to'] }} flex items-center justify-center text-white text-xs shadow-md flex-shrink-0">
                                    <i class="fa-solid {{ $meta['icon'] }}"></i>
                                </div>
                                <span class="text-xs font-bold text-slate-700 whitespace-nowrap">{{ $meta['label'] }}</span>
                            </div>
                        </td>

                        {{-- FROM --}}
                        <td>
                            <div class="flex items-center gap-2 min-w-[180px]">
                                <div class="w-7 h-7 rounded-lg bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center text-white text-[10px] flex-shrink-0 shadow-sm">
                                    <i class="fa-solid fa-paper-plane"></i>
                                </div>
                                <div>
                                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider leading-none mb-0.5">Learning Curve</p>
                                    <p class="text-xs font-semibold text-slate-600">{{ $senderEmail }}</p>
                                </div>
                            </div>
                        </td>

                        {{-- TO --}}
                        <td>
                            <div class="flex items-center gap-2 min-w-[180px]">
                                @if($log->user)
                                    <div class="w-7 h-7 rounded-lg bg-gradient-to-br from-teal-500 to-emerald-600 flex items-center justify-center text-white text-[10px] font-bold font-outfit flex-shrink-0 shadow-sm">
                                        {{ strtoupper(substr($log->recipient_name ?? $log->user->name ?? '?', 0, 1)) }}
                                    </div>
                                @else
                                    <div class="w-7 h-7 rounded-lg bg-slate-100 flex items-center justify-center text-slate-400 text-[10px] flex-shrink-0">
                                        <i class="fa-solid fa-user"></i>
                                    </div>
                                @endif
                                <div class="min-w-0">
                                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider leading-none mb-0.5">
                                        {{ $log->recipient_name ?? ($log->user->name ?? 'Unknown') }}
                                    </p>
                                    <p class="text-xs font-semibold text-slate-600 truncate max-w-[160px]">
                                        {{ $log->recipient_email }}
                                    </p>
                                </div>
                            </div>
                        </td>

                        {{-- Status --}}
                        <td>
                            @if($log->success)
                                <span class="badge badge-green">
                                    <i class="fa-solid fa-circle-check text-[8px]"></i> Delivered
                                </span>
                            @else
                                <div class="space-y-1">
                                    <span class="badge badge-rose">
                                        <i class="fa-solid fa-circle-xmark text-[8px]"></i> Failed
                                    </span>
                                    @if($log->error_message)
                                    <p class="text-[9px] text-rose-500 max-w-[120px] truncate" title="{{ $log->error_message }}">
                                        {{ Str::limit($log->error_message, 38) }}
                                    </p>
                                    @endif
                                </div>
                            @endif
                        </td>

                        {{-- Sent At --}}
                        <td>
                            <p class="text-xs font-semibold text-slate-700 whitespace-nowrap">
                                {{ $log->created_at->format('d M Y') }}
                            </p>
                            <p class="text-[10px] text-slate-400 whitespace-nowrap">
                                {{ $log->created_at->format('h:i A') }}
                                · <span class="text-emerald-600 font-medium">{{ $log->created_at->diffForHumans() }}</span>
                            </p>
                        </td>

                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-16 text-center">
                            <div class="shimmer-bg rounded-2xl mx-auto max-w-xs py-10 px-6">
                                <div class="w-14 h-14 rounded-2xl bg-emerald-50 flex items-center justify-center mx-auto mb-4">
                                    <i class="fa-solid fa-inbox text-2xl text-emerald-500"></i>
                                </div>
                                <p class="font-bold text-slate-600 text-sm">No mail logs found</p>
                                <p class="text-slate-400 text-xs mt-1">Try adjusting your filters</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($logs->hasPages())
        <div class="px-6 py-4 border-t border-slate-100 flex items-center justify-between flex-wrap gap-3">
            <p class="text-[10px] text-slate-400 font-bold">
                Page {{ $logs->currentPage() }} of {{ $logs->lastPage() }} · {{ $logs->total() }} total records
            </p>
            <div class="flex items-center gap-1.5">
                @if($logs->onFirstPage())
                <span class="w-8 h-8 rounded-xl flex items-center justify-center text-slate-300 cursor-not-allowed text-xs">
                    <i class="fa-solid fa-chevron-left"></i>
                </span>
                @else
                <a href="{{ $logs->previousPageUrl() }}" class="w-8 h-8 rounded-xl flex items-center justify-center bg-slate-100 text-slate-500 hover:bg-emerald-50 hover:text-emerald-600 transition text-xs">
                    <i class="fa-solid fa-chevron-left"></i>
                </a>
                @endif

                @foreach($logs->getUrlRange(max(1,$logs->currentPage()-2), min($logs->lastPage(),$logs->currentPage()+2)) as $page => $url)
                <a href="{{ $url }}" class="w-8 h-8 rounded-xl flex items-center justify-center text-[11px] font-bold transition
                    {{ $page == $logs->currentPage()
                        ? 'bg-gradient-to-br from-emerald-500 to-teal-600 text-white shadow-md shadow-emerald-500/20'
                        : 'bg-slate-100 text-slate-500 hover:bg-emerald-50 hover:text-emerald-600' }}">
                    {{ $page }}
                </a>
                @endforeach

                @if($logs->hasMorePages())
                <a href="{{ $logs->nextPageUrl() }}" class="w-8 h-8 rounded-xl flex items-center justify-center bg-slate-100 text-slate-500 hover:bg-emerald-50 hover:text-emerald-600 transition text-xs">
                    <i class="fa-solid fa-chevron-right"></i>
                </a>
                @else
                <span class="w-8 h-8 rounded-xl flex items-center justify-center text-slate-300 cursor-not-allowed text-xs">
                    <i class="fa-solid fa-chevron-right"></i>
                </span>
                @endif
            </div>
        </div>
        @endif
    </div>

</div>
@endsection
