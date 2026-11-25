<?php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class CampusStudent extends Authenticatable
{
    use Notifiable;

    protected $guard = 'campus';

    protected $fillable = [
        'name', 'college_email', 'password', 'college_name', 'student_id',
        'department', 'semester', 'phone', 'status', 'rejection_reason',
        'approved_at', 'approved_by', 'otp', 'otp_expires_at', 'email_verified',
    ];

    protected $hidden = ['password', 'remember_token', 'otp'];

    protected function casts(): array
    {
        return [
            'password'       => 'hashed',
            'email_verified' => 'boolean',
            'approved_at'    => 'datetime',
            'otp_expires_at' => 'datetime',
        ];
    }

    public function enrollments()
    {
        return $this->hasMany(CampusEnrollment::class);
    }

    public function courses()
    {
        return $this->belongsToMany(Course::class, 'campus_enrollments', 'campus_student_id', 'course_id')
                    ->withPivot('progress', 'completed_at')
                    ->withTimestamps();
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function isApproved(): bool  { return $this->status === 'approved'; }
    public function isPending(): bool   { return $this->status === 'pending'; }
    public function isRejected(): bool  { return $this->status === 'rejected'; }

    public function getCollegeDomain(): string
    {
        return strtolower(substr(strrchr($this->college_email, '@'), 1));
    }
}
