<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>College Registration — Learning Curve</title>
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800;900&family=Outfit:wght@400;700;800;900&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
body { font-family: 'Plus Jakarta Sans', sans-serif; background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 50%, #0f172a 100%); min-height: 100vh; }
.glass { background: rgba(255,255,255,0.07); backdrop-filter: blur(20px); border: 1px solid rgba(255,255,255,0.12); }
.inp { width:100%; padding:.7rem 1rem; background:rgba(255,255,255,0.08); border:1px solid rgba(255,255,255,0.15); border-radius:.875rem; color:#f1f5f9; font-size:.85rem; outline:none; transition:all .2s; }
.inp::placeholder { color: rgba(148,163,184,0.5); }
.inp:focus { border-color:rgba(99,102,241,.6); box-shadow:0 0 0 3px rgba(99,102,241,.15); background:rgba(255,255,255,0.12); }
.btn { width:100%; padding:.85rem; border-radius:.875rem; background:linear-gradient(135deg,#6366f1,#8b5cf6); color:#fff; font-weight:800; font-size:.875rem; border:none; cursor:pointer; transition:all .2s; }
.btn:hover { background:linear-gradient(135deg,#4f46e5,#7c3aed); transform:translateY(-1px); box-shadow:0 8px 24px rgba(99,102,241,.4); }
label.lbl { display:block; font-size:.7rem; font-weight:800; color:#94a3b8; text-transform:uppercase; letter-spacing:.08em; margin-bottom:.4rem; }
</style>
</head>
<body class="min-h-screen flex items-center justify-center p-4 py-10">

<div class="relative z-10 w-full max-w-lg">
    <div class="text-center mb-8">
        <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-gradient-to-br from-indigo-500 to-violet-600 shadow-xl mb-4">
            <i class="fa-solid fa-building-columns text-white text-xl"></i>
        </div>
        <h1 class="text-2xl font-black text-white" style="font-family:'Outfit',sans-serif">Register Your Institution</h1>
        <p class="text-indigo-300 text-sm mt-1">Apply for College Admin access on Learning Curve</p>
    </div>

    @if($errors->any())
    <div class="glass rounded-2xl p-4 mb-4 text-rose-200 text-sm">
        @foreach($errors->all() as $e)<p class="flex items-center gap-2"><i class="fa-solid fa-circle-exclamation text-rose-400 flex-shrink-0"></i>{{ $e }}</p>@endforeach
    </div>
    @endif

    <div class="glass rounded-3xl p-7">
        <form method="POST" action="{{ route('college.register.post') }}" class="space-y-4">
            @csrf

            <div class="grid grid-cols-1 gap-4">
                <div>
                    <label class="lbl">College / Institution Name <span class="text-indigo-400">*</span></label>
                    <input type="text" name="college_name" value="{{ old('college_name') }}" required
                           class="inp" placeholder="e.g. ABC Engineering College">
                </div>
                <div>
                    <label class="lbl">Official Email Address <span class="text-indigo-400">*</span></label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                           class="inp" placeholder="admin@abccollege.edu">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="lbl">Password <span class="text-indigo-400">*</span></label>
                    <input type="password" name="password" required class="inp" placeholder="Min 8 characters">
                </div>
                <div>
                    <label class="lbl">Confirm Password</label>
                    <input type="password" name="password_confirmation" required class="inp" placeholder="Repeat">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="lbl">Contact Person</label>
                    <input type="text" name="contact_person" value="{{ old('contact_person') }}"
                           class="inp" placeholder="Principal / Coordinator">
                </div>
                <div>
                    <label class="lbl">Phone</label>
                    <input type="text" name="phone" value="{{ old('phone') }}"
                           class="inp" placeholder="+91 XXXXX XXXXX">
                </div>
            </div>

            <div>
                <label class="lbl">Student Email Domain</label>
                <input type="text" name="domain" value="{{ old('domain') }}"
                       class="inp" placeholder="e.g. students.abccollege.edu">
                <p class="text-indigo-400/60 text-[10px] mt-1">Students with this domain can register directly under your college</p>
            </div>

            <div>
                <label class="lbl">College Address</label>
                <input type="text" name="address" value="{{ old('address') }}" class="inp" placeholder="City, State">
            </div>

            <div>
                <label class="lbl">Website</label>
                <input type="url" name="website" value="{{ old('website') }}" class="inp" placeholder="https://www.abccollege.edu">
            </div>

            <button type="submit" class="btn mt-2">
                <i class="fa-solid fa-paper-plane mr-2"></i> Submit Application
            </button>

            <div class="glass rounded-2xl p-3.5 flex items-start gap-3">
                <i class="fa-solid fa-circle-info text-indigo-400 text-xs mt-0.5 flex-shrink-0"></i>
                <p class="text-slate-400 text-[11px]">After submission, Learning Curve admin will review and <span class="text-indigo-300 font-bold">activate your account</span>. You'll receive login access once approved.</p>
            </div>
        </form>
    </div>

    <p class="text-center mt-5 text-slate-600 text-xs">
        Already registered?
        <a href="{{ route('college.login') }}" class="text-indigo-400 font-bold hover:text-indigo-300 ml-1">Sign in →</a>
        &nbsp;•&nbsp;
        <a href="{{ route('home') }}" class="hover:text-slate-400 transition">← Home</a>
    </p>
</div>
</body>
</html>
