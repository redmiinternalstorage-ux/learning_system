@extends('campus.layout')
@section('title',$course->title)
@section('page-title',$course->title)

@section('content')
<div class="space-y-5">
    {{-- Course header --}}
    <div class="glass-card p-6">
        <div class="flex items-start justify-between gap-4">
            <div class="flex items-start gap-4">
                <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-emerald-400 to-teal-500 flex items-center justify-center text-white text-xl font-black shadow-md flex-shrink-0">
                    {{ strtoupper(substr($course->title, 0, 1)) }}
                </div>
                <div>
                    <h2 class="font-extrabold text-slate-900 dark:text-white text-lg">{{ $course->title }}</h2>
                    <p class="text-sm text-slate-500 mt-0.5">{{ $course->instructor_name }} • {{ $course->category }} • {{ $course->level }}</p>
                    <div class="flex items-center gap-3 mt-2">
                        <span class="px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-700 text-xs font-black">FREE Campus Access</span>
                        <span class="text-xs text-slate-400"><i class="fa-solid fa-clock mr-1"></i>{{ $course->duration_hours }}h</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Progress bar --}}
        <div class="mt-5">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-bold text-slate-600 dark:text-slate-300">Your Progress</span>
                <span class="text-sm font-black text-emerald-600" id="progress-display">{{ $enrollment->progress }}%</span>
            </div>
            <div class="w-full bg-slate-100 dark:bg-slate-700 rounded-full h-3 cursor-pointer" id="progress-bar-wrapper">
                <div class="bg-gradient-to-r from-emerald-500 to-teal-500 rounded-full h-3 transition-all" id="progress-bar" style="width:{{ $enrollment->progress }}%"></div>
            </div>
            <div class="flex justify-between mt-1">
                <span class="text-[10px] text-slate-400">Click bar to update progress</span>
                @if($enrollment->progress >= 100)
                <span class="text-[10px] font-black text-emerald-600">🎉 Completed!</span>
                @endif
            </div>
        </div>
    </div>

    <div class="grid lg:grid-cols-3 gap-5">
        {{-- Course info --}}
        <div class="lg:col-span-2 space-y-4">
            @if($course->description)
            <div class="glass-card p-5">
                <h3 class="font-extrabold text-slate-900 dark:text-white text-sm mb-3">About This Course</h3>
                <p class="text-sm text-slate-600 dark:text-slate-300 leading-relaxed">{{ $course->description }}</p>
            </div>
            @endif

            @if($course->video_link || $course->video_path)
            <div class="glass-card p-5">
                <h3 class="font-extrabold text-slate-900 dark:text-white text-sm mb-3">Course Video</h3>
                @if($course->video_link)
                <div class="aspect-video rounded-xl overflow-hidden bg-slate-900">
                    <iframe src="{{ $course->video_link }}" class="w-full h-full" frameborder="0" allowfullscreen></iframe>
                </div>
                @else
                <video controls class="w-full rounded-xl" src="{{ asset('storage/' . $course->video_path) }}"></video>
                @endif
            </div>
            @endif

            @if($tasks->count())
            <div class="glass-card p-5">
                <h3 class="font-extrabold text-slate-900 dark:text-white text-sm mb-3">Course Tasks ({{ $tasks->count() }})</h3>
                <div class="space-y-2">
                    @foreach($tasks as $task)
                    <div class="p-3 rounded-xl bg-emerald-50/60 dark:bg-emerald-900/20 border border-emerald-100 dark:border-emerald-800/30">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs font-bold text-slate-900 dark:text-white">{{ $task->title }}</p>
                                @if($task->due_date)
                                <p class="text-[10px] text-slate-500 mt-0.5">Due: {{ \Carbon\Carbon::parse($task->due_date)->format('d M Y') }}</p>
                                @endif
                            </div>
                            <span class="text-[10px] font-black text-emerald-700 bg-emerald-100 px-2 py-0.5 rounded-full">{{ $task->points }} pts</span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        {{-- Sidebar --}}
        <div class="space-y-4">
            <div class="glass-card p-5">
                <h3 class="font-extrabold text-slate-900 dark:text-white text-sm mb-3">Update Progress</h3>
                <div class="space-y-2">
                    @foreach([0,25,50,75,100] as $p)
                    <button onclick="setProgress({{ $p }})"
                            class="w-full flex items-center justify-between p-2.5 rounded-xl {{ $enrollment->progress >= $p ? 'bg-emerald-100 dark:bg-emerald-900/40 border-emerald-300' : 'bg-slate-50 dark:bg-slate-800/50 border-slate-200' }} border text-xs font-bold text-slate-700 dark:text-slate-300 hover:border-emerald-400 transition">
                        <span>{{ $p }}% {{ $p === 100 ? '🎉' : '' }}</span>
                        @if($enrollment->progress >= $p)
                        <i class="fa-solid fa-check text-emerald-500"></i>
                        @endif
                    </button>
                    @endforeach
                </div>
                <div id="progress-msg" class="mt-3 text-xs text-center hidden"></div>
            </div>

            <div class="glass-card p-4 text-xs space-y-2">
                <div class="flex justify-between"><span class="text-slate-500">Category</span><span class="font-bold text-slate-700 dark:text-slate-300">{{ $course->category }}</span></div>
                <div class="flex justify-between"><span class="text-slate-500">Level</span><span class="font-bold text-slate-700 dark:text-slate-300">{{ $course->level }}</span></div>
                <div class="flex justify-between"><span class="text-slate-500">Duration</span><span class="font-bold text-slate-700 dark:text-slate-300">{{ $course->duration_hours }}h</span></div>
                <div class="flex justify-between"><span class="text-slate-500">Instructor</span><span class="font-bold text-slate-700 dark:text-slate-300">{{ $course->instructor_name }}</span></div>
                <div class="flex justify-between"><span class="text-slate-500">Enrolled</span><span class="font-bold text-emerald-600">{{ $course->created_at->format('d M Y') }}</span></div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function setProgress(val) {
    fetch("{{ route('campus.update-progress', $course) }}", {
        method: 'POST',
        headers: {'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content, 'Content-Type':'application/json', 'Accept':'application/json'},
        body: JSON.stringify({progress: val})
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            document.getElementById('progress-bar').style.width = val + '%';
            document.getElementById('progress-display').textContent = val + '%';
            var msg = document.getElementById('progress-msg');
            msg.textContent = val >= 100 ? '🎉 Course completed!' : '✅ Progress updated!';
            msg.className = 'mt-3 text-xs text-center text-emerald-600 font-bold';
            msg.classList.remove('hidden');
            setTimeout(() => { if(val >= 100) location.reload(); }, 1000);
        }
    });
}
</script>
@endpush
