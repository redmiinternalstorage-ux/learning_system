<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class CollegeAdmin extends Authenticatable
{
    use Notifiable;

    protected $guard = 'college';

    protected $fillable = [
        'college_name', 'email', 'password', 'phone', 'address',
        'website', 'logo', 'contact_person', 'domain', 'status', 'notes',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return ['password' => 'hashed'];
    }

    public function students()
    {
        return $this->hasMany(CampusStudent::class, 'college_admin_id');
    }

    public function isActive(): bool { return $this->status === 'active'; }
    public function isPending(): bool { return $this->status === 'pending'; }

    /** Count students by status */
    public function studentsCount(string $status = null): int
    {
        $q = $this->students();
        if ($status) $q->where('status', $status);
        return $q->count();
    }
}
