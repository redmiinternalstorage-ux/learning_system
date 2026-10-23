<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ad extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'type',
        'media_path',
        'link_url',
        'powered_by',
        'start_date',
        'deadline',
        'duration_days',
        'total_amount',
        'advance_paid',
        'is_active',
    ];

    protected $casts = [
        'start_date' => 'date',
        'deadline' => 'datetime',
        'is_active' => 'boolean',
        'duration_days' => 'integer',
        'total_amount' => 'decimal:2',
        'advance_paid' => 'decimal:2',
    ];

    /**
     * Scope a query to only include active, unexpired ads.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
                     ->where(function ($q) {
                         $q->whereNull('deadline')
                           ->orWhere('deadline', '>', now());
                     });
    }

    /**
     * Get remaining balance / due amount.
     */
    public function getDueAmountAttribute()
    {
        return max(0, (float)$this->total_amount - (float)$this->advance_paid);
    }

    /**
     * Get payment status string.
     */
    public function getPaymentStatusAttribute()
    {
        $due = $this->due_amount;
        if ((float)$this->total_amount <= 0) {
            return 'Free / N/A';
        }
        if ($due <= 0) {
            return 'Fully Paid';
        }
        if ((float)$this->advance_paid > 0) {
            return 'Advance Paid';
        }
        return 'Payment Pending';
    }
}
