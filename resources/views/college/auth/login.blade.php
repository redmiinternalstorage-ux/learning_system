<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>College Portal Login — Learning Curve</title>
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800;900&family=Outfit:wght@400;700;800;900&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
body { font-family: 'Plus Jakarta Sans', sans-serif; background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 50%, #0f172a 100%); min-height: 100vh; }
.glass { background: rgba(255,255,255,0.07); backdrop-filter: blur(20px); border: 1px solid rgba(255,255,255,0.12); }
.inp { width:100%; padding:.75rem 1rem .75rem 2.75rem; background:rgba(255,255,255,0.08); border:1px solid rgba(255,255,255,0.15); border-radius:.875rem; color:#f1f5f9; font-size:.85rem; outline:none; transition:all .2s; }
.inp::placeholder { color: rgba(148,163,184,0.5); }
.inp:focus { border-color:rgba(99,102,241,.6); box-shadow:0 0 0 3px rgba(99,102,241,.15); background:rgba(255,255,255,0.12); }
.inp.error { border-color: rgba(239,68,68,.6); }
.btn { width:100%; padding:.85rem; border-radius:.875rem; background:linear-gradient(135deg,#6366f1,#8b5cf6); color:#fff; font-weight:800; font-size:.875rem; border:none; cursor:pointer; transition:all .2s; }
.btn:hover { background:linear-gradient(135deg,#4f46e5,#7c3aed); transform:translateY(-1px); box-shadow:0 8px 24px rgba(99,102,241,.4); }
@keyframes float { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-8px)} }
.float { animation: float 3s ease-in-out infinite; }
</style>
</head>
<body class="min-h-screen flex items-center justify-center p-4">

{{-- BG blobs --}}
<div class="fixed inset-0 overflow-hidden pointer-events-none">
    <div class="absolute top-20 left-10 w-72 h-72 bg-indigo-500/10 rounded-full blur-3xl animate-pulse"></div>
    <div class="absolute bottom-20 right-10 w-80 h-80 bg-violet-500/10 rounded-full blur-3xl animate-pulse" style="animation-delay:1.5s"></div>
</div>

<div class="relative z-10 w-full max-w-md">

    {{-- Logo --}}
    <div class="text-center mb-8">
        <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-gradient-to-br from-indigo-500 to-violet-600 shadow-2xl shadow-indigo-500/30 mb-4 float">
            <i class="fa-solid fa-building-columns text-white text-2xl"></i>
        </div>
        <h1 class="text-3xl font-black text-white" style="font-family:'Outfit',sans-serif">College Portal</h1>
        <p class="text-indigo-300 text-sm mt-1">Learning Curve • Institution Admin Access</p>
    </div>

    {{-- Flash messages --}}
    @if(session('success'))
    <div class="glass rounded-2xl p-4 mb-4 text-emerald-200 text-sm flex items-center gap-2">
        <i class="fa-solid fa-circle-check text-emerald-400 flex-shrink-0"></i> {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div class="glass rounded-2xl p-4 mb-4 text-rose-200 text-sm flex items-center gap-2">
        <i class="fa-solid fa-circle-exclamation text-rose-400 flex-shrink-0"></i> {{ session('error') }}
    </div>
    @endif

    {{-- Card --}}
    <div class="glass rounded-3xl p-7 shadow-2xl">
        <div class="flex items-center gap-3 mb-6">
            <div class="w-10 h-10 rounded-2xl bg-indigo-500/20 border border-indigo-500/30 flex items-center justify-center">
                <i class="fa-solid fa-shield-halved text-indigo-400 text-sm"></i>
            </div>
            <div>
                <h2 class="text-white font-black text-lg" style="font-family:'Outfit',sans-serif">Institution Sign In</h2>
                <p class="text-slate-400 text-[11px]">Authorised college administrators only</p>
            </div>
        </div>

        @if($errors->any())
        <div class="mb-5 p-3.5 rounded-2xl text-rose-300 text-xs font-semibold flex items-start gap-2" style="background:rgba(239,68,68,.1);border:1px solid rgba(239,68,68,.25)">
            <i class="fa-solid fa-circle-exclamation flex-shrink-0 mt-0.5"></i>
            <div>@foreach($errors->all() as $e)<p>{{ $e }}</p>@endforeach</div>
        </div>
        @endif

        <form method="POST" action="{{ route('college.login.post') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">College Email</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                        <i class="fa-regular fa-envelope text-slate-500 text-xs"></i>
                    </div>
                    <input type="email" name="email" value="{{ old('email') }}" required
                           placeholder="admin@yourcollege.edu"
                           class="inp {{ $errors->has('email') ? 'error' : '' }}">
                </div>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Password</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                        <i class="fa-solid fa-lock text-slate-500 text-xs"></i>
                    </div>
                    <input type="password" name="password" id="pwd" required placeholder="••••••••" class="inp pr-11">
                    <button type="button" onclick="togglePwd()" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-500 hover:text-indigo-400 transition">
                        <i class="fa-regular fa-eye text-xs" id="eyeIcon"></i>
                    </button>
                </div>
            </div>
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="remember" class="w-3.5 h-3.5 rounded accent-indigo-500">
                <span class="text-xs text-slate-400">Keep me signed in</span>
            </label>
            <button type="submit" class="btn mt-2">
                <i class="fa-solid fa-building-columns mr-2"></i> Access College Portal
            </button>
        </form>

        <div class="my-5 border-t border-white/10"></div>

        <p class="text-center text-slate-500 text-xs">
            New institution?
            <a href="{{ route('college.register') }}" class="text-indigo-400 font-bold hover:text-indigo-300 ml-1">Apply for access →</a>
        </p>
    </div>

    <p class="text-center mt-5 text-slate-600 text-xs">
        <a href="{{ route('campus.login') }}" class="hover:text-slate-400 transition">Student / Campus login</a>
        &nbsp;•&nbsp;
        <a href="{{ route('home') }}" class="hover:text-slate-400 transition">← Back to home</a>
    </p>
</div>

<script>
function togglePwd() {
    const i = document.getElementById('pwd');
    const ic = document.getElementById('eyeIcon');
    i.type = i.type === 'password' ? 'text' : 'password';
    ic.className = i.type === 'password' ? 'fa-regular fa-eye text-xs' : 'fa-regular fa-eye-slash text-xs';
}
</script>
</body>
</html>
