<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $fillable = [
        'user_id', 'action', 'target_type', 'target_id',
        'target_label', 'description', 'ip',
    ];

    // ── Action meta: icon + color ────────────────────────────────────────
    public static array $actionMeta = [
        'user.created'      => ['icon' => 'fa-user-plus',        'color' => '#10b981', 'label' => 'User Registered'],
        'user.blocked'      => ['icon' => 'fa-ban',              'color' => '#e11d48', 'label' => 'User Blocked'],
        'user.unblocked'    => ['icon' => 'fa-circle-check',     'color' => '#0284c7', 'label' => 'User Unblocked'],
        'user.deleted'      => ['icon' => 'fa-user-slash',       'color' => '#dc2626', 'label' => 'User Deleted'],
        'user.updated'      => ['icon' => 'fa-pen',              'color' => '#d97706', 'label' => 'User Updated'],
        'user.data_downloaded' => ['icon' => 'fa-file-arrow-down', 'color' => '#0d9488', 'label' => 'Data Report Downloaded'],
        'user.data_emailed'    => ['icon' => 'fa-envelope-circle-check', 'color' => '#059669', 'label' => 'Data Report Emailed'],
        'course.created'    => ['icon' => 'fa-book-open',        'color' => '#7c3aed', 'label' => 'Course Created'],
        'course.updated'    => ['icon' => 'fa-pen-to-square',    'color' => '#4f46e5', 'label' => 'Course Updated'],
        'course.deleted'    => ['icon' => 'fa-trash-can',        'color' => '#dc2626', 'label' => 'Course Deleted'],
        'course.published'  => ['icon' => 'fa-globe',            'color' => '#059669', 'label' => 'Course Published'],
        'course.unpublished'=> ['icon' => 'fa-eye-slash',        'color' => '#94a3b8', 'label' => 'Course Unpublished'],
        'task.created'      => ['icon' => 'fa-file-pen',         'color' => '#0ea5e9', 'label' => 'Task Created'],
        'task.deleted'      => ['icon' => 'fa-file-xmark',       'color' => '#dc2626', 'label' => 'Task Deleted'],
        'submission.graded' => ['icon' => 'fa-star',             'color' => '#f59e0b', 'label' => 'Submission Graded'],
        'mail.sent'         => ['icon' => 'fa-paper-plane',      'color' => '#8b5cf6', 'label' => 'Mail Sent'],
        'ad.created'        => ['icon' => 'fa-rectangle-ad',     'color' => '#06b6d4', 'label' => 'Ad Created'],
        'ad.deleted'        => ['icon' => 'fa-rectangle-xmark',  'color' => '#dc2626', 'label' => 'Ad Deleted'],
        'settings.updated'  => ['icon' => 'fa-gear',             'color' => '#64748b', 'label' => 'Settings Updated'],
        'announcement.created' => ['icon' => 'fa-bullhorn',      'color' => '#f97316', 'label' => 'Announcement Created'],
    ];

    public function getMeta(): array
    {
        return self::$actionMeta[$this->action]
            ?? ['icon' => 'fa-circle-dot', 'color' => '#64748b', 'label' => ucfirst(str_replace('.', ' ', $this->action))];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
