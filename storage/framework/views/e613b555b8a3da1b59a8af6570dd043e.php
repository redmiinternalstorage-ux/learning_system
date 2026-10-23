<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Learning Curve — Learn Without Limits</title>
<link rel="icon" type="image/svg+xml" href="/favicon.svg">
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800;900&family=Outfit:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<script>
    document.documentElement.classList.remove('dark');
    tailwind.config = {
        theme: { extend: { fontFamily: { sans: ['"Plus Jakarta Sans"','sans-serif'], outfit: ['"Outfit"','sans-serif'] } } }
    }
</script>
<style>
* { box-sizing: border-box; margin: 0; padding: 0; }
body { font-family: 'Plus Jakarta Sans', sans-serif; background: #fff; overflow-x: hidden; }
.font-outfit { font-family: 'Outfit', sans-serif; }

/* ── Navbar ── */
.navbar {
    background: rgba(255,255,255,0.92);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    border-bottom: 1px solid rgba(5,150,105,0.08);
    position: sticky; top: 0; z-index: 100;
}

/* ── Buttons ── */
.btn-green {
    background: linear-gradient(135deg, #059669 0%, #0d9488 100%);
    color: #fff; font-weight: 700; border-radius: 0.875rem;
    transition: all 0.25s cubic-bezier(0.4,0,0.2,1);
    box-shadow: 0 4px 15px rgba(5,150,105,0.3);
}
.btn-green:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(5,150,105,0.4); background: linear-gradient(135deg, #047857 0%, #0f766e 100%); }

.btn-white {
    background: #fff; color: #047857; font-weight: 700; border-radius: 0.875rem;
    border: 2px solid #d1fae5;
    transition: all 0.2s;
}
.btn-white:hover { background: #f0fdf4; border-color: #6ee7b7; transform: translateY(-1px); }

/* ── Gradient text ── */
.grad-text { background: linear-gradient(135deg, #059669, #0d9488, #10b981); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }
.grad-text-2 { background: linear-gradient(135deg, #f59e0b, #ef4444); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }

/* ── Hero floating cards ── */
.float-card {
    background: rgba(255,255,255,0.95);
    backdrop-filter: blur(20px);
    border-radius: 1.25rem;
    box-shadow: 0 20px 60px -10px rgba(0,0,0,0.12);
    border: 1px solid rgba(255,255,255,0.8);
}

/* ── Stats bar ── */
.stat-bar { background: linear-gradient(135deg, #064e3b 0%, #065f46 50%, #047857 100%); }

/* ── Course cards ── */
.course-card {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 1.25rem;
    overflow: hidden;
    transition: all 0.35s cubic-bezier(0.4,0,0.2,1);
    position: relative;
}
.course-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 24px 50px -12px rgba(5,150,105,0.18);
    border-color: #6ee7b7;
}

/* ── Feature cards ── */
.feat-card {
    border-radius: 1.5rem;
    padding: 1.75rem;
    position: relative;
    overflow: hidden;
    transition: all 0.3s;
}
.feat-card:hover { transform: translateY(-4px); }

/* ── Marquee ── */
@keyframes marquee { 0% { transform: translateX(0); } 100% { transform: translateX(-50%); } }
.marquee-track { display: flex; animation: marquee 25s linear infinite; width: max-content; }
.marquee-track:hover { animation-play-state: paused; }

/* ── Blob animations ── */
@keyframes blob1 { 0%,100%{border-radius:60% 40% 30% 70%/60% 30% 70% 40%;} 50%{border-radius:30% 60% 70% 40%/50% 60% 30% 60%;} }
@keyframes blob2 { 0%,100%{border-radius:40% 60% 60% 40%/40% 40% 60% 60%;} 50%{border-radius:60% 40% 40% 60%/60% 60% 40% 40%;} }
.blob1 { animation: blob1 8s ease-in-out infinite; }
.blob2 { animation: blob2 10s ease-in-out infinite; }

/* ── Glow pulse ── */
@keyframes glow { 0%,100%{opacity:0.6;} 50%{opacity:1;} }
.glow-pulse { animation: glow 3s ease-in-out infinite; }

/* ── Number counter cards ── */
.counter-card {
    background: rgba(255,255,255,0.12);
    border: 1px solid rgba(255,255,255,0.2);
    border-radius: 1rem;
    backdrop-filter: blur(10px);
}

/* Scrollbar */
::-webkit-scrollbar { width:5px; }
::-webkit-scrollbar-track { background:transparent; }
::-webkit-scrollbar-thumb { background:#a7f3d0; border-radius:9999px; }
</style>
</head>
<body>


<header class="navbar">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between gap-4">
        
        <a href="<?php echo e(route('home')); ?>" class="flex items-center gap-2.5 flex-shrink-0">
            <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center shadow-md shadow-emerald-500/30">
                <i class="fa-solid fa-graduation-cap text-white text-sm"></i>
            </div>
            <div class="hidden sm:block">
                <p class="font-black text-slate-900 text-sm leading-tight font-outfit">Learning Curve</p>
                <p class="text-emerald-600 text-[9px] font-bold uppercase tracking-widest">LMS Portal</p>
            </div>
        </a>

        
        <nav class="hidden md:flex items-center gap-1">
            <a href="<?php echo e(route('courses.public')); ?>" class="px-4 py-2 rounded-xl text-sm font-semibold text-slate-600 hover:text-emerald-700 hover:bg-emerald-50 transition">Courses</a>
            <a href="#categories" class="px-4 py-2 rounded-xl text-sm font-semibold text-slate-600 hover:text-emerald-700 hover:bg-emerald-50 transition">Categories</a>
            <a href="#features" class="px-4 py-2 rounded-xl text-sm font-semibold text-slate-600 hover:text-emerald-700 hover:bg-emerald-50 transition">Features</a>
            <a href="<?php echo e(route('developers')); ?>" class="px-4 py-2 rounded-xl text-sm font-semibold text-emerald-600 hover:text-emerald-700 hover:bg-emerald-50 transition flex items-center gap-1.5">
                <i class="fa-solid fa-code text-xs"></i> Developers
            </a>
        </nav>

        
        <div class="flex items-center gap-2.5 flex-shrink-0">
            <?php if(auth()->guard()->check()): ?>
                <a href="<?php echo e(route('dashboard')); ?>"
                   class="btn-green px-5 py-2.5 text-sm flex items-center gap-2 shadow-lg shadow-emerald-500/25">
                    <i class="fa-solid fa-gauge-high"></i>
                    <span class="hidden sm:inline font-bold">Dashboard</span>
                </a>
            <?php else: ?>
                
                <a href="<?php echo e(route('login')); ?>"
                   class="hidden sm:flex items-center gap-2.5 px-5 py-2.5 rounded-2xl border-2 border-emerald-200 bg-white hover:bg-emerald-50 hover:border-emerald-400 text-emerald-700 font-bold text-sm transition-all duration-200 group">
                    <span class="w-7 h-7 rounded-xl bg-emerald-100 group-hover:bg-emerald-200 flex items-center justify-center transition flex-shrink-0">
                        <i class="fa-solid fa-right-to-bracket text-emerald-600 text-xs"></i>
                    </span>
                    <span>Login</span>
                </a>
                
                <a href="<?php echo e(route('register')); ?>"
                   class="flex items-center gap-2.5 px-5 py-2.5 rounded-2xl btn-green text-sm font-bold shadow-lg shadow-emerald-500/30 group">
                    <span class="w-7 h-7 rounded-xl bg-white/20 group-hover:bg-white/30 flex items-center justify-center transition flex-shrink-0">
                        <i class="fa-solid fa-user-plus text-white text-xs"></i>
                    </span>
                    <span class="hidden sm:inline">Register Free</span>
                    <span class="sm:hidden">Register</span>
                </a>
            <?php endif; ?>
            <button id="mob-btn" class="md:hidden w-10 h-10 rounded-xl bg-slate-100 hover:bg-emerald-50 hover:text-emerald-700 flex items-center justify-center text-slate-600 transition border border-slate-200">
                <i class="fa-solid fa-bars text-sm"></i>
            </button>
        </div>
    </div>
    
    <div id="mob-nav" class="hidden md:hidden px-4 pb-4 pt-1 space-y-1 bg-white border-t border-slate-100">
        <a href="<?php echo e(route('courses.public')); ?>" class="block py-2.5 px-3 rounded-xl text-sm font-semibold text-slate-700 hover:bg-emerald-50 hover:text-emerald-700 transition">Courses</a>
        <a href="#categories" class="block py-2.5 px-3 rounded-xl text-sm font-semibold text-slate-700 hover:bg-emerald-50 hover:text-emerald-700 transition">Categories</a>
        <a href="#features" class="block py-2.5 px-3 rounded-xl text-sm font-semibold text-slate-700 hover:bg-emerald-50 hover:text-emerald-700 transition">Features</a>
        <?php if(auth()->guard()->guest()): ?>
        <a href="<?php echo e(route('login')); ?>" class="block py-2.5 px-3 rounded-xl text-sm font-semibold text-slate-700 hover:bg-emerald-50 transition">Login</a>
        <a href="<?php echo e(route('register')); ?>" class="block py-2.5 px-3 rounded-xl text-sm font-bold text-emerald-700 bg-emerald-50 hover:bg-emerald-100 transition">Register Free</a>
        <?php endif; ?>
        <?php if(auth()->guard()->check()): ?>
        <a href="<?php echo e(route('dashboard')); ?>" class="block py-2.5 px-3 rounded-xl text-sm font-bold text-emerald-700 bg-emerald-50 hover:bg-emerald-100 transition">My Dashboard</a>
        <?php endif; ?>
    </div>
</header>



<div class="py-2.5 overflow-hidden border-b border-slate-100" style="background:#fff;">
    <div class="marquee-track gap-3 px-2">
        <?php
        $skills = [
            ['label'=>'HTML & CSS',       'c'=>'bg-orange-100 text-orange-700 border-orange-200'],
            ['label'=>'JavaScript',        'c'=>'bg-yellow-100 text-yellow-700 border-yellow-200'],
            ['label'=>'React',             'c'=>'bg-sky-100 text-sky-700 border-sky-200'],
            ['label'=>'Node.js',           'c'=>'bg-green-100 text-green-700 border-green-200'],
            ['label'=>'Python',            'c'=>'bg-blue-100 text-blue-700 border-blue-200'],
            ['label'=>'Machine Learning',  'c'=>'bg-violet-100 text-violet-700 border-violet-200'],
            ['label'=>'UI/UX Design',      'c'=>'bg-pink-100 text-pink-700 border-pink-200'],
            ['label'=>'Data Science',      'c'=>'bg-teal-100 text-teal-700 border-teal-200'],
            ['label'=>'Mobile Apps',       'c'=>'bg-indigo-100 text-indigo-700 border-indigo-200'],
            ['label'=>'Marketing',         'c'=>'bg-amber-100 text-amber-700 border-amber-200'],
            ['label'=>'Vue.js',            'c'=>'bg-emerald-100 text-emerald-700 border-emerald-200'],
            ['label'=>'TypeScript',        'c'=>'bg-blue-100 text-blue-700 border-blue-200'],
            ['label'=>'SQL',               'c'=>'bg-rose-100 text-rose-700 border-rose-200'],
            ['label'=>'Docker',            'c'=>'bg-cyan-100 text-cyan-700 border-cyan-200'],
            ['label'=>'REST APIs',         'c'=>'bg-lime-100 text-lime-700 border-lime-200'],
            ['label'=>'Flutter',           'c'=>'bg-sky-100 text-sky-700 border-sky-200'],
            ['label'=>'TailwindCSS',       'c'=>'bg-teal-100 text-teal-700 border-teal-200'],
            ['label'=>'Laravel',           'c'=>'bg-red-100 text-red-700 border-red-200'],
        ];
        ?>
        <?php $__currentLoopData = array_merge($skills, $skills); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full border text-[11px] font-bold whitespace-nowrap <?php echo e($s['c']); ?>">
            <i class="fa-solid fa-circle text-[5px]"></i><?php echo e($s['label']); ?>

        </span>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
</div>



<section class="relative min-h-[92vh] flex items-center overflow-hidden"
         style="background: linear-gradient(135deg, #f0fdf4 0%, #ecfdf5 30%, #f0fdfa 60%, #f8fafc 100%);">

    
    <div class="blob1 absolute -top-32 -left-32 w-[500px] h-[500px] opacity-40 pointer-events-none"
         style="background: radial-gradient(circle, rgba(52,211,153,0.4) 0%, rgba(16,185,129,0.1) 60%, transparent 80%); filter: blur(40px);"></div>
    <div class="blob2 absolute -bottom-40 -right-40 w-[600px] h-[600px] opacity-30 pointer-events-none"
         style="background: radial-gradient(circle, rgba(20,184,166,0.4) 0%, rgba(5,150,105,0.1) 60%, transparent 80%); filter: blur(50px);"></div>
    <div class="absolute top-1/3 right-1/4 w-48 h-48 opacity-20 pointer-events-none"
         style="background: radial-gradient(circle, rgba(245,158,11,0.5) 0%, transparent 70%); filter: blur(30px);"></div>

    
    <div class="absolute inset-0 pointer-events-none opacity-[0.03]"
         style="background-image: radial-gradient(#059669 1px, transparent 1px); background-size: 30px 30px;"></div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 w-full">
        <div class="grid lg:grid-cols-2 gap-12 lg:gap-16 items-center">

            
            <div class="text-center lg:text-left">

                
                <div class="inline-flex items-center gap-2.5 bg-white border border-emerald-100 rounded-full px-4 py-2 mb-7 shadow-sm shadow-emerald-100">
                    <span class="flex items-center justify-center w-5 h-5 rounded-full bg-gradient-to-br from-emerald-500 to-teal-500">
                        <i class="fa-solid fa-star text-white text-[9px]"></i>
                    </span>
                    <span class="text-xs font-bold text-emerald-800 tracking-wide"><?php echo e($totalCourses); ?>+ Expert-Crafted Courses</span>
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 glow-pulse"></span>
                </div>

                
                <h1 class="text-5xl sm:text-6xl lg:text-7xl font-black font-outfit leading-[1.08] mb-6 tracking-tight">
                    <span class="text-slate-900">Unlock</span><br>
                    <span class="grad-text">Your Future</span><br>
                    <span class="text-slate-900 text-4xl sm:text-5xl lg:text-6xl">with Learning.</span>
                </h1>

                <p class="text-slate-500 text-base sm:text-lg leading-relaxed mb-8 max-w-lg mx-auto lg:mx-0">
                    Master in-demand skills with world-class courses. From web development to AI — start free, grow fast, and earn certificates that employers value.
                </p>

                
                <div class="flex flex-wrap gap-3 justify-center lg:justify-start mb-10">
                    <a href="<?php echo e(route('courses.public')); ?>"
                       class="btn-green px-7 py-3.5 text-sm flex items-center gap-2.5 group">
                        <i class="fa-solid fa-compass text-sm"></i>
                        <span>Explore All Courses</span>
                        <i class="fa-solid fa-arrow-right text-xs group-hover:translate-x-1 transition-transform"></i>
                    </a>
                    <?php if(auth()->guard()->guest()): ?>
                    <a href="<?php echo e(route('register')); ?>"
                       class="btn-white px-7 py-3.5 text-sm flex items-center gap-2.5">
                        <i class="fa-solid fa-user-plus text-sm"></i>
                        <span>Join for Free</span>
                    </a>
                    <?php endif; ?>
                    <?php if(auth()->guard()->check()): ?>
                    <a href="<?php echo e(route('dashboard')); ?>"
                       class="btn-white px-7 py-3.5 text-sm flex items-center gap-2.5">
                        <i class="fa-solid fa-gauge-high text-sm"></i>
                        <span>My Dashboard</span>
                    </a>
                    <?php endif; ?>
                </div>

                
                <div class="flex flex-wrap items-center gap-4 justify-center lg:justify-start text-xs text-slate-500 font-semibold">
                    <span class="flex items-center gap-1.5"><i class="fa-solid fa-circle-check text-emerald-500"></i> Free Certificates</span>
                    <span class="flex items-center gap-1.5"><i class="fa-solid fa-circle-check text-emerald-500"></i> No Credit Card</span>
                    <span class="flex items-center gap-1.5"><i class="fa-solid fa-circle-check text-emerald-500"></i> Self-Paced</span>
                </div>
            </div>

            
            <div class="hidden lg:block">
                <div class="relative">
                    
                    <div class="float-card p-5 relative z-10">
                        
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">My Learning Path</p>
                                <p class="font-black text-slate-800 text-sm font-outfit mt-0.5">Full Stack Development</p>
                            </div>
                            <div class="w-10 h-10 rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center shadow-md">
                                <i class="fa-solid fa-code text-white text-sm"></i>
                            </div>
                        </div>

                        
                        <div class="mb-4">
                            <div class="flex justify-between text-xs font-semibold mb-2">
                                <span class="text-slate-500">Overall Progress</span>
                                <span class="text-emerald-600 font-bold">68%</span>
                            </div>
                            <div class="w-full bg-slate-100 rounded-full h-2.5 overflow-hidden">
                                <div class="h-full rounded-full bg-gradient-to-r from-emerald-500 to-teal-500 transition-all"
                                     style="width:68%"></div>
                            </div>
                        </div>

                        
                        <div class="space-y-2.5 mb-4">
                            <?php
                            $lessons = [
                                ['icon'=>'fa-html5','color'=>'bg-orange-100 text-orange-600','label'=>'HTML & CSS Fundamentals','done'=>true],
                                ['icon'=>'fa-js','color'=>'bg-yellow-100 text-yellow-600','label'=>'JavaScript Essentials','done'=>true],
                                ['icon'=>'fa-react','color'=>'bg-sky-100 text-sky-600','label'=>'React & Next.js','active'=>true],
                                ['icon'=>'fa-database','color'=>'bg-indigo-100 text-indigo-600','label'=>'Backend & Databases','done'=>false],
                            ];
                            ?>
                            <?php $__currentLoopData = $lessons; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $l): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="flex items-center gap-3 p-2.5 rounded-xl <?php echo e(isset($l['active']) ? 'bg-emerald-50 border border-emerald-100' : 'bg-slate-50'); ?>">
                                <div class="w-8 h-8 rounded-xl <?php echo e($l['color']); ?> flex items-center justify-center text-sm flex-shrink-0">
                                    <i class="fa-brands <?php echo e($l['icon']); ?>"></i>
                                </div>
                                <p class="text-xs font-semibold text-slate-700 flex-1"><?php echo e($l['label']); ?></p>
                                <?php if(isset($l['done']) && $l['done']): ?>
                                    <i class="fa-solid fa-circle-check text-emerald-500 text-sm"></i>
                                <?php elseif(isset($l['active']) && $l['active']): ?>
                                    <span class="text-[9px] bg-emerald-500 text-white px-2 py-0.5 rounded-full font-bold">ACTIVE</span>
                                <?php else: ?>
                                    <i class="fa-regular fa-circle text-slate-300 text-sm"></i>
                                <?php endif; ?>
                            </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>

                        <button class="w-full btn-green py-2.5 text-xs flex items-center justify-center gap-2">
                            <i class="fa-solid fa-play-circle"></i> Continue Learning
                        </button>
                    </div>

                    
                    <div class="absolute -top-5 -right-5 float-card px-4 py-3 flex items-center gap-2.5 z-20">
                        <div class="w-9 h-9 rounded-xl bg-amber-100 flex items-center justify-center flex-shrink-0">
                            <i class="fa-solid fa-award text-amber-500 text-base"></i>
                        </div>
                        <div>
                            <p class="text-[10px] text-slate-400 font-semibold">Certificate</p>
                            <p class="text-xs font-bold text-slate-800">Just Earned! 🎉</p>
                        </div>
                    </div>

                    <div class="absolute -bottom-5 -left-5 float-card px-4 py-3 flex items-center gap-2.5 z-20">
                        <div class="w-9 h-9 rounded-xl bg-emerald-100 flex items-center justify-center flex-shrink-0">
                            <i class="fa-solid fa-users text-emerald-600 text-base"></i>
                        </div>
                        <div>
                            <p class="text-[10px] text-slate-400 font-semibold">Learners Active</p>
                            <p class="text-xs font-bold text-slate-800">1,200+ Online</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>



<div class="stat-bar py-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <?php $__currentLoopData = [
                ['val'=>$totalCourses.'+', 'label'=>'Courses Available', 'icon'=>'fa-book-open'],
                ['val'=>'Expert',          'label'=>'Instructors',       'icon'=>'fa-chalkboard-user'],
                ['val'=>'Free',            'label'=>'Certificates',      'icon'=>'fa-award'],
                ['val'=>'24/7',            'label'=>'Support Access',    'icon'=>'fa-headset'],
            ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="counter-card p-5 text-center">
                <div class="w-10 h-10 rounded-2xl bg-white/10 flex items-center justify-center mx-auto mb-3">
                    <i class="fa-solid <?php echo e($s['icon']); ?> text-emerald-300 text-lg"></i>
                </div>
                <p class="text-3xl font-black text-white font-outfit"><?php echo e($s['val']); ?></p>
                <p class="text-emerald-300/80 text-[10px] font-bold uppercase tracking-wider mt-1"><?php echo e($s['label']); ?></p>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
</div>



<section id="categories" class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <span class="inline-flex items-center gap-2 bg-emerald-50 text-emerald-700 border border-emerald-100 text-xs font-bold px-3.5 py-1.5 rounded-full mb-4">
                <i class="fa-solid fa-layer-group text-emerald-500"></i> Browse by Topic
            </span>
            <h2 class="text-3xl sm:text-4xl font-black text-slate-900 font-outfit tracking-tight">
                What do you want to <span class="grad-text">learn today?</span>
            </h2>
            <p class="text-slate-400 text-sm mt-3 max-w-xl mx-auto">From beginner to expert — find courses in your area of interest and start building real skills.</p>
        </div>

        <?php
        $cats = [
            ['name'=>'Web Development',    'icon'=>'fa-code',          'bg'=>'from-blue-500 to-indigo-600',    'light'=>'bg-blue-50 border-blue-100',   'txt'=>'text-blue-600'],
            ['name'=>'Data Science',       'icon'=>'fa-chart-bar',     'bg'=>'from-teal-500 to-cyan-600',      'light'=>'bg-teal-50 border-teal-100',   'txt'=>'text-teal-600'],
            ['name'=>'Design',             'icon'=>'fa-pen-nib',       'bg'=>'from-pink-500 to-rose-600',      'light'=>'bg-pink-50 border-pink-100',   'txt'=>'text-pink-600'],
            ['name'=>'AI & ML',            'icon'=>'fa-robot',         'bg'=>'from-violet-500 to-purple-600',  'light'=>'bg-violet-50 border-violet-100','txt'=>'text-violet-600'],
            ['name'=>'Mobile Development', 'icon'=>'fa-mobile-screen', 'bg'=>'from-orange-500 to-amber-500',   'light'=>'bg-orange-50 border-orange-100','txt'=>'text-orange-600'],
            ['name'=>'Marketing',          'icon'=>'fa-bullhorn',      'bg'=>'from-amber-500 to-yellow-500',   'light'=>'bg-amber-50 border-amber-100', 'txt'=>'text-amber-600'],
        ];
        ?>

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
            <?php $__currentLoopData = $cats; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <a href="<?php echo e(route('courses.public', ['category' => $cat['name']])); ?>"
               class="group flex flex-col items-center gap-3.5 p-6 bg-white rounded-2xl border border-slate-100 hover:border-transparent hover:shadow-xl transition-all duration-300 text-center relative overflow-hidden">
                <div class="absolute inset-0 bg-gradient-to-br <?php echo e($cat['bg']); ?> opacity-0 group-hover:opacity-5 transition-opacity duration-300"></div>
                <div class="w-14 h-14 rounded-2xl bg-gradient-to-br <?php echo e($cat['bg']); ?> flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300">
                    <i class="fa-solid <?php echo e($cat['icon']); ?> text-white text-xl"></i>
                </div>
                <span class="text-xs font-bold text-slate-700 group-hover:text-slate-900 font-outfit uppercase tracking-wide leading-tight"><?php echo e($cat['name']); ?></span>
            </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
</section>



<section class="py-20 bg-slate-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col sm:flex-row items-start sm:items-end justify-between mb-12 gap-4">
            <div>
                <span class="inline-flex items-center gap-2 bg-emerald-50 text-emerald-700 border border-emerald-100 text-xs font-bold px-3.5 py-1.5 rounded-full mb-4">
                    <i class="fa-solid fa-fire text-orange-500"></i> Trending Now
                </span>
                <h2 class="text-3xl sm:text-4xl font-black text-slate-900 font-outfit tracking-tight">Featured <span class="grad-text">Courses</span></h2>
            </div>
            <a href="<?php echo e(route('courses.public')); ?>"
               class="btn-green px-5 py-2.5 text-xs flex items-center gap-2 flex-shrink-0">
                See All Courses <i class="fa-solid fa-arrow-right text-[10px]"></i>
            </a>
        </div>

        <?php
        $gradMap = [
            'Web Development'    => ['from-blue-500','to-indigo-600'],
            'Data Science'       => ['from-teal-500','to-cyan-600'],
            'Design'             => ['from-pink-500','to-rose-600'],
            'AI & ML'            => ['from-violet-500','to-purple-600'],
            'Mobile Development' => ['from-orange-500','to-amber-500'],
            'Marketing'          => ['from-amber-500','to-yellow-500'],
        ];
        ?>
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php $__empty_1 = true; $__currentLoopData = $featuredCourses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $course): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <?php $g = $gradMap[$course->category] ?? ['from-emerald-500','to-teal-600']; ?>
            <a href="<?php echo e(route('courses.show', $course)); ?>" class="course-card group flex flex-col">
                
                <div class="h-48 relative overflow-hidden">
                    <div class="absolute inset-0 bg-gradient-to-br <?php echo e($g[0]); ?> <?php echo e($g[1]); ?>"></div>
                    
                    <div class="absolute inset-0 opacity-10"
                         style="background-image:radial-gradient(circle, rgba(255,255,255,0.8) 1px, transparent 1px);background-size:20px 20px;"></div>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <i class="fa-solid fa-play-circle text-white/25 group-hover:text-white/50 text-6xl transition-all duration-400 group-hover:scale-110"></i>
                    </div>
                    
                    <div class="absolute top-3 left-3">
                        <span class="bg-white/90 backdrop-blur-sm text-slate-800 text-[10px] font-bold uppercase tracking-wide px-2.5 py-1 rounded-lg"><?php echo e($course->category); ?></span>
                    </div>
                    <?php if(!$course->price || $course->price == 0): ?>
                    <div class="absolute top-3 right-3">
                        <span class="bg-emerald-500 text-white text-[10px] font-bold px-2.5 py-1 rounded-lg">FREE</span>
                    </div>
                    <?php endif; ?>
                    
                    <div class="absolute bottom-3 left-3">
                        <span class="bg-black/40 backdrop-blur-sm text-white text-[10px] font-semibold px-2.5 py-1 rounded-lg flex items-center gap-1">
                            <i class="fa-solid fa-signal text-[9px]"></i> <?php echo e($course->level ?? 'All Levels'); ?>

                        </span>
                    </div>
                </div>

                
                <div class="p-5 flex flex-col flex-1 bg-white">
                    <h3 class="font-bold text-slate-800 text-sm leading-snug mb-2 group-hover:text-emerald-700 transition line-clamp-2"><?php echo e($course->title); ?></h3>
                    <p class="text-slate-400 text-[11px] leading-relaxed mb-3 line-clamp-2 flex-1">
                        <?php echo e($course->short_description ?? \Illuminate\Support\Str::limit(strip_tags($course->description ?? ''), 90)); ?>

                    </p>
                    <div class="flex items-center gap-2 mb-3">
                        <div class="w-6 h-6 rounded-full bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center flex-shrink-0">
                            <span class="text-white text-[9px] font-bold"><?php echo e(strtoupper(substr($course->instructor_name ?? 'I', 0, 1))); ?></span>
                        </div>
                        <p class="text-slate-500 text-[11px] font-semibold truncate"><?php echo e($course->instructor_name ?? 'Instructor'); ?></p>
                    </div>
                    <div class="flex items-center justify-between pt-3 border-t border-slate-100">
                        <div class="flex items-center gap-3 text-[11px] text-slate-400 font-medium">
                            <span class="flex items-center gap-1"><i class="fa-solid fa-clock text-emerald-500"></i> <?php echo e($course->duration_hours ?? 0); ?>h</span>
                        </div>
                        <span class="font-extrabold text-sm <?php echo e((!$course->price || $course->price == 0) ? 'text-emerald-600' : 'text-slate-800'); ?>">
                            <?php echo e((!$course->price || $course->price == 0) ? 'Free' : '₹'.number_format($course->price)); ?>

                        </span>
                    </div>
                </div>
            </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="col-span-3 text-center py-20">
                <i class="fa-solid fa-book-open text-slate-200 text-5xl mb-4 block"></i>
                <p class="text-slate-400 font-semibold">No courses published yet. Coming soon!</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
</section>



<section id="features" class="py-20 bg-slate-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-14">
            <span class="inline-flex items-center gap-2 bg-emerald-50 text-emerald-700 border border-emerald-100 text-xs font-bold px-3.5 py-1.5 rounded-full mb-4">
                <i class="fa-solid fa-sparkles text-amber-500"></i> Why Choose Us
            </span>
            <h2 class="text-3xl sm:text-4xl font-black text-slate-900 font-outfit tracking-tight">
                Everything you need to <span class="grad-text">grow fast</span>
            </h2>
        </div>

        <?php
        $features = [
            ['bg'=>'from-emerald-500 to-teal-600',  'icon'=>'fa-laptop-code',    'title'=>'Learn Anywhere',     'desc'=>'Access all courses on any device — mobile, tablet, or desktop. Learn at your pace, anytime.'],
            ['bg'=>'from-violet-500 to-purple-600',  'icon'=>'fa-award',          'title'=>'Free Certificates',  'desc'=>'Earn verified completion certificates to showcase your skills on LinkedIn and resumes.'],
            ['bg'=>'from-amber-500 to-orange-500',   'icon'=>'fa-bolt',           'title'=>'Fast-Track Learning','desc'=>'Structured curriculum designed to get you job-ready in weeks, not months.'],
            ['bg'=>'from-rose-500 to-pink-600',      'icon'=>'fa-headset',        'title'=>'24/7 Support',       'desc'=>'Live chat and dedicated support team always available to help you succeed.'],
            ['bg'=>'from-blue-500 to-indigo-600',    'icon'=>'fa-chart-line',     'title'=>'Progress Tracking',  'desc'=>'Real-time analytics dashboard tracks your progress, scores, and achievements.'],
            ['bg'=>'from-teal-500 to-cyan-600',      'icon'=>'fa-users',          'title'=>'Expert Instructors', 'desc'=>'Learn from industry professionals with real-world experience and proven track records.'],
        ];
        ?>

        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
            <?php $__currentLoopData = $features; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $f): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="feat-card bg-white border border-slate-100 hover:border-transparent hover:shadow-xl group">
                <div class="absolute inset-0 bg-gradient-to-br <?php echo e($f['bg']); ?> opacity-0 group-hover:opacity-[0.04] rounded-2xl transition-opacity duration-300 pointer-events-none"></div>
                <div class="w-12 h-12 rounded-2xl bg-gradient-to-br <?php echo e($f['bg']); ?> flex items-center justify-center mb-4 shadow-lg">
                    <i class="fa-solid <?php echo e($f['icon']); ?> text-white text-lg"></i>
                </div>
                <h3 class="font-extrabold text-slate-900 text-base font-outfit mb-2"><?php echo e($f['title']); ?></h3>
                <p class="text-slate-500 text-sm leading-relaxed"><?php echo e($f['desc']); ?></p>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
</section>



<?php if(auth()->guard()->guest()): ?>
<section class="py-20 relative overflow-hidden" style="background: linear-gradient(135deg, #064e3b 0%, #065f46 40%, #047857 70%, #059669 100%);">
    <div class="absolute inset-0 pointer-events-none">
        <div class="blob1 absolute -top-20 -right-20 w-80 h-80 opacity-20" style="background:radial-gradient(circle,rgba(52,211,153,0.8) 0%,transparent 70%);filter:blur(30px);"></div>
        <div class="blob2 absolute -bottom-20 -left-20 w-80 h-80 opacity-15" style="background:radial-gradient(circle,rgba(20,184,166,0.8) 0%,transparent 70%);filter:blur(40px);"></div>
        <div class="absolute inset-0 opacity-[0.04]"
             style="background-image:radial-gradient(circle, rgba(255,255,255,0.8) 1px, transparent 1px);background-size:32px 32px;"></div>
    </div>
    <div class="relative max-w-3xl mx-auto px-4 text-center">
        <div class="inline-flex items-center justify-center w-20 h-20 rounded-3xl bg-white/10 border border-white/20 mb-6 mx-auto">
            <i class="fa-solid fa-rocket text-white text-3xl"></i>
        </div>
        <h2 class="text-3xl sm:text-5xl font-black text-white mb-4 font-outfit tracking-tight leading-tight">
            Start Your Journey<br><span style="color:#86efac;">Today. For Free.</span>
        </h2>
        <p class="text-emerald-200/80 text-base mb-10 max-w-lg mx-auto">
            Join thousands of learners worldwide. No credit card required — just create an account and start learning immediately.
        </p>
        <div class="flex flex-wrap justify-center gap-4">
            <a href="<?php echo e(route('register')); ?>"
               class="px-9 py-4 rounded-2xl bg-white text-emerald-800 font-extrabold text-sm shadow-2xl hover:bg-emerald-50 transition flex items-center gap-2.5 group">
                <i class="fa-solid fa-user-plus"></i>
                Create Free Account
                <i class="fa-solid fa-arrow-right text-xs group-hover:translate-x-1 transition-transform"></i>
            </a>
            <a href="<?php echo e(route('courses.public')); ?>"
               class="px-9 py-4 rounded-2xl bg-white/10 border border-white/20 text-white font-bold text-sm hover:bg-white/20 transition flex items-center gap-2.5">
                <i class="fa-solid fa-compass"></i> Browse Courses
            </a>
        </div>
        <p class="text-emerald-400/70 text-xs mt-6">✓ Free forever &nbsp;·&nbsp; ✓ No hidden fees &nbsp;·&nbsp; ✓ Cancel anytime</p>
    </div>
</section>
<?php endif; ?>



<footer class="bg-white border-t border-slate-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <div class="flex flex-col md:flex-row items-center justify-between gap-6">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center shadow-md shadow-emerald-500/20">
                    <i class="fa-solid fa-graduation-cap text-white text-sm"></i>
                </div>
                <div>
                    <p class="font-black text-slate-900 text-sm font-outfit">Learning Curve</p>
                    <p class="text-emerald-600 text-[10px] font-bold uppercase tracking-wider">Learn Without Limits</p>
                </div>
            </div>
            <div class="flex flex-wrap justify-center gap-6 text-xs font-bold text-slate-400 uppercase tracking-wider">
                <a href="<?php echo e(route('courses.public')); ?>" class="hover:text-emerald-600 transition">Courses</a>
                <a href="#categories" class="hover:text-emerald-600 transition">Categories</a>
                <a href="<?php echo e(route('developers')); ?>" class="hover:text-emerald-600 transition">Developers</a>
                <?php if(auth()->guard()->check()): ?>
                <a href="<?php echo e(route('dashboard')); ?>" class="hover:text-emerald-600 transition">Dashboard</a>
                <?php else: ?>
                <a href="<?php echo e(route('login')); ?>" class="hover:text-emerald-600 transition">Sign In</a>
                <?php endif; ?>
            </div>
            <p class="text-xs text-slate-400 font-medium">© <?php echo e(date('Y')); ?> Learning Curve. All rights reserved.</p>
        </div>
    </div>
</footer>


<?php echo $__env->make('partials.ad-overlay', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php if(auth()->guard()->check()): ?>
<?php echo $__env->make('partials.ai-chatbot', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php endif; ?>
<?php echo $__env->make('partials.global-loader', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<script>
// Mobile nav toggle
document.getElementById('mob-btn')?.addEventListener('click', () => {
    document.getElementById('mob-nav').classList.toggle('hidden');
});

// Smooth scroll anchors
document.querySelectorAll('a[href^="#"]').forEach(a => {
    a.addEventListener('click', e => {
        const t = document.querySelector(a.getAttribute('href'));
        if (t) { e.preventDefault(); t.scrollIntoView({ behavior:'smooth', block:'start' }); }
        document.getElementById('mob-nav')?.classList.add('hidden');
    });
});
</script>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\a\lj\learning_system\resources\views/home.blade.php ENDPATH**/ ?>