<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalaryPayment extends Model
{
    // Fixed hourly rate: ₹1,000 per hour
    const HOURLY_RATE = 1000;

    protected $fillable = [
        'staff_id','paid_by','amount','currency',
        'period_label','period_from','period_to',
        'hours_worked','notes','mail_sent',
        'payment_status','bank_account','bank_name','paid_at','hourly_rate',
    ];

    protected $casts = [
        'period_from'    => 'date',
        'period_to'      => 'date',
        'paid_at'        => 'datetime',
        'mail_sent'      => 'boolean',
        'amount'         => 'decimal:2',
        'hourly_rate'    => 'decimal:2',
    ];

    public function staff()    { return $this->belongsTo(User::class, 'staff_id'); }
    public function paidBy()   { return $this->belongsTo(User::class, 'paid_by'); }

    /** Calculate salary from hours at ₹1,000/hour */
    public static function calculateFromHours(float $hours): float
    {
        return $hours * self::HOURLY_RATE;
    }

    public function getStatusBadgeAttribute(): string
    {
        return match($this->payment_status) {
            'paid'    => 'badge-green',
            'pending' => 'badge-amber',
            'failed'  => 'badge-rose',
            default   => 'badge-gray',
        };
    }
}
