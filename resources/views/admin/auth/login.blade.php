<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Portal Login — Learning Curve</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Outfit:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script>
        tailwind.config = {
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
        * { box-sizing: border-box; }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #f0fdf4;
            min-height: 100vh;
            overflow-x: hidden;
        }
        .font-outfit { font-family: 'Outfit', sans-serif; }

        /* Animated green background mesh */
        .bg-mesh {
            position: fixed; inset: 0; z-index: 0; pointer-events: none;
            background:
                radial-gradient(ellipse 80% 60% at 20% 10%,  rgba(5, 150, 105, 0.22) 0%, transparent 60%),
                radial-gradient(ellipse 60% 70% at 80% 90%,  rgba(16, 185, 129, 0.12) 0%, transparent 60%),
                radial-gradient(ellipse 50% 50% at 60% 30%,  rgba(52, 211, 153, 0.08) 0%, transparent 55%),
                #f0fdf4;
        }

        /* Floating particles */
        .particle {
            position: absolute; border-radius: 50%;
            animation: float linear infinite;
            opacity: 0.4;
        }
        @keyframes float {
            0%   { transform: translateY(100vh) rotate(0deg);   opacity: 0; }
            10%  { opacity: .3; }
            90%  { opacity: .3; }
            100% { transform: translateY(-10vh) rotate(720deg); opacity: 0; }
        }

        /* Card glass effect */
        .card {
            background: #ffffff;
            border: 1px solid #d1fae5;
            box-shadow: 0 8px 30px -8px rgba(5, 150, 105, 0.12);
        }

        /* Input */
        .inp {
            width: 100%;
            padding: .75rem 1rem .75rem 2.75rem;
            background: #f8fafc;
            border: 1px solid #d1fae5;
            border-radius: .875rem;
            color: #0f172a;
            font-size: .85rem;
            font-weight: 500;
            transition: all .2s;
            outline: none;
        }
        .inp::placeholder { color: rgba(148, 163, 184, 0.5); }
        .inp:focus {
            border-color: rgba(52, 211, 153, 0.6);
            background: rgba(5, 150, 105, 0.06);
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.15);
        }
        .inp.error {
            border-color: rgba(239, 68, 68, 0.6);
            box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.12);
        }

        /* Submit button */
        .btn-login {
            width: 100%;
            padding: .85rem 1.5rem;
            border-radius: .875rem;
            background: linear-gradient(135deg, #059669 0%, #0d9488 100%);
            color: white;
            font-size: .85rem;
            font-weight: 800;
            letter-spacing: .03em;
            border: none;
            cursor: pointer;
            transition: all .25s;
            box-shadow: 0 4px 24px rgba(5, 150, 105, 0.35), 0 1px 3px rgba(0,0,0,0.3);
            position: relative;
            overflow: hidden;
        }
        .btn-login::before {
            content: '';
            position: absolute; inset: 0;
            background: linear-gradient(135deg, rgba(255,255,255,0.1) 0%, transparent 60%);
        }
        .btn-login:hover {
            background: linear-gradient(135deg, #047857 0%, #0f766e 100%);
            box-shadow: 0 6px 32px rgba(5, 150, 105, 0.50), 0 1px 3px rgba(0,0,0,0.3);
            transform: translateY(-1px);
        }
        .btn-login:active { transform: translateY(0); }
        .btn-login:disabled { opacity: 0.6; cursor: not-allowed; transform: none; }

        /* Secure badge */
        .secure-pill {
            display: inline-flex; align-items: center; gap: .4rem;
            padding: .3rem .75rem; border-radius: 9999px;
            background: rgba(16, 185, 129, 0.1);
            border: 1px solid rgba(16, 185, 129, 0.25);
            color: #34d399;
            font-size: .65rem; font-weight: 800;
            text-transform: uppercase; letter-spacing: .08em;
        }

        /* Animations */
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(20px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        @keyframes scalePop {
            from { opacity: 0; transform: scale(0.94); }
            to   { opacity: 1; transform: scale(1); }
        }
        .au  { animation: fadeUp   .4s cubic-bezier(.16,1,.3,1) both; }
        .au1 { animation: fadeUp   .4s .06s cubic-bezier(.16,1,.3,1) both; }
        .au2 { animation: fadeUp   .4s .12s cubic-bezier(.16,1,.3,1) both; }
        .au3 { animation: fadeUp   .4s .18s cubic-bezier(.16,1,.3,1) both; }
        .sp  { animation: scalePop .4s cubic-bezier(.16,1,.3,1) both; }

        /* Divider */
        .divider {
            display: flex; align-items: center; gap: .75rem;
            color: rgba(148, 163, 184, 0.35);
            font-size: .65rem; font-weight: 700; text-transform: uppercase; letter-spacing: .1em;
        }
        .divider::before, .divider::after {
            content: ''; flex: 1;
            height: 1px; background: rgba(255,255,255,0.06);
        }

        /* Role indicators */
        .role-chip {
            display: flex; align-items: center; gap: .5rem;
            padding: .5rem .75rem; border-radius: .75rem;
            background: rgba(255,255,255,0.04);
            border: 1px solid rgba(255,255,255,0.07);
            font-size: .68rem; font-weight: 700; color: rgba(148,163,184,.7);
        }
        .role-chip .dot { width: 6px; height: 6px; border-radius: 50%; }
    </style>
</head>
<body>
    <!-- Animated background -->
    <div class="bg-mesh"></div>

    <!-- Floating particles -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none z-0" id="particles-container"></div>

    <!-- Main layout -->
    <div class="relative z-10 min-h-screen flex">

        <!-- ══ Left panel: Branding ══ -->
        <div class="hidden lg:flex flex-col justify-between w-[45%] xl:w-[42%] p-10 xl:p-14 border-r border-white/5">

            <!-- Logo -->
            <div class="flex items-center gap-3 au">
                <div class="w-11 h-11 rounded-2xl flex items-center justify-center shadow-xl shadow-emerald-500/30"
                     style="background: linear-gradient(135deg, #059669, #0d9488)">
                    <i class="fa-solid fa-leaf text-white text-base"></i>
                </div>
                <div>
                    <p class="text-white font-black text-base leading-tight font-outfit">Learning Curve</p>
                    <p class="text-emerald-400 text-[9px] font-extrabold uppercase tracking-widest mt-0.5">Admin Control Center</p>
                </div>
            </div>

            <!-- Centre hero text -->
            <div class="space-y-6">
                <div class="au1">
                    <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-[10px] font-black uppercase tracking-widest mb-5">
                        <i class="fa-solid fa-shield-halved"></i> Secure Admin Portal
                    </div>
                    <h1 class="text-4xl xl:text-5xl font-black text-white font-outfit leading-[1.1] tracking-tight">
                        Admin <br>
                        <span style="background: linear-gradient(135deg, #6ee7b7, #34d399, #10b981); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">
                            Control Hub
                        </span>
                    </h1>
                    <p class="text-slate-400 text-sm mt-4 leading-relaxed max-w-xs">
                        Centralised control for administrators, staff and finance — all behind a single secure gateway.
                    </p>
                </div>

                <!-- Access roles -->
                <div class="au2 space-y-2.5">
                    <p class="text-slate-500 text-[10px] font-black uppercase tracking-widest">Access Levels</p>
                    <div class="space-y-2">
                        <div class="role-chip">
                            <div class="dot bg-emerald-400"></div>
                            <span class="text-emerald-300">Main Admin</span>
                            <span class="ml-auto text-[9px] text-slate-600">Full system access</span>
                        </div>
                        <div class="role-chip">
                            <div class="dot bg-teal-400"></div>
                            <span class="text-teal-300">Staff</span>
                            <span class="ml-auto text-[9px] text-slate-600">Operations & support</span>
                        </div>
                        <div class="role-chip">
                            <div class="dot bg-amber-400"></div>
                            <span class="text-amber-300">Finance Department</span>
                            <span class="ml-auto text-[9px] text-slate-600">Payroll & transactions</span>
                        </div>
                        <div class="role-chip">
                            <div class="dot bg-green-400"></div>
                            <span class="text-green-300">Instructor</span>
                            <span class="ml-auto text-[9px] text-slate-600">Course management</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="au3 flex items-center justify-between">
                <div class="secure-pill">
                    <i class="fa-solid fa-lock text-[9px]"></i>
                    256-bit Encrypted
                </div>
                <p class="text-slate-700 text-[10px] font-medium">© {{ date('Y') }} Learning Curve</p>
            </div>
        </div>

        <!-- ══ Right panel: Login form ══ -->
        <div class="flex-1 flex items-center justify-center p-6 sm:p-10">
            <div class="w-full max-w-md">

                <!-- Mobile logo -->
                <div class="flex lg:hidden items-center justify-center gap-3 mb-8 au">
                    <div class="w-11 h-11 rounded-2xl flex items-center justify-center shadow-xl shadow-emerald-500/30"
                         style="background: linear-gradient(135deg, #059669, #0d9488)">
                        <i class="fa-solid fa-leaf text-white text-base"></i>
                    </div>
                    <div>
                        <p class="text-white font-black text-base font-outfit">Learning Curve</p>
                        <p class="text-emerald-400 text-[9px] font-extrabold uppercase tracking-widest mt-0.5">Admin Control Center</p>
                    </div>
                </div>

                <!-- Card -->
                <div class="card rounded-3xl p-7 sm:p-8 sp">

                    <!-- Header -->
                    <div class="mb-6">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-10 h-10 rounded-2xl flex items-center justify-center flex-shrink-0"
                                 style="background: rgba(5,150,105,0.2); border: 1px solid rgba(5,150,105,0.3);">
                                <i class="fa-solid fa-shield-halved text-emerald-400 text-sm"></i>
                            </div>
                            <div>
                                <h2 class="text-white text-xl font-black font-outfit tracking-tight">Secure Sign In</h2>
                                <p class="text-slate-500 text-[11px] font-medium mt-0.5">Admin Portal — Authorised Access Only</p>
                            </div>
                        </div>

                        {{-- Success Flash --}}
                        @if(session('success'))
                        <div class="mb-4 p-3.5 rounded-2xl flex items-center gap-3 text-emerald-300 text-xs font-semibold"
                             style="background: rgba(16,185,129,.1); border: 1px solid rgba(16,185,129,.25);">
                            <i class="fa-solid fa-circle-check flex-shrink-0"></i>
                            {{ session('success') }}
                        </div>
                        @endif

                        {{-- Error --}}
                        @if($errors->any())
                        <div class="mb-0 p-3.5 rounded-2xl flex items-start gap-3 text-rose-300 text-xs font-semibold"
                             style="background: rgba(239,68,68,.1); border: 1px solid rgba(239,68,68,.25);">
                            <i class="fa-solid fa-circle-exclamation flex-shrink-0 mt-0.5"></i>
                            <div>
                                @foreach($errors->all() as $e)
                                <p>{{ $e }}</p>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </div>

                    <!-- Form -->
                    <form method="POST" action="{{ route('admin.login.post') }}" id="loginForm">
                        @csrf

                        <!-- Email -->
                        <div class="mb-4">
                            <label class="block text-slate-400 text-[11px] font-extrabold uppercase tracking-widest mb-2">
                                Email Address
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                    <i class="fa-regular fa-envelope text-slate-600 text-xs"></i>
                                </div>
                                <input type="email" name="email" id="email"
                                       value="{{ old('email') }}"
                                       required autocomplete="email"
                                       placeholder="admin@learningcurve.com"
                                       class="inp {{ $errors->has('email') ? 'error' : '' }}">
                            </div>
                        </div>

                        <!-- Password -->
                        <div class="mb-5">
                            <label class="block text-slate-400 text-[11px] font-extrabold uppercase tracking-widest mb-2">
                                Password
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                    <i class="fa-solid fa-lock text-slate-600 text-xs"></i>
                                </div>
                                <input type="password" name="password" id="password"
                                       required autocomplete="current-password"
                                       placeholder="••••••••••"
                                       class="inp pr-11">
                                <button type="button" onclick="togglePwd()" id="eyeBtn"
                                        class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-600 hover:text-emerald-400 transition">
                                    <i class="fa-regular fa-eye text-xs" id="eyeIcon"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Remember + secure badge -->
                        <div class="flex items-center justify-between mb-6">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="remember"
                                       class="w-3.5 h-3.5 rounded text-emerald-600 border-slate-600 bg-slate-800 focus:ring-emerald-500">
                                <span class="text-slate-500 text-xs font-semibold">Keep me signed in</span>
                            </label>
                            <div class="secure-pill">
                                <i class="fa-solid fa-lock text-[9px]"></i>
                                Secured
                            </div>
                        </div>

                        <!-- Submit -->
                        <button type="submit" class="btn-login" id="submitBtn">
                            <span id="btnText" class="flex items-center justify-center gap-2 relative z-10">
                                <i class="fa-solid fa-shield-halved"></i>
                                Access Admin Portal
                                <i class="fa-solid fa-arrow-right text-xs"></i>
                            </span>
                        </button>
                    </form>

                    <!-- Divider -->
                    <div class="divider my-6">Protected Zone</div>

                    <!-- Info notice -->
                    <div class="rounded-2xl p-3.5 flex items-start gap-3"
                         style="background: rgba(5,150,105,.06); border: 1px solid rgba(5,150,105,.15);">
                        <i class="fa-solid fa-circle-info text-emerald-500 text-xs mt-0.5 flex-shrink-0"></i>
                        <p class="text-slate-500 text-[11px] leading-relaxed">
                            This portal is restricted to <span class="text-emerald-400 font-bold">authorised personnel</span> only.
                            Unauthorised access attempts are logged and monitored.
                        </p>
                    </div>

                    <!-- Link back to public site -->
                    <p class="text-center text-slate-700 text-[11px] font-medium mt-5">
                        Not admin staff?
                        <a href="{{ route('login') }}" class="text-emerald-400 font-bold hover:text-emerald-300 transition ml-1">
                            Student / Public Login →
                        </a>
                    </p>
                </div>

                <!-- Footer -->
                <p class="text-center text-slate-700 text-[10px] font-medium mt-6">
                    © {{ date('Y') }} Learning Curve · All rights reserved
                </p>
            </div>
        </div>
    </div>

    <script>
        // Password toggle
        function togglePwd() {
            const inp  = document.getElementById('password');
            const icon = document.getElementById('eyeIcon');
            if (inp.type === 'password') {
                inp.type   = 'text';
                icon.className = 'fa-regular fa-eye-slash text-xs';
            } else {
                inp.type   = 'password';
                icon.className = 'fa-regular fa-eye text-xs';
            }
        }

        // Form loading state
        document.getElementById('loginForm').addEventListener('submit', function() {
            const btn  = document.getElementById('submitBtn');
            const text = document.getElementById('btnText');
            btn.disabled = true;
            text.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin"></i> Authenticating...';
        });

        // Floating particles (green tones)
        const container = document.getElementById('particles-container');
        const colours   = ['#059669', '#10b981', '#34d399', '#0d9488', '#6ee7b7'];
        for (let i = 0; i < 16; i++) {
            const p = document.createElement('div');
            const size = Math.random() * 5 + 2;
            p.className = 'particle';
            p.style.cssText = `
                width:  ${size}px;
                height: ${size}px;
                background: ${colours[Math.floor(Math.random() * colours.length)]};
                left:  ${Math.random() * 100}%;
                animation-duration: ${Math.random() * 20 + 15}s;
                animation-delay:   -${Math.random() * 20}s;
                filter: blur(${Math.random() * 2}px);
                opacity: ${Math.random() * 0.25 + 0.05};
            `;
            container.appendChild(p);
        }
    </script>
@include('partials.global-loader')
</body>
</html>
