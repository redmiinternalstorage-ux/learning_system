<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MailLog extends Model
{
    protected $fillable = [
        'mail_type',
        'recipient_email',
        'recipient_name',
        'user_id',
        'success',
        'error_message',
        'otp_code',
        'otp_verified',
        'otp_verified_at',
        'triggered_by',
    ];

    protected $casts = [
        'success'         => 'boolean',
        'otp_verified'    => 'boolean',
        'otp_verified_at' => 'datetime',
    ];

    // ─── Mail type meta (label, colour, icon) ───────────────────────────
    public static array $typesMeta = [
        'welcome'              => ['label' => 'Welcome',           'color' => 'emerald', 'icon' => 'fa-party-horn',           'bg' => '#10b981'],
        'otp'                  => ['label' => 'OTP Sent',          'color' => 'violet',  'icon' => 'fa-key',                  'bg' => '#7c3aed'],
        'otp_resend'           => ['label' => 'OTP Resend',        'color' => 'indigo',  'icon' => 'fa-rotate-right',         'bg' => '#4f46e5'],
        'password_changed'     => ['label' => 'Password Changed',  'color' => 'amber',   'icon' => 'fa-lock-keyhole',         'bg' => '#d97706'],
        'account_blocked'      => ['label' => 'Account Blocked',   'color' => 'rose',    'icon' => 'fa-ban',                  'bg' => '#e11d48'],
        'account_unblocked'    => ['label' => 'Account Restored',  'color' => 'sky',     'icon' => 'fa-circle-check',         'bg' => '#0284c7'],
        'account_deleted'      => ['label' => 'Account Deleted',   'color' => 'red',     'icon' => 'fa-trash-can',            'bg' => '#dc2626'],
        'auto_password_changed'=> ['label' => 'Auto Pwd Changed',  'color' => 'orange',  'icon' => 'fa-clock-rotate-left',   'bg' => '#ea580c'],
        'admin_direct'         => ['label' => 'Admin Direct Mail', 'color' => 'purple',  'icon' => 'fa-paper-plane',          'bg' => '#7c3aed'],
        'ticket_opened'        => ['label' => 'Ticket Opened',     'color' => 'sky',     'icon' => 'fa-ticket',               'bg' => '#0ea5e9'],
        'ticket_reply'         => ['label' => 'Ticket Reply',      'color' => 'violet',  'icon' => 'fa-reply',                'bg' => '#7c3aed'],
        'salary_paid'          => ['label' => 'Salary Paid',       'color' => 'amber',   'icon' => 'fa-sack-dollar',          'bg' => '#d97706'],
        'course_certificate'   => ['label' => 'Certificate',       'color' => 'violet',  'icon' => 'fa-award',                'bg' => '#7c3aed'],
        'data_report'          => ['label' => 'Data Report',       'color' => 'teal',    'icon' => 'fa-file-invoice',         'bg' => '#0d9488'],
    ];

    public function getMeta(): array
    {
        return self::$typesMeta[$this->mail_type] ?? ['label' => ucfirst($this->mail_type), 'color' => 'gray', 'icon' => 'fa-envelope', 'bg' => '#64748b'];
    }

    // ─── Relationships ───────────────────────────────────────────────────
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // ─── Scopes ──────────────────────────────────────────────────────────
    public function scopeOfType($query, string $type)
    {
        return $query->where('mail_type', $type);
    }

    public function scopeSuccessful($query)
    {
        return $query->where('success', true);
    }

    public function scopeFailed($query)
    {
        return $query->where('success', false);
    }
}
