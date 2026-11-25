@extends('admin.layout')
@section('title','Campus Analytics')
@section('page-title','Campus Analytics 📊')

@section('content')
<div class="space-y-5">

    {{-- Top Courses --}}
    <div class="glass rounded-2xl p-5">
        <h3 class="font-extrabold text-slate-900 text-sm mb-4">Most Popular Courses (Campus)</h3>
        @forelse($topCourses as $i => $item)
        <div class="flex items-center gap-4 p-3 rounded-xl hover:bg-slate-50 transition mb-2">
            <span class="w-7 h-7 rounded-lg flex items-center justify-center text-xs font-black {{ $i === 0 ? 'bg-amber-100 text-amber-700' : ($i === 1 ? 'bg-slate-200 text-slate-700' : 'bg-orange-100 text-orange-700') }}">{{ $i + 1 }}</span>
            <div class="flex-1">
                <p class="text-xs font-bold text-slate-900">{{ $item->course?->title ?? 'Unknown Course' }}</p>
                <div class="flex items-center gap-2 mt-1">
                    <div class="flex-1 bg-slate-100 rounded-full h-1.5">
                        <div class="bg-gradient-to-r from-emerald-500 to-teal-500 rounded-full h-1.5" style="width:{{ min(100, $item->count * 10) }}%"></div>
                    </div>
                    <span class="text-[10px] font-black text-slate-500">{{ round($item->avg_progress) }}% avg</span>
                </div>
            </div>
            <span class="text-sm font-black text-emerald-700 font-outfit flex-shrink-0">{{ $item->count }}</span>
        </div>
        @empty
        <p class="text-xs text-slate-500 text-center py-4">No enrollments yet</p>
        @endforelse
    </div>

    <div class="grid lg:grid-cols-2 gap-5">
        {{-- College breakdown --}}
        <div class="glass rounded-2xl p-5">
            <h3 class="font-extrabold text-slate-900 text-sm mb-4">Students by College</h3>
            @php $maxCount = $collegeBreakdown->max('count') ?: 1; @endphp
            @forelse($collegeBreakdown as $item)
            <div class="mb-3">
                <div class="flex items-center justify-between mb-1">
                    <span class="text-xs font-bold text-slate-800 truncate max-w-[200px]">{{ $item->college_name ?? 'Unknown' }}</span>
                    <span class="text-xs font-black text-emerald-700 ml-2 flex-shrink-0">{{ $item->count }}</span>
                </div>
                <div class="w-full bg-slate-100 rounded-full h-2">
                    <div class="bg-gradient-to-r from-emerald-500 to-teal-500 rounded-full h-2" style="width:{{ round(($item->count / $maxCount) * 100) }}%"></div>
                </div>
            </div>
            @empty
            <p class="text-xs text-slate-500 text-center py-4">No data yet</p>
            @endforelse
        </div>

        {{-- Summary stats --}}
        <div class="glass rounded-2xl p-5">
            <h3 class="font-extrabold text-slate-900 text-sm mb-4">Program Summary</h3>
            @php
                $total      = \App\Models\CampusStudent::count();
                $approved   = \App\Models\CampusStudent::where('status','approved')->count();
                $enrolled   = \App\Models\CampusEnrollment::count();
                $completed  = \App\Models\CampusEnrollment::where('progress',100)->count();
                $domains    = \App\Models\CampusDomain::where('is_active',true)->count();
                $avgProg    = \App\Models\CampusEnrollment::avg('progress');
            @endphp
            <div class="space-y-3 text-xs">
                @foreach([
                    ['Total Registered', $total, 'emerald'],
                    ['Approved Students', $approved, 'teal'],
                    ['Total Enrollments', $enrolled, 'blue'],
                    ['Courses Completed', $completed, 'purple'],
                    ['Active Domains', $domains, 'amber'],
                    ['Avg Course Progress', round($avgProg ?? 0).'%', 'rose'],
                ] as [$label, $val, $color])
                <div class="flex items-center justify-between p-3 rounded-xl bg-{{ $color }}-50 dark:bg-{{ $color }}-900/20 border border-{{ $color }}-100 dark:border-{{ $color }}-800/30">
                    <span class="text-slate-600 font-semibold">{{ $label }}</span>
                    <span class="font-black text-{{ $color }}-700 text-sm font-outfit">{{ $val }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Monthly registrations --}}
    @if($monthlyRegistrations->count())
    <div class="glass rounded-2xl p-5">
        <h3 class="font-extrabold text-slate-900 text-sm mb-4">Monthly Registrations</h3>
        @php $maxReg = $monthlyRegistrations->max('count') ?: 1; @endphp
        <div class="flex items-end gap-2 h-32">
            @foreach($monthlyRegistrations as $item)
            @php $h = max(8, round(($item->count / $maxReg) * 100)); @endphp
            <div class="flex-1 flex flex-col items-center gap-1">
                <span class="text-[9px] font-black text-emerald-700">{{ $item->count }}</span>
                <div class="w-full bg-gradient-to-t from-emerald-500 to-teal-400 rounded-t-lg" style="height:{{ $h }}%"></div>
                <span class="text-[8px] text-slate-400 font-bold">{{ \Carbon\Carbon::parse($item->month)->format('M') }}</span>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection
