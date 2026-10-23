<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\AdminTaskController;
use App\Http\Controllers\StudentTaskController;
use App\Http\Controllers\AdminAdController;

// Home: marketing landing page for everyone. Logged-in users also see it (with Dashboard button in nav).
Route::get('/', [CourseController::class, 'home'])->name('home');
Route::get('/login-page', [AuthController::class, 'landing'])->name('login.page'); // optional direct login
Route::get('/explore', [CourseController::class, 'home'])->name('explore');
Route::get('/courses', [CourseController::class, 'publicCourses'])->name('courses.public');
Route::get('/courses/{course}', [CourseController::class, 'show'])->name('courses.show');
Route::get('/developers', [CourseController::class, 'developers'])->name('developers');

// ── Admin Authentication (separate from student login) ────────────────────────
Route::get('/admin/login',  [\App\Http\Controllers\AdminAuthController::class, 'showLogin'])->name('admin.login');
Route::post('/admin/login', [\App\Http\Controllers\AdminAuthController::class, 'login'])->name('admin.login.post');
Route::post('/admin/logout', [\App\Http\Controllers\AdminAuthController::class, 'logout'])
    ->middleware('auth')
    ->name('admin.logout');

// Guest only
Route::middleware('guest')->group(function () {
    Route::get('/login',     [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login',    [AuthController::class, 'login']);
    Route::get('/register',  [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);

    // Forgot Password / OTP
    Route::get('/forgot-password',       [App\Http\Controllers\ForgotPasswordController::class, 'showEmailForm'])->name('password.forgot');
    Route::post('/forgot-password',      [App\Http\Controllers\ForgotPasswordController::class, 'sendOtp'])->name('password.send-otp');
    Route::get('/verify-otp',            [App\Http\Controllers\ForgotPasswordController::class, 'showOtpForm'])->name('password.otp.form');
    Route::post('/verify-otp',           [App\Http\Controllers\ForgotPasswordController::class, 'verifyOtp'])->name('password.otp.verify');
    Route::post('/resend-otp',           [App\Http\Controllers\ForgotPasswordController::class, 'resendOtp'])->name('password.otp.resend');
    Route::get('/reset-password',        [App\Http\Controllers\ForgotPasswordController::class, 'showResetForm'])->name('password.reset.form');
    Route::post('/reset-password',       [App\Http\Controllers\ForgotPasswordController::class, 'resetPassword'])->name('password.reset');
});

// Authenticated
Route::middleware('auth')->group(function () {
    Route::get('/dashboard',                          [AuthController::class, 'dashboard'])->name('dashboard');
    Route::post('/logout',                            [AuthController::class, 'logout'])->name('logout');
    Route::post('/courses/{course}/enroll',           [CourseController::class, 'enroll'])->name('courses.enroll');
    Route::post('/courses/{course}/unenroll',         [CourseController::class, 'unenroll'])->name('courses.unenroll');
    Route::get('/my-courses',                         [CourseController::class, 'myCourses'])->name('courses.mine');
    Route::get('/tasks',                              [StudentTaskController::class, 'index'])->name('tasks.index');
    Route::get('/tasks/{task}',                       [StudentTaskController::class, 'show'])->name('tasks.show');
    Route::post('/tasks/{task}/submit',               [StudentTaskController::class, 'submit'])->name('tasks.submit');
    Route::get('/profile',                            [App\Http\Controllers\ProfileController::class, 'show'])->name('profile');
    Route::get('/settings',                           [App\Http\Controllers\ProfileController::class, 'show'])->name('settings');
    Route::post('/profile',                           [App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/date-password',             [App\Http\Controllers\ProfileController::class, 'addDatePassword'])->name('profile.date-password.add');
    Route::post('/profile/date-password/{datePassword}/toggle', [App\Http\Controllers\ProfileController::class, 'toggleDatePassword'])->name('profile.date-password.toggle');
    Route::delete('/profile/date-password/{datePassword}',      [App\Http\Controllers\ProfileController::class, 'deleteDatePassword'])->name('profile.date-password.delete');

    // User Data Report & Email Export (legacy direct routes kept for compatibility)
    Route::get('/profile/download-data', [\App\Http\Controllers\UserDataExportController::class, 'downloadData'])->name('profile.data.download');
    Route::post('/profile/email-data',   [\App\Http\Controllers\UserDataExportController::class, 'emailData'])->name('profile.data.email');

    // Background Export Tracker (user + admin both use this)
    Route::post('/data-export/start',            [\App\Http\Controllers\DataExportTrackerController::class, 'startExport'])->name('data.export.start');
    Route::get('/data-export/history',           [\App\Http\Controllers\DataExportTrackerController::class, 'history'])->name('data.export.history');
    Route::get('/data-export/{dataExport}/file', [\App\Http\Controllers\DataExportTrackerController::class, 'downloadFile'])->name('data.export.file.download');

    // Help Center (student)
    Route::get('/help',                     [\App\Http\Controllers\HelpCenterController::class, 'index'])->name('help.index');
    Route::post('/help/tickets',            [\App\Http\Controllers\HelpCenterController::class, 'store'])->name('help.store');
    Route::get('/help/tickets/{ticket}',    [\App\Http\Controllers\HelpCenterController::class, 'show'])->name('help.ticket');
    Route::post('/help/tickets/{ticket}/reply', [\App\Http\Controllers\HelpCenterController::class, 'reply'])->name('help.reply');

    // Progress Tracker
    Route::get('/progress', [\App\Http\Controllers\ProgressController::class, 'index'])->name('progress.index');

    // Certificates & Demo Certificates
    Route::get('/certificates/{course}/view',     [\App\Http\Controllers\CertificateController::class, 'view'])->name('certificates.view');
    Route::get('/certificates/{course}/download', [\App\Http\Controllers\CertificateController::class, 'download'])->name('certificates.download');
    Route::get('/demo-certificates',              [\App\Http\Controllers\CertificateController::class, 'demoIndex'])->name('certificates.demo');
    Route::get('/demo-certificates/{type}/view',     [\App\Http\Controllers\CertificateController::class, 'demoView'])->name('certificates.demo.view');
    Route::get('/demo-certificates/{type}/download', [\App\Http\Controllers\CertificateController::class, 'demoDownload'])->name('certificates.demo.download');

    // AI Chatbot
    Route::post('/ai-chat', [\App\Http\Controllers\AiChatController::class, 'ask'])->name('ai.chat');

    // Team Internal WhatsApp Chat (Staff & Admin)
    Route::get('/team-chat',              [\App\Http\Controllers\StaffChatController::class, 'index'])->name('team-chat.index');
    Route::post('/team-chat/{user}/send', [\App\Http\Controllers\StaffChatController::class, 'send'])->name('team-chat.send');
    Route::get('/team-chat/{user}/poll',  [\App\Http\Controllers\StaffChatController::class, 'poll'])->name('team-chat.poll');

    // Live Chat (student)
    Route::get('/chat',                     [\App\Http\Controllers\ChatController::class, 'student'])->name('chat.student');
    Route::post('/chat/send',               [\App\Http\Controllers\ChatController::class, 'studentSend'])->name('chat.student.send');
    Route::get('/chat/poll',                [\App\Http\Controllers\ChatController::class, 'studentPoll'])->name('chat.student.poll');
    Route::post('/chat/close',              [\App\Http\Controllers\ChatController::class, 'studentClose'])->name('chat.student.close');
});

// ── Admin Panel Selection & Department Auth Gate ────────────────────────────
Route::middleware('auth')->prefix('admin')->group(function () {
    Route::get('/select-panel',     [\App\Http\Controllers\StaffController::class, 'panelSelect'])->name('admin.panel-select');
    Route::post('/verify-department', [\App\Http\Controllers\AdminAuthController::class, 'verifyDepartment'])->name('admin.verify-department');
    Route::get('/switch-department', [\App\Http\Controllers\AdminAuthController::class, 'switchDepartment'])->name('admin.switch-department');
});

// Admin Department (requires active 'admin' department session)
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/',                                              [AdminController::class, 'dashboard'])->name('dashboard');
    Route::post('/change-password',                              [AdminController::class, 'changePassword'])->name('change-password');
    Route::get('/users',                                         [AdminController::class, 'users'])->name('users');
    Route::post('/users/bulk',                                   [AdminController::class, 'bulkUsers'])->name('users.bulk');
    Route::get('/users/{user}/detail',                           [AdminController::class, 'getUserDetail'])->name('users.detail');
    Route::get('/users/{user}/edit',                             [AdminController::class, 'editUser'])->name('users.edit');
    Route::put('/users/{user}',                                  [AdminController::class, 'updateUser'])->name('users.update');
    Route::delete('/users/{user}',                               [AdminController::class, 'deleteUser'])->name('users.delete');
    Route::post('/users/{user}/toggle-block',                    [AdminController::class, 'toggleBlock'])->name('users.toggle-block');
    Route::get('/courses',                                       [AdminController::class, 'courses'])->name('courses');
    Route::post('/courses/bulk',                                 [AdminController::class, 'bulkCourses'])->name('courses.bulk');
    Route::get('/courses/create',                                [AdminController::class, 'createCourse'])->name('courses.create');
    Route::post('/courses',                                      [AdminController::class, 'storeCourse'])->name('courses.store');
    Route::get('/courses/{course}/edit',                         [AdminController::class, 'editCourse'])->name('courses.edit');
    Route::put('/courses/{course}',                              [AdminController::class, 'updateCourse'])->name('courses.update');
    Route::delete('/courses/{course}',                           [AdminController::class, 'deleteCourse'])->name('courses.delete');
    Route::post('/courses/{course}/toggle-publish',              [AdminController::class, 'togglePublish'])->name('courses.toggle');
    Route::get('/courses/{course}/tasks',                        [AdminTaskController::class, 'index'])->name('courses.tasks.index');
    Route::get('/courses/{course}/tasks/create',                 [AdminTaskController::class, 'create'])->name('courses.tasks.create');
    Route::post('/courses/{course}/tasks',                       [AdminTaskController::class, 'store'])->name('courses.tasks.store');
    Route::get('/tasks/{task}/edit',                             [AdminTaskController::class, 'edit'])->name('tasks.edit');
    Route::put('/tasks/{task}',                                  [AdminTaskController::class, 'update'])->name('tasks.update');
    Route::delete('/tasks/{task}',                               [AdminTaskController::class, 'destroy'])->name('tasks.delete');
    Route::get('/tasks/{task}/submissions',                      [AdminTaskController::class, 'submissions'])->name('tasks.submissions');
    Route::post('/submissions/{submission}/grade',               [AdminTaskController::class, 'grade'])->name('submissions.grade');
    // Announcements
    Route::get('/announcements',                                 [App\Http\Controllers\AnnouncementController::class, 'index'])->name('announcements');
    Route::get('/announcements/create',                          [App\Http\Controllers\AnnouncementController::class, 'create'])->name('announcements.create');
    Route::post('/announcements',                                [App\Http\Controllers\AnnouncementController::class, 'store'])->name('announcements.store');
    Route::get('/announcements/{a}/edit',                        [App\Http\Controllers\AnnouncementController::class, 'edit'])->name('announcements.edit');
    Route::put('/announcements/{a}',                             [App\Http\Controllers\AnnouncementController::class, 'update'])->name('announcements.update');
    Route::delete('/announcements/{a}',                          [App\Http\Controllers\AnnouncementController::class, 'destroy'])->name('announcements.destroy');
    Route::post('/announcements/{a}/toggle',                     [App\Http\Controllers\AnnouncementController::class, 'toggle'])->name('announcements.toggle');

    // Ads Management
    Route::resource('ads', AdminAdController::class);
    Route::post('/ads/{ad}/toggle',                              [AdminAdController::class, 'toggle'])->name('ads.toggle');

    // Mail Logs & Direct Mail
    Route::get('/mail-logs',     [\App\Http\Controllers\AdminMailController::class, 'index'])->name('mail-logs');
    Route::get('/mail-compose',  [\App\Http\Controllers\AdminMailController::class, 'compose'])->name('mail-compose');
    Route::post('/mail-compose', [\App\Http\Controllers\AdminMailController::class, 'send'])->name('mail-send');

    // Enrollments
    Route::get('/enrollments',               [\App\Http\Controllers\AdminEnrollmentController::class, 'index'])->name('enrollments');
    Route::post('/enrollments',              [\App\Http\Controllers\AdminEnrollmentController::class, 'enroll'])->name('enrollments.store');
    Route::delete('/enrollments/unenroll',   [\App\Http\Controllers\AdminEnrollmentController::class, 'unenroll'])->name('enrollments.unenroll');

    // Certificates Management (Admin)
    Route::get('/certificates',                                      [\App\Http\Controllers\AdminCertificateController::class, 'index'])->name('certificates');
    Route::get('/certificates/{user}/{course}/view',                 [\App\Http\Controllers\AdminCertificateController::class, 'view'])->name('certificates.view');
    Route::get('/certificates/{user}/{course}/download',             [\App\Http\Controllers\AdminCertificateController::class, 'download'])->name('certificates.download');
    Route::post('/certificates/{user}/{course}/resend-email',        [\App\Http\Controllers\AdminCertificateController::class, 'resendEmail'])->name('certificates.resend-email');

    // Analytics
    Route::get('/analytics', [\App\Http\Controllers\AdminAnalyticsController::class, 'index'])->name('analytics');

    // Activity Log
    Route::get('/activity-logs',   [\App\Http\Controllers\AdminActivityController::class, 'index'])->name('activity-logs');
    Route::delete('/activity-logs/clear', [\App\Http\Controllers\AdminActivityController::class, 'clear'])->name('activity-logs.clear');

    // Site Settings
    Route::get('/settings',  [\App\Http\Controllers\AdminSettingsController::class, 'index'])->name('settings');
    Route::post('/settings', [\App\Http\Controllers\AdminSettingsController::class, 'update'])->name('settings.update');
    Route::post('/settings/toggle-maintenance', [\App\Http\Controllers\AdminSettingsController::class, 'toggleMaintenance'])->name('settings.toggle-maintenance');

    // Master System & Finance Sheet Export (Send to akpatel2029@gmail.com & Direct Download)
    Route::get('/export-master',  [\App\Http\Controllers\AdminMasterExportController::class, 'downloadMasterData'])->name('export.master.download');
    Route::post('/email-master',   [\App\Http\Controllers\AdminMasterExportController::class, 'emailMasterData'])->name('export.master.email');

    // Support Tickets (admin)
    Route::get('/support',                                  [\App\Http\Controllers\AdminSupportController::class, 'index'])->name('support.index');
    Route::get('/support/{ticket}',                         [\App\Http\Controllers\AdminSupportController::class, 'show'])->name('support.show');
    Route::post('/support/{ticket}/reply',                  [\App\Http\Controllers\AdminSupportController::class, 'reply'])->name('support.reply');
    Route::post('/support/{ticket}/status',                 [\App\Http\Controllers\AdminSupportController::class, 'updateStatus'])->name('support.status');
    Route::post('/support/{ticket}/transfer',               [\App\Http\Controllers\StaffTicketController::class, 'transfer'])->name('support.transfer');
    Route::delete('/support/{ticket}',                      [\App\Http\Controllers\AdminSupportController::class, 'destroy'])->name('support.destroy');

    // Live Chat (admin)
    Route::get('/chat',                                     [\App\Http\Controllers\ChatController::class, 'adminIndex'])->name('chat.index');
    Route::get('/chat/{sessionId}',                         [\App\Http\Controllers\ChatController::class, 'adminShow'])->name('chat.show');
    Route::post('/chat/{sessionId}/send',                   [\App\Http\Controllers\ChatController::class, 'adminSend'])->name('chat.send');
    Route::post('/chat/{sessionId}/transfer',               [\App\Http\Controllers\ChatController::class, 'transferChat'])->name('chat.transfer');
    Route::get('/chat/{sessionId}/poll',                    [\App\Http\Controllers\ChatController::class, 'adminPoll'])->name('chat.poll');
    Route::post('/chat/{sessionId}/close',                  [\App\Http\Controllers\ChatController::class, 'adminClose'])->name('chat.close');

    // Attendance Sheet (admin)
    Route::get('/attendance',        [\App\Http\Controllers\StaffWorkLogController::class, 'adminAttendance'])->name('attendance.index');
    Route::post('/attendance/toggle', [\App\Http\Controllers\StaffWorkLogController::class, 'toggleAttendance'])->name('attendance.toggle');

    // Staff Management (Main Admin only)
    Route::middleware('main_admin')->group(function () {
        Route::get('/staff',                       [\App\Http\Controllers\StaffController::class, 'index'])->name('staff.index');
        Route::get('/staff/create',                [\App\Http\Controllers\StaffController::class, 'create'])->name('staff.create');
        Route::post('/staff',                      [\App\Http\Controllers\StaffController::class, 'store'])->name('staff.store');
        Route::get('/staff/{staff}',               [\App\Http\Controllers\StaffController::class, 'show'])->name('staff.show');
        Route::get('/staff/{staff}/edit',          [\App\Http\Controllers\StaffController::class, 'edit'])->name('staff.edit');
        Route::put('/staff/{staff}',               [\App\Http\Controllers\StaffController::class, 'update'])->name('staff.update');
        Route::post('/staff/{staff}/toggle-block', [\App\Http\Controllers\StaffController::class, 'toggleBlock'])->name('staff.toggle-block');
        Route::delete('/staff/{staff}',            [\App\Http\Controllers\StaffController::class, 'destroy'])->name('staff.destroy');
    });
});

// ── Staff Portal (staff role only) ───────────────────────────────────────────
Route::middleware(['auth','staff'])->prefix('staff-portal')->name('staff.')->group(function () {
    Route::get('/',                     [\App\Http\Controllers\StaffWorkLogController::class, 'dashboard'])->name('dashboard');
    Route::post('/attendance/mark-present', [\App\Http\Controllers\StaffWorkLogController::class, 'markPresent'])->name('attendance.mark-present');
    Route::post('/clock-in',            [\App\Http\Controllers\StaffWorkLogController::class, 'clockIn'])->name('clock-in');
    Route::post('/clock-out',           [\App\Http\Controllers\StaffWorkLogController::class, 'clockOut'])->name('clock-out');
    Route::get('/status',               [\App\Http\Controllers\StaffWorkLogController::class, 'status'])->name('status');
    Route::get('/history',              [\App\Http\Controllers\StaffWorkLogController::class, 'history'])->name('history');
    Route::post('/bank-details',        [\App\Http\Controllers\StaffWorkLogController::class, 'updateBankDetails'])->name('bank-details.update');

    // Staff Live Chat
    Route::get('/chat',                [\App\Http\Controllers\ChatController::class, 'adminIndex'])->name('chat.index');
    Route::get('/chat/{sessionId}',    [\App\Http\Controllers\ChatController::class, 'adminShow'])->name('chat.show');
    Route::post('/chat/{sessionId}/send',     [\App\Http\Controllers\ChatController::class, 'adminSend'])->name('chat.send');
    Route::post('/chat/{sessionId}/transfer', [\App\Http\Controllers\ChatController::class, 'transferChat'])->name('chat.transfer');
    Route::get('/chat/{sessionId}/poll',      [\App\Http\Controllers\ChatController::class, 'adminPoll'])->name('chat.poll');
    Route::post('/chat/{sessionId}/close',     [\App\Http\Controllers\ChatController::class, 'adminClose'])->name('chat.close');

    // Staff Tickets
    Route::get('/tickets',                 [\App\Http\Controllers\StaffTicketController::class, 'index'])->name('tickets.index');
    Route::get('/tickets/{ticket}',        [\App\Http\Controllers\StaffTicketController::class, 'show'])->name('tickets.show');
    Route::post('/tickets/{ticket}/reply',    [\App\Http\Controllers\StaffTicketController::class, 'reply'])->name('tickets.reply');
    Route::post('/tickets/{ticket}/status',   [\App\Http\Controllers\StaffTicketController::class, 'updateStatus'])->name('tickets.status');
    Route::post('/tickets/{ticket}/transfer', [\App\Http\Controllers\StaffTicketController::class, 'transfer'])->name('tickets.transfer');
});

// ── Finance Department ────────────────────────────────────────────────────────
Route::middleware(['auth', 'finance'])->prefix('finance')->name('finance.')->group(function () {
    Route::get('/',                                     [\App\Http\Controllers\FinanceController::class, 'dashboard'])->name('dashboard');
    Route::get('/staff/{staff}',                        [\App\Http\Controllers\FinanceController::class, 'staffDetail'])->name('staff');
    Route::post('/staff/{staff}/pay',                   [\App\Http\Controllers\FinanceController::class, 'pay'])->name('pay');
    Route::post('/payments/{payment}/mark-paid',        [\App\Http\Controllers\FinanceController::class, 'markPaid'])->name('payments.mark-paid');
    Route::post('/transfer',                            [\App\Http\Controllers\FinanceController::class, 'transfer'])->name('transfer');
    Route::post('/client-payment',                      [\App\Http\Controllers\FinanceController::class, 'clientPayment'])->name('client-payment');
    Route::post('/transactions/{transaction}/status',   [\App\Http\Controllers\FinanceController::class, 'updateStatus'])->name('transactions.status');
    Route::post('/transactions/{transaction}/mark',     [\App\Http\Controllers\FinanceController::class, 'markTransaction'])->name('transactions.mark');
    Route::post('/auto-approve-all',                    [\App\Http\Controllers\FinanceController::class, 'autoApproveAll'])->name('auto-approve-all');
    Route::post('/verify-pin',                          [\App\Http\Controllers\FinanceController::class, 'verifyPin'])->name('verify-pin');
    Route::post('/verify-action-pin',                   [\App\Http\Controllers\FinanceController::class, 'verifyActionPin'])->name('verify-action-pin');
    Route::get('/history',                              [\App\Http\Controllers\FinanceController::class, 'history'])->name('history');
    Route::post('/export-sheet',                        [\App\Http\Controllers\FinanceController::class, 'exportSheet'])->name('export-sheet');
});
