<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Hub') — Learning Curve</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Outfit:wght@400;600;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="/css/interactions.css">

    <script>
        document.documentElement.classList.remove('dark');
        localStorage.setItem('color-theme', 'light');
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        brand: { 50:'#f0fdf4',100:'#dcfce7',200:'#a7f3d0',300:'#6ee7b7',400:'#34d399',500:'#10b981',600:'#059669',700:'#047857',800:'#065f46',900:'#064e3b' },
                        emerald: { 50:'#f0fdf4',100:'#dcfce7',200:'#a7f3d0',300:'#6ee7b7',400:'#34d399',500:'#10b981',600:'#059669',700:'#047857',800:'#065f46',900:'#064e3b' },
                        rose:  { 50:'#fff1f2',100:'#ffe4e6',200:'#fecdd3',300:'#fda4af',400:'#fb7185',450:'#fb7185',500:'#f43f5e',600:'#e11d48',650:'#d90429',700:'#be123c',800:'#9f1239',900:'#881337',950:'#4c0519' },
                    },
                    fontFamily: {
                        sans:   ['"Plus Jakarta Sans"','ui-sans-serif','system-ui','-apple-system','sans-serif'],
                        outfit: ['"Outfit"','sans-serif'],
                    }
                }
            }
        }
    </script>

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f8fafc; }
        .font-outfit { font-family: 'Outfit', sans-serif; }

        /* Sidebar */
        .sidebar-bg {
            background: #ffffff;
            border-right: 1px solid #e2e8f0;
            box-shadow: 2px 0 12px rgba(0,0,0,0.02);
        }

        /* Nav items */
        .nav-item {
            display:flex; align-items:center; gap:0.875rem;
            padding:0.7rem 1rem; border-radius:0.875rem;
            font-size:0.7rem; font-weight:700; text-transform:uppercase; letter-spacing:0.07em;
            color: #64748b; transition: all 0.2s;
            position: relative;
        }
        .nav-item:hover { color:#047857; background:#f0fdf4; }
        .nav-item.active {
            color:#047857 !important;
            background: linear-gradient(90deg, rgba(16,185,129,0.12) 0%, rgba(16,185,129,0.02) 100%);
            border-left: 3px solid #059669;
            padding-left: calc(1rem - 3px);
        }
        .nav-item .nav-icon { width:1rem; text-align:center; font-size:0.85rem; color:#94a3b8; }
        .nav-item:hover .nav-icon { color:#059669; }
        .nav-item.active .nav-icon { color:#059669; }

        /* Glass cards */
        .glass { background:#ffffff; border:1px solid #e2e8f0; box-shadow:0 4px 20px -2px rgba(0,0,0,0.03); transition:all 0.3s cubic-bezier(0.4,0,0.2,1); }

        /* Stat hover lift */
        .stat-lift:hover { transform:translateY(-3px); border-color:#a7f3d0; box-shadow:0 16px 32px -8px rgba(5,150,105,0.12); }

        /* Input glow */
        .inp {
            width:100%; padding:0.7rem 1rem; border-radius:0.875rem;
            border:1px solid #cbd5e1; font-size:0.8rem;
            background:white; color:#0f172a; transition:all 0.2s;
        }
        .inp:focus { outline:none; border-color:#059669; box-shadow:0 0 0 3px rgba(5,150,105,0.15); }
        .inp::placeholder { color:#94a3b8; }

        /* Gradient buttons */
        .btn-primary {
            display:inline-flex; align-items:center; justify-content:center; gap:0.5rem;
            padding:0.7rem 1.5rem; border-radius:0.875rem;
            background:linear-gradient(135deg,#059669,#0d9488);
            color:white; font-size:0.75rem; font-weight:700;
            box-shadow:0 4px 15px rgba(5,150,105,0.25);
            transition:all 0.2s; border:none; cursor:pointer;
        }
        .btn-primary:hover { background:linear-gradient(135deg,#047857,#0f766e); box-shadow:0 6px 20px rgba(5,150,105,0.35); transform:translateY(-1px); }
        .btn-secondary {
            display:inline-flex; align-items:center; justify-content:center; gap:0.5rem;
            padding:0.7rem 1.25rem; border-radius:0.875rem;
            background:#f0fdf4; border:1px solid #a7f3d0;
            color:#047857; font-size:0.75rem; font-weight:700; cursor:pointer; transition:all 0.2s;
        }
        .btn-secondary:hover { background:#dcfce7; border-color:#6ee7b7; color:#065f46; }

        /* Badge chips */
        .badge { display:inline-flex; align-items:center; gap:0.3rem; padding:0.2rem 0.65rem; border-radius:9999px; font-size:0.68rem; font-weight:700; text-transform:uppercase; letter-spacing:0.05em; }
        .badge-green, .badge-emerald { background:#f0fdf4; color:#047857; border:1px solid #bbf7d0; }
        .badge-purple { background:#ecfdf5; color:#047857; border:1px solid #a7f3d0; }
        .badge-blue   { background:#eff6ff; color:#1d4ed8; border:1px solid #bfdbfe; }
        .badge-amber  { background:#fffbeb; color:#b45309; border:1px solid #fde68a; }
        .badge-rose   { background:#fff1f2; color:#be123c; border:1px solid #fecdd3; }
        .badge-gray   { background:#f8fafc; color:#64748b; border:1px solid #e2e8f0; }

        /* Section label */
        .section-label { font-size:0.65rem; font-weight:800; text-transform:uppercase; letter-spacing:0.12em; color:#059669; margin-bottom:0.5rem; padding:0 0.25rem; }

        /* Topbar */
        .topbar { background:rgba(255,255,255,0.92); backdrop-filter:blur(20px); -webkit-backdrop-filter:blur(20px); border-bottom:1px solid #e2e8f0; }

        /* Table */
        .admin-table th { padding:0.875rem 1.25rem; font-size:0.65rem; font-weight:800; text-transform:uppercase; letter-spacing:0.1em; color:#047857; background:#f0fdf4; border-bottom:1px solid #dcfce7; }
        .admin-table td { padding:1rem 1.25rem; font-size:0.78rem; border-bottom:1px solid #f1f5f9; color:#1e293b; }
        .admin-table tr:last-child td { border-bottom:none; }
        .admin-table tr:hover td { background:#f8fafc; }

        /* Form section divider */
        .form-section { border-bottom:1px solid #e2e8f0; padding-bottom:0.5rem; margin-bottom:1.25rem; }

        /* Animations */
        @keyframes slideInRight { from{transform:translateX(80px);opacity:0} to{transform:translateX(0);opacity:1} }
        @keyframes fadeUp { from{transform:translateY(16px);opacity:0} to{transform:translateY(0);opacity:1} }
        .anim-slide { animation:slideInRight 0.4s cubic-bezier(0.16,1,0.3,1) forwards; }
        .anim-up { animation:fadeUp 0.35s ease forwards; }

        /* Scrollbar */
        ::-webkit-scrollbar { width:5px; height:5px; }
        ::-webkit-scrollbar-track { background:transparent; }
        ::-webkit-scrollbar-thumb { background:#a7f3d0; border-radius:9999px; }
        ::-webkit-scrollbar-thumb:hover { background:#059669; }
    </style>
    @stack('styles')
</head>
<body class="bg-slate-50 min-h-screen text-slate-800 flex transition-colors duration-300">

    <!-- Mobile Backdrop -->
    <div id="sidebar-backdrop" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-40 hidden lg:hidden" onclick="toggleSidebar()"></div>

    <!-- ═══════════════════ SIDEBAR ═══════════════════ -->
    <aside id="admin-sidebar" class="w-64 sidebar-bg fixed top-0 left-0 h-full flex flex-col z-50 transform -translate-x-full lg:translate-x-0 transition-transform duration-300">

        <!-- Logo -->
        <div class="px-5 py-5 border-b border-emerald-100 flex items-center justify-between flex-shrink-0">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-gradient-to-br from-emerald-500 via-teal-500 to-emerald-600 flex items-center justify-center shadow-md shadow-emerald-500/20">
                    <i class="fa-solid fa-leaf text-white text-sm"></i>
                </div>
                <div>
                    <p class="font-extrabold text-slate-900 text-sm leading-tight font-outfit">Learning Curve</p>
                    <p class="text-emerald-600 text-[9px] font-extrabold uppercase tracking-widest mt-0.5">
                        @if(Auth::user()->isAdmin()) Main Admin @else Staff Panel @endif
                    </p>
                </div>
            </div>
            <button class="lg:hidden text-slate-400 hover:text-slate-700" onclick="toggleSidebar()">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <!-- Nav -->
        <nav class="flex-1 px-3 py-5 overflow-y-auto space-y-1">
            <p class="section-label px-2 mt-1 mb-2">Main</p>

            <a href="{{ route('admin.dashboard') }}" class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="nav-icon fa-solid fa-gauge-high"></i> Dashboard
            </a>
            <a href="{{ route('admin.users') }}" class="nav-item {{ request()->routeIs('admin.users*') ? 'active' : '' }}">
                <i class="nav-icon fa-solid fa-users"></i> Users
            </a>
            <a href="{{ route('admin.courses') }}" class="nav-item {{ request()->routeIs('admin.courses*') || request()->routeIs('admin.tasks*') ? 'active' : '' }}">
                <i class="nav-icon fa-solid fa-book-open"></i> Courses
            </a>
            <a href="{{ route('admin.announcements') }}" class="nav-item {{ request()->routeIs('admin.announcements*') ? 'active' : '' }}">
                <i class="nav-icon fa-solid fa-bullhorn"></i> Announcements
            </a>
            <a href="{{ route('admin.ads.index') }}" class="nav-item {{ request()->routeIs('admin.ads*') ? 'active' : '' }}">
                <i class="nav-icon fa-solid fa-rectangle-ad"></i> Ad Manager
            </a>
            <a href="{{ route('admin.mail-logs') }}" class="nav-item {{ request()->routeIs('admin.mail-logs*') ? 'active' : '' }}">
                <i class="nav-icon fa-solid fa-envelope-open-text"></i> Mail Logs
            </a>
            <a href="{{ route('admin.mail-compose') }}" class="nav-item {{ request()->routeIs('admin.mail-compose') || request()->routeIs('admin.mail-send') ? 'active' : '' }}">
                <i class="nav-icon fa-solid fa-paper-plane"></i> Send Mail
            </a>

            <div class="my-3 border-t border-slate-100"></div>
            <p class="section-label px-2 mb-2">Insights</p>

            @if(Auth::user()->isAdmin())
            <a href="{{ route('admin.staff.index') }}" class="nav-item {{ request()->routeIs('admin.staff*') ? 'active' : '' }}">
                <i class="nav-icon fa-solid fa-user-tie"></i> Staff Admin
                @php $staffCount = \Illuminate\Support\Facades\Cache::remember('sidebar_staff_count', 60, fn() => \App\Models\User::where('role','staff')->count()); @endphp
                @if($staffCount > 0)
                <span class="ml-auto text-[8px] font-black bg-emerald-600 text-white px-1.5 py-0.5 rounded-full min-w-[18px] text-center">{{ $staffCount }}</span>
                @endif
            </a>
            <a href="{{ route('admin.attendance.index') }}" class="nav-item {{ request()->routeIs('admin.attendance*') ? 'active' : '' }}">
                <i class="nav-icon fa-solid fa-calendar-check text-teal-600"></i> Attendance Sheet
            </a>
            @endif

            <a href="{{ route('admin.analytics') }}" class="nav-item {{ request()->routeIs('admin.analytics') ? 'active' : '' }}">
                <i class="nav-icon fa-solid fa-chart-line"></i> Analytics
            </a>
            <a href="{{ route('admin.enrollments') }}" class="nav-item {{ request()->routeIs('admin.enrollments*') ? 'active' : '' }}">
                <i class="nav-icon fa-solid fa-graduation-cap"></i> Enrollments
            </a>
            <a href="{{ route('admin.certificates') }}" class="nav-item {{ request()->routeIs('admin.certificates*') ? 'active' : '' }}">
                <i class="nav-icon fa-solid fa-award"></i> Certificates
            </a>
            <a href="{{ route('admin.activity-logs') }}" class="nav-item {{ request()->routeIs('admin.activity-logs*') ? 'active' : '' }}">
                <i class="nav-icon fa-solid fa-clock-rotate-left"></i> Activity Log
            </a>
            <button type="button" onclick="openAdminExportModal()" class="nav-item w-full text-left">
                <i class="nav-icon fa-solid fa-vault text-emerald-600"></i> Data Vault &amp; Exports
            </button>
            <a href="{{ route('admin.settings') }}" class="nav-item {{ request()->routeIs('admin.settings*') ? 'active' : '' }}">
                <i class="nav-icon fa-solid fa-gear"></i> Site Settings
            </a>

            <div class="my-3 border-t border-slate-100"></div>
            <p class="section-label px-2 mb-2">Support</p>

            <a href="{{ route('team-chat.index') }}" class="nav-item {{ request()->routeIs('team-chat*') ? 'active' : '' }}">
                <i class="nav-icon fa-solid fa-comments text-emerald-600"></i> Learning Curve Team Chat
            </a>
            <a href="{{ route('admin.support.index') }}" class="nav-item {{ request()->routeIs('admin.support*') || request()->routeIs('admin.chat*') ? 'active' : '' }}">
                <i class="nav-icon fa-solid fa-headset"></i> Help & Support Center
                @php $unread = \Illuminate\Support\Facades\Cache::remember('sidebar_unread_tickets', 30, fn() => \App\Models\SupportTicket::where('admin_read',false)->count()); @endphp
                @if($unread > 0)
                <span class="ml-auto text-[8px] font-black bg-rose-500 text-white px-1.5 py-0.5 rounded-full min-w-[18px] text-center">{{ $unread }}</span>
                @endif
            </a>
            @if(Auth::user()->isAdmin())
            <a href="{{ route('finance.dashboard') }}" class="nav-item {{ request()->routeIs('finance*') ? 'active' : '' }}">
                <i class="nav-icon fa-solid fa-building-columns"></i> Finance Dept
            </a>
            @endif

            <div class="my-3 border-t border-slate-100"></div>
            <p class="section-label px-2 mb-2">Live Stats</p>

            <div class="mx-1 rounded-2xl bg-emerald-50/60 border border-emerald-100 p-4 space-y-3">
                <div class="flex justify-between items-center">
                    <div class="flex items-center gap-2 text-slate-600">
                        <i class="fa-solid fa-users text-[10px] text-emerald-600"></i>
                        <span class="text-[10px] font-bold">Users</span>
                    </div>
                    <span class="text-emerald-900 font-black text-sm font-outfit">{{ \Illuminate\Support\Facades\Cache::remember('sidebar_user_count', 60, fn() => \App\Models\User::count()) }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <div class="flex items-center gap-2 text-slate-600">
                        <i class="fa-solid fa-book text-[10px] text-teal-600"></i>
                        <span class="text-[10px] font-bold">Courses</span>
                    </div>
                    <span class="text-emerald-900 font-black text-sm font-outfit">{{ \Illuminate\Support\Facades\Cache::remember('sidebar_course_count', 60, fn() => \App\Models\Course::count()) }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <div class="flex items-center gap-2 text-amber-700">
                        <i class="fa-solid fa-clock text-[10px] text-amber-600"></i>
                        <span class="text-[10px] font-bold">Pending</span>
                    </div>
                    <span class="text-amber-600 font-black text-sm font-outfit">{{ \Illuminate\Support\Facades\Cache::remember('sidebar_pending_count', 30, fn() => \App\Models\TaskSubmission::where('status','pending')->count()) }}</span>
                </div>
            </div>
        </nav>

        <!-- Footer -->
        <div class="px-3 py-4 border-t border-slate-100 space-y-1 flex-shrink-0">
            <a href="{{ route('admin.panel-select') }}" class="nav-item">
                <i class="nav-icon fa-solid fa-grid-2"></i> Panel Select
            </a>
            <a href="{{ route('dashboard') }}" class="nav-item">
                <i class="nav-icon fa-solid fa-arrow-left"></i> Student View
            </a>
            <form method="POST" action="{{ route('admin.logout') }}">
                @csrf
                <button type="submit" class="nav-item w-full text-rose-600 hover:text-rose-700 hover:bg-rose-50">
                    <i class="nav-icon fa-solid fa-right-from-bracket text-rose-500"></i> Logout
                </button>
            </form>
        </div>
    </aside>

    <!-- ═══════════════════ MAIN WRAPPER ═══════════════════ -->
    <div class="flex-1 lg:ml-64 flex flex-col min-h-screen bg-slate-50">

        <!-- Topbar -->
        <header class="topbar sticky top-0 z-30 px-5 py-3.5 flex items-center justify-between shadow-sm">
            <div class="flex items-center gap-3">
                <button class="lg:hidden w-9 h-9 rounded-xl bg-emerald-50 text-emerald-700 hover:bg-emerald-100 flex items-center justify-center transition border border-emerald-200/60" onclick="toggleSidebar()">
                    <i class="fa-solid fa-bars text-sm"></i>
                </button>
                <div>
                    <h1 class="font-black text-slate-900 text-base leading-tight font-outfit">@yield('page-title','Dashboard')</h1>
                    <div class="flex items-center gap-2 text-[9px] font-bold text-emerald-700 uppercase tracking-widest mt-0.5">
                        <span class="flex items-center gap-1.5"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Admin Hub</span>
                        <span>•</span>
                        <span class="text-slate-500">{{ now()->format('l, d M Y') }}</span>
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <!-- Live Admin Session & Uptime Timer -->
                <div class="hidden xl:flex items-center gap-2 px-3 py-1.5 rounded-xl bg-slate-100 border border-slate-200 text-xs font-mono font-bold text-slate-700" title="Admin Session & Live Clock Timer">
                    <i class="fa-solid fa-stopwatch text-emerald-600 animate-pulse"></i>
                    <span id="admin-topbar-timer">00:00:00</span>
                </div>

                <!-- Maintenance Mode Quick Toggle Button -->
                @php
                    $isMaintActive = (bool) \App\Models\SiteSetting::get('maintenance_mode', false);
                @endphp
                <form method="POST" action="{{ route('admin.settings.toggle-maintenance') }}" class="inline-block">
                    @csrf
                    @if($isMaintActive)
                        <button type="submit"
                                class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-extrabold text-xs shadow-md transition animate-pulse"
                                title="Maintenance Mode ON. Click to Turn OFF & Make Site Live">
                            <i class="fa-solid fa-power-off"></i>
                            <span>MAINTENANCE ON</span>
                        </button>
                    @else
                        <button type="submit"
                                class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-emerald-100 hover:bg-emerald-200 text-emerald-900 border border-emerald-300 font-extrabold text-xs transition"
                                title="Click to Enable Maintenance Mode (Block Student Access)">
                            <i class="fa-solid fa-power-off text-emerald-600"></i>
                            <span class="hidden sm:inline">Site Live</span>
                        </button>
                    @endif
                </form>

                <!-- Department Switcher Button -->
                <a href="{{ route('admin.switch-department') }}"
                   class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-amber-50 text-amber-900 hover:bg-amber-100 transition text-xs font-extrabold border border-amber-200"
                   title="Switch to another department">
                    <i class="fa-solid fa-arrows-rotate text-amber-600 text-xs"></i>
                    <span class="hidden sm:inline">Switch Dept</span>
                </a>

                <!-- Change Password Button -->
                <button onclick="document.getElementById('change-password-modal').classList.remove('hidden')"
                        class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-emerald-50 text-emerald-900 hover:bg-emerald-100 transition text-xs font-extrabold border border-emerald-200"
                        title="Change Admin Password">
                    <i class="fa-solid fa-key text-emerald-600 text-xs"></i>
                    <span class="hidden sm:inline">Change Password</span>
                </button>

                <!-- Quick Command Palette Button -->
                <button onclick="toggleCommandPalette()"
                        class="hidden md:flex items-center gap-2 px-3 py-1.5 rounded-xl bg-emerald-50 text-emerald-800 hover:bg-emerald-100/80 transition text-xs font-semibold border border-emerald-200/80">
                    <i class="fa-solid fa-magnifying-glass text-[11px] text-emerald-600"></i>
                    <span>Search / Cmds</span>
                    <kbd class="px-1.5 py-0.5 text-[9px] font-black bg-white text-emerald-700 border border-emerald-200 rounded-md">Ctrl+K</kbd>
                </button>

                <!-- Admin badge -->
                <div class="flex items-center gap-3 pl-3 border-l border-slate-200">
                    <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center text-white font-bold text-sm font-outfit shadow-sm">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>
                    <div class="hidden sm:block">
                        <p class="font-bold text-slate-900 text-xs leading-none">{{ Auth::user()->name }}</p>
                        @if(Auth::user()->isAdmin())
                        <span class="badge badge-green mt-1 inline-block">Main Admin</span>
                        @else
                        <span class="badge badge-blue mt-1 inline-block">Staff</span>
                        @endif
                    </div>
                </div>
            </div>
        </header>

        <!-- Command Palette Modal -->
        <div id="cmd-palette-backdrop" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-50 hidden flex items-start justify-center pt-20 px-4" onclick="closeCmdPaletteOnBackdrop(event)">
            <div class="bg-white border border-emerald-100 max-w-xl w-full rounded-3xl p-4 shadow-2xl space-y-4 anim-up" onclick="event.stopPropagation()">
                <div class="relative flex items-center border-b border-emerald-100 pb-3">
                    <i class="fa-solid fa-magnifying-glass absolute left-3 text-emerald-600 text-sm"></i>
                    <input type="text" id="cmd-search-input" placeholder="Type a command or jump to page..." onkeyup="filterCmdPalette()"
                           class="w-full pl-10 pr-10 py-2 bg-emerald-50/50 rounded-xl border border-emerald-100 text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:border-emerald-500 font-medium">
                    <button onclick="toggleCommandPalette()" class="absolute right-2 text-slate-400 hover:text-slate-600">
                        <i class="fa-solid fa-xmark text-sm"></i>
                    </button>
                </div>
                <div id="cmd-palette-list" class="max-h-80 overflow-y-auto space-y-1">
                    <p class="text-[9px] font-bold uppercase tracking-widest text-emerald-600 px-3 py-1">Quick Navigation</p>
                    <a href="{{ route('admin.dashboard') }}" class="cmd-item flex items-center justify-between p-3 rounded-2xl hover:bg-emerald-50 hover:text-emerald-800 transition text-xs font-bold text-slate-700">
                        <span class="flex items-center gap-2"><i class="fa-solid fa-gauge-high text-emerald-600 w-4 text-center"></i> Dashboard</span>
                        <span class="text-[10px] text-slate-400 font-normal">System Overview</span>
                    </a>
                    <a href="{{ route('admin.users') }}" class="cmd-item flex items-center justify-between p-3 rounded-2xl hover:bg-emerald-50 hover:text-emerald-800 transition text-xs font-bold text-slate-700">
                        <span class="flex items-center gap-2"><i class="fa-solid fa-users text-teal-600 w-4 text-center"></i> Manage Users</span>
                        <span class="text-[10px] text-slate-400 font-normal">Directory & Roles</span>
                    </a>
                    <a href="{{ route('admin.courses') }}" class="cmd-item flex items-center justify-between p-3 rounded-2xl hover:bg-emerald-50 hover:text-emerald-800 transition text-xs font-bold text-slate-700">
                        <span class="flex items-center gap-2"><i class="fa-solid fa-book-open text-emerald-600 w-4 text-center"></i> Courses Manager</span>
                        <span class="text-[10px] text-slate-400 font-normal">Catalog & Lessons</span>
                    </a>
                    <a href="{{ route('admin.courses.create') }}" class="cmd-item flex items-center justify-between p-3 rounded-2xl hover:bg-emerald-50 hover:text-emerald-800 transition text-xs font-bold text-slate-700">
                        <span class="flex items-center gap-2"><i class="fa-solid fa-plus text-emerald-500 w-4 text-center"></i> Add New Course</span>
                        <span class="text-[10px] text-slate-400 font-normal">Create Course</span>
                    </a>
                    <a href="{{ route('admin.enrollments') }}" class="cmd-item flex items-center justify-between p-3 rounded-2xl hover:bg-emerald-50 hover:text-emerald-800 transition text-xs font-bold text-slate-700">
                        <span class="flex items-center gap-2"><i class="fa-solid fa-graduation-cap text-teal-600 w-4 text-center"></i> Enrollments</span>
                        <span class="text-[10px] text-slate-400 font-normal">Student Progress</span>
                    </a>
                    <a href="{{ route('admin.analytics') }}" class="cmd-item flex items-center justify-between p-3 rounded-2xl hover:bg-emerald-50 hover:text-emerald-800 transition text-xs font-bold text-slate-700">
                        <span class="flex items-center gap-2"><i class="fa-solid fa-chart-line text-emerald-600 w-4 text-center"></i> Analytics</span>
                        <span class="text-[10px] text-slate-400 font-normal">Charts & Insights</span>
                    </a>
                    <a href="{{ route('admin.support.index') }}" class="cmd-item flex items-center justify-between p-3 rounded-2xl hover:bg-emerald-50 hover:text-emerald-800 transition text-xs font-bold text-slate-700">
                        <span class="flex items-center gap-2"><i class="fa-solid fa-ticket text-rose-500 w-4 text-center"></i> Help Tickets</span>
                        <span class="text-[10px] text-slate-400 font-normal">Student Support</span>
                    </a>
                    <a href="{{ route('admin.chat.index') }}" class="cmd-item flex items-center justify-between p-3 rounded-2xl hover:bg-emerald-50 hover:text-emerald-800 transition text-xs font-bold text-slate-700">
                        <span class="flex items-center gap-2"><i class="fa-solid fa-comments text-emerald-600 w-4 text-center"></i> Live Chat</span>
                        <span class="text-[10px] text-slate-400 font-normal">Realtime Support</span>
                    </a>
                    <a href="{{ route('admin.mail-logs') }}" class="cmd-item flex items-center justify-between p-3 rounded-2xl hover:bg-emerald-50 hover:text-emerald-800 transition text-xs font-bold text-slate-700">
                        <span class="flex items-center gap-2"><i class="fa-solid fa-envelope-open-text text-teal-600 w-4 text-center"></i> Mail Logs</span>
                        <span class="text-[10px] text-slate-400 font-normal">Sent Emails</span>
                    </a>
                    <a href="{{ route('admin.mail-compose') }}" class="cmd-item flex items-center justify-between p-3 rounded-2xl hover:bg-emerald-50 hover:text-emerald-800 transition text-xs font-bold text-slate-700">
                        <span class="flex items-center gap-2"><i class="fa-solid fa-paper-plane text-emerald-600 w-4 text-center"></i> Send Mail</span>
                        <span class="text-[10px] text-slate-400 font-normal">Direct Mailer</span>
                    </a>
                    <a href="{{ route('admin.announcements') }}" class="cmd-item flex items-center justify-between p-3 rounded-2xl hover:bg-emerald-50 hover:text-emerald-800 transition text-xs font-bold text-slate-700">
                        <span class="flex items-center gap-2"><i class="fa-solid fa-bullhorn text-amber-500 w-4 text-center"></i> Announcements</span>
                        <span class="text-[10px] text-slate-400 font-normal">Broadcasts</span>
                    </a>
                    <a href="{{ route('admin.ads.index') }}" class="cmd-item flex items-center justify-between p-3 rounded-2xl hover:bg-emerald-50 hover:text-emerald-800 transition text-xs font-bold text-slate-700">
                        <span class="flex items-center gap-2"><i class="fa-solid fa-rectangle-ad text-emerald-600 w-4 text-center"></i> Ad Manager</span>
                        <span class="text-[10px] text-slate-400 font-normal">Ad Campaigns</span>
                    </a>
                    <a href="{{ route('admin.activity-logs') }}" class="cmd-item flex items-center justify-between p-3 rounded-2xl hover:bg-emerald-50 hover:text-emerald-800 transition text-xs font-bold text-slate-700">
                        <span class="flex items-center gap-2"><i class="fa-solid fa-clock-rotate-left text-teal-500 w-4 text-center"></i> Activity Logs</span>
                        <span class="text-[10px] text-slate-400 font-normal">Audit Logs</span>
                    </a>
                    <a href="{{ route('admin.settings') }}" class="cmd-item flex items-center justify-between p-3 rounded-2xl hover:bg-emerald-50 hover:text-emerald-800 transition text-xs font-bold text-slate-700">
                        <span class="flex items-center gap-2"><i class="fa-solid fa-gear text-slate-500 w-4 text-center"></i> Site Settings</span>
                        <span class="text-[10px] text-slate-400 font-normal">System Config</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Change Password Modal -->
        <div id="change-password-modal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
            <div class="bg-white border border-slate-200 max-w-md w-full rounded-3xl p-6 shadow-2xl space-y-5 anim-up" onclick="event.stopPropagation()">
                <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-lg border border-emerald-100">
                            <i class="fa-solid fa-key"></i>
                        </div>
                        <div>
                            <h3 class="font-extrabold text-slate-800 text-sm font-outfit">Change Admin Password</h3>
                            <p class="text-slate-400 text-xs">Update your account security credentials</p>
                        </div>
                    </div>
                    <button type="button" onclick="document.getElementById('change-password-modal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 text-sm">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>

                <form method="POST" action="{{ route('admin.change-password') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Current Password *</label>
                        <input type="password" name="current_password" required placeholder="Enter current password" class="inp">
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">New Password *</label>
                        <input type="password" name="password" required minlength="8" placeholder="Minimum 8 characters" class="inp">
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Confirm New Password *</label>
                        <input type="password" name="password_confirmation" required minlength="8" placeholder="Re-enter new password" class="inp">
                    </div>

                    <div class="flex gap-3 pt-2">
                        <button type="submit" class="btn-primary flex-1">
                            <i class="fa-solid fa-floppy-disk"></i> Save New Password
                        </button>
                        <button type="button" onclick="document.getElementById('change-password-modal').classList.add('hidden')" class="btn-secondary">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Main Content -->
        <main class="flex-1 p-5 lg:p-7 space-y-5">

            <!-- Flash Messages -->
            @if(session('success'))
            <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center gap-3 anim-slide">
                <div class="w-8 h-8 rounded-xl bg-emerald-100 flex items-center justify-center flex-shrink-0 text-emerald-700"><i class="fa-solid fa-circle-check"></i></div>
                {{ session('success') }}
            </div>
            @endif
            @if(session('error'))
            <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold flex items-center gap-3 anim-slide">
                <div class="w-8 h-8 rounded-xl bg-rose-100 flex items-center justify-center flex-shrink-0 text-rose-700"><i class="fa-solid fa-triangle-exclamation"></i></div>
                {{ session('error') }}
            </div>
            @endif
            @if(session('welcome'))
            <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center gap-3 anim-slide">
                <div class="w-8 h-8 rounded-xl bg-emerald-100 flex items-center justify-center flex-shrink-0 text-emerald-700"><i class="fa-solid fa-crown"></i></div>
                {{ session('welcome') }}
            </div>
            @endif

            @yield('content')
        </main>
    </div>

    <script>
        function toggleSidebar() {
            const s = document.getElementById('admin-sidebar');
            const b = document.getElementById('sidebar-backdrop');
            s.classList.toggle('-translate-x-full');
            b.classList.toggle('hidden');
        }
        function toggleTheme() {
            document.documentElement.classList.remove('dark');
            localStorage.setItem('color-theme','light');
        }
        function toggleCommandPalette() {
            const el = document.getElementById('cmd-palette-backdrop');
            el.classList.toggle('hidden');
            if (!el.classList.contains('hidden')) {
                const inp = document.getElementById('cmd-search-input');
                inp.value = '';
                filterCmdPalette();
                setTimeout(() => inp.focus(), 50);
            }
        }
        function closeCmdPaletteOnBackdrop(e) {
            if (e.target.id === 'cmd-palette-backdrop') {
                toggleCommandPalette();
            }
        }
        function filterCmdPalette() {
            const query = document.getElementById('cmd-search-input').value.toLowerCase();
            const items = document.querySelectorAll('#cmd-palette-list .cmd-item');
            items.forEach(item => {
                const text = item.textContent.toLowerCase();
                item.style.display = text.includes(query) ? 'flex' : 'none';
            });
        }
        document.addEventListener('keydown', function(e) {
            if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'k') {
                e.preventDefault();
                toggleCommandPalette();
            }
            if (e.key === 'Escape') {
                const el = document.getElementById('cmd-palette-backdrop');
                if (el && !el.classList.contains('hidden')) {
                    toggleCommandPalette();
                }
            }
        });

        // Live Admin Session Timer
        let adminTimerSeconds = 0;
        setInterval(() => {
            adminTimerSeconds++;
            const h = String(Math.floor(adminTimerSeconds / 3600)).padStart(2, '0');
            const m = String(Math.floor((adminTimerSeconds % 3600) / 60)).padStart(2, '0');
            const s = String(adminTimerSeconds % 60).padStart(2, '0');
            const el = document.getElementById('admin-topbar-timer');
            if (el) el.textContent = `${h}:${m}:${s}`;
        }, 1000);

        // ── Admin Data Vault & Exports Modal ──────────────────────────────────
        function openAdminExportModal() {
            const m = document.getElementById('adminMasterExportModal');
            if (m) {
                m.classList.remove('hidden');
                loadAdminExportHistory();
            }
        }
        function closeAdminExportModal() {
            const m = document.getElementById('adminMasterExportModal');
            if (m) m.classList.add('hidden');
        }

        async function startAdminExport(action) {
            const btnDown = document.getElementById('btn-admin-export-download');
            const btnEmail = document.getElementById('btn-admin-export-email');
            [btnDown, btnEmail].forEach(b => { if(b){ b.disabled = true; }});

            const label = action === 'download' ? '⚡ Generating PDF in background...' : '⚡ Processing & dispatching email...';
            showToastNotice(label, 'info');
            closeAdminExportModal();

            try {
                const res = await fetch("{{ route('data.export.start') }}", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}",
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ export_type: 'master_db_pdf', action: action })
                });
                const data = await res.json();
                if (data.ok) {
                    showToastNotice(data.message, 'success');
                    if (action === 'download' && data.file_url) {
                        const a = document.createElement('a');
                        a.style.display = 'none';
                        a.href = data.file_url;
                        a.download = 'master-finance-sheet-' + (new Date().toISOString().slice(0,10)) + '.pdf';
                        document.body.appendChild(a);
                        a.click();
                        a.remove();
                    }
                } else {
                    showToastNotice(data.message || '❌ Export failed', 'error');
                }
            } catch (err) {
                showToastNotice('❌ Export error: ' + err.message, 'error');
            } finally {
                [btnDown, btnEmail].forEach(b => { if(b){ b.disabled = false; }});
                // Reopen modal with refreshed history after 1s
                setTimeout(() => {
                    const m = document.getElementById('adminMasterExportModal');
                    if (m && m.classList.contains('hidden')) {
                        m.classList.remove('hidden');
                        loadAdminExportHistory();
                    }
                }, 1200);
            }
        }

        async function loadAdminExportHistory() {
            const container = document.getElementById('admin-export-history');
            if (!container) return;
            container.innerHTML = '<div class="text-center py-4"><i class="fa-solid fa-spinner animate-spin text-emerald-500 text-lg"></i></div>';
            try {
                const res = await fetch("{{ route('data.export.history') }}?all=1", {
                    headers: { 'Accept': 'application/json' }
                });
                const data = await res.json();
                if (!data.ok || !data.exports.length) {
                    container.innerHTML = '<p class="text-center text-slate-400 text-[11px] py-3">No export history yet.</p>';
                    return;
                }
                container.innerHTML = data.exports.map(e => `
                    <div class="flex items-center justify-between gap-2 py-2.5 border-b border-slate-800 last:border-0">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <div class="w-7 h-7 rounded-xl flex-shrink-0 flex items-center justify-center text-[11px]
                                ${ e.status === 'completed' ? 'bg-emerald-900/60 text-emerald-400' :
                                   e.status === 'failed'    ? 'bg-rose-900/60 text-rose-400' :
                                   'bg-amber-900/60 text-amber-400' }">
                                <i class="fa-solid ${ e.status === 'completed' ? 'fa-circle-check' : e.status === 'failed' ? 'fa-circle-xmark' : 'fa-spinner fa-spin' }"></i>
                            </div>
                            <div class="min-w-0">
                                <p class="text-[11px] font-bold text-slate-200 truncate">${ e.title }</p>
                                <p class="text-[9px] text-slate-500">${ e.ago } ${ e.file_size && e.file_size !== 'Pending' ? '· ' + e.file_size : '' }
                                ${ e.target_email ? '· 📧 ' + e.target_email : '' }</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-1.5 flex-shrink-0">
                            <span class="px-2 py-0.5 rounded-full text-[9px] font-black border
                                ${ e.status === 'completed' ? 'bg-emerald-900/50 text-emerald-300 border-emerald-700' :
                                   e.status === 'failed'    ? 'bg-rose-900/50 text-rose-300 border-rose-700' :
                                   'bg-amber-900/50 text-amber-300 border-amber-700' }">${ e.status_label }</span>
                            ${ e.download_route ? `<a href="${e.download_route}" class="w-6 h-6 rounded-lg bg-emerald-700 hover:bg-emerald-600 text-white flex items-center justify-center text-[10px] transition" title="Download File"><i class="fa-solid fa-download"></i></a>` : '' }
                        </div>
                    </div>
                `).join('');
            } catch (err) {
                container.innerHTML = '<p class="text-center text-rose-400 text-[11px] py-3">Failed to load history.</p>';
            }
        }

        function showToastNotice(msg, type = 'success') {
            let container = document.getElementById('global-toast-container');
            if (!container) {
                container = document.createElement('div');
                container.id = 'global-toast-container';
                container.className = 'fixed bottom-5 right-5 z-[9999] space-y-2 max-w-md';
                document.body.appendChild(container);
            }
            const toast = document.createElement('div');
            const bgClass = type === 'success' ? 'bg-emerald-900 border-emerald-500 text-white' : (type === 'error' ? 'bg-rose-900 border-rose-500 text-white' : 'bg-slate-900 border-slate-700 text-slate-100');
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

    {{-- ── Admin Data Vault & Exports Modal ── --}}
    <div id="adminMasterExportModal" class="fixed inset-0 z-[9999] bg-slate-950/70 backdrop-blur-xs flex items-center justify-center p-4 hidden">
        <div class="bg-slate-900 border border-slate-800 text-slate-100 rounded-3xl max-w-lg w-full p-6 shadow-2xl relative animate-modal flex flex-col max-h-[90vh]">
            <button onclick="closeAdminExportModal()" class="absolute top-4 right-4 text-slate-400 hover:text-white transition">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>

            {{-- Header --}}
            <div class="flex items-center gap-3.5 mb-4">
                <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-600 text-white flex items-center justify-center text-xl shadow-lg shadow-emerald-500/20 flex-shrink-0">
                    <i class="fa-solid fa-vault"></i>
                </div>
                <div>
                    <h3 class="font-extrabold text-white text-base font-outfit">Data Vault &amp; Exports</h3>
                    <span class="px-2 py-0.5 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 text-[10px] font-bold">Background Processing — No Page Freeze</span>
                </div>
            </div>

            {{-- Info box --}}
            <div class="p-3.5 rounded-2xl bg-slate-950/70 border border-slate-800 mb-4 text-[11px] text-slate-400 space-y-1">
                <p class="flex items-center gap-2"><i class="fa-solid fa-bolt text-amber-400"></i> <strong class="text-slate-300">Zero Delay:</strong> Runs completely in background, no page reload.</p>
                <p class="flex items-center gap-2"><i class="fa-solid fa-paper-plane text-emerald-400"></i> <strong class="text-slate-300">Email Target:</strong> akpatel2029@gmail.com</p>
                <p class="flex items-center gap-2"><i class="fa-solid fa-shield text-teal-400"></i> <strong class="text-slate-300">Contains:</strong> All users, staff payroll, ads, finance transactions.</p>
            </div>

            {{-- Action Buttons --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-5">
                <button type="button" id="btn-admin-export-download" onclick="startAdminExport('download')"
                        class="py-3 px-4 rounded-2xl bg-slate-800 hover:bg-slate-700 text-white font-bold text-xs border border-slate-700 transition flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed">
                    <i class="fa-solid fa-file-pdf text-emerald-400 text-sm"></i> Download Sheet (PDF)
                </button>
                <button type="button" id="btn-admin-export-email" onclick="startAdminExport('email')"
                        class="py-3 px-4 rounded-2xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white font-black text-xs shadow-lg shadow-emerald-600/20 transition flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed">
                    <i class="fa-solid fa-paper-plane text-sm"></i> Send to akpatel2029@gmail.com
                </button>
            </div>

            {{-- Export History --}}
            <div class="border-t border-slate-800 pt-4 flex-1 overflow-hidden flex flex-col">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-[9px] font-black uppercase tracking-widest text-emerald-500">Export History</p>
                    <button onclick="loadAdminExportHistory()" class="text-[9px] text-slate-400 hover:text-emerald-400 transition font-bold flex items-center gap-1">
                        <i class="fa-solid fa-rotate-right text-[9px]"></i> Refresh
                    </button>
                </div>
                <div id="admin-export-history" class="overflow-y-auto flex-1 min-h-[80px] max-h-[200px] pr-1 scrollbar-thin">
                    <p class="text-center text-slate-500 text-[11px] py-3">Loading history...</p>
                </div>
            </div>
        </div>
    </div>

    @include('partials.global-loader')
    @stack('scripts')
</body>
</html>
