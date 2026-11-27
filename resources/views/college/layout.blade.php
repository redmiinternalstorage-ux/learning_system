<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>@yield('title','College Portal') — Learning Curve</title>
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Outfit:wght@400;700;800;900&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
body { font-family: 'Plus Jakarta Sans', sans-serif; background: #f8fafc; }
.font-outfit { font-family: 'Outfit', sans-serif; }
.sidebar { background: #1e1b4b; }
.nav-item { display:flex; align-items:center; gap:.875rem; padding:.65rem 1rem; border-radius:.875rem; font-size:.7rem; font-weight:700; text-transform:uppercase; letter-spacing:.07em; color:rgba(167,139,250,.7); transition:all .2s; }
.nav-item:hover { color:#a78bfa; background:rgba(167,139,250,.1); }
.nav-item.active { color:#fff !important; background:linear-gradient(90deg,rgba(99,102,241,.3),rgba(99,102,241,.05)); border-left:3px solid #6366f1; padding-left:calc(1rem - 3px); }
.nav-icon { width:1rem; text-align:center; font-size:.85rem; }
.glass { background:#fff; border:1px solid #e2e8f0; box-shadow:0 4px 20px -2px rgba(0,0,0,0.03); }
.inp { width:100%; padding:.65rem 1rem; border-radius:.875rem; border:1px solid #cbd5e1; font-size:.8rem; background:#fff; color:#0f172a; outline:none; transition:all .2s; }
.inp:focus { border-color:#6366f1; box-shadow:0 0 0 3px rgba(99,102,241,.12); }
.btn-primary { display:inline-flex; align-items:center; justify-content:center; gap:.5rem; padding:.65rem 1.5rem; border-radius:.875rem; background:linear-gradient(135deg,#6366f1,#8b5cf6); color:#fff; font-size:.75rem; font-weight:700; border:none; cursor:pointer; transition:all .2s; }
.btn-primary:hover { background:linear-gradient(135deg,#4f46e5,#7c3aed); transform:translateY(-1px); }
.btn-secondary { display:inline-flex; align-items:center; gap:.5rem; padding:.65rem 1.25rem; border-radius:.875rem; background:#f5f3ff; border:1px solid #c4b5fd; color:#5b21b6; font-size:.75rem; font-weight:700; cursor:pointer; transition:all .2s; }
.btn-secondary:hover { background:#ede9fe; }
.badge { display:inline-flex; align-items:center; padding:.2rem .65rem; border-radius:9999px; font-size:.68rem; font-weight:700; text-transform:uppercase; }
.badge-green { background:#f0fdf4; color:#047857; border:1px solid #bbf7d0; }
.badge-amber { background:#fffbeb; color:#b45309; border:1px solid #fde68a; }
.badge-rose  { background:#fff1f2; color:#be123c; border:1px solid #fecdd3; }
.badge-indigo { background:#eef2ff; color:#3730a3; border:1px solid #c7d2fe; }
.admin-table th { padding:.8rem 1.2rem; font-size:.65rem; font-weight:800; text-transform:uppercase; letter-spacing:.1em; color:#6366f1; background:#f5f3ff; border-bottom:1px solid #e0e7ff; }
.admin-table td { padding:.9rem 1.2rem; font-size:.78rem; border-bottom:1px solid #f1f5f9; }
.admin-table tr:last-child td { border-bottom:none; }
.section-label { font-size:.6rem; font-weight:800; text-transform:uppercase; letter-spacing:.12em; color:#818cf8; padding:0 .25rem; margin-bottom:.4rem; }
::-webkit-scrollbar { width:4px; }
::-webkit-scrollbar-thumb { background:#c4b5fd; border-radius:9999px; }
</style>
@stack('styles')
</head>
<body class="bg-slate-50 min-h-screen flex">

{{-- Sidebar --}}
<aside class="w-60 sidebar fixed top-0 left-0 h-full flex flex-col z-50 hidden lg:flex">
    {{-- Logo --}}
    <div class="px-5 py-5 border-b border-indigo-900/50">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-indigo-500 to-violet-600 flex items-center justify-center shadow-md">
                <i class="fa-solid fa-building-columns text-white text-sm"></i>
            </div>
            <div>
                <p class="font-black text-white text-xs font-outfit leading-tight">{{ Auth::guard('college')->user()->college_name }}</p>
                <p class="text-indigo-400 text-[9px] font-bold uppercase tracking-widest mt-0.5">College Portal</p>
            </div>
        </div>
    </div>

    {{-- Nav --}}
    <nav class="flex-1 px-3 py-5 overflow-y-auto space-y-1">
        <p class="section-label px-2 mb-2">Main</p>
        <a href="{{ route('college.dashboard') }}" class="nav-item {{ request()->routeIs('college.dashboard') ? 'active' : '' }}">
            <i class="nav-icon fa-solid fa-gauge-high"></i> Dashboard
        </a>
        <a href="{{ route('college.students') }}" class="nav-item {{ request()->routeIs('college.students*') ? 'active' : '' }}">
            <i class="nav-icon fa-solid fa-users"></i> Students
            @php $pendingCount = Auth::guard('college')->user()->studentsCount('pending'); @endphp
            @if($pendingCount > 0)
            <span class="ml-auto text-[8px] font-black bg-indigo-500 text-white px-1.5 py-0.5 rounded-full">{{ $pendingCount }}</span>
            @endif
        </a>
        <a href="{{ route('college.profile') }}" class="nav-item {{ request()->routeIs('college.profile*') ? 'active' : '' }}">
            <i class="nav-icon fa-solid fa-building"></i> College Profile
        </a>

        <div class="my-3 border-t border-indigo-900/40"></div>
        <p class="section-label px-2 mb-2">Quick Stats</p>
        <div class="mx-1 rounded-2xl p-3.5 space-y-2.5" style="background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.08)">
            @php $college = Auth::guard('college')->user(); @endphp
            <div class="flex justify-between items-center">
                <span class="text-[10px] font-bold text-indigo-300">Total Students</span>
                <span class="text-white font-black text-sm font-outfit">{{ $college->studentsCount() }}</span>
            </div>
            <div class="flex justify-between items-center">
                <span class="text-[10px] font-bold text-indigo-300">Approved</span>
                <span class="text-emerald-400 font-black text-sm font-outfit">{{ $college->studentsCount('approved') }}</span>
            </div>
            <div class="flex justify-between items-center">
                <span class="text-[10px] font-bold text-indigo-300">Pending</span>
                <span class="text-amber-400 font-black text-sm font-outfit">{{ $college->studentsCount('pending') }}</span>
            </div>
        </div>
    </nav>

    {{-- Footer --}}
    <div class="px-3 py-4 border-t border-indigo-900/50 space-y-1">
        <a href="{{ route('campus.home') }}" class="nav-item">
            <i class="nav-icon fa-solid fa-graduation-cap"></i> Campus Portal
        </a>
        <form method="POST" action="{{ route('college.logout') }}">
            @csrf
            <button type="submit" class="nav-item w-full text-rose-400 hover:text-rose-300 hover:bg-rose-900/20">
                <i class="nav-icon fa-solid fa-right-from-bracket"></i> Logout
            </button>
        </form>
    </div>
</aside>

{{-- Main --}}
<div class="flex-1 lg:ml-60 flex flex-col min-h-screen">
    {{-- Topbar --}}
    <header class="bg-white border-b border-slate-200 px-5 py-3.5 flex items-center justify-between shadow-sm sticky top-0 z-30">
        <div>
            <h1 class="font-black text-slate-900 text-sm font-outfit">@yield('page-title','Dashboard')</h1>
            <p class="text-[9px] font-bold text-indigo-500 uppercase tracking-widest mt-0.5">College Admin • {{ now()->format('d M Y') }}</p>
        </div>
        <div class="flex items-center gap-3">
            <div class="flex items-center gap-2 pl-3 border-l border-slate-200">
                <div class="w-8 h-8 rounded-xl bg-gradient-to-br from-indigo-500 to-violet-600 flex items-center justify-center text-white font-bold text-xs">
                    {{ strtoupper(substr(Auth::guard('college')->user()->college_name, 0, 1)) }}
                </div>
                <div class="hidden sm:block">
                    <p class="font-bold text-slate-800 text-xs leading-none truncate max-w-[120px]">{{ Auth::guard('college')->user()->college_name }}</p>
                    <p class="text-[9px] text-indigo-500 font-bold mt-0.5">College Admin</p>
                </div>
            </div>
        </div>
    </header>

    {{-- Flash --}}
    @if(session('success'))
    <div class="mx-5 mt-4 p-3.5 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs font-semibold flex items-center gap-2">
        <i class="fa-solid fa-circle-check text-emerald-500 flex-shrink-0"></i> {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div class="mx-5 mt-4 p-3.5 rounded-2xl bg-rose-50 border border-rose-200 text-rose-700 text-xs font-semibold flex items-center gap-2">
        <i class="fa-solid fa-circle-exclamation text-rose-500 flex-shrink-0"></i> {{ session('error') }}
    </div>
    @endif

    <main class="flex-1 p-5">
        @yield('content')
    </main>
</div>
</body>
</html>
