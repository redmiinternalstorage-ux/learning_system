<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $course->title }} - Learning Curve</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Outfit:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="/css/interactions.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                        outfit: ['"Outfit"', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .font-outfit { font-family: 'Outfit', sans-serif; }
        .video-container { position: relative; padding-bottom: 56.25%; height: 0; overflow: hidden; border-radius: 16px; }
        .video-container iframe,
        .video-container video { position: absolute; top: 0; left: 0; width: 100%; height: 100%; border-radius: 16px; }
        .btn-gradient {
            background: linear-gradient(135deg, #059669, #0d9488);
        }
        .btn-gradient:hover {
            background: linear-gradient(135deg, #047857, #0f766e);
        }
    </style>
</head>
<body class="bg-[#f8fffe] text-slate-800">

{{-- Navbar --}}
<nav class="bg-white border-b border-slate-200/80 sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center justify-between h-16">
        <a href="{{ route('explore') }}" class="flex items-center gap-2.5">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-emerald-500 to-teal-600 flex items-center justify-center shadow-md shadow-emerald-500/20">
                <i class="fa-solid fa-graduation-cap text-white text-sm"></i>
            </div>
            <span class="font-extrabold text-gray-900 text-lg font-outfit">Learning Curve</span>
        </a>
        <div class="flex items-center gap-3 text-xs font-bold uppercase tracking-wider">
            @auth
                <a href="{{ route('dashboard') }}" class="px-4 py-2.5 rounded-xl btn-gradient text-white shadow-xs transition">
                    <i class="fa-solid fa-gauge mr-1.5"></i>Dashboard
                </a>
            @else
                <a href="{{ route('login') }}" class="px-4 py-2.5 rounded-xl border border-slate-200 hover:bg-slate-50 text-slate-700 transition">
                    <i class="fa-solid fa-right-to-bracket mr-1.5"></i>Login
                </a>
                <a href="{{ route('register') }}" class="px-4 py-2.5 rounded-xl btn-gradient text-white shadow-xs transition">
                    <i class="fa-solid fa-user-plus mr-1.5"></i>Register
                </a>
            @endauth
        </div>
    </div>
</nav>

@if(session('success'))
<div class="max-w-7xl mx-auto px-4 pt-4">
    <div class="p-3.5 rounded-xl bg-emerald-50 border border-emerald-100 text-emerald-800 text-xs font-semibold flex items-center gap-2 shadow-2xs">
        <i class="fa-solid fa-circle-check text-emerald-500"></i> {{ session('success') }}
    </div>
</div>
@endif

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="grid lg:grid-cols-3 gap-8">

        {{-- Left: Course content --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Breadcrumb --}}
            <div class="flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-slate-400">
                <a href="{{ route('courses.public') }}" class="hover:text-emerald-600 transition">Courses</a>
                <i class="fa-solid fa-chevron-right text-[10px]"></i>
                <span class="text-slate-600 font-extrabold">{{ $course->title }}</span>
            </div>

            {{-- VIDEO THUMBNAIL / PLAY BUTTON (enrolled users only) --}}
            @if($enrolled && ($course->video_type === 'link' || $course->video_type === 'upload'))

            @php
                $videoSrc = null; $embedUrl = null; $isDirect = false; $isUpload = false;
                if ($course->video_type === 'upload' && $course->video_path) {
                    $videoSrc = Storage::url($course->video_path);
                    $isUpload = true;
                } elseif ($course->video_type === 'link' && $course->video_link) {
                    $vl = $course->video_link;
                    if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]{11})/', $vl, $m)) {
                        $embedUrl = 'https://www.youtube.com/embed/'.$m[1].'?rel=0&modestbranding=1&autoplay=1';
                    } elseif (preg_match('/vimeo\.com\/(\d+)/', $vl, $m)) {
                        $embedUrl = 'https://player.vimeo.com/video/'.$m[1].'?autoplay=1';
                    } elseif (preg_match('/\.(mp4|webm|ogg|mov)(\?.*)?$/i', $vl)) {
                        $videoSrc = $vl; $isDirect = true;
                    } else {
                        $videoSrc = $vl;
                    }
                }
                $catColors = ['Web Development'=>'from-blue-600 to-emerald-700','Data Science'=>'from-emerald-500 to-purple-700','Design'=>'from-pink-500 to-rose-600','AI & ML'=>'from-purple-500 to-violet-700','Mobile Development'=>'from-orange-500 to-amber-600','Marketing'=>'from-yellow-500 to-orange-500'];
                $grad = $catColors[$course->category] ?? 'from-emerald-500 to-purple-600';
            @endphp

            {{-- Clickable thumbnail --}}
            <div class="relative rounded-2xl overflow-hidden cursor-pointer group shadow-lg select-none"
                 style="padding-bottom:56.25%;" onclick="openVideoModal()">
                <div class="absolute inset-0 bg-gradient-to-br {{ $grad }}"></div>
                <div class="absolute inset-0 opacity-10" style="background-image:radial-gradient(circle at 1px 1px,white 1px,transparent 0);background-size:28px 28px;"></div>
                <div class="absolute inset-0 bg-slate-950/20 group-hover:bg-slate-950/45 transition-all duration-300"></div>
                <div class="absolute inset-0 flex flex-col items-center justify-center gap-4">
                    <div class="w-20 h-20 rounded-full bg-white/20 backdrop-blur-sm border-2 border-white/50 flex items-center justify-center
                                group-hover:scale-110 group-hover:bg-white/30 transition-all duration-300 shadow-2xl">
                        <i class="fa-solid fa-play text-white text-3xl ml-1"></i>
                    </div>
                    <div class="text-center">
                        <p class="text-white font-extrabold text-lg font-outfit">Watch Course Video</p>
                        <p class="text-white/70 text-xs font-bold uppercase tracking-widest mt-1">Click to play</p>
                    </div>
                </div>
                <div class="absolute bottom-0 left-0 right-0 px-5 py-4 bg-gradient-to-t from-slate-950/80 via-slate-950/30 to-transparent">
                    <span class="text-[10px] bg-white/20 border border-white/20 text-white px-2.5 py-1 rounded-lg font-bold uppercase tracking-wider backdrop-blur-sm">{{ $course->category }}</span>
                    <p class="text-white font-bold text-sm mt-1.5 truncate">{{ $course->title }}</p>
                </div>
            </div>

            {{-- ══ VIDEO MODAL ══ --}}
            <div id="video-modal"
                 class="fixed inset-0 z-[1000] flex items-center justify-center p-4 hidden"
                 onclick="if(event.target===this) closeVideoModal()">
                <div class="absolute inset-0 bg-slate-950/90 backdrop-blur-md"></div>
                <div class="relative w-full max-w-4xl z-10">
                    {{-- Header --}}
                    <div class="flex items-center justify-between mb-3 px-1">
                        <div class="min-w-0 pr-4">
                            <p class="text-white font-extrabold text-sm font-outfit truncate">{{ $course->title }}</p>
                            <p class="text-slate-400 text-[10px] font-bold uppercase tracking-widest mt-0.5">{{ $course->instructor_name }}</p>
                        </div>
                        <button onclick="closeVideoModal()"
                                class="w-9 h-9 rounded-xl bg-white/10 hover:bg-white/20 border border-white/10 text-white flex items-center justify-center transition text-sm flex-shrink-0">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>
                    {{-- Player --}}
                    <div class="relative rounded-2xl overflow-hidden bg-black shadow-2xl" style="padding-bottom:56.25%;">
                        @if($isUpload || $isDirect)
                            <video id="modal-video" controls controlslist="nodownload"
                                   class="absolute inset-0 w-full h-full">
                                <source src="{{ $videoSrc }}" type="video/mp4">
                            </video>
                        @elseif($embedUrl)
                            <iframe id="modal-iframe"
                                    class="absolute inset-0 w-full h-full"
                                    src="" data-src="{{ $embedUrl }}"
                                    frameborder="0"
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                    allowfullscreen></iframe>
                        @else
                            <div class="absolute inset-0 flex flex-col items-center justify-center gap-4 bg-slate-900">
                                <i class="fa-solid fa-circle-play text-white/20 text-6xl"></i>
                                <a href="{{ $videoSrc ?? $course->video_link }}" target="_blank"
                                   class="px-6 py-3 rounded-xl btn-gradient text-white font-bold text-sm shadow-lg flex items-center gap-2">
                                    <i class="fa-solid fa-up-right-from-square"></i> Open Video in New Tab
                                </a>
                            </div>
                        @endif
                    </div>
                    <p class="text-center text-slate-500 text-[10px] font-bold uppercase tracking-widest mt-3">
                        Press <kbd class="px-1.5 py-0.5 rounded bg-white/10 text-white text-[9px]">ESC</kbd> to close
                    </p>
                </div>
            </div>

            {{-- Modal animation style + JS --}}
            <style>
                @keyframes modalSlideUp { from{transform:translateY(30px);opacity:0} to{transform:translateY(0);opacity:1} }
                #video-modal > div:last-child { animation: modalSlideUp 0.3s cubic-bezier(0.16,1,0.3,1) forwards; }
            </style>
            <script>
                function openVideoModal() {
                    document.getElementById('video-modal').classList.remove('hidden');
                    document.body.style.overflow = 'hidden';
                    // lazy-load iframe src
                    const iframe = document.getElementById('modal-iframe');
                    if (iframe && !iframe.src) iframe.src = iframe.dataset.src;
                    // autoplay html5 video
                    const vid = document.getElementById('modal-video');
                    if (vid) { vid.currentTime = 0; vid.play().catch(()=>{}); }
                }
                function closeVideoModal() {
                    document.getElementById('video-modal').classList.add('hidden');
                    document.body.style.overflow = '';
                    // pause/reset
                    const iframe = document.getElementById('modal-iframe');
                    if (iframe) { const s = iframe.src; iframe.src = ''; iframe.src = s; }
                    const vid = document.getElementById('modal-video');
                    if (vid) { vid.pause(); }
                }
                document.addEventListener('keydown', e => { if(e.key==='Escape') closeVideoModal(); });
            </script>

            @elseif(!$enrolled && ($course->video_type === 'link' || $course->video_type === 'upload'))
            {{-- Locked video preview for non-enrolled --}}
            <div class="rounded-2xl overflow-hidden relative shadow-md" style="padding-bottom:56.25%;">
                @php
                $catColors = ['Web Development'=>'from-blue-650 to-emerald-700','Data Science'=>'from-emerald-500 to-purple-750','Design'=>'from-pink-500 to-rose-600','AI & ML'=>'from-purple-500 to-violet-600','Mobile Development'=>'from-orange-500 to-amber-600','Marketing'=>'from-yellow-500 to-orange-500'];
                $grad = $catColors[$course->category] ?? 'from-emerald-500 to-purple-600';
                @endphp
                <div class="absolute inset-0 bg-gradient-to-br {{ $grad }}"></div>
                <div class="absolute inset-0 flex flex-col items-center justify-center text-white gap-3 bg-slate-950/20 backdrop-blur-xs">
                    <div class="w-16 h-16 rounded-full bg-white/20 flex items-center justify-center backdrop-blur-sm border border-white/20 shadow-md">
                        <i class="fa-solid fa-lock text-3xl"></i>
                    </div>
                    <p class="font-extrabold text-xl font-outfit tracking-tight">Enroll to watch</p>
                    <p class="text-white/80 text-xs font-bold uppercase tracking-wide">This course includes premium video content</p>
                </div>
            </div>

            @else
            {{-- No video — show gradient banner --}}
            @php
            $catColors = ['Web Development'=>'from-blue-650 to-emerald-700','Data Science'=>'from-emerald-500 to-purple-750','Design'=>'from-pink-500 to-rose-600','AI & ML'=>'from-purple-500 to-violet-600','Mobile Development'=>'from-orange-500 to-amber-600','Marketing'=>'from-yellow-500 to-orange-500'];
            $grad = $catColors[$course->category] ?? 'from-emerald-500 to-purple-600';
            @endphp
            <div class="rounded-2xl overflow-hidden h-52 relative shadow-xs">
                <div class="absolute inset-0 bg-gradient-to-br {{ $grad }}"></div>
                <div class="absolute inset-0 flex items-center justify-center bg-slate-950/10">
                    <i class="fa-solid fa-graduation-cap text-white/30 text-7xl"></i>
                </div>
                <div class="absolute bottom-0 left-0 right-0 p-5 bg-gradient-to-t from-slate-950/70 via-slate-950/30 to-transparent">
                    <span class="text-[10px] bg-white/20 border border-white/20 backdrop-blur-xs text-white px-2.5 py-1 rounded-lg font-bold uppercase tracking-wider">{{ $course->category }}</span>
                    <h1 class="text-white text-2xl font-black mt-2 font-outfit tracking-tight">{{ $course->title }}</h1>
                </div>
            </div>
            @endif

            {{-- Meta info --}}
            <div class="flex flex-wrap gap-4 text-xs font-bold uppercase tracking-wider text-slate-455">
                <span><i class="fa-solid fa-user-tie mr-1.5 text-emerald-550"></i>{{ $course->instructor_name }}</span>
                <span><i class="fa-solid fa-signal mr-1.5 text-emerald-555"></i>{{ $course->level }}</span>
                <span><i class="fa-solid fa-clock mr-1.5 text-emerald-550"></i>{{ $course->duration_hours }} hours</span>
                <span><i class="fa-solid fa-book mr-1.5 text-emerald-550"></i>{{ $course->lessons_count }} lessons</span>
            </div>

            {{-- Description --}}
            <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-2xs">
                <h2 class="font-extrabold text-slate-900 mb-3 font-outfit text-base">About this course</h2>
                <p class="text-slate-500 text-[13px] leading-relaxed">{{ $course->description }}</p>
            </div>

            {{-- Tasks (enrolled only) --}}
            @if($enrolled && $tasks->count())
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-2xs overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 flex items-center gap-2">
                    <i class="fa-solid fa-file-pen text-emerald-600"></i>
                    <h2 class="font-extrabold text-slate-900 font-outfit text-base">Assignments</h2>
                </div>
                <div class="divide-y divide-slate-100">
                    @foreach($tasks as $task)
                    <a href="{{ route('tasks.show', $task) }}" class="flex items-center gap-4 px-6 py-4 hover:bg-slate-50 transition-all duration-200">
                        <div class="w-10 h-10 rounded-xl bg-emerald-50 border border-emerald-100 flex items-center justify-center flex-shrink-0">
                            <i class="fa-solid fa-file-signature text-emerald-600"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-bold text-slate-800 text-sm truncate">{{ $task->title }}</p>
                            @if($task->due_date)
                            <p class="text-slate-400 text-[10px] font-bold uppercase tracking-wider mt-0.5">Due: {{ \Carbon\Carbon::parse($task->due_date)->format('d M Y') }}</p>
                            @endif
                        </div>
                        <span class="text-[10px] bg-emerald-50 text-emerald-700 border border-emerald-100 px-2.5 py-1 rounded-full font-bold uppercase tracking-wider flex-shrink-0">
                            {{ $task->points }} pts
                        </span>
                    </a>
                    @endforeach
                </div>
            </div>
            @endif

        </div>

        {{-- Right: Enroll card --}}
        <div>
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 sticky top-24">
                <div class="text-center mb-5">
                    <p class="text-4xl font-black font-outfit {{ $course->price == 0 ? 'text-emerald-500' : 'text-slate-900' }}">
                        {{ $course->price_display }}
                    </p>
                    @if($course->price == 0)
                    <p class="text-emerald-500 text-xs font-bold uppercase tracking-wider mt-1.5">Free forever</p>
                    @endif
                </div>

                @auth
                    @if($enrolled)
                        <div class="w-full py-3.5 rounded-xl bg-emerald-50 border border-emerald-100 text-emerald-800 font-bold text-xs uppercase tracking-wider text-center mb-3 flex items-center justify-center gap-2">
                            <i class="fa-solid fa-circle-check text-emerald-500 text-sm"></i> You are enrolled
                        </div>
                        <a href="{{ route('tasks.index') }}"
                           class="w-full py-3 rounded-xl border border-emerald-200 text-emerald-650 font-bold text-xs uppercase tracking-wider text-center hover:bg-emerald-50/50 transition-all duration-200 flex items-center justify-center gap-2 mb-3 shadow-3xs">
                            <i class="fa-solid fa-file-pen"></i> View Assignments
                        </a>
                        <form method="POST" action="{{ route('courses.unenroll', $course) }}">
                            @csrf
                            <button type="submit" onclick="return confirm('Unenroll from this course?')"
                                    class="w-full py-2 rounded-xl text-rose-500 hover:text-rose-600 text-[10px] font-bold uppercase tracking-wider hover:bg-rose-50 transition-all duration-200">
                                Unenroll
                            </button>
                        </form>
                    @else
                        <form method="POST" action="{{ route('courses.enroll', $course) }}">
                            @csrf
                            <button type="submit"
                                    class="w-full py-3.5 rounded-xl btn-gradient text-white font-bold text-xs uppercase tracking-wider transition-all duration-200 shadow-sm flex items-center justify-center gap-2">
                                <i class="fa-solid fa-bolt"></i> Enroll Now — It's {{ $course->price == 0 ? 'Free' : $course->price_display }}
                            </button>
                        </form>
                    @endif
                @else
                    <a href="{{ route('register') }}"
                       class="w-full py-3.5 rounded-xl btn-gradient text-white font-bold text-xs uppercase tracking-wider transition-all duration-200 shadow-sm flex items-center justify-center gap-2">
                        <i class="fa-solid fa-bolt"></i> Enroll for Free
                    </a>
                    <p class="text-center text-slate-400 text-xs font-semibold mt-4">
                        Already have an account?
                        <a href="{{ route('login') }}" class="text-emerald-650 font-extrabold hover:underline ml-1">Log in</a>
                    </p>
                @endauth

                <div class="mt-5 space-y-3 border-t border-slate-100 pt-5">
                    @if($course->video_type === 'link' || $course->video_type === 'upload')
                    <div class="flex items-center gap-2 text-xs font-semibold text-slate-500">
                        <i class="fa-solid fa-video text-emerald-500 w-4 text-center"></i>
                        @if($course->video_type === 'upload') Includes video lesson
                        @else Video via link included
                        @endif
                    </div>
                    @endif
                    <div class="flex items-center gap-2 text-xs font-semibold text-slate-500">
                        <i class="fa-solid fa-infinity text-emerald-500 w-4 text-center"></i> Lifetime access
                    </div>
                    <div class="flex items-center gap-2 text-xs font-semibold text-slate-500">
                        <i class="fa-solid fa-mobile-screen text-emerald-500 w-4 text-center"></i> Access on all devices
                    </div>
                    <div class="flex items-center gap-2 text-xs font-semibold text-slate-500">
                        <i class="fa-solid fa-award text-emerald-500 w-4 text-center"></i> Certificate on completion
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<footer class="bg-white text-slate-400 py-10 mt-20 border-t border-emerald-100">
    <div class="max-w-7xl mx-auto px-4 text-center text-xs font-medium uppercase tracking-wider">
        © {{ date('Y') }} Learning Curve. All rights reserved.
    </div>
</footer>
@include('partials.ad-overlay')
@auth
@include('partials.ai-chatbot')
@endauth
@include('partials.global-loader')
</body>
</html>
