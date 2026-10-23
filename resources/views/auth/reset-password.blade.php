<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Reset Password - Learning Curve</title>
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="/css/interactions.css">
<style>body{font-family:'Plus Jakarta Sans',sans-serif; background:#f0fdf4;}</style>
</head>
<body class="min-h-screen">

<nav class="bg-white border-b border-emerald-100 sticky top-0 z-50 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center justify-between h-16">
        <a href="{{ route('home') }}" class="flex items-center gap-2.5">
            <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center shadow-sm">
                <i class="fa-solid fa-graduation-cap text-white text-sm"></i>
            </div>
            <span class="font-extrabold text-gray-900 text-lg font-outfit">Learning Curve</span>
        </a>
    </div>
</nav>

<div class="max-w-md mx-auto px-4 py-12">

    {{-- Steps --}}
    <div class="flex items-center justify-center gap-2 mb-8">
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center text-xs font-bold">
                <i class="fa-solid fa-check text-xs"></i>
            </div>
            <span class="text-xs font-medium text-gray-400">Email</span>
        </div>
        <div class="w-8 h-px bg-emerald-400"></div>
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center text-xs font-bold">
                <i class="fa-solid fa-check text-xs"></i>
            </div>
            <span class="text-xs font-medium text-gray-400">OTP</span>
        </div>
        <div class="w-8 h-px bg-emerald-400"></div>
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 rounded-full bg-emerald-600 text-white flex items-center justify-center text-xs font-bold">3</div>
            <span class="text-xs font-semibold text-emerald-600">New Password</span>
        </div>
    </div>

    <div class="text-center mb-8">
        <div class="w-16 h-16 rounded-2xl bg-emerald-50 border border-emerald-100 flex items-center justify-center mx-auto mb-4">
            <i class="fa-solid fa-key text-emerald-600 text-2xl"></i>
        </div>
        <h1 class="text-2xl font-bold text-gray-900 mb-2">Set New Password</h1>
        <p class="text-gray-500 text-sm">
            For <span class="font-semibold text-emerald-600">{{ $email }}</span>
        </p>
    </div>

    @if ($errors->any())
    <div class="mb-5 p-4 rounded-xl bg-red-50 border border-red-200 space-y-1">
        @foreach($errors->all() as $e)
            <p class="text-red-600 text-sm flex items-center gap-2">
                <i class="fa-solid fa-circle-exclamation text-xs"></i> {{ $e }}
            </p>
        @endforeach
    </div>
    @endif

    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-8">
        <form method="POST" action="{{ route('password.reset') }}" class="space-y-5">
            @csrf

            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">New Password</label>
                <div class="relative">
                    <i class="fa-solid fa-lock absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                    <input type="password" name="password" id="pwd1" required autofocus
                           placeholder="Min. 8 characters"
                           oninput="checkStrength(this.value)"
                           class="w-full pl-10 pr-12 py-2.5 rounded-xl border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-300 focus:border-emerald-400 bg-white">
                    <button type="button" onclick="toggle('pwd1','eye1')"
                            class="absolute right-3.5 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition">
                        <i class="fa-regular fa-eye text-sm" id="eye1"></i>
                    </button>
                </div>
                {{-- Strength bar --}}
                <div class="mt-2 h-1.5 bg-gray-100 rounded-full overflow-hidden">
                    <div id="strengthBar" class="h-full w-0 rounded-full bg-red-400 transition-all duration-300"></div>
                </div>
                <p id="strengthLabel" class="text-xs text-gray-400 mt-1 h-4"></p>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Confirm New Password</label>
                <div class="relative">
                    <i class="fa-solid fa-shield-halved absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                    <input type="password" name="password_confirmation" id="pwd2" required
                           placeholder="Re-enter new password"
                           oninput="checkMatch()"
                           class="w-full pl-10 pr-12 py-2.5 rounded-xl border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-300 focus:border-emerald-400 bg-white">
                    <button type="button" onclick="toggle('pwd2','eye2')"
                            class="absolute right-3.5 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition">
                        <i class="fa-regular fa-eye text-sm" id="eye2"></i>
                    </button>
                </div>
                <p id="matchMsg" class="text-xs mt-1 h-4"></p>
            </div>

            <button type="submit" id="submit-btn"
                    class="w-full py-3 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-bold text-xs uppercase tracking-wider transition-all duration-200 shadow-sm flex items-center justify-center gap-2 group">
                <i class="fa-solid fa-check-circle"></i> Reset Password
                <i class="fa-solid fa-arrow-right group-hover:translate-x-1 transition-transform"></i>
            </button>
        </form>
    </div>
</div>

<script>
function toggle(inputId, iconId) {
    const i = document.getElementById(inputId);
    const ic = document.getElementById(iconId);
    i.type = i.type === 'password' ? 'text' : 'password';
    ic.className = i.type === 'password' ? 'fa-regular fa-eye text-sm' : 'fa-regular fa-eye-slash text-sm';
}

function checkStrength(val) {
    const bar = document.getElementById('strengthBar');
    const lbl = document.getElementById('strengthLabel');
    let s = 0;
    if (val.length >= 8) s++;
    if (/[A-Z]/.test(val)) s++;
    if (/[0-9]/.test(val)) s++;
    if (/[^A-Za-z0-9]/.test(val)) s++;
    const map = [
        ['0%',   'bg-red-400',    ''],
        ['25%',  'bg-red-400',    'Weak'],
        ['50%',  'bg-amber-400',  'Fair'],
        ['75%',  'bg-blue-400',   'Strong'],
        ['100%', 'bg-emerald-500',   'Very Strong ✓'],
    ];
    bar.style.width = map[s][0];
    bar.className = `h-full rounded-full transition-all duration-300 ${map[s][1]}`;
    lbl.textContent = map[s][2];
    checkMatch();
}

function checkMatch() {
    const p1 = document.getElementById('pwd1').value;
    const p2 = document.getElementById('pwd2').value;
    const msg = document.getElementById('matchMsg');
    if (!p2) { msg.textContent = ''; return; }
    if (p1 === p2) {
        msg.textContent = '✓ Passwords match';
        msg.className = 'text-xs mt-1 h-4 text-emerald-600 font-semibold';
    } else {
        msg.textContent = '✗ Passwords do not match';
        msg.className = 'text-xs mt-1 h-4 text-red-500';
    }
}
</script>
@include('partials.global-loader')
</body>
</html>
