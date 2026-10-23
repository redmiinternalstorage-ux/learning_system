<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StaffWorkLog extends Model
{
    protected $fillable = ['user_id','clock_in','clock_out','duration_seconds','notes','work_date','status','marked_at'];

    protected $casts = [
        'clock_in'  => 'datetime',
        'clock_out' => 'datetime',
        'marked_at' => 'datetime',
        'work_date' => 'date',
    ];

    public function staff() { return $this->belongsTo(User::class, 'user_id'); }

    /** Duration as HH:MM:SS string */
    public function getDurationFormattedAttribute(): string
    {
        $s = $this->duration_seconds;
        return sprintf('%02d:%02d:%02d', intdiv($s,3600), intdiv($s%3600,60), $s%60);
    }

    /** Duration in decimal hours */
    public function getDurationHoursAttribute(): float
    {
        return round($this->duration_seconds / 3600, 2);
    }
}
