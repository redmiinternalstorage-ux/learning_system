@extends('admin.layout')
@section('title','Analytics')
@section('page-title','Analytics')

@push('styles')
<style>
  .chart-bar { transition: height 0.6s cubic-bezier(.4,0,.2,1); }
  .stat-ring { transform: rotate(-90deg); }
  .stat-ring circle { transition: stroke-dashoffset 1s ease; }
</style>
@endpush

@section('content')
<div class="space-y-6">

  {{-- Hero --}}
  <div class="relative rounded-3xl overflow-hidden p-7 sm:p-9"
       style="background:linear-gradient(135deg,#0a001a 0%,#1a0040 40%,#000d30 100%)">
    <div class="absolute inset-0 pointer-events-none">
      <div class="absolute top-0 right-0 w-80 h-80 bg-blue-500/20 rounded-full blur-3xl -translate-y-1/2 translate-x-1/3"></div>
      <div class="absolute bottom-0 left-0 w-64 h-64 bg-purple-500/15 rounded-full blur-3xl translate-y-1/2 -translate-x-1/4"></div>
    </div>
    <div class="relative z-10">
      <p class="text-blue-300 text-[10px] font-black uppercase tracking-widest mb-2">Admin Panel</p>
      <h2 class="text-white text-2xl sm:text-3xl font-black font-outfit">📊 Analytics Dashboard</h2>
      <p class="text-blue-200/60 text-sm mt-2">Platform overview · Last updated {{ now()->format('d M Y, h:i A') }}</p>
    </div>
  </div>

  {{-- Quick KPIs --}}
  <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
    @php
    $kpis = [
      ['label'=>'Registered Today',  'value'=>$registrations['today'],  'icon'=>'fa-user-plus',      'from'=>'from-emerald-500','to'=>'to-teal-600'],
      ['label'=>'This Week',         'value'=>$registrations['week'],   'icon'=>'fa-calendar-week',  'from'=>'from-sky-500',   'to'=>'to-cyan-600'],
      ['label'=>'This Month',        'value'=>$registrations['month'],  'icon'=>'fa-calendar',       'from'=>'from-violet-500','to'=>'to-purple-600'],
      ['label'=>'Total Submissions', 'value'=>$submissionStats['total'],'icon'=>'fa-file-pen',       'from'=>'from-amber-500', 'to'=>'to-orange-600'],
      ['label'=>'Graded',            'value'=>$submissionStats['graded'],'icon'=>'fa-star',          'from'=>'from-rose-500',  'to'=>'to-pink-600'],
      ['label'=>'Pending',           'value'=>$submissionStats['pending'],'icon'=>'fa-clock',        'from'=>'from-slate-400', 'to'=>'to-slate-500'],
    ];
    @endphp
    @foreach($kpis as $k)
    <div class="glass stat-lift rounded-2xl p-4 flex flex-col gap-3">
      <div class="w-9 h-9 rounded-xl bg-gradient-to-br {{ $k['from'] }} {{ $k['to'] }} flex items-center justify-center text-white text-xs shadow-md">
        <i class="fa-solid {{ $k['icon'] }}"></i>
      </div>
      <div>
        <p class="text-xl font-black text-slate-800 dark:text-slate-100 font-outfit leading-none">{{ $k['value'] }}</p>
        <p class="text-[9px] font-bold uppercase tracking-wider text-slate-400 mt-1">{{ $k['label'] }}</p>
      </div>
    </div>
    @endforeach
  </div>

  <div class="grid lg:grid-cols-3 gap-5">

    {{-- User Growth Bar Chart --}}
    <div class="lg:col-span-2 glass rounded-3xl p-6">
      <div class="flex items-center justify-between mb-5">
        <div>
          <h3 class="font-extrabold text-slate-800 dark:text-slate-100 text-sm font-outfit">User Growth</h3>
          <p class="text-slate-400 text-[10px] mt-0.5">New registrations per month</p>
        </div>
        <div class="w-8 h-8 rounded-xl bg-gradient-to-br from-purple-500 to-fuchsia-600 flex items-center justify-center text-white text-xs shadow-md">
          <i class="fa-solid fa-users"></i>
        </div>
      </div>
      @php $maxU = $userGrowth->max('count') ?: 1; @endphp
      <div class="flex items-end gap-2 h-36">
        @forelse($userGrowth as $row)
        @php $h = max(4, round(($row->count / $maxU) * 100)); @endphp
        <div class="flex-1 flex flex-col items-center gap-1">
          <span class="text-[9px] font-bold text-purple-600 dark:text-purple-400">{{ $row->count }}</span>
          <div class="w-full rounded-t-lg bg-gradient-to-t from-purple-500 to-fuchsia-400 chart-bar" style="height:{{ $h }}%"></div>
          <span class="text-[8px] text-slate-400 font-bold rotate-0 whitespace-nowrap">{{ Str::before($row->month,' ') }}</span>
        </div>
        @empty
        <div class="flex-1 text-center text-slate-400 text-xs py-10">No data yet</div>
        @endforelse
      </div>
    </div>

    {{-- Role Breakdown Donut --}}
    <div class="glass rounded-3xl p-6">
      <h3 class="font-extrabold text-slate-800 dark:text-slate-100 text-sm font-outfit mb-1">User Roles</h3>
      <p class="text-slate-400 text-[10px] mb-5">Distribution by role</p>
      @php
        $total = $roleBreakdown->sum();
        $roleColors = ['student'=>'#10b981','instructor'=>'#7c3aed','admin'=>'#e11d48'];
        $roleIcons  = ['student'=>'fa-graduation-cap','instructor'=>'fa-chalkboard-user','admin'=>'fa-shield-halved'];
      @endphp
      <div class="space-y-3">
        @foreach($roleBreakdown as $role => $count)
        @php
          $pct = $total > 0 ? round(($count/$total)*100) : 0;
          $col = $roleColors[$role] ?? '#64748b';
        @endphp
        <div>
          <div class="flex justify-between text-xs font-bold mb-1">
            <span class="flex items-center gap-1.5 text-slate-700 dark:text-slate-300">
              <i class="fa-solid {{ $roleIcons[$role] ?? 'fa-user' }} text-[10px]" style="color:{{ $col }}"></i>
              {{ ucfirst($role) }}
            </span>
            <span style="color:{{ $col }}">{{ $count }} ({{ $pct }}%)</span>
          </div>
          <div class="h-2 rounded-full bg-slate-100 dark:bg-white/10 overflow-hidden">
            <div class="h-full rounded-full" style="width:{{ $pct }}%;background:{{ $col }}"></div>
          </div>
        </div>
        @endforeach
      </div>
    </div>

  </div>

  <div class="grid lg:grid-cols-2 gap-5">

    {{-- Top 5 Courses --}}
    <div class="glass rounded-3xl p-6">
      <div class="flex items-center justify-between mb-5">
        <div>
          <h3 class="font-extrabold text-slate-800 dark:text-slate-100 text-sm font-outfit">Top Courses</h3>
          <p class="text-slate-400 text-[10px] mt-0.5">By enrollment count</p>
        </div>
        <i class="fa-solid fa-trophy text-amber-400"></i>
      </div>
      @php $maxC = $topCourses->max('users_count') ?: 1; @endphp
      <div class="space-y-3">
        @forelse($topCourses as $i => $course)
        @php $pct = max(4, round(($course->users_count/$maxC)*100)); @endphp
        <div class="flex items-center gap-3">
          <span class="w-5 h-5 rounded-lg text-[9px] font-black flex items-center justify-center text-white flex-shrink-0
            {{ $i===0?'bg-amber-400':($i===1?'bg-slate-400':($i===2?'bg-orange-500':'bg-slate-200 dark:bg-white/10 text-slate-400 dark:text-slate-500')) }}">
            {{ $i+1 }}
          </span>
          <div class="flex-1 min-w-0">
            <p class="text-xs font-bold text-slate-700 dark:text-slate-300 truncate">{{ $course->title }}</p>
            <div class="mt-1 h-1.5 rounded-full bg-slate-100 dark:bg-white/10 overflow-hidden">
              <div class="h-full rounded-full bg-gradient-to-r from-amber-400 to-orange-500" style="width:{{ $pct }}%"></div>
            </div>
          </div>
          <span class="text-xs font-black text-slate-600 dark:text-slate-300 flex-shrink-0">{{ $course->users_count }}</span>
        </div>
        @empty
        <p class="text-slate-400 text-xs">No courses yet.</p>
        @endforelse
      </div>
    </div>

    {{-- Top Students --}}
    <div class="glass rounded-3xl p-6">
      <div class="flex items-center justify-between mb-5">
        <div>
          <h3 class="font-extrabold text-slate-800 dark:text-slate-100 text-sm font-outfit">Top Students</h3>
          <p class="text-slate-400 text-[10px] mt-0.5">By total grade points earned</p>
        </div>
        <i class="fa-solid fa-star text-yellow-400"></i>
      </div>
      <div class="space-y-3">
        @forelse($topStudents as $i => $student)
        <div class="flex items-center gap-3">
          <div class="w-8 h-8 rounded-xl bg-gradient-to-br from-indigo-400 to-purple-600 flex items-center justify-center text-white text-xs font-bold font-outfit flex-shrink-0">
            {{ strtoupper(substr($student->name,0,1)) }}
          </div>
          <div class="flex-1 min-w-0">
            <p class="text-xs font-bold text-slate-700 dark:text-slate-300 truncate">{{ $student->name }}</p>
            <p class="text-[10px] text-slate-400">{{ $student->submissions_count }} submissions</p>
          </div>
          <span class="text-xs font-black text-indigo-600 dark:text-indigo-400 flex-shrink-0">
            {{ number_format($student->submissions_sum_grade ?? 0) }} pts
          </span>
        </div>
        @empty
        <p class="text-slate-400 text-xs">No graded submissions yet.</p>
        @endforelse
      </div>
    </div>

  </div>

  <div class="grid lg:grid-cols-2 gap-5">

    {{-- Enrollment Growth --}}
    <div class="glass rounded-3xl p-6">
      <div class="flex items-center justify-between mb-5">
        <div>
          <h3 class="font-extrabold text-slate-800 dark:text-slate-100 text-sm font-outfit">Enrollments</h3>
          <p class="text-slate-400 text-[10px] mt-0.5">Monthly enrollment trend</p>
        </div>
        <div class="w-8 h-8 rounded-xl bg-gradient-to-br from-sky-500 to-cyan-600 flex items-center justify-center text-white text-xs shadow-md">
          <i class="fa-solid fa-graduation-cap"></i>
        </div>
      </div>
      @php $maxE = $enrollmentGrowth->max('count') ?: 1; @endphp
      <div class="flex items-end gap-2 h-28">
        @forelse($enrollmentGrowth as $row)
        @php $h = max(4, round(($row->count / $maxE) * 100)); @endphp
        <div class="flex-1 flex flex-col items-center gap-1">
          <span class="text-[9px] font-bold text-sky-600 dark:text-sky-400">{{ $row->count }}</span>
          <div class="w-full rounded-t-lg bg-gradient-to-t from-sky-500 to-cyan-400 chart-bar" style="height:{{ $h }}%"></div>
          <span class="text-[8px] text-slate-400 font-bold">{{ Str::before($row->month,' ') }}</span>
        </div>
        @empty
        <div class="flex-1 text-center text-slate-400 text-xs py-8">No data yet</div>
        @endforelse
      </div>
    </div>

    {{-- Course Category Breakdown --}}
    <div class="glass rounded-3xl p-6">
      <h3 class="font-extrabold text-slate-800 dark:text-slate-100 text-sm font-outfit mb-1">Course Categories</h3>
      <p class="text-slate-400 text-[10px] mb-4">Distribution of courses by category</p>
      @php
        $catTotal = $categoryBreakdown->sum('count') ?: 1;
        $catColors = ['#7c3aed','#0ea5e9','#10b981','#f59e0b','#e11d48','#ec4899','#06b6d4','#8b5cf6'];
      @endphp
      <div class="space-y-2.5">
        @forelse($categoryBreakdown as $i => $cat)
        @php $pct = round(($cat->count/$catTotal)*100); $col = $catColors[$i%count($catColors)]; @endphp
        <div>
          <div class="flex justify-between text-[11px] font-bold mb-1">
            <span class="text-slate-600 dark:text-slate-400 truncate">{{ $cat->category ?: 'Uncategorized' }}</span>
            <span style="color:{{ $col }}">{{ $cat->count }} ({{ $pct }}%)</span>
          </div>
          <div class="h-1.5 rounded-full bg-slate-100 dark:bg-white/10 overflow-hidden">
            <div class="h-full rounded-full" style="width:{{ $pct }}%;background:{{ $col }}"></div>
          </div>
        </div>
        @empty
        <p class="text-slate-400 text-xs">No courses yet.</p>
        @endforelse
      </div>
    </div>

  </div>

  {{-- Mail Types --}}
  <div class="glass rounded-3xl p-6">
    <div class="flex items-center justify-between mb-5">
      <div>
        <h3 class="font-extrabold text-slate-800 dark:text-slate-100 text-sm font-outfit">Mail Breakdown</h3>
        <p class="text-slate-400 text-[10px] mt-0.5">Emails sent by type</p>
      </div>
      <a href="{{ route('admin.mail-logs') }}" class="text-[10px] font-bold text-purple-500 hover:text-purple-700 transition">View All Logs →</a>
    </div>
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3">
      @forelse($mailTypes as $mt)
      @php $meta = \App\Models\MailLog::$typesMeta[$mt->mail_type] ?? ['icon'=>'fa-envelope','label'=>$mt->mail_type,'bg'=>'#64748b']; @endphp
      <div class="text-center p-4 rounded-2xl border border-slate-200/60 dark:border-white/5 bg-slate-50/50 dark:bg-white/[0.02]">
        <div class="w-10 h-10 rounded-xl flex items-center justify-center text-white text-sm mx-auto mb-2 shadow-md"
             style="background:{{ $meta['bg'] }}">
          <i class="fa-solid {{ $meta['icon'] }}"></i>
        </div>
        <p class="text-lg font-black font-outfit text-slate-800 dark:text-slate-100">{{ $mt->count }}</p>
        <p class="text-[9px] font-bold text-slate-400 uppercase tracking-wide mt-0.5">{{ $meta['label'] }}</p>
      </div>
      @empty
      <div class="col-span-full text-center text-slate-400 text-xs py-6">No mail logs yet.</div>
      @endforelse
    </div>
  </div>

</div>
@endsection
