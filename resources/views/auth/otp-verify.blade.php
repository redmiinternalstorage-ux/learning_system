<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Verify OTP - Learning Curve</title>
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="/css/interactions.css">
<style>
    body { font-family: 'Plus Jakarta Sans', sans-serif; background: #f0fdf4; }
    .otp-input {
        width: 48px; height: 56px;
        text-align: center; font-size: 22px; font-weight: 800;
        border: 2px solid #d1fae5; border-radius: 12px;
        color: #064e3b; outline: none; transition: all 0.2s;
        background: white;
    }
    .otp-input:focus { border-color: #059669; box-shadow: 0 0 0 3px rgba(5,150,105,0.15); }
    .otp-input.filled { border-color: #059669; background: #f0fdf4; }
    .timer-circle { position: relative; width: 80px; height: 80px; }
    .timer-circle svg { transform: rotate(-90deg); }
    .timer-circle .progress { transition: stroke-dashoffset 1s linear; }
    .timer-text { position: absolute; inset: 0; display: flex; flex-direction: column; align-items: center; justify-content: center; }
</style>
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
            <div class="w-8 h-8 rounded-full bg-emerald-600 text-white flex items-center justify-center text-xs font-bold">2</div>
            <span class="text-xs font-semibold text-emerald-600">OTP</span>
        </div>
        <div class="w-8 h-px bg-gray-300"></div>
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 rounded-full bg-gray-200 text-gray-400 flex items-center justify-center text-xs font-bold">3</div>
            <span class="text-xs font-medium text-gray-400">New Password</span>
        </div>
    </div>

    <div class="text-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900 mb-2">Enter OTP</h1>
        <p class="text-gray-500 text-sm">
            We sent a 6-digit code to<br>
            <span class="font-semibold text-emerald-600">{{ $email }}</span>
        </p>
    </div>

    @if(session('success'))
    <div class="mb-4 p-3 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm flex items-center gap-2">
        <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
    </div>
    @endif
    @if ($errors->any())
    <div class="mb-4 p-3 rounded-xl bg-red-50 border border-red-200 text-red-600 text-sm flex items-center gap-2">
        <i class="fa-solid fa-circle-exclamation"></i> {{ $errors->first() }}
    </div>
    @endif

    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-8">

        {{-- Timer --}}
        <div class="flex flex-col items-center mb-8">
            <div class="timer-circle mb-2">
                <svg width="80" height="80" viewBox="0 0 80 80">
                    <circle cx="40" cy="40" r="34" fill="none" stroke="#e5e7eb" stroke-width="6"/>
                    <circle id="timer-circle" cx="40" cy="40" r="34" fill="none"
                            stroke="#059669" stroke-width="6"
                            stroke-dasharray="213.6"
                            stroke-dashoffset="0"
                            stroke-linecap="round"
                            class="progress"/>
                </svg>
                <div class="timer-text">
                    <span id="timer-display" class="text-xl font-black text-emerald-700">5:00</span>
                    <span class="text-xs text-gray-400 font-medium">left</span>
                </div>
            </div>
            <p id="timer-status" class="text-xs text-gray-500 font-medium">OTP expires in 5 minutes</p>
        </div>

        {{-- OTP Input boxes --}}
        <form method="POST" action="{{ route('password.otp.verify') }}" id="otp-form">
            @csrf
            <input type="hidden" name="otp" id="otp-hidden">

            <div class="flex justify-center gap-3 mb-6">
                @for($i = 0; $i < 6; $i++)
                <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]"
                       class="otp-input" id="otp-{{ $i }}"
                       oninput="otpInput(this, {{ $i }})"
                       onkeydown="otpKeydown(event, {{ $i }})"
                       onpaste="otpPaste(event)">
                @endfor
            </div>

            <button type="submit" id="verify-btn"
                    class="w-full py-3 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-bold text-xs uppercase tracking-wider transition-all duration-200 shadow-sm flex items-center justify-center gap-2 group"
                    disabled style="opacity:0.5;cursor:not-allowed;">
                <i class="fa-solid fa-shield-check"></i> Verify OTP
            </button>
        </form>

        {{-- Resend --}}
        <div class="mt-5 text-center">
            <p class="text-gray-500 text-sm">Did not receive the OTP?</p>
            <form method="POST" action="{{ route('password.otp.resend') }}" id="resend-form" class="mt-2">
                @csrf
                <button type="submit" id="resend-btn"
                        class="text-emerald-600 font-semibold text-sm hover:underline disabled:opacity-40 disabled:cursor-not-allowed"
                        disabled>
                    <i class="fa-solid fa-rotate-right mr-1"></i> Resend OTP
                    <span id="resend-countdown"></span>
                </button>
            </form>
        </div>
    </div>

    <p class="text-center text-gray-400 text-sm mt-4">
        <a href="{{ route('password.forgot') }}" class="text-emerald-600 font-semibold hover:underline">
            <i class="fa-solid fa-arrow-left mr-1"></i> Use different email
        </a>
    </p>
</div>

<script>
const TOTAL = {{ $secondsLeft ?? 300 }};
const CIRCUMFERENCE = 213.6;
let timeLeft = TOTAL;

function pad(n) { return n.toString().padStart(2,'0'); }

function updateTimer() {
    const mins = Math.floor(timeLeft / 60);
    const secs = timeLeft % 60;
    document.getElementById('timer-display').textContent = pad(mins) + ':' + pad(secs);

    const offset = CIRCUMFERENCE - (timeLeft / 300) * CIRCUMFERENCE;
    document.getElementById('timer-circle').style.strokeDashoffset = offset;

    const circle = document.getElementById('timer-circle');
    const display = document.getElementById('timer-display');
    if (timeLeft <= 60) {
        circle.style.stroke = '#ef4444';
        display.style.color = '#dc2626';
        document.getElementById('timer-status').textContent = '⚠️ OTP expiring soon!';
        document.getElementById('timer-status').style.color = '#dc2626';
    } else if (timeLeft <= 120) {
        circle.style.stroke = '#f59e0b';
        display.style.color = '#d97706';
    }

    if (timeLeft <= 0) {
        clearInterval(timerInterval);
        document.getElementById('timer-display').textContent = '0:00';
        document.getElementById('timer-status').textContent = '❌ OTP expired. Please resend.';
        document.getElementById('timer-status').style.color = '#dc2626';
        document.getElementById('verify-btn').disabled = true;
        document.getElementById('verify-btn').style.opacity = '0.4';
        document.getElementById('resend-btn').disabled = false;
        document.getElementById('resend-countdown').textContent = '';
    }

    if (timeLeft <= (TOTAL - 60)) {
        document.getElementById('resend-btn').disabled = false;
        document.getElementById('resend-countdown').textContent = '';
    } else {
        const wait = TOTAL - 60 - (TOTAL - timeLeft);
        document.getElementById('resend-countdown').textContent = ' (wait ' + wait + 's)';
    }

    timeLeft--;
}

const timerInterval = setInterval(updateTimer, 1000);
updateTimer();

// OTP input logic
function otpInput(el, idx) {
    el.value = el.value.replace(/[^0-9]/g,'');
    if (el.value) {
        el.classList.add('filled');
        if (idx < 5) document.getElementById('otp-' + (idx+1)).focus();
    } else {
        el.classList.remove('filled');
    }
    updateHidden();
}

function otpKeydown(e, idx) {
    if (e.key === 'Backspace') {
        const el = document.getElementById('otp-' + idx);
        if (!el.value && idx > 0) {
            document.getElementById('otp-' + (idx-1)).focus();
        }
        el.classList.remove('filled');
        updateHidden();
    } else if (e.key === 'ArrowLeft' && idx > 0) {
        document.getElementById('otp-' + (idx-1)).focus();
    } else if (e.key === 'ArrowRight' && idx < 5) {
        document.getElementById('otp-' + (idx+1)).focus();
    }
}

function otpPaste(e) {
    e.preventDefault();
    const text = (e.clipboardData || window.clipboardData).getData('text').replace(/[^0-9]/g,'').slice(0,6);
    text.split('').forEach((ch, i) => {
        const el = document.getElementById('otp-' + i);
        if (el) { el.value = ch; el.classList.add('filled'); }
    });
    updateHidden();
    if (text.length === 6) document.getElementById('otp-5').focus();
}

function updateHidden() {
    let otp = '';
    for (let i = 0; i < 6; i++) {
        otp += document.getElementById('otp-' + i).value || '';
    }
    document.getElementById('otp-hidden').value = otp;
    const btn = document.getElementById('verify-btn');
    if (otp.length === 6 && timeLeft > 0) {
        btn.disabled = false;
        btn.style.opacity = '1';
        btn.style.cursor = 'pointer';
    } else {
        btn.disabled = true;
        btn.style.opacity = '0.5';
        btn.style.cursor = 'not-allowed';
    }
}

// Focus first box
document.getElementById('otp-0').focus();
</script>
@include('partials.global-loader')
</body>
</html>
