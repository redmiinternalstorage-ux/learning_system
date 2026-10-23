<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = ['name', 'email', 'password', 'role', 'is_blocked',
                           'auto_change_pass', 'auto_change_pass_date', 'auto_change_pass_value',
                           'created_by', 'position', 'salary', 'salary_currency',
                           'salary_cycle', 'joining_date', 'staff_notes',
                           'bank_account', 'bank_name', 'bank_ifsc', 'account_holder'];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at'      => 'datetime',
            'password'               => 'hashed',
            'is_blocked'             => 'boolean',
            'auto_change_pass'       => 'boolean',
            'auto_change_pass_date'  => 'date',
            'joining_date'           => 'date',
            'salary'                 => 'decimal:2',
        ];
    }

    public function isAdmin(): bool   { return $this->role === 'admin'; }
    public function isStaff(): bool   { return $this->role === 'staff'; }
    public function isFinance(): bool { return $this->role === 'finance'; }

    /** True for admin, staff, finance — can access admin panel */
    public function hasAdminAccess(): bool
    {
        return in_array($this->role, ['admin', 'staff', 'finance']);
    }

    public function isInstructor(): bool { return $this->role === 'instructor'; }
    public function isStudent(): bool    { return $this->role === 'student'; }
    public function isBlocked(): bool    { return $this->is_blocked; }

    public function staffMembers()
    {
        return $this->hasMany(User::class, 'created_by')->where('role', 'staff');
    }
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    public function workLogs()
    {
        return $this->hasMany(\App\Models\StaffWorkLog::class, 'user_id');
    }
    public function salaryPayments()
    {
        return $this->hasMany(\App\Models\SalaryPayment::class, 'staff_id');
    }

    public function courses()
    {
        return $this->belongsToMany(Course::class)->withPivot('progress');
    }

    public function submissions()
    {
        return $this->hasMany(TaskSubmission::class);
    }

    public function datePasswords()
    {
        return $this->hasMany(UserDatePassword::class);
    }

    public function tickets()
    {
        return $this->hasMany(SupportTicket::class);
    }

    /**
     * Check if entered password matches date-configured password for today's day of month
     */
    public function checkDatePassword(string $plainPassword): bool
    {
        $todayDay = (int) now()->format('j'); // 1 to 31
        $dateRule = $this->datePasswords()
            ->where('day_of_month', $todayDay)
            ->where('is_active', true)
            ->first();

        if ($dateRule && \Illuminate\Support\Facades\Hash::check($plainPassword, $dateRule->password)) {
            return true;
        }

        return false;
    }

    /**
     * Recalculate and sync real-time course progress for this student.
     */
    public function updateCourseProgress(Course $course): int
    {
        $taskIds = $course->tasks()->pluck('id');
        $totalTasks = $taskIds->count();

        if ($totalTasks === 0) {
            $progress = 0;
        } else {
            $gradedCount = TaskSubmission::where('user_id', $this->id)
                ->whereIn('task_id', $taskIds)
                ->where('status', 'graded')
                ->count();
            
            $progress = (int) min(100, round(($gradedCount / $totalTasks) * 100));
        }

        if ($this->courses()->where('course_id', $course->id)->exists()) {
            $this->courses()->updateExistingPivot($course->id, ['progress' => $progress]);
        }

        return $progress;
    }
}
