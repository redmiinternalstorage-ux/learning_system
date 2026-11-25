<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Campus Learning Portal — Learning Curve</title>
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800;900&family=Outfit:wght@400;600;700;800;900&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<script>
tailwind.config = { darkMode: 'class', theme: { extend: { fontFamily: { sans: ['"Plus Jakarta Sans"','sans-serif'], outfit:['"Outfit"','sans-serif'] } } } }
</script>
<style>
body { font-family: 'Plus Jakarta Sans', sans-serif; }
.glass { background: rgba(255,255,255,0.08); backdrop-filter: blur(16px); border: 1px solid rgba(255,255,255,0.15); }
.inp { width:100%; padding:.7rem 1rem; border-radius:.875rem; border:1px solid #d1fae5; font-size:.85rem; background:rgba(255,255,255,0.9); color:#0f172a; transition:all .2s; outline:none; }
.inp:focus { border-color:#059669; box-shadow:0 0 0 3px rgba(5,150,105,.15); background:#fff; }
.btn-primary { width:100%; padding:.85rem; border-radius:.875rem; background:linear-gradient(135deg,#059669,#0d9488); color:#fff; font-weight:800; font-size:.875rem; border:none; cursor:pointer; transition:all .2s; }
.btn-primary:hover { background:linear-gradient(135deg,#047857,#0f766e); transform:translateY(-1px); box-shadow:0 8px 24px rgba(5,150,105,.35); }
@keyframes float { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-10px)} }
.float { animation: float 3s ease-in-out infinite; }
</style>
</head>
<body class="min-h-screen bg-gradient-to-br from-emerald-900 via-teal-900 to-slate-900 flex items-center justify-center p-4">

{{-- Animated BG blobs --}}
<div class="fixed inset-0 overflow-hidden pointer-events-none">
    <div class="absolute top-20 left-20 w-96 h-96 bg-emerald-500/10 rounded-full blur-3xl animate-pulse"></div>
    <div class="absolute bottom-20 right-20 w-80 h-80 bg-teal-500/10 rounded-full blur-3xl animate-pulse" style="animation-delay:1.5s"></div>
    <div class="absolute top-1/2 left-1/2 w-64 h-64 bg-green-500/5 rounded-full blur-3xl" style="transform:translate(-50%,-50%)"></div>
</div>

<div class="relative z-10 w-full max-w-md">

    {{-- Logo --}}
    <div class="text-center mb-8">
        <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-gradient-to-br from-emerald-400 to-teal-500 shadow-2xl shadow-emerald-500/30 mb-4 float">
            <i class="fa-solid fa-graduation-cap text-white text-2xl"></i>
        </div>
        <h1 class="text-3xl font-black text-white font-outfit">Campus Portal</h1>
        <p class="text-emerald-300 text-sm mt-1">Learning Curve • Free for College Students</p>
    </div>

    {{-- Tabs --}}
    <div class="glass rounded-3xl p-1 flex mb-6">
        <button onclick="switchTab('login')" id="tab-login"
                class="flex-1 py-2.5 rounded-2xl text-sm font-bold transition-all {{ ($mode??'login') === 'login' ? 'bg-emerald-500 text-white shadow-lg' : 'text-emerald-300 hover:text-white' }}">
            <i class="fa-solid fa-right-to-bracket mr-1.5"></i> Login
        </button>
        <button onclick="switchTab('register')" id="tab-register"
                class="flex-1 py-2.5 rounded-2xl text-sm font-bold transition-all {{ ($mode??'login') === 'register' ? 'bg-emerald-500 text-white shadow-lg' : 'text-emerald-300 hover:text-white' }}">
            <i class="fa-solid fa-user-graduate mr-1.5"></i> Register
        </button>
    </div>

    {{-- Flash messages --}}
    @if(session('success'))
    <div class="glass rounded-2xl p-4 mb-4 text-emerald-200 text-sm flex items-center gap-2">
        <i class="fa-solid fa-circle-check text-emerald-400"></i> {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div class="glass rounded-2xl p-4 mb-4 text-rose-200 text-sm flex items-center gap-2 border-rose-500/30">
        <i class="fa-solid fa-circle-exclamation text-rose-400"></i> {{ session('error') }}
    </div>
    @endif
    @if(session('info'))
    <div class="glass rounded-2xl p-4 mb-4 text-amber-200 text-sm flex items-center gap-2">
        <i class="fa-solid fa-circle-info text-amber-400"></i> {{ session('info') }}
    </div>
    @endif

    {{-- LOGIN FORM --}}
    <div id="form-login" class="{{ ($mode??'login') === 'register' ? 'hidden' : '' }}">
        <div class="glass rounded-3xl p-7">
            <form method="POST" action="{{ route('campus.login.post') }}">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-emerald-300 mb-1.5">College Email</label>
                        <input type="email" name="college_email" value="{{ old('college_email') }}"
                               class="inp" placeholder="yourname@college.ac.in" required>
                        @error('college_email')
                        <p class="text-rose-400 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-emerald-300 mb-1.5">Password</label>
                        <input type="password" name="password" class="inp" placeholder="Enter your password" required>
                        @error('password')
                        <p class="text-rose-400 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="flex items-center gap-2">
                        <input type="checkbox" name="remember" id="remember" class="rounded">
                        <label for="remember" class="text-xs text-emerald-300">Remember me</label>
                    </div>
                    <button type="submit" class="btn-primary mt-2">
                        <i class="fa-solid fa-right-to-bracket mr-2"></i> Login to Campus Portal
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- REGISTER FORM --}}
    <div id="form-register" class="{{ ($mode??'login') === 'login' ? 'hidden' : '' }}">
        <div class="glass rounded-3xl p-7">
            <form method="POST" action="{{ route('campus.register.post') }}">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-emerald-300 mb-1.5">Full Name</label>
                        <input type="text" name="name" value="{{ old('name') }}"
                               class="inp" placeholder="Your full name" required>
                        @error('name')<p class="text-rose-400 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-emerald-300 mb-1.5">College Email <span class="text-emerald-400">*</span></label>
                        <input type="email" name="college_email" value="{{ old('college_email') }}"
                               class="inp" placeholder="yourname@college.ac.in" required>
                        <p class="text-emerald-400/70 text-[10px] mt-1">Must be your official college email (admin-verified domain)</p>
                        @error('college_email')<p class="text-rose-400 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-emerald-300 mb-1.5">Student ID</label>
                            <input type="text" name="student_id" value="{{ old('student_id') }}"
                                   class="inp" placeholder="Roll number">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-emerald-300 mb-1.5">Semester</label>
                            <input type="text" name="semester" value="{{ old('semester') }}"
                                   class="inp" placeholder="e.g. 3rd Sem">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-emerald-300 mb-1.5">Department</label>
                        <input type="text" name="department" value="{{ old('department') }}"
                               class="inp" placeholder="e.g. Computer Science">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-emerald-300 mb-1.5">Password</label>
                        <input type="password" name="password" class="inp" placeholder="Min 8 characters" required>
                        @error('password')<p class="text-rose-400 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-emerald-300 mb-1.5">Confirm Password</label>
                        <input type="password" name="password_confirmation" class="inp" placeholder="Repeat password" required>
                    </div>
                    <button type="submit" class="btn-primary mt-2">
                        <i class="fa-solid fa-user-graduate mr-2"></i> Apply for Campus Access
                    </button>
                    <p class="text-center text-emerald-400/70 text-xs">After registration, verify your email and wait for admin approval.</p>
                </div>
            </form>
        </div>
    </div>

    {{-- Back to main site --}}
    <p class="text-center text-emerald-400/60 text-xs mt-5">
        <a href="{{ route('home') }}" class="hover:text-emerald-300 transition">← Back to main website</a>
        &nbsp;•&nbsp;
        <a href="{{ route('login') }}" class="hover:text-emerald-300 transition">Student login</a>
    </p>
</div>

<script>
function switchTab(tab) {
    document.getElementById('form-login').classList.toggle('hidden', tab !== 'login');
    document.getElementById('form-register').classList.toggle('hidden', tab !== 'register');
    document.getElementById('tab-login').className = 'flex-1 py-2.5 rounded-2xl text-sm font-bold transition-all ' + (tab === 'login' ? 'bg-emerald-500 text-white shadow-lg' : 'text-emerald-300 hover:text-white');
    document.getElementById('tab-register').className = 'flex-1 py-2.5 rounded-2xl text-sm font-bold transition-all ' + (tab === 'register' ? 'bg-emerald-500 text-white shadow-lg' : 'text-emerald-300 hover:text-white');
}
</script>
</body>
</html>
