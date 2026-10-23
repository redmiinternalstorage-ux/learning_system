<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($course->title); ?> - Learning Curve</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Outfit:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="/css/interactions.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                        outfit: ['"Outfit"', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .font-outfit { font-family: 'Outfit', sans-serif; }
        .video-container { position: relative; padding-bottom: 56.25%; height: 0; overflow: hidden; border-radius: 16px; }
        .video-container iframe,
        .video-container video { position: absolute; top: 0; left: 0; width: 100%; height: 100%; border-radius: 16px; }
        .btn-gradient {
            background: linear-gradient(135deg, #059669, #0d9488);
        }
        .btn-gradient:hover {
            background: linear-gradient(135deg, #047857, #0f766e);
        }
    </style>
</head>
<body class="bg-[#f8fffe] text-slate-800">


<nav class="bg-white border-b border-slate-200/80 sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center justify-between h-16">
        <a href="<?php echo e(route('explore')); ?>" class="flex items-center gap-2.5">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-emerald-500 to-teal-600 flex items-center justify-center shadow-md shadow-emerald-500/20">
                <i class="fa-solid fa-graduation-cap text-white text-sm"></i>
            </div>
            <span class="font-extrabold text-gray-900 text-lg font-outfit">Learning Curve</span>
        </a>
        <div class="flex items-center gap-3 text-xs font-bold uppercase tracking-wider">
            <?php if(auth()->guard()->check()): ?>
                <a href="<?php echo e(route('dashboard')); ?>" class="px-4 py-2.5 rounded-xl btn-gradient text-white shadow-xs transition">
                    <i class="fa-solid fa-gauge mr-1.5"></i>Dashboard
                </a>
            <?php else: ?>
                <a href="<?php echo e(route('login')); ?>" class="px-4 py-2.5 rounded-xl border border-slate-200 hover:bg-slate-50 text-slate-700 transition">
                    <i class="fa-solid fa-right-to-bracket mr-1.5"></i>Login
                </a>
                <a href="<?php echo e(route('register')); ?>" class="px-4 py-2.5 rounded-xl btn-gradient text-white shadow-xs transition">
                    <i class="fa-solid fa-user-plus mr-1.5"></i>Register
                </a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<?php if(session('success')): ?>
<div class="max-w-7xl mx-auto px-4 pt-4">
    <div class="p-3.5 rounded-xl bg-emerald-50 border border-emerald-100 text-emerald-800 text-xs font-semibold flex items-center gap-2 shadow-2xs">
        <i class="fa-solid fa-circle-check text-emerald-500"></i> <?php echo e(session('success')); ?>

    </div>
</div>
<?php endif; ?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="grid lg:grid-cols-3 gap-8">

        
        <div class="lg:col-span-2 space-y-6">

            
            <div class="flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-slate-400">
                <a href="<?php echo e(route('courses.public')); ?>" class="hover:text-emerald-600 transition">Courses</a>
                <i class="fa-solid fa-chevron-right text-[10px]"></i>
                <span class="text-slate-600 font-extrabold"><?php echo e($course->title); ?></span>
            </div>

            
            <?php if($enrolled && ($course->video_type === 'link' || $course->video_type === 'upload')): ?>

            <?php
                $videoSrc = null; $embedUrl = null; $isDirect = false; $isUpload = false;
                if ($course->video_type === 'upload' && $course->video_path) {
                    $videoSrc = Storage::url($course->video_path);
                    $isUpload = true;
                } elseif ($course->video_type === 'link' && $course->video_link) {
                    $vl = $course->video_link;
                    if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]{11})/', $vl, $m)) {
                        $embedUrl = 'https://www.youtube.com/embed/'.$m[1].'?rel=0&modestbranding=1&autoplay=1';
                    } elseif (preg_match('/vimeo\.com\/(\d+)/', $vl, $m)) {
                        $embedUrl = 'https://player.vimeo.com/video/'.$m[1].'?autoplay=1';
                    } elseif (preg_match('/\.(mp4|webm|ogg|mov)(\?.*)?$/i', $vl)) {
                        $videoSrc = $vl; $isDirect = true;
                    } else {
                        $videoSrc = $vl;
                    }
                }
                $catColors = ['Web Development'=>'from-blue-600 to-emerald-700','Data Science'=>'from-emerald-500 to-purple-700','Design'=>'from-pink-500 to-rose-600','AI & ML'=>'from-purple-500 to-violet-700','Mobile Development'=>'from-orange-500 to-amber-600','Marketing'=>'from-yellow-500 to-orange-500'];
                $grad = $catColors[$course->category] ?? 'from-emerald-500 to-purple-600';
            ?>

            
            <div class="relative rounded-2xl overflow-hidden cursor-pointer group shadow-lg select-none"
                 style="padding-bottom:56.25%;" onclick="openVideoModal()">
                <div class="absolute inset-0 bg-gradient-to-br <?php echo e($grad); ?>"></div>
                <div class="absolute inset-0 opacity-10" style="background-image:radial-gradient(circle at 1px 1px,white 1px,transparent 0);background-size:28px 28px;"></div>
                <div class="absolute inset-0 bg-slate-950/20 group-hover:bg-slate-950/45 transition-all duration-300"></div>
                <div class="absolute inset-0 flex flex-col items-center justify-center gap-4">
                    <div class="w-20 h-20 rounded-full bg-white/20 backdrop-blur-sm border-2 border-white/50 flex items-center justify-center
                                group-hover:scale-110 group-hover:bg-white/30 transition-all duration-300 shadow-2xl">
                        <i class="fa-solid fa-play text-white text-3xl ml-1"></i>
                    </div>
                    <div class="text-center">
                        <p class="text-white font-extrabold text-lg font-outfit">Watch Course Video</p>
                        <p class="text-white/70 text-xs font-bold uppercase tracking-widest mt-1">Click to play</p>
                    </div>
                </div>
                <div class="absolute bottom-0 left-0 right-0 px-5 py-4 bg-gradient-to-t from-slate-950/80 via-slate-950/30 to-transparent">
                    <span class="text-[10px] bg-white/20 border border-white/20 text-white px-2.5 py-1 rounded-lg font-bold uppercase tracking-wider backdrop-blur-sm"><?php echo e($course->category); ?></span>
                    <p class="text-white font-bold text-sm mt-1.5 truncate"><?php echo e($course->title); ?></p>
                </div>
            </div>

            
            <div id="video-modal"
                 class="fixed inset-0 z-[1000] flex items-center justify-center p-4 hidden"
                 onclick="if(event.target===this) closeVideoModal()">
                <div class="absolute inset-0 bg-slate-950/90 backdrop-blur-md"></div>
                <div class="relative w-full max-w-4xl z-10">
                    
                    <div class="flex items-center justify-between mb-3 px-1">
                        <div class="min-w-0 pr-4">
                            <p class="text-white font-extrabold text-sm font-outfit truncate"><?php echo e($course->title); ?></p>
                            <p class="text-slate-400 text-[10px] font-bold uppercase tracking-widest mt-0.5"><?php echo e($course->instructor_name); ?></p>
                        </div>
                        <button onclick="closeVideoModal()"
                                class="w-9 h-9 rounded-xl bg-white/10 hover:bg-white/20 border border-white/10 text-white flex items-center justify-center transition text-sm flex-shrink-0">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>
                    
                    <div class="relative rounded-2xl overflow-hidden bg-black shadow-2xl" style="padding-bottom:56.25%;">
                        <?php if($isUpload || $isDirect): ?>
                            <video id="modal-video" controls controlslist="nodownload"
                                   class="absolute inset-0 w-full h-full">
                                <source src="<?php echo e($videoSrc); ?>" type="video/mp4">
                            </video>
                        <?php elseif($embedUrl): ?>
                            <iframe id="modal-iframe"
                                    class="absolute inset-0 w-full h-full"
                                    src="" data-src="<?php echo e($embedUrl); ?>"
                                    frameborder="0"
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                    allowfullscreen></iframe>
                        <?php else: ?>
                            <div class="absolute inset-0 flex flex-col items-center justify-center gap-4 bg-slate-900">
                                <i class="fa-solid fa-circle-play text-white/20 text-6xl"></i>
                                <a href="<?php echo e($videoSrc ?? $course->video_link); ?>" target="_blank"
                                   class="px-6 py-3 rounded-xl btn-gradient text-white font-bold text-sm shadow-lg flex items-center gap-2">
                                    <i class="fa-solid fa-up-right-from-square"></i> Open Video in New Tab
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                    <p class="text-center text-slate-500 text-[10px] font-bold uppercase tracking-widest mt-3">
                        Press <kbd class="px-1.5 py-0.5 rounded bg-white/10 text-white text-[9px]">ESC</kbd> to close
                    </p>
                </div>
            </div>

            
            <style>
                @keyframes modalSlideUp { from{transform:translateY(30px);opacity:0} to{transform:translateY(0);opacity:1} }
                #video-modal > div:last-child { animation: modalSlideUp 0.3s cubic-bezier(0.16,1,0.3,1) forwards; }
            </style>
            <script>
                function openVideoModal() {
                    document.getElementById('video-modal').classList.remove('hidden');
                    document.body.style.overflow = 'hidden';
                    // lazy-load iframe src
                    const iframe = document.getElementById('modal-iframe');
                    if (iframe && !iframe.src) iframe.src = iframe.dataset.src;
                    // autoplay html5 video
                    const vid = document.getElementById('modal-video');
                    if (vid) { vid.currentTime = 0; vid.play().catch(()=>{}); }
                }
                function closeVideoModal() {
                    document.getElementById('video-modal').classList.add('hidden');
                    document.body.style.overflow = '';
                    // pause/reset
                    const iframe = document.getElementById('modal-iframe');
                    if (iframe) { const s = iframe.src; iframe.src = ''; iframe.src = s; }
                    const vid = document.getElementById('modal-video');
                    if (vid) { vid.pause(); }
                }
                document.addEventListener('keydown', e => { if(e.key==='Escape') closeVideoModal(); });
            </script>

            <?php elseif(!$enrolled && ($course->video_type === 'link' || $course->video_type === 'upload')): ?>
            
            <div class="rounded-2xl overflow-hidden relative shadow-md" style="padding-bottom:56.25%;">
                <?php
                $catColors = ['Web Development'=>'from-blue-650 to-emerald-700','Data Science'=>'from-emerald-500 to-purple-750','Design'=>'from-pink-500 to-rose-600','AI & ML'=>'from-purple-500 to-violet-600','Mobile Development'=>'from-orange-500 to-amber-600','Marketing'=>'from-yellow-500 to-orange-500'];
                $grad = $catColors[$course->category] ?? 'from-emerald-500 to-purple-600';
                ?>
                <div class="absolute inset-0 bg-gradient-to-br <?php echo e($grad); ?>"></div>
                <div class="absolute inset-0 flex flex-col items-center justify-center text-white gap-3 bg-slate-950/20 backdrop-blur-xs">
                    <div class="w-16 h-16 rounded-full bg-white/20 flex items-center justify-center backdrop-blur-sm border border-white/20 shadow-md">
                        <i class="fa-solid fa-lock text-3xl"></i>
                    </div>
                    <p class="font-extrabold text-xl font-outfit tracking-tight">Enroll to watch</p>
                    <p class="text-white/80 text-xs font-bold uppercase tracking-wide">This course includes premium video content</p>
                </div>
            </div>

            <?php else: ?>
            
            <?php
            $catColors = ['Web Development'=>'from-blue-650 to-emerald-700','Data Science'=>'from-emerald-500 to-purple-750','Design'=>'from-pink-500 to-rose-600','AI & ML'=>'from-purple-500 to-violet-600','Mobile Development'=>'from-orange-500 to-amber-600','Marketing'=>'from-yellow-500 to-orange-500'];
            $grad = $catColors[$course->category] ?? 'from-emerald-500 to-purple-600';
            ?>
            <div class="rounded-2xl overflow-hidden h-52 relative shadow-xs">
                <div class="absolute inset-0 bg-gradient-to-br <?php echo e($grad); ?>"></div>
                <div class="absolute inset-0 flex items-center justify-center bg-slate-950/10">
                    <i class="fa-solid fa-graduation-cap text-white/30 text-7xl"></i>
                </div>
                <div class="absolute bottom-0 left-0 right-0 p-5 bg-gradient-to-t from-slate-950/70 via-slate-950/30 to-transparent">
                    <span class="text-[10px] bg-white/20 border border-white/20 backdrop-blur-xs text-white px-2.5 py-1 rounded-lg font-bold uppercase tracking-wider"><?php echo e($course->category); ?></span>
                    <h1 class="text-white text-2xl font-black mt-2 font-outfit tracking-tight"><?php echo e($course->title); ?></h1>
                </div>
            </div>
            <?php endif; ?>

            
            <div class="flex flex-wrap gap-4 text-xs font-bold uppercase tracking-wider text-slate-455">
                <span><i class="fa-solid fa-user-tie mr-1.5 text-emerald-550"></i><?php echo e($course->instructor_name); ?></span>
                <span><i class="fa-solid fa-signal mr-1.5 text-emerald-555"></i><?php echo e($course->level); ?></span>
                <span><i class="fa-solid fa-clock mr-1.5 text-emerald-550"></i><?php echo e($course->duration_hours); ?> hours</span>
                <span><i class="fa-solid fa-book mr-1.5 text-emerald-550"></i><?php echo e($course->lessons_count); ?> lessons</span>
            </div>

            
            <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-2xs">
                <h2 class="font-extrabold text-slate-900 mb-3 font-outfit text-base">About this course</h2>
                <p class="text-slate-500 text-[13px] leading-relaxed"><?php echo e($course->description); ?></p>
            </div>

            
            <?php if($enrolled && $tasks->count()): ?>
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-2xs overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 flex items-center gap-2">
                    <i class="fa-solid fa-file-pen text-emerald-600"></i>
                    <h2 class="font-extrabold text-slate-900 font-outfit text-base">Assignments</h2>
                </div>
                <div class="divide-y divide-slate-100">
                    <?php $__currentLoopData = $tasks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $task): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <a href="<?php echo e(route('tasks.show', $task)); ?>" class="flex items-center gap-4 px-6 py-4 hover:bg-slate-50 transition-all duration-200">
                        <div class="w-10 h-10 rounded-xl bg-emerald-50 border border-emerald-100 flex items-center justify-center flex-shrink-0">
                            <i class="fa-solid fa-file-signature text-emerald-600"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-bold text-slate-800 text-sm truncate"><?php echo e($task->title); ?></p>
                            <?php if($task->due_date): ?>
                            <p class="text-slate-400 text-[10px] font-bold uppercase tracking-wider mt-0.5">Due: <?php echo e(\Carbon\Carbon::parse($task->due_date)->format('d M Y')); ?></p>
                            <?php endif; ?>
                        </div>
                        <span class="text-[10px] bg-emerald-50 text-emerald-700 border border-emerald-100 px-2.5 py-1 rounded-full font-bold uppercase tracking-wider flex-shrink-0">
                            <?php echo e($task->points); ?> pts
                        </span>
                    </a>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
            <?php endif; ?>

        </div>

        
        <div>
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 sticky top-24">
                <div class="text-center mb-5">
                    <p class="text-4xl font-black font-outfit <?php echo e($course->price == 0 ? 'text-emerald-500' : 'text-slate-900'); ?>">
                        <?php echo e($course->price_display); ?>

                    </p>
                    <?php if($course->price == 0): ?>
                    <p class="text-emerald-500 text-xs font-bold uppercase tracking-wider mt-1.5">Free forever</p>
                    <?php endif; ?>
                </div>

                <?php if(auth()->guard()->check()): ?>
                    <?php if($enrolled): ?>
                        <div class="w-full py-3.5 rounded-xl bg-emerald-50 border border-emerald-100 text-emerald-800 font-bold text-xs uppercase tracking-wider text-center mb-3 flex items-center justify-center gap-2">
                            <i class="fa-solid fa-circle-check text-emerald-500 text-sm"></i> You are enrolled
                        </div>
                        <a href="<?php echo e(route('tasks.index')); ?>"
                           class="w-full py-3 rounded-xl border border-emerald-200 text-emerald-650 font-bold text-xs uppercase tracking-wider text-center hover:bg-emerald-50/50 transition-all duration-200 flex items-center justify-center gap-2 mb-3 shadow-3xs">
                            <i class="fa-solid fa-file-pen"></i> View Assignments
                        </a>
                        <form method="POST" action="<?php echo e(route('courses.unenroll', $course)); ?>">
                            <?php echo csrf_field(); ?>
                            <button type="submit" onclick="return confirm('Unenroll from this course?')"
                                    class="w-full py-2 rounded-xl text-rose-500 hover:text-rose-600 text-[10px] font-bold uppercase tracking-wider hover:bg-rose-50 transition-all duration-200">
                                Unenroll
                            </button>
                        </form>
                    <?php else: ?>
                        <form method="POST" action="<?php echo e(route('courses.enroll', $course)); ?>">
                            <?php echo csrf_field(); ?>
                            <button type="submit"
                                    class="w-full py-3.5 rounded-xl btn-gradient text-white font-bold text-xs uppercase tracking-wider transition-all duration-200 shadow-sm flex items-center justify-center gap-2">
                                <i class="fa-solid fa-bolt"></i> Enroll Now — It's <?php echo e($course->price == 0 ? 'Free' : $course->price_display); ?>

                            </button>
                        </form>
                    <?php endif; ?>
                <?php else: ?>
                    <a href="<?php echo e(route('register')); ?>"
                       class="w-full py-3.5 rounded-xl btn-gradient text-white font-bold text-xs uppercase tracking-wider transition-all duration-200 shadow-sm flex items-center justify-center gap-2">
                        <i class="fa-solid fa-bolt"></i> Enroll for Free
                    </a>
                    <p class="text-center text-slate-400 text-xs font-semibold mt-4">
                        Already have an account?
                        <a href="<?php echo e(route('login')); ?>" class="text-emerald-650 font-extrabold hover:underline ml-1">Log in</a>
                    </p>
                <?php endif; ?>

                <div class="mt-5 space-y-3 border-t border-slate-100 pt-5">
                    <?php if($course->video_type === 'link' || $course->video_type === 'upload'): ?>
                    <div class="flex items-center gap-2 text-xs font-semibold text-slate-500">
                        <i class="fa-solid fa-video text-emerald-500 w-4 text-center"></i>
                        <?php if($course->video_type === 'upload'): ?> Includes video lesson
                        <?php else: ?> Video via link included
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                    <div class="flex items-center gap-2 text-xs font-semibold text-slate-500">
                        <i class="fa-solid fa-infinity text-emerald-500 w-4 text-center"></i> Lifetime access
                    </div>
                    <div class="flex items-center gap-2 text-xs font-semibold text-slate-500">
                        <i class="fa-solid fa-mobile-screen text-emerald-500 w-4 text-center"></i> Access on all devices
                    </div>
                    <div class="flex items-center gap-2 text-xs font-semibold text-slate-500">
                        <i class="fa-solid fa-award text-emerald-500 w-4 text-center"></i> Certificate on completion
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<footer class="bg-white text-slate-400 py-10 mt-20 border-t border-emerald-100">
    <div class="max-w-7xl mx-auto px-4 text-center text-xs font-medium uppercase tracking-wider">
        © <?php echo e(date('Y')); ?> Learning Curve. All rights reserved.
    </div>
</footer>
<?php echo $__env->make('partials.ad-overlay', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php if(auth()->guard()->check()): ?>
<?php echo $__env->make('partials.ai-chatbot', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php endif; ?>
<?php echo $__env->make('partials.global-loader', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\a\lj\learning_system\resources\views/courses/show.blade.php ENDPATH**/ ?>