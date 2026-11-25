<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Verify Email — Campus Portal</title>
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800;900&display=swap" rel="stylesheet">
<style>body{font-family:'Plus Jakarta Sans',sans-serif;} .glass{background:rgba(255,255,255,0.08);backdrop-filter:blur(16px);border:1px solid rgba(255,255,255,0.15);} .inp{width:100%;padding:.8rem 1.2rem;border-radius:.875rem;border:2px solid #d1fae5;font-size:1.1rem;letter-spacing:.3em;text-align:center;background:rgba(255,255,255,0.9);color:#0f172a;outline:none;font-weight:800;} .inp:focus{border-color:#059669;box-shadow:0 0 0 3px rgba(5,150,105,.15);}</style>
</head>
<body class="min-h-screen bg-gradient-to-br from-emerald-900 via-teal-900 to-slate-900 flex items-center justify-center p-4">
<div class="fixed inset-0 overflow-hidden pointer-events-none">
    <div class="absolute top-20 left-20 w-96 h-96 bg-emerald-500/10 rounded-full blur-3xl animate-pulse"></div>
    <div class="absolute bottom-20 right-20 w-80 h-80 bg-teal-500/10 rounded-full blur-3xl animate-pulse" style="animation-delay:1.5s"></div>
</div>
<div class="relative z-10 w-full max-w-md">
    <div class="text-center mb-8">
        <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-gradient-to-br from-emerald-400 to-teal-500 shadow-2xl shadow-emerald-500/30 mb-4">
            <i class="fa-solid fa-envelope-open-text text-white text-2xl"></i>
        </div>
        <h1 class="text-2xl font-black text-white">Verify Your Email</h1>
        <p class="text-emerald-300 text-sm mt-1">OTP sent to <strong>{{ $email }}</strong></p>
    </div>

    @if(session('success'))
    <div class="glass rounded-2xl p-4 mb-4 text-emerald-200 text-sm flex items-center gap-2"><i class="fa-solid fa-circle-check text-emerald-400"></i> {{ session('success') }}</div>
    @endif

    <div class="glass rounded-3xl p-7">
        <form method="POST" action="{{ route('campus.verify-email.post') }}">
            @csrf
            <label class="block text-xs font-bold text-emerald-300 mb-3 text-center">Enter 6-Digit OTP</label>
            <input type="text" name="otp" class="inp" placeholder="000000" maxlength="6" autocomplete="off" autofocus>
            @error('otp')<p class="text-rose-400 text-xs mt-2 text-center">{{ $message }}</p>@enderror
            <p class="text-emerald-400/60 text-xs text-center mt-2">Expires in 10 minutes</p>
            <button type="submit" class="mt-5 w-full py-3.5 rounded-2xl bg-gradient-to-r from-emerald-500 to-teal-500 text-white font-black text-sm hover:opacity-90 transition">
                <i class="fa-solid fa-shield-halved mr-2"></i> Verify Email
            </button>
        </form>
        <form method="POST" action="{{ route('campus.resend-otp') }}" class="mt-4 text-center">
            @csrf
            <button type="submit" class="text-emerald-400 hover:text-emerald-300 text-xs font-bold transition">
                <i class="fa-solid fa-rotate-right mr-1"></i> Resend OTP
            </button>
        </form>
    </div>
    <p class="text-center text-emerald-400/60 text-xs mt-5">
        <a href="{{ route('campus.login') }}" class="hover:text-emerald-300 transition">← Back to Login</a>
    </p>
</div>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</body>
</html>
