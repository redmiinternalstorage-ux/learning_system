<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FinanceTransaction extends Model
{
    protected $fillable = [
        'transaction_code',
        'transaction_type',
        'flow_type',
        'from_account_id',
        'to_account_id',
        'sender_name',
        'receiver_name',
        'client_name',
        'client_email',
        'staff_id',
        'salary_payment_id',
        'amount',
        'status',
        'auto_approved',
        'notes',
        'created_by',
    ];

    public function fromAccount()
    {
        return $this->belongsTo(FinanceAccount::class, 'from_account_id');
    }

    public function toAccount()
    {
        return $this->belongsTo(FinanceAccount::class, 'to_account_id');
    }

    public function staff()
    {
        return $this->belongsTo(User::class, 'staff_id');
    }

    public function salaryPayment()
    {
        return $this->belongsTo(SalaryPayment::class, 'salary_payment_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Helper to generate unique transaction code
     */
    public static function generateCode()
    {
        return 'TXN-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -5));
    }
}
