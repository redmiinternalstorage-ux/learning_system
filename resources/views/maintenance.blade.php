<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Under Maintenance — Learning Curve</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800;900&family=Outfit:wght@600;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #04140e; color: #f8fafc; }
        .font-outfit { font-family: 'Outfit', sans-serif; }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4 relative overflow-hidden">

    <!-- Ambient background glows -->
    <div class="absolute top-1/4 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[500px] h-[500px] bg-emerald-500/15 rounded-full blur-[120px] pointer-events-none"></div>
    <div class="absolute bottom-10 right-10 w-[300px] h-[300px] bg-teal-500/10 rounded-full blur-[90px] pointer-events-none"></div>

    <div class="max-w-xl w-full text-center relative z-10 space-y-8 p-8 sm:p-10 rounded-3xl bg-slate-900/80 border border-emerald-500/20 backdrop-blur-xl shadow-2xl">
        
        <!-- Animated Maintenance Icon -->
        <div class="relative w-24 h-24 mx-auto flex items-center justify-center">
            <div class="absolute inset-0 rounded-3xl bg-emerald-500/20 animate-pulse border border-emerald-500/30"></div>
            <i class="fa-solid fa-gears text-5xl text-emerald-400 animate-spin" style="animation-duration: 8s;"></i>
        </div>

        <!-- Title & Description -->
        <div class="space-y-3">
            <span class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-emerald-500/10 border border-emerald-500/30 text-emerald-300 text-xs font-bold uppercase tracking-widest">
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span> Under Maintenance
            </span>
            <h1 class="text-3xl sm:text-4xl font-black font-outfit text-white leading-tight">
                We'll Be Back Online Shortly! 🛠️
            </h1>
            <p class="text-slate-300 text-sm leading-relaxed max-w-md mx-auto">
                <strong>Learning Curve LMS</strong> is currently undergoing scheduled maintenance & system optimization. All user services will resume shortly.
            </p>
        </div>

        <!-- Contact Support Section -->
        <div class="p-6 rounded-2xl bg-slate-950/60 border border-emerald-500/20 space-y-4">
            <div>
                <p class="text-xs font-bold text-slate-200">Need Urgent Assistance?</p>
                <p class="text-[11px] text-slate-400 mt-0.5">Feel free to reach out to our support team directly via email.</p>
            </div>
            
            <a href="mailto:{{ \App\Models\SiteSetting::get('contact_email', 'admin@gmail.com') }}" 
               class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-400 hover:to-teal-500 text-white font-extrabold text-xs shadow-lg transition transform hover:scale-105">
                <i class="fa-solid fa-envelope text-sm"></i> Contact Support Team
            </a>
        </div>

        <div class="flex flex-col sm:flex-row items-center justify-between gap-2 pt-2 border-t border-slate-800/60">
            <p class="text-[11px] text-slate-500 font-medium">
                © {{ date('Y') }} Learning Curve LMS. All rights reserved.
            </p>
            <a href="{{ route('admin.login') }}" class="inline-flex items-center gap-1.5 text-[11px] text-slate-400 hover:text-emerald-400 transition font-bold">
                <i class="fa-solid fa-shield-halved text-[10px] text-emerald-500"></i> Admin / Staff Sign In
            </a>
        </div>

    </div>

</body>
</html>
