<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Forgot Password - Learning Curve</title>
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="/css/interactions.css">
<style>body{font-family:'Plus Jakarta Sans',sans-serif; background:#f0fdf4;}</style>
</head>
<body class="bg-gray-50 min-h-screen">

<nav class="bg-white border-b border-emerald-100 sticky top-0 z-50 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center justify-between h-16">
        <a href="{{ route('home') }}" class="flex items-center gap-2.5">
            <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center shadow-sm">
                <i class="fa-solid fa-graduation-cap text-white text-sm"></i>
            </div>
            <span class="font-extrabold text-gray-900 text-lg font-outfit">Learning Curve</span>
        </a>
        <div class="flex items-center gap-3">
            <a href="{{ route('login') }}" class="px-4 py-2 rounded-xl border border-gray-200 hover:bg-gray-50 text-gray-700 text-sm font-semibold transition">
                <i class="fa-solid fa-right-to-bracket mr-1.5"></i> Login
            </a>
            <a href="{{ route('register') }}" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold transition">
                <i class="fa-solid fa-user-plus mr-1.5"></i> Register
            </a>
        </div>
    </div>
</nav>

<div class="max-w-md mx-auto px-4 py-16">

    {{-- Steps indicator --}}
    <div class="flex items-center justify-center gap-2 mb-8">
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 rounded-full bg-emerald-600 text-white flex items-center justify-center text-xs font-bold">1</div>
            <span class="text-xs font-semibold text-emerald-600">Email</span>
        </div>
        <div class="w-8 h-px bg-gray-300"></div>
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 rounded-full bg-gray-200 text-gray-400 flex items-center justify-center text-xs font-bold">2</div>
            <span class="text-xs font-medium text-gray-400">OTP</span>
        </div>
        <div class="w-8 h-px bg-gray-300"></div>
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 rounded-full bg-gray-200 text-gray-400 flex items-center justify-center text-xs font-bold">3</div>
            <span class="text-xs font-medium text-gray-400">New Password</span>
        </div>
    </div>

    <div class="text-center mb-8">
        <div class="w-16 h-16 rounded-2xl bg-emerald-100 flex items-center justify-center mx-auto mb-4">
            <i class="fa-solid fa-lock text-emerald-600 text-2xl"></i>
        </div>
        <h1 class="text-2xl font-bold text-gray-900 mb-2">Forgot Password?</h1>
        <p class="text-gray-500 text-sm">Enter your email to receive a 6-digit OTP</p>
    </div>

    @if ($errors->any())
    <div class="mb-5 p-4 rounded-xl bg-red-50 border border-red-200 text-red-600 text-sm flex items-center gap-2">
        <i class="fa-solid fa-circle-exclamation"></i> {{ $errors->first() }}
    </div>
    @endif

    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-8">
        <form method="POST" action="{{ route('password.send-otp') }}" class="space-y-5">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Email Address</label>
                <div class="relative">
                    <i class="fa-regular fa-envelope absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                    <input type="email" name="email" value="{{ old('email') }}" required autofocus
                           placeholder="your@email.com"
                           class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-300 focus:border-emerald-400 bg-white">
                </div>
            </div>
            <button type="submit"
                    class="w-full py-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-sm transition shadow-sm flex items-center justify-center gap-2 group">
                <i class="fa-solid fa-paper-plane"></i> Send OTP
                <i class="fa-solid fa-arrow-right group-hover:translate-x-1 transition-transform"></i>
            </button>
        </form>
        <p class="text-center text-gray-400 text-sm mt-5">
            Remember your password?
            <a href="{{ route('login') }}" class="text-emerald-600 font-semibold hover:underline ml-1">Sign in</a>
        </p>
    </div>
</div>
@include('partials.global-loader')
</body>
</html>
