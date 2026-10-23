
<?php $__env->startSection('title', 'Dashboard'); ?>
<?php $__env->startSection('page-title', 'Dashboard'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">

    
    <div class="relative rounded-3xl overflow-hidden p-7 sm:p-9 shadow-md"
         style="background:linear-gradient(135deg,#059669 0%,#047857 50%,#065f46 100%)">
        <div class="absolute inset-0 pointer-events-none">
            <div class="absolute top-0 right-0 w-80 h-80 bg-emerald-400/20 rounded-full blur-3xl -translate-y-1/2 translate-x-1/2"></div>
            <div class="absolute bottom-0 left-0 w-60 h-60 bg-teal-400/15 rounded-full blur-2xl translate-y-1/2 -translate-x-1/4"></div>
            <div class="absolute top-1/2 left-1/2 w-40 h-40 bg-green-300/10 rounded-full blur-2xl -translate-x-1/2 -translate-y-1/2"></div>
        </div>
        <div class="relative z-10 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <p class="text-emerald-200 text-xs font-bold uppercase tracking-widest mb-2 flex items-center gap-1.5"><i class="fa-solid fa-sparkles"></i> Control Center</p>
                <h2 class="text-white text-2xl sm:text-3xl font-black font-outfit leading-tight">
                    Welcome back, <?php echo e(Auth::user()->name); ?>! 🌿
                </h2>
                <p class="text-emerald-100/80 text-sm mt-2 font-medium"><?php echo e(now()->format('l, d F Y')); ?> · Platform overview</p>
            </div>
            <div class="flex gap-3 flex-wrap">
                <a href="<?php echo e(route('admin.courses.create')); ?>"
                   class="flex items-center gap-2 px-4 py-2.5 rounded-2xl bg-white/15 hover:bg-white/25 border border-white/20 text-white text-xs font-bold transition backdrop-blur-sm shadow-sm">
                    <i class="fa-solid fa-plus text-emerald-200"></i> New Course
                </a>
                <a href="<?php echo e(route('admin.users')); ?>"
                   class="flex items-center gap-2 px-4 py-2.5 rounded-2xl bg-emerald-800/40 hover:bg-emerald-800/60 border border-emerald-300/30 text-white text-xs font-bold transition backdrop-blur-sm shadow-sm">
                    <i class="fa-solid fa-users text-emerald-200"></i> Manage Users
                </a>
            </div>
        </div>
    </div>

    
    <div class="glass p-5 rounded-3xl border border-emerald-200/80 bg-gradient-to-r from-emerald-50/70 via-teal-50/50 to-white flex flex-col md:flex-row md:items-center justify-between gap-4 shadow-sm">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-emerald-600 to-teal-700 flex items-center justify-center text-white text-xl shadow-md shadow-emerald-600/20 flex-shrink-0">
                <i class="fa-solid fa-vault"></i>
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <h3 class="font-black text-slate-900 text-sm font-outfit">Data Vault &amp; Exports</h3>
                    <span class="px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 text-[10px] font-bold border border-emerald-200">Background Processing</span>
                </div>
                <p class="text-slate-500 text-xs mt-0.5">Export all user &amp; finance data as PDF — runs in background, tracks history. Auto-send to <strong>akpatel2029@gmail.com</strong>.</p>
            </div>
        </div>
        <div class="flex items-center gap-2.5 flex-wrap">
            <button type="button" onclick="openAdminExportModal()"
               class="flex items-center gap-2 px-4 py-2.5 rounded-2xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold transition shadow-xs">
                <i class="fa-solid fa-vault text-emerald-400"></i> Open Data Vault
            </button>
        </div>
    </div>

    
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <?php
        $primaryStats = [
            ['label'=>'Total Members',   'value'=>$stats['total_users'],    'icon'=>'fa-users',           'from'=>'from-emerald-500','to'=>'to-teal-600',   'link'=>route('admin.users')],
            ['label'=>'Students',        'value'=>$stats['students'],       'icon'=>'fa-graduation-cap',  'from'=>'from-teal-500',   'to'=>'to-emerald-700', 'link'=>route('admin.users').'?role=student'],
            ['label'=>'Instructors',     'value'=>$stats['instructors'],    'icon'=>'fa-chalkboard-user', 'from'=>'from-green-600',  'to'=>'to-teal-700',    'link'=>route('admin.users').'?role=instructor'],
            ['label'=>'New This Month',  'value'=>$stats['new_this_month'], 'icon'=>'fa-user-plus',       'from'=>'from-amber-500', 'to'=>'to-orange-600', 'link'=>route('admin.users')],
        ];
        ?>
        <?php $__currentLoopData = $primaryStats; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <a href="<?php echo e($s['link']); ?>"
           class="glass stat-lift rounded-2xl p-5 flex flex-col gap-4 group cursor-pointer border border-emerald-100">
            <div class="flex items-start justify-between">
                <div class="w-11 h-11 rounded-2xl bg-gradient-to-br <?php echo e($s['from']); ?> <?php echo e($s['to']); ?> flex items-center justify-center text-white text-sm shadow-md">
                    <i class="fa-solid <?php echo e($s['icon']); ?>"></i>
                </div>
                <span class="text-2xl font-black text-slate-900 font-outfit"><?php echo e($s['value']); ?></span>
            </div>
            <div>
                <p class="text-[10px] font-bold uppercase tracking-widest text-slate-500"><?php echo e($s['label']); ?></p>
            </div>
        </a>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <?php
        $secondStats = [
            ['label'=>'Total Courses',      'value'=>$stats['total_courses'],     'sub'=>$stats['published_courses'].' published',  'icon'=>'fa-book-open',         'from'=>'from-emerald-600','to'=>'to-teal-700',   'link'=>route('admin.courses')],
            ['label'=>'Assignments',        'value'=>$stats['total_tasks'],       'sub'=>'across all courses',                      'icon'=>'fa-file-pen',           'from'=>'from-teal-600',   'to'=>'to-cyan-700',   'link'=>route('admin.courses')],
            ['label'=>'Pending Grading',    'value'=>$stats['pending_grading'],   'sub'=>$stats['total_submissions'].' total',       'icon'=>'fa-clock-rotate-left',  'from'=>'from-rose-500',   'to'=>'to-pink-600',   'link'=>route('admin.courses')],
        ];
        ?>
        <?php $__currentLoopData = $secondStats; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <a href="<?php echo e($s['link']); ?>"
           class="glass stat-lift rounded-2xl p-5 flex items-center gap-4 cursor-pointer border border-emerald-100">
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br <?php echo e($s['from']); ?> <?php echo e($s['to']); ?> flex items-center justify-center text-white text-base shadow-md flex-shrink-0">
                <i class="fa-solid <?php echo e($s['icon']); ?>"></i>
            </div>
            <div>
                <p class="text-2xl font-black text-slate-900 font-outfit leading-none"><?php echo e($s['value']); ?></p>
                <p class="text-[10px] font-bold uppercase tracking-widest text-slate-500 mt-1"><?php echo e($s['label']); ?></p>
                <p class="text-[10px] text-emerald-700 font-medium mt-0.5"><?php echo e($s['sub']); ?></p>
            </div>
        </a>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        <?php
        $extraStats = [
            ['label'=>'Total Enrollments', 'value'=>$stats['total_enrollments'], 'icon'=>'fa-graduation-cap', 'from'=>'from-emerald-500','to'=>'to-teal-600',   'link'=>route('admin.enrollments')],
            ['label'=>'Completed Courses', 'value'=>$stats['completed_courses'], 'icon'=>'fa-circle-check',  'from'=>'from-teal-500',   'to'=>'to-green-600',  'link'=>route('admin.enrollments').'?progress=completed'],
            ['label'=>'Mails Sent',        'value'=>$stats['mails_sent'],        'icon'=>'fa-paper-plane',   'from'=>'from-emerald-600','to'=>'to-cyan-600',   'link'=>route('admin.mail-logs')],
            ['label'=>'Activity Today',    'value'=>$stats['activity_today'],    'icon'=>'fa-clock-rotate-left','from'=>'from-amber-500','to'=>'to-orange-600', 'link'=>route('admin.activity-logs')],
        ];
        ?>
        <?php $__currentLoopData = $extraStats; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <a href="<?php echo e($s['link']); ?>" class="glass stat-lift rounded-2xl p-4 flex items-center gap-3 cursor-pointer border border-emerald-100">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br <?php echo e($s['from']); ?> <?php echo e($s['to']); ?> flex items-center justify-center text-white text-sm shadow-sm flex-shrink-0">
                <i class="fa-solid <?php echo e($s['icon']); ?>"></i>
            </div>
            <div>
                <p class="text-xl font-black text-slate-900 font-outfit leading-none"><?php echo e($s['value']); ?></p>
                <p class="text-[9px] font-bold uppercase tracking-widest text-slate-500 mt-1"><?php echo e($s['label']); ?></p>
            </div>
        </a>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    
    <div class="grid lg:grid-cols-2 gap-5">

        
        <div class="glass rounded-3xl overflow-hidden border border-emerald-100">
            <div class="px-6 py-4 border-b border-emerald-100/80 bg-emerald-50/40 flex items-center justify-between">
                <div>
                    <h3 class="font-extrabold text-slate-900 text-sm font-outfit">Recent Registrations</h3>
                    <p class="text-slate-500 text-xs mt-0.5">Latest accounts created</p>
                </div>
                <a href="<?php echo e(route('admin.users')); ?>"
                   class="px-3 py-1.5 rounded-xl bg-emerald-100 text-emerald-800 text-[10px] font-bold hover:bg-emerald-200 transition">
                    View All →
                </a>
            </div>
            <div class="divide-y divide-slate-100">
                <?php $__currentLoopData = $recentUsers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="px-6 py-3.5 flex items-center gap-3 hover:bg-emerald-50/30 transition">
                    <div class="w-9 h-9 rounded-xl bg-gradient-to-br
                        <?php if($user->role==='admin'): ?> from-rose-500 to-pink-600
                        <?php elseif($user->role==='instructor'): ?> from-emerald-600 to-teal-700
                        <?php else: ?> from-emerald-500 to-teal-600 <?php endif; ?>
                        flex items-center justify-center text-white font-bold text-xs font-outfit shadow-sm flex-shrink-0">
                        <?php echo e(strtoupper(substr($user->name, 0, 1))); ?>

                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-bold text-slate-800 text-xs truncate"><?php echo e($user->name); ?></p>
                        <p class="text-slate-500 text-[10px] truncate"><?php echo e($user->email); ?></p>
                    </div>
                    <div class="flex items-center gap-2 flex-shrink-0">
                        <?php if($user->isAdmin()): ?>
                            <span class="badge badge-rose">Admin</span>
                        <?php elseif($user->isInstructor()): ?>
                            <span class="badge badge-emerald">Instructor</span>
                        <?php else: ?>
                            <span class="badge badge-green">Student</span>
                        <?php endif; ?>
                        <a href="<?php echo e(route('admin.users.edit', $user)); ?>" class="text-emerald-700 hover:text-emerald-900 text-[10px] font-bold transition">Edit</a>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>

        
        <div class="glass rounded-3xl overflow-hidden border border-emerald-100">
            <div class="px-6 py-4 border-b border-emerald-100/80 bg-emerald-50/40 flex items-center justify-between">
                <div>
                    <h3 class="font-extrabold text-slate-900 text-sm font-outfit">Recent Submissions</h3>
                    <p class="text-slate-500 text-xs mt-0.5">Latest task submissions</p>
                </div>
                <span class="badge badge-amber"><?php echo e($stats['pending_grading']); ?> pending</span>
            </div>
            <div class="divide-y divide-slate-100">
                <?php $__empty_1 = true; $__currentLoopData = $recentSubmissions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sub): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="px-6 py-3.5 flex items-start gap-3 hover:bg-emerald-50/30 transition">
                    <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center text-white font-bold text-xs font-outfit shadow-sm flex-shrink-0">
                        <?php echo e(strtoupper(substr($sub->user->name ?? '?', 0, 1))); ?>

                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-bold text-slate-800 text-xs truncate"><?php echo e($sub->user->name ?? 'Deleted User'); ?></p>
                        <p class="text-slate-600 text-[10px] truncate"><?php echo e($sub->task->title ?? 'Unknown Task'); ?></p>
                        <p class="text-slate-400 text-[10px]"><?php echo e($sub->task->course->title ?? ''); ?></p>
                    </div>
                    <div class="flex-shrink-0">
                        <?php if($sub->status === 'graded'): ?>
                            <span class="badge badge-green">Graded</span>
                        <?php else: ?>
                            <span class="badge badge-amber">Pending</span>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="px-6 py-10 text-center text-slate-400 text-xs">No submissions yet.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    
    <div class="glass rounded-3xl p-6 border border-emerald-100">
        <h3 class="font-extrabold text-slate-900 text-sm font-outfit mb-4">Quick Actions</h3>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
            <?php
            $actions = [
                ['href'=>route('admin.courses.create'),       'icon'=>'fa-circle-plus',   'label'=>'New Course',    'from'=>'from-emerald-500','to'=>'to-teal-600'],
                ['href'=>route('admin.announcements.create'), 'icon'=>'fa-bullhorn',       'label'=>'Announcement',  'from'=>'from-amber-500',  'to'=>'to-orange-500'],
                ['href'=>route('admin.users'),                'icon'=>'fa-users',          'label'=>'Manage Users',  'from'=>'from-emerald-600','to'=>'to-teal-700'],
                ['href'=>route('admin.ads.index'),            'icon'=>'fa-rectangle-ad',   'label'=>'Ad Manager',    'from'=>'from-teal-500',   'to'=>'to-cyan-600'],
                ['href'=>route('admin.mail-compose'),         'icon'=>'fa-paper-plane',    'label'=>'Send Mail',     'from'=>'from-emerald-500','to'=>'to-green-600'],
                ['href'=>route('admin.analytics'),            'icon'=>'fa-chart-line',     'label'=>'Analytics',     'from'=>'from-teal-600',   'to'=>'to-emerald-700'],
                ['href'=>route('admin.enrollments'),          'icon'=>'fa-graduation-cap', 'label'=>'Enrollments',   'from'=>'from-emerald-600', 'to'=>'to-teal-600'],
                ['href'=>route('admin.settings'),             'icon'=>'fa-gear',           'label'=>'Settings',      'from'=>'from-slate-600',  'to'=>'to-slate-700'],
            ];
            ?>
            <?php $__currentLoopData = $actions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <a href="<?php echo e($a['href']); ?>"
               class="group flex flex-col items-center gap-3 p-5 rounded-2xl border border-emerald-100 hover:border-emerald-300
                      hover:bg-emerald-50/60 transition-all duration-200 hover:shadow-md hover:-translate-y-0.5 text-center">
                <div class="w-11 h-11 rounded-2xl bg-gradient-to-br <?php echo e($a['from']); ?> <?php echo e($a['to']); ?> flex items-center justify-center text-white text-base shadow-sm group-hover:shadow-md transition-all">
                    <i class="fa-solid <?php echo e($a['icon']); ?>"></i>
                </div>
                <span class="text-xs font-bold text-slate-700 group-hover:text-emerald-800 transition-colors"><?php echo e($a['label']); ?></span>
            </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>

    
    <div class="glass rounded-3xl overflow-hidden border border-emerald-100">
        <div class="px-6 py-4 border-b border-emerald-100/80 bg-emerald-50/40 flex items-center justify-between">
            <div>
                <h3 class="font-extrabold text-slate-900 text-sm font-outfit">Recent Activity</h3>
                <p class="text-slate-500 text-xs mt-0.5">Latest admin actions</p>
            </div>
            <a href="<?php echo e(route('admin.activity-logs')); ?>" class="px-3 py-1.5 rounded-xl bg-emerald-100 text-emerald-800 text-[10px] font-bold hover:bg-emerald-200 transition">View All →</a>
        </div>
        <div class="divide-y divide-slate-100">
            <?php $__empty_1 = true; $__currentLoopData = $recentActivity; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <?php $m = $log->getMeta(); ?>
            <div class="px-6 py-3 flex items-center gap-3 hover:bg-emerald-50/30 transition">
                <div class="w-8 h-8 rounded-xl flex items-center justify-center text-white text-xs flex-shrink-0 shadow-sm"
                     style="background:<?php echo e($m['color']); ?>">
                    <i class="fa-solid <?php echo e($m['icon']); ?>"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-bold text-slate-800 truncate"><?php echo e($m['label']); ?><?php if($log->target_label): ?> — <span class="font-normal text-slate-500"><?php echo e($log->target_label); ?></span><?php endif; ?></p>
                    <p class="text-[10px] text-slate-400"><?php echo e($log->user?->name ?? 'System'); ?> · <?php echo e($log->created_at->diffForHumans()); ?></p>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="px-6 py-8 text-center text-slate-400 text-xs">No activity yet.</div>
            <?php endif; ?>
        </div>
    </div>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\a\lj\learning_system\resources\views/admin/dashboard.blade.php ENDPATH**/ ?>