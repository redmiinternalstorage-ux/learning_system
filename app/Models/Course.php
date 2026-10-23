<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'description', 'short_description',
        'instructor_name', 'category', 'level', 'language',
        'duration_hours', 'price', 'thumbnail',
        'is_published', 'lessons_count',
        'video_type', 'video_link', 'video_path',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'price'        => 'decimal:2',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class)->withTimestamps();
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function getPriceDisplayAttribute(): string
    {
        return $this->price > 0 ? '₹' . number_format($this->price, 0) : 'Free';
    }
}
