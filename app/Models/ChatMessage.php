<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatMessage extends Model
{
    protected $fillable = [
        'session_id','user_id','admin_id',
        'message','is_admin','is_read','chat_status',
        'assigned_to', 'transferred_by', 'transfer_note',
    ];
    protected $casts = ['is_admin'=>'boolean','is_read'=>'boolean'];

    public function user()          { return $this->belongsTo(User::class, 'user_id'); }
    public function admin()         { return $this->belongsTo(User::class, 'admin_id'); }
    public function assignedTo()    { return $this->belongsTo(User::class, 'assigned_to'); }
    public function transferredBy() { return $this->belongsTo(User::class, 'transferred_by'); }
}
