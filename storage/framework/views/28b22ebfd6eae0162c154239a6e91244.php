
<?php $__env->startSection('title', 'Assignments'); ?>
<?php $__env->startSection('page-title', 'Assignments'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-5">

    
    <?php if(!empty($completedCourseIds)): ?>
    <?php $__currentLoopData = $completedCourses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <div class="bg-gradient-to-r from-indigo-50 via-violet-50 to-purple-50 rounded-2xl border border-violet-200 shadow-sm p-4 flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-indigo-500 to-violet-600 flex items-center justify-center text-2xl shadow-md shadow-indigo-500/25 flex-shrink-0">🏆</div>
        <div class="flex-1 min-w-0">
            <p class="font-extrabold text-slate-800 text-sm font-outfit leading-tight">Certificate earned: <span class="text-indigo-600"><?php echo e($cc->title); ?></span></p>
            <p class="text-slate-400 text-xs mt-0.5">All assignments completed. Certificate sent to your email!</p>
        </div>
        <div class="flex gap-2 flex-shrink-0">
            <a href="<?php echo e(route('certificates.download', $cc)); ?>"
               class="flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-gradient-to-r from-indigo-500 to-violet-600 text-white text-xs font-bold shadow-sm transition hover:from-indigo-600 hover:to-violet-700">
                <i class="fa-solid fa-download text-[10px]"></i> Download
            </a>
            <a href="<?php echo e(route('certificates.view', $cc)); ?>" target="_blank"
               class="flex items-center gap-1.5 px-3.5 py-2 rounded-xl border border-indigo-200 text-indigo-600 text-xs font-bold transition hover:bg-indigo-50">
                <i class="fa-solid fa-eye text-[10px]"></i> Preview
            </a>
        </div>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    <?php endif; ?>

    
    <?php if(!$tasks->isEmpty()): ?>
    <?php
    $totalPts  = $tasks->sum('points');
    $earnedPts = collect($submissions)->sum(fn($s) => $s && $s->status==='graded' ? $s->grade : 0);
    $submitted = collect($submissions)->filter(fn($s) => $s !== null)->count();
    ?>
    <div class="grid grid-cols-3 gap-4">
        <div class="bg-white rounded-2xl p-4 border border-slate-100 shadow-sm text-center">
            <p class="text-2xl font-black text-emerald-600 font-outfit"><?php echo e($tasks->count()); ?></p>
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mt-1">Total</p>
        </div>
        <div class="bg-white rounded-2xl p-4 border border-slate-100 shadow-sm text-center">
            <p class="text-2xl font-black text-emerald-600 font-outfit"><?php echo e($submitted); ?></p>
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mt-1">Submitted</p>
        </div>
        <div class="bg-white rounded-2xl p-4 border border-slate-100 shadow-sm text-center">
            <p class="text-2xl font-black text-amber-600 font-outfit"><?php echo e($earnedPts); ?><span class="text-base text-slate-300">/<?php echo e($totalPts); ?></span></p>
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mt-1">Points</p>
        </div>
    </div>
    <?php endif; ?>

    <?php if($tasks->isEmpty()): ?>
    <div class="bg-white rounded-3xl border border-slate-100 shadow-sm p-14 text-center">
        <div class="w-20 h-20 rounded-3xl bg-gradient-to-br from-emerald-100 to-teal-100 flex items-center justify-center mx-auto mb-5">
            <i class="fa-solid fa-file-pen text-emerald-500 text-3xl"></i>
        </div>
        <p class="font-extrabold text-slate-700 text-base font-outfit">No assignments yet</p>
        <p class="text-slate-400 text-sm mt-1 mb-5">Enroll in courses to receive assignments</p>
        <a href="<?php echo e(route('courses.public')); ?>"
           class="inline-flex items-center gap-2 px-6 py-3 rounded-2xl bg-gradient-to-r from-emerald-500 to-teal-600 text-white text-sm font-bold shadow-md transition hover:from-emerald-600 hover:to-teal-700">
            <i class="fa-solid fa-compass"></i> Explore Courses
        </a>
    </div>
    <?php else: ?>

    
    <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100">
            <h3 class="font-extrabold text-slate-800 text-sm font-outfit">All Assignments</h3>
        </div>
        <div class="divide-y divide-slate-50">
            <?php $__currentLoopData = $tasks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $task): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php $sub = $submissions[$task->id] ?? null; ?>
            <a href="<?php echo e(route('tasks.show', $task)); ?>"
               class="flex items-center gap-4 px-6 py-4 hover:bg-slate-50/60 transition group">

                
                <div class="w-11 h-11 rounded-2xl flex items-center justify-center flex-shrink-0 text-sm shadow-sm
                    <?php if($sub && $sub->status==='graded'): ?> bg-gradient-to-br from-emerald-400 to-teal-600 text-white
                    <?php elseif($sub): ?> bg-gradient-to-br from-blue-400 to-indigo-600 text-white
                    <?php else: ?> bg-gradient-to-br from-slate-200 to-slate-300 text-slate-500 <?php endif; ?>">
                    <?php if($sub && $sub->status==='graded'): ?> <i class="fa-solid fa-star"></i>
                    <?php elseif($sub): ?> <i class="fa-solid fa-check"></i>
                    <?php else: ?> <i class="fa-solid fa-file-pen"></i>
                    <?php endif; ?>
                </div>

                <div class="flex-1 min-w-0">
                    <p class="font-bold text-slate-800 text-sm truncate group-hover:text-emerald-600 transition"><?php echo e($task->title); ?></p>
                    <p class="text-slate-400 text-xs mt-0.5 truncate"><?php echo e($task->course->title); ?></p>
                </div>

                <div class="flex items-center gap-3 flex-shrink-0">
                    <?php if($task->due_date): ?>
                    <span class="hidden sm:flex items-center gap-1 text-xs font-semibold text-slate-400">
                        <i class="fa-regular fa-calendar text-[10px]"></i>
                        <?php echo e(\Carbon\Carbon::parse($task->due_date)->format('d M')); ?>

                    </span>
                    <?php endif; ?>

                    <span class="text-xs font-bold px-2.5 py-1 rounded-xl
                        <?php if($sub && $sub->status==='graded'): ?> bg-emerald-50 text-emerald-600
                        <?php elseif($sub): ?> bg-blue-50 text-blue-600
                        <?php else: ?> bg-slate-100 text-slate-500 <?php endif; ?>">
                        <?php if($sub && $sub->status==='graded'): ?>
                            <i class="fa-solid fa-star text-[9px] mr-0.5"></i><?php echo e($sub->grade); ?>/<?php echo e($task->points); ?>

                        <?php elseif($sub): ?> Submitted
                        <?php else: ?> <?php echo e($task->points); ?> pts
                        <?php endif; ?>
                    </span>

                    <i class="fa-solid fa-chevron-right text-slate-300 text-xs group-hover:text-indigo-400 group-hover:translate-x-0.5 transition-all"></i>
                </div>
            </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\a\lj\learning_system\resources\views/tasks/index.blade.php ENDPATH**/ ?>