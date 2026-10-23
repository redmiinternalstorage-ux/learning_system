<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserDatePassword extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'day_of_month',
        'password',
        'is_active',
    ];

    protected $casts = [
        'day_of_month' => 'integer',
        'is_active'    => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
