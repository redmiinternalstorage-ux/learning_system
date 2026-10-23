
<?php $__env->startSection('title', 'My Courses'); ?>
<?php $__env->startSection('page-title', 'My Courses'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-5">

    
    <div class="flex items-center justify-between">
        <div>
            <p class="text-slate-400 text-xs font-bold uppercase tracking-wider"><?php echo e($courses->count()); ?> course<?php echo e($courses->count() !== 1 ? 's' : ''); ?> enrolled</p>
        </div>
        <a href="<?php echo e(route('courses.public')); ?>"
           class="flex items-center gap-2 px-4 py-2 rounded-xl bg-gradient-to-r from-indigo-500 to-purple-600 hover:from-indigo-600 hover:to-purple-700 text-white text-xs font-bold shadow-md shadow-indigo-500/20 transition">
            <i class="fa-solid fa-compass"></i> Browse More
        </a>
    </div>

    <?php if($courses->isEmpty()): ?>
    
    <div class="bg-white rounded-3xl border border-slate-100 shadow-sm p-14 text-center">
        <div class="w-20 h-20 rounded-3xl bg-gradient-to-br from-indigo-100 to-purple-100 flex items-center justify-center mx-auto mb-5">
            <i class="fa-solid fa-book-open text-indigo-400 text-3xl"></i>
        </div>
        <p class="font-extrabold text-slate-700 text-base font-outfit">No courses yet</p>
        <p class="text-slate-400 text-sm mt-1 mb-5">Start your learning journey — explore hundreds of courses</p>
        <a href="<?php echo e(route('courses.public')); ?>"
           class="inline-flex items-center gap-2 px-6 py-3 rounded-2xl bg-gradient-to-r from-indigo-500 to-purple-600 text-white text-sm font-bold shadow-md shadow-indigo-500/20 transition hover:from-indigo-600 hover:to-purple-700">
            <i class="fa-solid fa-compass"></i> Explore Courses
        </a>
    </div>
    <?php else: ?>

    
    <?php
    $catMeta = [
        'Web Development'    => ['from-blue-500','to-indigo-600','fa-code'],
        'Data Science'       => ['from-violet-500','to-purple-700','fa-chart-bar'],
        'Design'             => ['from-pink-500','to-rose-600','fa-pen-nib'],
        'AI & ML'            => ['from-purple-500','to-violet-700','fa-robot'],
        'Mobile Development' => ['from-orange-500','to-amber-600','fa-mobile-screen'],
        'Marketing'          => ['from-yellow-500','to-orange-500','fa-bullhorn'],
    ];
    ?>
    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
        <?php $__currentLoopData = $courses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $course): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php
        $meta = $catMeta[$course->category] ?? ['from-indigo-500','to-purple-600','fa-book'];
        ?>
        <?php $isCourseCompleted = in_array($course->id, $completedCourseIds); ?>

        <div class="bg-white rounded-2xl border <?php echo e($isCourseCompleted ? 'border-violet-200 shadow-violet-100' : 'border-slate-100'); ?> shadow-sm hover:shadow-md hover:-translate-y-1 transition-all duration-200 overflow-hidden group flex flex-col">
            
            <a href="<?php echo e(route('courses.show', $course)); ?>" class="block">
            <div class="h-32 relative overflow-hidden">
                <div class="absolute inset-0 bg-gradient-to-br <?php echo e($meta[0]); ?> <?php echo e($meta[1]); ?>"></div>
                <div class="absolute inset-0 opacity-10"
                     style="background-image:radial-gradient(circle at 1px 1px,white 1px,transparent 0);background-size:24px 24px;"></div>
                <div class="absolute inset-0 flex items-center justify-center">
                    <i class="fa-solid <?php echo e($meta[2]); ?> text-white/25 text-5xl"></i>
                </div>
                <div class="absolute top-3 right-3">
                    <?php if($isCourseCompleted): ?>
                    <span class="text-[10px] font-bold bg-amber-400/90 backdrop-blur-sm border border-amber-300/50 text-amber-900 px-2.5 py-1 rounded-lg uppercase tracking-wider flex items-center gap-1">
                        🏆 Completed
                    </span>
                    <?php else: ?>
                    <span class="text-[10px] font-bold bg-white/20 backdrop-blur-sm border border-white/25 text-white px-2.5 py-1 rounded-lg uppercase tracking-wider">
                        <?php echo e($course->level); ?>

                    </span>
                    <?php endif; ?>
                </div>
                <div class="absolute bottom-3 left-4">
                    <div class="w-10 h-10 rounded-xl bg-white/20 backdrop-blur-sm border border-white/25 flex items-center justify-center text-white font-black text-lg font-outfit">
                        <?php echo e(strtoupper(substr($course->title, 0, 1))); ?>

                    </div>
                </div>
            </div>
            </a>

            
            <div class="p-4 flex flex-col flex-1">
                <a href="<?php echo e(route('courses.show', $course)); ?>" class="block">
                <h3 class="font-bold text-slate-800 text-sm leading-snug mb-1 group-hover:text-indigo-600 transition line-clamp-2"><?php echo e($course->title); ?></h3>
                <p class="text-slate-400 text-xs mb-3"><?php echo e($course->instructor_name); ?></p>
                </a>

                <?php $prog = (int)($course->pivot->progress ?? 0); ?>
                <?php if($isCourseCompleted || $prog >= 100): ?>
                
                <div class="flex gap-2 mt-auto pt-3 border-t border-slate-50">
                    <a href="<?php echo e(route('certificates.download', $course)); ?>"
                       class="flex-1 py-2 rounded-xl bg-gradient-to-r from-indigo-500 to-violet-600 text-white text-[11px] font-bold text-center flex items-center justify-center gap-1 shadow-sm hover:from-indigo-600 hover:to-violet-700 transition">
                        <i class="fa-solid fa-download text-[9px]"></i> Certificate
                    </a>
                    <a href="<?php echo e(route('certificates.view', $course)); ?>" target="_blank"
                       class="flex-1 py-2 rounded-xl border border-indigo-200 text-indigo-600 text-[11px] font-bold text-center flex items-center justify-center gap-1 hover:bg-indigo-50 transition">
                        <i class="fa-solid fa-eye text-[9px]"></i> Preview
                    </a>
                </div>
                <?php else: ?>
                <div class="mt-auto space-y-2 pt-3 border-t border-slate-50">
                    <div class="flex items-center justify-between text-[11px] font-semibold">
                        <span class="text-slate-400">Course Progress</span>
                        <span class="text-indigo-600 font-bold"><?php echo e($prog); ?>%</span>
                    </div>
                    <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
                        <div class="bg-gradient-to-r from-indigo-500 to-purple-600 h-full rounded-full transition-all duration-500" style="width: <?php echo e($prog); ?>%"></div>
                    </div>
                    <div class="flex items-center justify-between text-[11px] font-semibold text-slate-400 pt-1">
                        <span><i class="fa-solid fa-book mr-1 text-indigo-400"></i><?php echo e($course->lessons_count ?? 0); ?> lessons</span>
                        <a href="<?php echo e(route('courses.show', $course)); ?>" class="text-indigo-500 font-bold group-hover:translate-x-0.5 transition-transform inline-block">Continue →</a>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\a\lj\learning_system\resources\views/courses/mine.blade.php ENDPATH**/ ?>