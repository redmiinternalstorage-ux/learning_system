<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Learning Curve Portal')</title>
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">

    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Outfit:wght@400;600;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="/css/app.css">
    <link rel="stylesheet" href="/css/interactions.css">

    <script>
        // Always use light mode for auth pages
        document.documentElement.classList.remove('dark');

        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                        outfit: ['"Outfit"', 'sans-serif'],
                    }
                }
            }
        }
    </script>

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f0fdf4; }
        .font-outfit { font-family: 'Outfit', sans-serif; }

        .glass-card {
            background: rgba(255, 255, 255, 0.97);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.9);
            box-shadow: 0 4px 24px -4px rgba(5, 150, 105, 0.08);
        }

        .text-gradient-primary {
            background: linear-gradient(135deg, #059669, #10b981);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .btn-gradient-primary {
            background: linear-gradient(135deg, #059669, #0d9488);
            transition: all 0.2s ease;
        }
        .btn-gradient-primary:hover {
            background: linear-gradient(135deg, #047857, #0f766e);
            box-shadow: 0 8px 20px -4px rgba(5, 150, 105, 0.35);
        }

        /* Active tab */
        .tab-active {
            background: linear-gradient(135deg, #059669, #0d9488) !important;
            color: white !important;
            box-shadow: 0 4px 12px rgba(5, 150, 105, 0.25) !important;
        }

        /* Form input focus — green */
        input:focus, select:focus {
            border-color: #059669 !important;
            box-shadow: 0 0 0 3px rgba(5, 150, 105, 0.12) !important;
        }

        /* Background mesh */
        body {
            background-color: #f0fdf4;
            background-image:
                radial-gradient(at 0% 0%, rgba(16, 185, 129, 0.12) 0px, transparent 50%),
                radial-gradient(at 100% 0%, rgba(5, 150, 105, 0.08) 0px, transparent 50%),
                radial-gradient(at 100% 100%, rgba(110, 231, 183, 0.06) 0px, transparent 50%),
                radial-gradient(at 0% 100%, rgba(52, 211, 153, 0.10) 0px, transparent 50%);
        }
    </style>
    @stack('styles')
</head>
<body class="min-h-screen text-slate-800">

    <div class="min-h-screen flex flex-col">

        <!-- Header — visible, fixed top with logo + tab switcher -->
        <header class="sticky top-0 z-40 bg-white/90 backdrop-blur-md border-b border-emerald-100 shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 py-3 flex items-center justify-between">
                <!-- Logo -->
                <a href="{{ route('home') }}" class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center shadow-sm">
                        <i class="fa-solid fa-graduation-cap text-white text-sm"></i>
                    </div>
                    <div>
                        <p class="font-extrabold text-slate-900 text-sm leading-tight font-outfit tracking-tight">Learning Curve</p>
                        <p class="text-emerald-600 text-[9px] font-extrabold uppercase tracking-widest">Learning Portal</p>
                    </div>
                </a>

                <!-- Tab switcher — always visible in header -->
                <div class="inline-flex p-1 rounded-xl bg-slate-100 border border-slate-200 gap-1">
                    <button type="button" onclick="switchAuthTab('login')" id="tab-login-btn"
                            class="px-4 py-2 rounded-lg font-extrabold text-xs transition-all flex items-center gap-1.5 tab-active">
                        <i class="fa-solid fa-right-to-bracket text-[10px]"></i> Sign In
                    </button>
                    <button type="button" onclick="switchAuthTab('register')" id="tab-register-btn"
                            class="px-4 py-2 rounded-lg font-bold text-xs text-slate-600 hover:text-slate-900 hover:bg-white transition-all flex items-center gap-1.5">
                        <i class="fa-solid fa-user-plus text-[10px]"></i> Register
                    </button>
                </div>

                <!-- Back to home link -->
                <a href="{{ route('courses.public') }}" class="hidden sm:flex items-center gap-1.5 text-xs font-bold text-slate-500 hover:text-emerald-600 transition">
                    <i class="fa-solid fa-book-open text-[10px]"></i> Browse Courses
                </a>
            </div>
        </header>

        <!-- Main Content -->
        <main class="flex-1 px-4 sm:px-6 pb-10 pt-6 max-w-7xl mx-auto w-full">

            {{-- Flash messages --}}
            @if ($errors->any())
            <div class="mb-5 p-4 rounded-2xl bg-red-50 border border-red-200 text-red-700 text-sm flex items-center gap-2 max-w-3xl mx-auto">
                <i class="fa-solid fa-circle-exclamation flex-shrink-0 text-red-500"></i>
                <span>{{ $errors->first() }}</span>
            </div>
            @endif

            @if(session('success'))
            <div class="mb-5 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm flex items-center gap-2 max-w-3xl mx-auto">
                <i class="fa-solid fa-circle-check flex-shrink-0 text-emerald-500"></i>
                <span>{{ session('success') }}</span>
            </div>
            @endif

            @yield('content')
        </main>

        <footer class="text-center text-slate-400 text-xs py-5 font-medium border-t border-slate-100">
            © {{ date('Y') }} Learning Curve. All rights reserved.
        </footer>
    </div>

    <script>
        // ── Tab switcher ────────────────────────────────────────
        function switchAuthTab(tab) {
            const loginContent    = document.getElementById('login-tab-content');
            const registerContent = document.getElementById('register-tab-content');
            const loginBtn        = document.getElementById('tab-login-btn');
            const registerBtn     = document.getElementById('tab-register-btn');

            if (!loginContent || !registerContent) return;

            if (tab === 'login') {
                loginContent.classList.remove('hidden');
                registerContent.classList.add('hidden');
                loginBtn.className    = 'px-4 py-2 rounded-lg font-extrabold text-xs transition-all flex items-center gap-1.5 tab-active';
                registerBtn.className = 'px-4 py-2 rounded-lg font-bold text-xs text-slate-600 hover:text-slate-900 hover:bg-white transition-all flex items-center gap-1.5';
                loginBtn.innerHTML    = '<i class="fa-solid fa-right-to-bracket text-[10px]"></i> Sign In';
                registerBtn.innerHTML = '<i class="fa-solid fa-user-plus text-[10px]"></i> Register';
            } else {
                registerContent.classList.remove('hidden');
                loginContent.classList.add('hidden');
                registerBtn.className = 'px-4 py-2 rounded-lg font-extrabold text-xs transition-all flex items-center gap-1.5 tab-active';
                loginBtn.className    = 'px-4 py-2 rounded-lg font-bold text-xs text-slate-600 hover:text-slate-900 hover:bg-white transition-all flex items-center gap-1.5';
                registerBtn.innerHTML = '<i class="fa-solid fa-user-plus text-[10px]"></i> Register';
                loginBtn.innerHTML    = '<i class="fa-solid fa-right-to-bracket text-[10px]"></i> Sign In';
            }
        }

        // ── Password visibility toggle ──────────────────────────
        function togglePassword(inputId, iconId) {
            const input = document.getElementById(inputId);
            const icon  = document.getElementById(iconId);
            if (!input) return;
            input.type     = input.type === 'password' ? 'text' : 'password';
            icon.className = input.type === 'password' ? 'fa-regular fa-eye' : 'fa-regular fa-eye-slash';
        }

        // ── Password strength meter ─────────────────────────────
        function checkPasswordStrength(val) {
            let score = 0;
            if (val.length >= 8) score++;
            if (/[A-Z]/.test(val)) score++;
            if (/[0-9]/.test(val)) score++;
            if (/[^A-Za-z0-9]/.test(val)) score++;
            const bar = document.getElementById('strengthBar');
            const lbl = document.getElementById('strengthLabel');
            if (!bar || !lbl) return;
            const levels = [
                ['0%',   'bg-red-400',      'Too weak'],
                ['25%',  'bg-red-400',      'Weak'],
                ['50%',  'bg-amber-400',    'Fair'],
                ['75%',  'bg-emerald-400',  'Strong'],
                ['100%', 'bg-emerald-500',  'Very strong ✓'],
            ];
            bar.style.width   = levels[score][0];
            bar.className     = 'h-full rounded-full transition-all duration-300 ' + levels[score][1];
            lbl.textContent   = levels[score][2];
        }

        // Legacy no-ops
        function setRole(role) {}
        function setRegisterRole(role) {}
    </script>
    @include('partials.global-loader')
    @stack('scripts')
</body>
</html>
