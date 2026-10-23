<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') - Learning Curve</title>
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Outfit:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="/css/interactions.css">
    <script>
        // Always light mode for student portal
        document.documentElement.classList.remove('dark');
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans:   ['"Plus Jakarta Sans"', 'sans-serif'],
                        outfit: ['"Outfit"', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .font-outfit { font-family: 'Outfit', sans-serif; }

        /* ── Sidebar — clean white with green accent ── */
        .sidebar {
            background: #ffffff;
            border-right: 1px solid #e2e8f0;
            box-shadow: 2px 0 16px rgba(0, 0, 0, 0.04);
        }

        /* ── Nav items ── */
        .nav-link {
            display: flex; align-items: center; gap: 0.75rem;
            padding: 0.65rem 0.875rem; border-radius: 0.875rem;
            font-size: 0.78rem; font-weight: 600; color: #64748b;
            transition: all 0.2s; text-decoration: none; position: relative;
        }
        .nav-link:hover { color: #047857; background: #f0fdf4; }
        .nav-link.active {
            background: linear-gradient(90deg, rgba(5, 150, 105, 0.12) 0%, rgba(5, 150, 105, 0.03) 100%);
            color: #047857;
            border-left: 3px solid #059669;
            padding-left: calc(0.875rem - 3px);
            font-weight: 700;
        }
        .nav-link .icon { width: 1rem; text-align: center; font-size: 0.875rem; color: #94a3b8; }
        .nav-link:hover .icon { color: #059669; }
        .nav-link.active .icon { color: #059669; }

        /* ── Topbar ── */
        .topbar {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(16px);
            border-bottom: 1px solid #e2e8f0;
            box-shadow: 0 1px 8px rgba(0,0,0,0.04);
        }

        /* ── Card hover lift ── */
        .card-lift { transition: transform 0.25s ease, box-shadow 0.25s ease; }
        .card-lift:hover { transform: translateY(-3px); box-shadow: 0 16px 40px -8px rgba(5, 150, 105, 0.10); }

        /* ── Flash messages ── */
        @keyframes slideDown { from{transform:translateY(-8px);opacity:0} to{transform:translateY(0);opacity:1} }
        .flash { animation: slideDown 0.25s ease forwards; }

        /* ── Coming Soon Modal ── */
        @keyframes modalPop {
            from { transform: scale(0.90) translateY(16px); opacity: 0; }
            to   { transform: scale(1)    translateY(0);    opacity: 1; }
        }
        .animate-modal { animation: modalPop 0.28s cubic-bezier(0.34, 1.56, 0.64, 1) forwards; }

        /* ── Scrollbar ── */
        ::-webkit-scrollbar { width: 4px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #a7f3d0; border-radius: 9999px; }
        ::-webkit-scrollbar-thumb:hover { background: #059669; }

        /* ── Shared input style ── */
        .inp {
            width: 100%; padding: 0.65rem 1rem; border-radius: 0.875rem;
            border: 1px solid #cbd5e1; font-size: 0.8rem;
            background: white; color: #1e293b; transition: all 0.2s;
            outline: none;
        }
        .inp:focus { border-color: #059669; box-shadow: 0 0 0 3px rgba(5, 150, 105, 0.12); }
        .inp::placeholder { color: #94a3b8; }
        textarea.inp { resize: vertical; line-height: 1.6; }
        select.inp { appearance: auto; }

        /* ── Badge chips ── */
        .badge { display:inline-flex; align-items:center; gap:0.3rem; padding:0.2rem 0.65rem; border-radius:9999px; font-size:0.68rem; font-weight:700; text-transform:uppercase; letter-spacing:0.05em; }
        .badge-green, .badge-emerald { background:#f0fdf4; color:#047857; border:1px solid #bbf7d0; }
        .badge-purple { background:#f0fdf4; color:#047857; border:1px solid #a7f3d0; }
        .badge-blue   { background:#eff6ff; color:#1d4ed8; border:1px solid #bfdbfe; }
        .badge-amber  { background:#fffbeb; color:#b45309; border:1px solid #fde68a; }
        .badge-rose   { background:#fff1f2; color:#be123c; border:1px solid #fecdd3; }
        .badge-gray   { background:#f8fafc; color:#64748b; border:1px solid #e2e8f0; }

        /* ── Chat bubbles ── */
        .bubble-me    { background: linear-gradient(135deg, #059669, #0d9488); color: white; }
        .bubble-admin { background: #f1f5f9; color: #1e293b; }

        /* ── Section labels ── */
        .section-label { font-size: 0.63rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.12em; color: #059669; }

        /* ── Sidebar logo area ── */
        .sidebar-brand {
            background: linear-gradient(135deg, #f0fdf4, #dcfce7);
            border-bottom: 1px solid #d1fae5;
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 min-h-screen flex">

{{-- Mobile backdrop --}}
<div id="mob-backdrop" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-40 hidden lg:hidden" onclick="toggleSidebar()"></div>

{{-- ══════════════ SIDEBAR ══════════════ --}}
<aside id="app-sidebar" class="sidebar w-64 fixed top-0 left-0 h-full flex flex-col z-50 transform -translate-x-full lg:translate-x-0 transition-transform duration-300 shadow-xl lg:shadow-none">

    {{-- Logo --}}
    <div class="sidebar-brand px-5 py-4 flex items-center justify-between flex-shrink-0">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-2xl bg-gradient-to-br from-emerald-500 via-teal-500 to-emerald-600 flex items-center justify-center shadow-md shadow-emerald-500/25">
                <i class="fa-solid fa-graduation-cap text-white text-sm"></i>
            </div>
            <div>
                <p class="font-extrabold text-slate-900 text-sm font-outfit leading-tight">Learning Curve</p>
                <p class="text-emerald-600 text-[9px] font-bold uppercase tracking-widest mt-0.5">LMS Portal</p>
            </div>
        </div>
        <button class="lg:hidden text-slate-400 hover:text-slate-700 transition" onclick="toggleSidebar()">
            <i class="fa-solid fa-xmark text-lg"></i>
        </button>
    </div>

    {{-- User mini card --}}
    <div class="mx-3 mt-3 p-3 rounded-2xl bg-emerald-50 border border-emerald-100 flex items-center gap-3 flex-shrink-0">
        <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center text-white font-bold text-sm font-outfit flex-shrink-0 shadow-sm">
            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
        </div>
        <div class="min-w-0">
            <p class="text-slate-900 font-bold text-xs truncate">{{ Auth::user()->name }}</p>
            <p class="text-slate-500 text-[10px] truncate">{{ Auth::user()->email }}</p>
        </div>
    </div>

    {{-- Navigation --}}
    <nav class="flex-1 px-3 py-4 space-y-0.5 overflow-y-auto mt-1">

        <p class="section-label px-3 mb-2 mt-1">Menu</p>

        <a href="{{ route('dashboard') }}"
           class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="icon fa-solid fa-house"></i> Home
        </a>
        <a href="{{ route('courses.public') }}"
           class="nav-link {{ request()->routeIs('courses.public*') ? 'active' : '' }}">
            <i class="icon fa-solid fa-compass"></i> Explore
        </a>
        <a href="{{ route('courses.mine') }}"
           class="nav-link {{ request()->routeIs('courses.mine') ? 'active' : '' }}">
            <i class="icon fa-solid fa-book-open"></i> My Courses
        </a>
        <a href="{{ route('tasks.index') }}"
           class="nav-link {{ request()->routeIs('tasks.*') ? 'active' : '' }}">
            <i class="icon fa-solid fa-file-pen"></i> Assignments
        </a>
        <a href="{{ route('profile') }}"
           class="nav-link {{ request()->routeIs('profile') || request()->routeIs('settings') ? 'active' : '' }}">
            <i class="icon fa-solid fa-user-gear"></i> Settings & Profile
        </a>

        <p class="section-label px-3 mb-2 mt-4">Features</p>

        <a href="{{ route('progress.index') }}"
           class="nav-link {{ request()->routeIs('progress.*') ? 'active' : '' }}">
            <i class="icon fa-solid fa-chart-line"></i> Progress Tracker
        </a>
        <a href="{{ route('certificates.demo') }}"
           class="nav-link {{ request()->routeIs('certificates.demo*') || (request()->routeIs('certificates.*') && !request()->routeIs('certificates.demo*')) ? 'active' : '' }}">
            <i class="icon fa-solid fa-award"></i> Certificates
        </a>

        <p class="section-label px-3 mb-2 mt-4">Support</p>

        <a href="{{ route('help.index') }}"
           class="nav-link {{ request()->routeIs('help.*') || request()->routeIs('chat.*') ? 'active' : '' }}">
            <i class="icon fa-solid fa-headset"></i> Help & Support Center
            @php
                $myOpen = \Illuminate\Support\Facades\Cache::remember('sidebar_unread_user_' . Auth::id(), 45, function () {
                    return \App\Models\SupportTicket::where('user_id', Auth::id())
                        ->where('user_read', false)->where('admin_replied', true)->count();
                });
            @endphp
            @if($myOpen > 0)
            <span class="ml-auto text-[8px] font-black bg-rose-500 text-white px-1.5 py-0.5 rounded-full min-w-[18px] text-center">{{ $myOpen }}</span>
            @endif
        </a>

        <a href="{{ route('developers') }}"
           class="nav-link {{ request()->routeIs('developers') ? 'active' : '' }}">
            <i class="icon fa-solid fa-code"></i> Developers Team
        </a>

        <button type="button" onclick="openUserDataModal()"
                class="nav-link w-full text-left">
            <i class="icon fa-solid fa-database text-emerald-600"></i> Data Backup &amp; Export
        </button>

        @if(Auth::user()->isAdmin())
        <div class="pt-3 mt-3 border-t border-slate-100">
            <a href="{{ route('admin.panel-select') }}" class="nav-link" style="color:#059669;">
                <i class="icon fa-solid fa-shield-halved" style="color:#059669;"></i> Admin Panel
            </a>
        </div>
        @endif
        @if(Auth::user()->isStaff())
        <div class="pt-3 mt-3 border-t border-slate-100">
            <a href="{{ route('admin.panel-select') }}" class="nav-link" style="color:#0891b2;">
                <i class="icon fa-solid fa-user-tie" style="color:#0891b2;"></i> Staff Panel
            </a>
        </div>
        @endif
    </nav>

    {{-- Logout --}}
    <div class="px-3 pb-4 pt-3 border-t border-slate-100 flex-shrink-0">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="nav-link w-full text-left text-rose-500 hover:text-rose-600 hover:bg-rose-50">
                <i class="icon fa-solid fa-right-from-bracket text-rose-400"></i> Logout
            </button>
        </form>
    </div>
</aside>

{{-- ══════════════ MAIN ══════════════ --}}
<div class="flex-1 lg:ml-64 flex flex-col min-h-screen">

    {{-- Topbar --}}
    <header class="topbar sticky top-0 z-30 px-5 py-3 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <button class="lg:hidden w-9 h-9 rounded-xl bg-emerald-50 text-emerald-700 border border-emerald-100 flex items-center justify-center hover:bg-emerald-100 transition" onclick="toggleSidebar()">
                <i class="fa-solid fa-bars text-sm"></i>
            </button>
            <div>
                <h1 class="font-extrabold text-slate-900 text-base font-outfit leading-tight">@yield('page-title', 'Dashboard')</h1>
                <p class="text-slate-400 text-[10px] font-bold uppercase tracking-wider mt-0.5">{{ now()->format('l, d M Y') }}</p>
            </div>
        </div>

        <div class="flex items-center gap-3">
            {{-- Quick explore link --}}
            <a href="{{ route('courses.public') }}"
               class="hidden sm:flex items-center gap-1.5 px-3 py-2 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-700 text-xs font-bold transition border border-emerald-100">
                <i class="fa-solid fa-compass text-xs"></i> Explore
            </a>
            {{-- Avatar --}}
            <div class="flex items-center gap-2.5">
                <a href="{{ route('profile') }}" class="w-9 h-9 rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center text-white font-bold text-sm shadow-sm shadow-emerald-500/20 hover:shadow-md transition">
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                </a>
                <div class="hidden sm:block">
                    <p class="font-bold text-slate-900 text-xs leading-none">{{ Auth::user()->name }}</p>
                    <p class="text-slate-400 text-[10px] mt-0.5">Student</p>
                </div>
            </div>
        </div>
    </header>

    {{-- Flash messages --}}
    @if(session('success') || session('error') || session('info') || session('welcome'))
    <div class="px-5 pt-4 space-y-2">
        @if(session('success'))
        <div class="flash p-3.5 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center gap-2">
            <i class="fa-solid fa-circle-check text-emerald-500 flex-shrink-0"></i> {{ session('success') }}
        </div>
        @endif
        @if(session('error'))
        <div class="flash p-3.5 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold flex items-center gap-2">
            <i class="fa-solid fa-circle-exclamation text-rose-500 flex-shrink-0"></i> {{ session('error') }}
        </div>
        @endif
        @if(session('info'))
        <div class="flash p-3.5 rounded-2xl bg-teal-50 border border-teal-200 text-teal-800 text-xs font-semibold flex items-center gap-2">
            <i class="fa-solid fa-circle-info text-teal-500 flex-shrink-0"></i> {{ session('info') }}
        </div>
        @endif
        @if(session('welcome'))
        <div class="flash p-3.5 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center gap-2">
            <i class="fa-solid fa-party-horn text-emerald-500 flex-shrink-0"></i> {{ session('welcome') }}
        </div>
        @endif
    </div>
    @endif

    <main class="flex-1 p-5 lg:p-6">
        @yield('content')
    </main>
</div>

@include('partials.ad-overlay')
@auth
@include('partials.ai-chatbot')
@endauth
@include('partials.global-loader')
@stack('scripts')

{{-- ── User Data Backup & Export Modal (BG Tracker) ── --}}
<div id="userDataModal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-slate-100 animate-modal relative flex flex-col max-h-[90vh]">
        <button onclick="closeUserDataModal()" class="absolute top-4 right-4 text-slate-400 hover:text-slate-600 transition">
            <i class="fa-solid fa-xmark text-lg"></i>
        </button>
        <div class="flex items-center gap-3 mb-3">
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-600 text-white flex items-center justify-center text-xl shadow-md shadow-emerald-500/20">
                <i class="fa-solid fa-vault"></i>
            </div>
            <div>
                <h3 class="font-extrabold text-slate-900 text-base font-outfit">My Data Backup &amp; Export</h3>
                <p class="text-slate-400 text-xs mt-0.5">Background processing — no page freeze</p>
            </div>
        </div>
        <p class="text-slate-500 text-xs leading-relaxed mb-4">
            Export your complete learning profile, courses, task submissions, grades &amp; account metadata. Runs in the background — page won't reload.
        </p>

        <div class="space-y-2.5 mb-4">
            <button type="button" id="btn-user-export-download" onclick="startUserExport('download')"
               class="w-full flex items-center justify-between p-3.5 rounded-2xl bg-slate-50 hover:bg-slate-100 border border-slate-200 transition group disabled:opacity-50 disabled:cursor-not-allowed">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center text-sm font-bold">
                        <i class="fa-solid fa-file-pdf"></i>
                    </div>
                    <div class="text-left">
                        <p class="text-xs font-bold text-slate-800">Download PDF Report</p>
                        <p class="text-[10px] text-slate-400">Runs in background, auto-downloads when ready</p>
                    </div>
                </div>
                <i class="fa-solid fa-download text-slate-400 group-hover:text-emerald-600 transition text-sm"></i>
            </button>

            <button type="button" id="btn-user-export-email" onclick="startUserExport('email')"
                    class="w-full flex items-center justify-between p-3.5 rounded-2xl bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 transition group disabled:opacity-50 disabled:cursor-not-allowed">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-emerald-600 text-white flex items-center justify-center text-sm font-bold shadow-sm">
                        <i class="fa-solid fa-envelope-circle-check"></i>
                    </div>
                    <div class="text-left">
                        <p class="text-xs font-bold text-emerald-900">Receive Report on Mail</p>
                        <p class="text-[10px] text-emerald-600 font-medium">Send PDF to {{ Auth::user()->email }}</p>
                    </div>
                </div>
                <i class="fa-solid fa-paper-plane text-emerald-600 text-sm"></i>
            </button>
        </div>

        {{-- Export History --}}
        <div class="border-t border-slate-100 pt-4 flex-1 overflow-hidden flex flex-col">
            <div class="flex items-center justify-between mb-2">
                <p class="text-[9px] font-black uppercase tracking-widest text-emerald-700">My Export History</p>
                <button onclick="loadUserExportHistory()" class="text-[9px] text-slate-400 hover:text-emerald-600 transition font-bold flex items-center gap-1">
                    <i class="fa-solid fa-rotate-right text-[9px]"></i> Refresh
                </button>
            </div>
            <div id="user-export-history" class="overflow-y-auto flex-1 min-h-[60px] max-h-[180px] pr-1">
                <p class="text-center text-slate-400 text-[11px] py-3">Loading history...</p>
            </div>
        </div>
    </div>
</div>

<script>
function toggleSidebar() {
    const s = document.getElementById('app-sidebar');
    const b = document.getElementById('mob-backdrop');
    s.classList.toggle('-translate-x-full');
    b.classList.toggle('hidden');
}

function openUserDataModal() {
    document.getElementById('userDataModal').classList.remove('hidden');
    loadUserExportHistory();
}

function closeUserDataModal() {
    document.getElementById('userDataModal').classList.add('hidden');
}

async function startUserExport(action) {
    const btnDown  = document.getElementById('btn-user-export-download');
    const btnEmail = document.getElementById('btn-user-export-email');
    [btnDown, btnEmail].forEach(b => { if(b) b.disabled = true; });

    const label = action === 'download' ? '⚡ Generating your PDF in background...' : '⚡ Processing & sending email in background...';
    showStudentToastNotice(label, 'info');
    closeUserDataModal();

    try {
        const res = await fetch("{{ route('data.export.start') }}", {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}",
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ export_type: 'user_data_pdf', action: action })
        });
        const data = await res.json();
        if (data.ok) {
            showStudentToastNotice(data.message, 'success');
            if (action === 'download' && data.file_url) {
                const a = document.createElement('a');
                a.style.display = 'none';
                a.href = data.file_url;
                a.download = 'my-data-sheet-' + (new Date().toISOString().slice(0,10)) + '.pdf';
                document.body.appendChild(a);
                a.click();
                a.remove();
            }
        } else {
            showStudentToastNotice(data.message || '❌ Export failed', 'error');
        }
    } catch (err) {
        showStudentToastNotice('❌ Export error: ' + err.message, 'error');
    } finally {
        [btnDown, btnEmail].forEach(b => { if(b) b.disabled = false; });
        setTimeout(() => {
            const m = document.getElementById('userDataModal');
            if (m && m.classList.contains('hidden')) {
                m.classList.remove('hidden');
                loadUserExportHistory();
            }
        }, 1200);
    }
}

async function loadUserExportHistory() {
    const container = document.getElementById('user-export-history');
    if (!container) return;
    container.innerHTML = '<div class="text-center py-3"><i class="fa-solid fa-spinner animate-spin text-emerald-500"></i></div>';
    try {
        const res = await fetch("{{ route('data.export.history') }}", {
            headers: { 'Accept': 'application/json' }
        });
        const data = await res.json();
        if (!data.ok || !data.exports.length) {
            container.innerHTML = '<p class="text-center text-slate-400 text-[11px] py-3">No export history yet.</p>';
            return;
        }
        container.innerHTML = data.exports.map(e => `
            <div class="flex items-center justify-between gap-2 py-2.5 border-b border-slate-100 last:border-0">
                <div class="flex items-center gap-2 min-w-0">
                    <div class="w-7 h-7 rounded-xl flex-shrink-0 flex items-center justify-center text-[11px]
                        ${ e.status === 'completed' ? 'bg-emerald-100 text-emerald-700' :
                           e.status === 'failed'    ? 'bg-rose-100 text-rose-600' :
                           'bg-amber-100 text-amber-600' }">
                        <i class="fa-solid ${ e.status === 'completed' ? 'fa-circle-check' : e.status === 'failed' ? 'fa-circle-xmark' : 'fa-spinner fa-spin' }"></i>
                    </div>
                    <div class="min-w-0">
                        <p class="text-[11px] font-bold text-slate-800 truncate">${ e.title }</p>
                        <p class="text-[9px] text-slate-400">${ e.ago }${ e.file_size && e.file_size !== 'Pending' ? ' · ' + e.file_size : '' }${ e.target_email ? ' · 📧' : '' }</p>
                    </div>
                </div>
                <div class="flex items-center gap-1.5 flex-shrink-0">
                    <span class="px-2 py-0.5 rounded-full text-[9px] font-black border
                        ${ e.status === 'completed' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' :
                           e.status === 'failed'    ? 'bg-rose-50 text-rose-600 border-rose-200' :
                           'bg-amber-50 text-amber-600 border-amber-200' }">${ e.status_label }</span>
                    ${ e.download_route ? `<a href="${e.download_route}" class="w-6 h-6 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white flex items-center justify-center text-[10px] transition" title="Download File"><i class="fa-solid fa-download"></i></a>` : '' }
                </div>
            </div>
        `).join('');
    } catch (err) {
        container.innerHTML = '<p class="text-center text-rose-500 text-[11px] py-3">Failed to load history.</p>';
    }
}

function showStudentToastNotice(msg, type = 'success') {
    let container = document.getElementById('student-toast-container');
    if (!container) {
        container = document.createElement('div');
        container.id = 'student-toast-container';
        container.className = 'fixed bottom-5 right-5 z-[9999] space-y-2 max-w-md';
        document.body.appendChild(container);
    }
    const toast = document.createElement('div');
    const bgClass = type === 'success' ? 'bg-white border-emerald-200 text-emerald-800' : (type === 'error' ? 'bg-white border-rose-200 text-rose-800' : 'bg-white border-slate-200 text-slate-700');
    toast.className = `p-4 rounded-2xl border shadow-xl text-xs font-bold flex items-center gap-2 transition transform translate-y-2 opacity-0 ${bgClass}`;
    toast.innerHTML = `<i class="fa-solid ${type === 'success' ? 'fa-circle-check text-emerald-400' : 'fa-circle-info text-blue-400'} text-base"></i> <span>${msg}</span>`;
    container.appendChild(toast);
    setTimeout(() => { toast.classList.remove('translate-y-2', 'opacity-0'); }, 10);
    setTimeout(() => {
        toast.classList.add('opacity-0', 'translate-y-2');
        setTimeout(() => toast.remove(), 300);
    }, 4500);
}
</script>
</body>
</html>
