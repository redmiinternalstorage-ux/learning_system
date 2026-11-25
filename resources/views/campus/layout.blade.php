<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>@yield('title','Campus Portal') — Learning Curve</title>
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800;900&family=Outfit:wght@400;600;700;800;900&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<script>
(function(){var s=localStorage.getItem('lc-theme');if(s==='dark')document.documentElement.classList.add('dark');})();
tailwind.config={darkMode:'class',theme:{extend:{fontFamily:{sans:['"Plus Jakarta Sans"','sans-serif'],outfit:['"Outfit"','sans-serif']}}}}
</script>
<style>
body{font-family:'Plus Jakarta Sans',sans-serif;}
.sidebar{background:#fff;border-right:1px solid #d1fae5;box-shadow:2px 0 16px rgba(5,150,105,.04);}
html.dark .sidebar{background:#0f2318;border-right-color:#1a3a28;}
html.dark body{background:#071510;color:#e2e8f0;}
html.dark .topbar{background:rgba(7,21,16,.95)!important;border-bottom-color:#1a3a28!important;}
html.dark .glass-card{background:#0f2318;border-color:#1a3a28;}
.nav-link{display:flex;align-items:center;gap:.75rem;padding:.6rem .875rem;border-radius:.875rem;font-size:.75rem;font-weight:700;color:#64748b;transition:all .2s;text-decoration:none;}
.nav-link:hover{color:#059669;background:#f0fdf4;}
.nav-link.active{background:linear-gradient(90deg,rgba(5,150,105,.12),rgba(5,150,105,.03));color:#047857;border-left:3px solid #059669;padding-left:calc(.875rem - 3px);font-weight:800;}
html.dark .nav-link{color:#94a3b8;}
html.dark .nav-link:hover,.html.dark .nav-link.active{color:#34d399;background:rgba(16,185,129,.1);}
.glass-card{background:#fff;border:1px solid #d1fae5;border-radius:1.25rem;box-shadow:0 4px 20px -2px rgba(5,150,105,.06);}
.topbar{background:rgba(255,255,255,.95);backdrop-filter:blur(16px);border-bottom:1px solid #d1fae5;box-shadow:0 1px 8px rgba(5,150,105,.05);}
.inp{width:100%;padding:.65rem 1rem;border-radius:.875rem;border:1px solid #d1fae5;font-size:.8rem;background:#fff;color:#0f172a;transition:all .2s;outline:none;}
.inp:focus{border-color:#059669;box-shadow:0 0 0 3px rgba(5,150,105,.12);}
html.dark .inp{background:#0f2318;border-color:#1a3a28;color:#e2e8f0;}
.btn-green{display:inline-flex;align-items:center;justify-content:center;gap:.5rem;padding:.65rem 1.25rem;border-radius:.875rem;background:linear-gradient(135deg,#059669,#0d9488);color:#fff;font-size:.75rem;font-weight:700;border:none;cursor:pointer;transition:all .2s;}
.btn-green:hover{background:linear-gradient(135deg,#047857,#0f766e);transform:translateY(-1px);box-shadow:0 6px 20px rgba(5,150,105,.3);}
::-webkit-scrollbar{width:4px;} ::-webkit-scrollbar-thumb{background:#a7f3d0;border-radius:9999px;}
</style>
@stack('styles')
</head>
<body class="bg-emerald-50/30 dark:bg-[#071510] min-h-screen flex">

{{-- Mobile backdrop --}}
<div id="mob-backdrop" class="fixed inset-0 bg-slate-900/50 z-40 hidden lg:hidden" onclick="toggleSidebar()"></div>

{{-- SIDEBAR --}}
<aside id="campus-sidebar" class="sidebar w-64 fixed top-0 left-0 h-full flex flex-col z-50 transform -translate-x-full lg:translate-x-0 transition-transform duration-300">
    {{-- Logo --}}
    <div class="px-5 py-4 border-b border-emerald-100 dark:border-[#1a3a28] flex items-center justify-between flex-shrink-0">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center shadow-md shadow-emerald-500/25">
                <i class="fa-solid fa-graduation-cap text-white text-sm"></i>
            </div>
            <div>
                <p class="font-extrabold text-slate-900 dark:text-white text-sm font-outfit">Campus Portal</p>
                <p class="text-emerald-600 text-[9px] font-bold uppercase tracking-widest">Learning Curve</p>
            </div>
        </div>
        <button class="lg:hidden text-slate-400 hover:text-slate-600" onclick="toggleSidebar()">
            <i class="fa-solid fa-xmark text-lg"></i>
        </button>
    </div>

    {{-- Student card --}}
    @php $campusStudent = Auth::guard('campus')->user(); @endphp
    <div class="mx-3 mt-3 p-3 rounded-2xl bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-100 dark:border-emerald-800/50 flex items-center gap-3 flex-shrink-0">
        <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center text-white font-black text-sm font-outfit flex-shrink-0">
            {{ strtoupper(substr($campusStudent->name, 0, 1)) }}
        </div>
        <div class="min-w-0">
            <p class="text-slate-900 dark:text-white font-bold text-xs truncate">{{ $campusStudent->name }}</p>
            <p class="text-emerald-600 text-[10px] truncate">{{ $campusStudent->college_name ?? 'Campus Student' }}</p>
        </div>
    </div>

    {{-- Nav --}}
    <nav class="flex-1 px-3 py-4 space-y-0.5 overflow-y-auto">
        <p class="text-[9px] font-black uppercase tracking-widest text-emerald-600 px-3 mb-2">Main</p>
        <a href="{{ route('campus.dashboard') }}" class="nav-link {{ request()->routeIs('campus.dashboard') ? 'active' : '' }}">
            <i class="fa-solid fa-house w-4 text-center"></i> Dashboard
        </a>
        <a href="{{ route('campus.courses') }}" class="nav-link {{ request()->routeIs('campus.courses*') || request()->routeIs('campus.course-detail') ? 'active' : '' }}">
            <i class="fa-solid fa-book-open w-4 text-center"></i> Browse Courses
        </a>
        <a href="{{ route('campus.progress') }}" class="nav-link {{ request()->routeIs('campus.progress') ? 'active' : '' }}">
            <i class="fa-solid fa-chart-line w-4 text-center"></i> My Progress
        </a>
        <a href="{{ route('campus.leaderboard') }}" class="nav-link {{ request()->routeIs('campus.leaderboard') ? 'active' : '' }}">
            <i class="fa-solid fa-trophy w-4 text-center"></i> Leaderboard
        </a>
        <a href="{{ route('campus.profile') }}" class="nav-link {{ request()->routeIs('campus.profile') ? 'active' : '' }}">
            <i class="fa-solid fa-user-gear w-4 text-center"></i> Profile
        </a>

        <div class="pt-3 mt-3 border-t border-emerald-100 dark:border-emerald-800/30">
            <p class="text-[9px] font-black uppercase tracking-widest text-emerald-600 px-3 mb-2">Info</p>
            @php
                $totalEnrolled   = \App\Models\CampusEnrollment::where('campus_student_id', $campusStudent->id)->count();
                $totalCompleted  = \App\Models\CampusEnrollment::where('campus_student_id', $campusStudent->id)->where('progress', 100)->count();
            @endphp
            <div class="mx-1 p-3 rounded-xl bg-emerald-50/80 dark:bg-emerald-900/20 border border-emerald-100 dark:border-emerald-800/30 space-y-2 text-xs">
                <div class="flex justify-between"><span class="text-slate-500">Enrolled</span><span class="font-black text-emerald-700">{{ $totalEnrolled }}</span></div>
                <div class="flex justify-between"><span class="text-slate-500">Completed</span><span class="font-black text-emerald-700">{{ $totalCompleted }}</span></div>
                <div class="flex justify-between"><span class="text-slate-500">College</span><span class="font-black text-emerald-700 truncate max-w-[100px]">{{ $campusStudent->college_name ?? '-' }}</span></div>
            </div>
        </div>
    </nav>

    {{-- Logout --}}
    <div class="px-3 pb-4 border-t border-emerald-100 dark:border-emerald-800/30">
        <form method="POST" action="{{ route('campus.logout') }}" class="mt-3">
            @csrf
            <button type="submit" class="nav-link w-full text-left text-rose-500 hover:text-rose-600 hover:bg-rose-50">
                <i class="fa-solid fa-right-from-bracket w-4 text-center"></i> Logout
            </button>
        </form>
        <a href="{{ route('home') }}" class="nav-link text-slate-400 hover:text-slate-600 mt-1">
            <i class="fa-solid fa-globe w-4 text-center"></i> Main Website
        </a>
    </div>
</aside>

{{-- MAIN --}}
<div class="flex-1 lg:ml-64 flex flex-col min-h-screen">
    {{-- Topbar --}}
    <header class="topbar sticky top-0 z-30 px-5 py-3 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <button class="lg:hidden w-9 h-9 rounded-xl bg-emerald-50 text-emerald-700 border border-emerald-100 flex items-center justify-center hover:bg-emerald-100 transition" onclick="toggleSidebar()">
                <i class="fa-solid fa-bars text-sm"></i>
            </button>
            <div>
                <h1 class="font-extrabold text-slate-900 dark:text-white text-base font-outfit">@yield('page-title','Dashboard')</h1>
                <p class="text-slate-400 text-[10px] font-bold uppercase tracking-wider mt-0.5">{{ now()->format('l, d M Y') }}</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            {{-- Dark mode --}}
            <button onclick="toggleDarkMode()" class="w-9 h-9 rounded-xl flex items-center justify-center transition border bg-slate-100 hover:bg-slate-200 border-slate-200 text-slate-600 dark:bg-slate-700 dark:border-slate-600 dark:text-yellow-400">
                <i id="theme-icon-campus" class="fa-solid fa-moon text-sm"></i>
            </button>
            {{-- Status badge --}}
            <span class="hidden sm:flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs font-bold">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Campus Student
            </span>
        </div>
    </header>

    {{-- Flash --}}
    @if(session('success') || session('error') || session('info') || session('welcome'))
    <div class="px-5 pt-4 space-y-2">
        @foreach(['success'=>'emerald','error'=>'rose','info'=>'teal','welcome'=>'emerald'] as $key=>$color)
        @if(session($key))
        <div class="p-3.5 rounded-2xl bg-{{ $color }}-50 border border-{{ $color }}-200 text-{{ $color }}-800 text-xs font-semibold flex items-center gap-2">
            <i class="fa-solid fa-circle-check text-{{ $color }}-500 flex-shrink-0"></i> {{ session($key) }}
        </div>
        @endif
        @endforeach
    </div>
    @endif

    <main class="flex-1 p-5 lg:p-6">
        @yield('content')
    </main>
</div>

<script>
function toggleSidebar(){document.getElementById('campus-sidebar').classList.toggle('-translate-x-full');document.getElementById('mob-backdrop').classList.toggle('hidden');}
function toggleDarkMode(){var d=document.documentElement.classList.toggle('dark');localStorage.setItem('lc-theme',d?'dark':'light');updateIcon();}
function updateIcon(){var i=document.getElementById('theme-icon-campus');if(i)i.className='fa-solid '+(document.documentElement.classList.contains('dark')?'fa-sun':'fa-moon')+' text-sm';}
document.addEventListener('DOMContentLoaded',updateIcon);
</script>
@stack('scripts')
</body>
</html>
