<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Access Portal — Learning Curve</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Outfit:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: { extend: { fontFamily: { sans: ['"Plus Jakarta Sans"'], outfit: ['"Outfit"'] } } }
        }
        document.documentElement.classList.remove('dark');
        localStorage.setItem('color-theme', 'light');
    </script>
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #f8fafc;
            min-height: 100vh;
            overflow-x: hidden;
            color: #0f172a;
        }
        .font-outfit { font-family: 'Outfit', sans-serif; }

        .bg-mesh {
            position: fixed; inset: 0; z-index: 0; pointer-events: none;
            background:
                radial-gradient(ellipse 70% 60% at 15% 15%, rgba(16,185,129,.12) 0%, transparent 60%),
                radial-gradient(ellipse 60% 60% at 85% 85%, rgba(13,148,136,.08) 0%, transparent 60%),
                radial-gradient(ellipse 50% 40% at 50% 50%, rgba(52,211,153,.06) 0%, transparent 60%),
                #f8fafc;
        }

        /* Role cards */
        .role-card {
            position: relative;
            background: #ffffff;
            border: 1.5px solid #e2e8f0;
            border-radius: 1.5rem;
            padding: 1.5rem;
            cursor: pointer;
            transition: all .3s cubic-bezier(.4,0,.2,1);
            text-decoration: none;
            display: block;
            overflow: hidden;
            box-shadow: 0 4px 15px -2px rgba(0,0,0,0.03);
        }
        .role-card::before {
            content: '';
            position: absolute; inset: 0;
            background: var(--card-glow, transparent);
            opacity: 0;
            transition: opacity .3s;
            border-radius: inherit;
        }
        .role-card:hover {
            border-color: var(--card-border, #a7f3d0);
            transform: translateY(-4px);
            box-shadow: var(--card-shadow, 0 16px 32px -8px rgba(5,150,105,0.12));
        }
        .role-card:hover::before { opacity: 1; }

        /* Main Admin card */
        .card-admin {
            --card-border:  #6ee7b7;
            --card-glow:    linear-gradient(135deg, rgba(16,185,129,.05) 0%, transparent 60%);
            --card-shadow:  0 20px 40px -10px rgba(16,185,129,.15);
        }
        /* Staff card */
        .card-staff {
            --card-border:  #a7f3d0;
            --card-glow:    linear-gradient(135deg, rgba(13,148,136,.05) 0%, transparent 60%);
            --card-shadow:  0 20px 40px -10px rgba(13,148,136,.12);
        }
        /* Finance card */
        .card-finance {
            --card-border:  #fde68a;
            --card-glow:    linear-gradient(135deg, rgba(245,158,11,.05) 0%, transparent 60%);
            --card-shadow:  0 20px 40px -10px rgba(245,158,11,.12);
        }
        /* Student card */
        .card-student {
            --card-border:  #bbf7d0;
            --card-glow:    linear-gradient(135deg, rgba(16,185,129,.05) 0%, transparent 60%);
            --card-shadow:  0 20px 40px -10px rgba(16,185,129,.12);
        }

        /* Icon circle */
        .card-icon {
            width: 3.25rem; height: 3.25rem;
            border-radius: 1rem;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
            transition: transform .3s;
        }
        .role-card:hover .card-icon { transform: scale(1.1) rotate(-3deg); }

        /* Badge */
        .role-badge {
            display: inline-flex; align-items: center; gap: .3rem;
            padding: .2rem .6rem; border-radius: 9999px;
            font-size: .6rem; font-weight: 800;
            text-transform: uppercase; letter-spacing: .07em;
        }

        /* Permission pills */
        .perm-pill {
            display: inline-flex; align-items: center; gap: .3rem;
            padding: .2rem .55rem; border-radius: 9999px;
            font-size: .6rem; font-weight: 700;
            background: #f0fdf4;
            border: 1px solid #dcfce7;
            color: #047857;
        }
        .perm-pill i { font-size: .55rem; }

        /* Animations */
        @keyframes fadeUp { from { opacity:0; transform:translateY(18px); } to { opacity:1; transform:translateY(0); } }
        .au  { animation: fadeUp .4s cubic-bezier(.16,1,.3,1) both; }
        .au1 { animation: fadeUp .4s .06s cubic-bezier(.16,1,.3,1) both; }
        .au2 { animation: fadeUp .4s .12s cubic-bezier(.16,1,.3,1) both; }
        .au3 { animation: fadeUp .4s .18s cubic-bezier(.16,1,.3,1) both; }
        .au4 { animation: fadeUp .4s .24s cubic-bezier(.16,1,.3,1) both; }
        .au5 { animation: fadeUp .4s .30s cubic-bezier(.16,1,.3,1) both; }

        /* Arrow */
        .arrow-icon { transition: transform .25s; }
        .role-card:hover .arrow-icon { transform: translateX(4px); color: #059669; }

        /* Divider */
        .sect-divider {
            display: flex; align-items: center; gap: .75rem;
            color: #059669;
            font-size: .6rem; font-weight: 800; text-transform: uppercase; letter-spacing: .12em;
        }
        .sect-divider::before, .sect-divider::after {
            content: ''; flex: 1; height: 1px;
            background: #e2e8f0;
        }

        /* User avatar */
        .user-avatar {
            width: 2.5rem; height: 2.5rem; border-radius: .875rem;
            display: flex; align-items: center; justify-content: center;
            font-weight: 900; font-size: 1rem;
            background: linear-gradient(135deg, #059669, #0d9488);
            color: white;
            flex-shrink: 0;
        }
    </style>
</head>
<body>
    <div class="bg-mesh"></div>

    
    <form id="direct-dept-form" method="POST" action="<?php echo e(route('admin.verify-department')); ?>">
        <?php echo csrf_field(); ?>
        <input type="hidden" name="department" id="direct-dept-input">
    </form>

    <div class="relative z-10 min-h-screen flex items-center justify-center p-4 sm:p-6">
        <div class="w-full max-w-lg">

            <!-- Top bar with user info -->
            <div class="flex items-center justify-between mb-8 au">
                <!-- Logo -->
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl flex items-center justify-center shadow-md shadow-emerald-500/20"
                         style="background: linear-gradient(135deg, #059669, #0d9488);">
                        <i class="fa-solid fa-leaf text-white text-sm"></i>
                    </div>
                    <div>
                        <p class="text-slate-900 font-black text-sm leading-none font-outfit">Learning Curve</p>
                        <p class="text-emerald-700 text-[8px] font-black uppercase tracking-widest mt-0.5">Control Center</p>
                    </div>
                </div>

                <!-- User pill + logout -->
                <div class="flex items-center gap-3">
                    <div class="flex items-center gap-2 px-3 py-1.5 rounded-xl bg-white border border-emerald-100 shadow-sm">
                        <div class="user-avatar text-sm"><?php echo e(strtoupper(substr(Auth::user()->name, 0, 1))); ?></div>
                        <div>
                            <p class="text-slate-900 text-xs font-bold leading-none"><?php echo e(Auth::user()->name); ?></p>
                            <p class="text-emerald-700 text-[9px] font-semibold mt-0.5">
                                <?php if(Auth::user()->isAdmin()): ?> Main Admin
                                <?php elseif(Auth::user()->isStaff()): ?> Staff Member
                                <?php elseif(Auth::user()->isFinance()): ?> Finance Dept
                                <?php else: ?> <?php echo e(ucfirst(Auth::user()->role)); ?>

                                <?php endif; ?>
                            </p>
                        </div>
                    </div>
                    <form method="POST" action="<?php echo e(route('admin.logout')); ?>" class="inline">
                        <?php echo csrf_field(); ?>
                        <button type="submit"
                                class="w-8 h-8 rounded-xl flex items-center justify-center text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition border border-slate-200"
                                title="Logout">
                            <i class="fa-solid fa-right-from-bracket text-xs"></i>
                        </button>
                    </form>
                </div>
            </div>

            <!-- Welcome heading -->
            <div class="mb-7 au1">
                <?php if(session('welcome')): ?>
                <div class="mb-4 p-3.5 rounded-2xl flex items-center gap-3 text-emerald-800 text-xs font-semibold bg-emerald-50 border border-emerald-200">
                    <i class="fa-solid fa-hand-wave flex-shrink-0 text-emerald-600"></i>
                    <?php echo e(session('welcome')); ?>

                </div>
                <?php endif; ?>
                <?php if(session('error')): ?>
                <div class="mb-4 p-3.5 rounded-2xl flex items-center gap-3 text-rose-800 text-xs font-semibold bg-rose-50 border border-rose-200">
                    <i class="fa-solid fa-circle-exclamation flex-shrink-0 text-rose-600"></i>
                    <?php echo e(session('error')); ?>

                </div>
                <?php endif; ?>

                <h1 class="text-slate-900 text-2xl font-black font-outfit tracking-tight">Choose Your Portal</h1>
                <p class="text-slate-500 text-sm mt-1.5">Select the section you want to access. Each portal is tailored to its role.</p>
            </div>

            <!-- ── Cards ── -->
            <div class="space-y-3">

                
                <?php if(Auth::user()->isAdmin()): ?>
                <div onclick="selectDepartment('admin', 'Main Admin Panel', 'Complete system control — users, courses, analytics & configuration.', <?php echo e(Auth::user()->isAdmin() ? 'true' : 'false'); ?>)"
                     class="role-card card-admin au2">
                    <div class="flex items-center gap-4">
                        <div class="card-icon shadow-md shadow-emerald-500/20"
                             style="background: linear-gradient(135deg, #059669, #0d9488);">
                            <i class="fa-solid fa-crown text-white text-lg"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 flex-wrap mb-1">
                                <p class="text-slate-900 font-extrabold text-base leading-none font-outfit">Main Admin Panel</p>
                                <span class="role-badge bg-emerald-100 text-emerald-800 border border-emerald-200">
                                    <i class="fa-solid fa-crown text-[8px]"></i> Full Access
                                </span>
                            </div>
                            <p class="text-slate-500 text-[11px] mb-3">Complete system control — users, courses, analytics, settings & all modules</p>
                            <div class="flex flex-wrap gap-1.5">
                                <span class="perm-pill"><i class="fa-solid fa-users"></i> Users</span>
                                <span class="perm-pill"><i class="fa-solid fa-book-open"></i> Courses</span>
                                <span class="perm-pill"><i class="fa-solid fa-chart-line"></i> Analytics</span>
                                <span class="perm-pill"><i class="fa-solid fa-gear"></i> Settings</span>
                            </div>
                        </div>
                        <i class="fa-solid fa-arrow-right arrow-icon text-slate-400 text-sm flex-shrink-0"></i>
                    </div>
                </div>
                <?php endif; ?>

                
                <?php if(Auth::user()->isAdmin() || Auth::user()->isStaff()): ?>
                <div onclick="selectDepartment('staff', 'Staff Department', 'Clock-in/out, work logs, salary overview, support tickets & live chat.', <?php echo e(Auth::user()->isStaff() || Auth::user()->isAdmin() ? 'true' : 'false'); ?>)"
                     class="role-card card-staff au3">
                    <div class="flex items-center gap-4">
                        <div class="card-icon shadow-md shadow-teal-500/20"
                             style="background: linear-gradient(135deg, #0d9488, #0f766e);">
                            <i class="fa-solid fa-users-gear text-white text-lg"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 flex-wrap mb-1">
                                <p class="text-slate-900 font-extrabold text-base leading-none font-outfit">Staff Department</p>
                                <span class="role-badge bg-teal-50 text-teal-800 border border-teal-200">
                                    <i class="fa-solid fa-user-check text-[8px]"></i> Staff Hub
                                </span>
                            </div>
                            <p class="text-slate-500 text-[11px] mb-3">
                                Work logs, attendance, bank details, chat support transfer & ticket management
                            </p>
                            <div class="flex flex-wrap gap-1.5">
                                <span class="perm-pill"><i class="fa-solid fa-calendar-check"></i> Attendance</span>
                                <span class="perm-pill"><i class="fa-solid fa-building-columns"></i> Bank Details</span>
                                <span class="perm-pill"><i class="fa-solid fa-money-bill"></i> Salary Breakdown</span>
                                <span class="perm-pill"><i class="fa-solid fa-comments"></i> Learning Curve Team Chat</span>
                            </div>
                        </div>
                        <i class="fa-solid fa-arrow-right arrow-icon text-slate-400 text-sm flex-shrink-0"></i>
                    </div>
                </div>
                <?php endif; ?>

                
                <?php if(Auth::user()->isAdmin() || Auth::user()->isFinance()): ?>
                <div onclick="selectDepartment('finance', 'Finance Department', 'Payroll processing, salary payouts, transactions & monetary balance controls.', <?php echo e(Auth::user()->isFinance() || Auth::user()->isAdmin() ? 'true' : 'false'); ?>)"
                     class="role-card card-finance au3">
                    <div class="flex items-center gap-4">
                        <div class="card-icon shadow-md shadow-amber-500/20"
                             style="background: linear-gradient(135deg, #d97706, #059669);">
                            <i class="fa-solid fa-building-columns text-white text-lg"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 flex-wrap mb-1">
                                <p class="text-slate-900 font-extrabold text-base leading-none font-outfit">Finance Department</p>
                                <span class="role-badge bg-amber-50 text-amber-800 border border-amber-200">
                                    <i class="fa-solid fa-shield text-[8px]"></i> Finance Hub
                                </span>
                            </div>
                            <p class="text-slate-500 text-[11px] mb-3">Payroll, salary processing, client payments, transfers & financial audit logs</p>
                            <div class="flex flex-wrap gap-1.5">
                                <span class="perm-pill"><i class="fa-solid fa-money-bill-wave"></i> Payroll</span>
                                <span class="perm-pill"><i class="fa-solid fa-receipt"></i> Transactions</span>
                                <span class="perm-pill"><i class="fa-solid fa-piggy-bank"></i> Reserve Bank</span>
                            </div>
                        </div>
                        <i class="fa-solid fa-arrow-right arrow-icon text-slate-400 text-sm flex-shrink-0"></i>
                    </div>
                </div>
                <?php endif; ?>

            </div>

            <!-- ── Divider ── -->
            <div class="sect-divider my-5 au4">Student View</div>

            <!-- Student view row -->
            <div class="grid grid-cols-1 gap-3 au5">
                <a href="<?php echo e(route('dashboard')); ?>" class="role-card card-student" style="padding: 1rem 1.25rem;">
                    <div class="flex items-center gap-3">
                        <div class="card-icon w-10 h-10 rounded-xl shadow-md shadow-emerald-500/20"
                             style="background: linear-gradient(135deg, #10b981, #059669);">
                            <i class="fa-solid fa-graduation-cap text-white text-sm"></i>
                        </div>
                        <div class="flex-1">
                            <p class="text-slate-900 font-bold text-sm leading-none">Student Portal View</p>
                            <p class="text-slate-500 text-[10px] mt-0.5">Switch to student learning dashboard & courses</p>
                        </div>
                        <i class="fa-solid fa-arrow-right arrow-icon text-slate-400 text-xs"></i>
                    </div>
                </a>
            </div>

            <!-- Footer -->
            <div class="mt-8 flex items-center justify-between au5">
                <div class="flex items-center gap-2 px-3 py-1.5 rounded-full bg-emerald-50 border border-emerald-200">
                    <i class="fa-solid fa-lock text-emerald-600 text-[9px]"></i>
                    <span class="text-emerald-800 text-[9px] font-black uppercase tracking-widest">Multi-Department Auth active</span>
                </div>
                <p class="text-slate-500 text-[10px]">
                    Signed in as <span class="text-slate-700 font-bold"><?php echo e(Auth::user()->email); ?></span>
                </p>
            </div>
        </div>
    </div>

    <!-- ═══════════════════ DEPARTMENT AUTH MODAL ═══════════════════ -->
    <div id="dept-auth-backdrop" class="fixed inset-0 bg-slate-900/60 backdrop-blur-md z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-emerald-100 relative anim-up">
            <button onclick="closeDeptModal()" class="absolute top-5 right-5 text-slate-400 hover:text-slate-600 w-8 h-8 rounded-full flex items-center justify-center bg-slate-100">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>

            <div class="flex items-center gap-3 mb-4">
                <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center shadow-lg shadow-emerald-500/20 text-white text-xl">
                    <i class="fa-solid fa-shield-halved"></i>
                </div>
                <div>
                    <h3 id="modal-dept-title" class="font-extrabold text-slate-900 text-lg font-outfit">Department Verification</h3>
                    <p class="text-emerald-700 text-[10px] font-black uppercase tracking-widest">Security Gate</p>
                </div>
            </div>

            <p id="modal-dept-desc" class="text-slate-500 text-xs mb-5">Please enter your account password to verify and grant access to this department.</p>

            <form method="POST" action="<?php echo e(route('admin.verify-department')); ?>" class="space-y-4">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="department" id="modal-dept-input" value="">

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Enter Password for Department Access</label>
                    <div class="relative">
                        <i class="fa-solid fa-key absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                        <input type="password" name="password" required placeholder="••••••••"
                               class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-300 text-sm text-slate-900 focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 font-medium">
                    </div>
                </div>

                <div class="pt-2 flex items-center justify-end gap-3">
                    <button type="button" onclick="closeDeptModal()" class="px-4 py-2.5 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-100">
                        Cancel
                    </button>
                    <button type="submit" class="px-5 py-2.5 rounded-xl text-xs font-extrabold text-white bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 shadow-md shadow-emerald-500/20">
                        <i class="fa-solid fa-lock-open mr-1.5"></i> Verify & Enter Department
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function selectDepartment(deptKey, title, desc, isPrimary) {
            if (isPrimary) {
                document.getElementById('direct-dept-input').value = deptKey;
                document.getElementById('direct-dept-form').submit();
            } else {
                openDeptModal(deptKey, title, desc);
            }
        }
        function openDeptModal(deptKey, title, desc) {
            document.getElementById('modal-dept-input').value = deptKey;
            document.getElementById('modal-dept-title').textContent = title;
            document.getElementById('modal-dept-desc').textContent = desc;
            document.getElementById('dept-auth-backdrop').classList.remove('hidden');
        }
        function closeDeptModal() {
            document.getElementById('dept-auth-backdrop').classList.add('hidden');
        }
    </script>
<?php echo $__env->make('partials.global-loader', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\a\lj\learning_system\resources\views/admin/panel-select.blade.php ENDPATH**/ ?>