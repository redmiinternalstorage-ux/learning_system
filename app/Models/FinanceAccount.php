<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FinanceAccount extends Model
{
    protected $fillable = [
        'name',
        'account_number',
        'type',
        'balance',
        'description',
    ];

    public function outgoingTransactions()
    {
        return $this->hasMany(FinanceTransaction::class, 'from_account_id');
    }

    public function incomingTransactions()
    {
        return $this->hasMany(FinanceTransaction::class, 'to_account_id');
    }
}
