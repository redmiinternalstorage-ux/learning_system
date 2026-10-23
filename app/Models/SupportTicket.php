<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupportTicket extends Model
{
    protected $fillable = [
        'user_id','subject','message','category',
        'status','priority','admin_replied',
        'user_read','admin_read','resolved_at',
        'assigned_to', 'transferred_by', 'transfer_note',
    ];

    protected $casts = [
        'admin_replied' => 'boolean',
        'user_read'     => 'boolean',
        'admin_read'    => 'boolean',
        'resolved_at'   => 'datetime',
    ];

    public static array $categories = [
        'general'     => ['label'=>'General',      'icon'=>'fa-circle-question',  'color'=>'#64748b'],
        'course'      => ['label'=>'Course Issue',  'icon'=>'fa-book-open',        'color'=>'#7c3aed'],
        'payment'     => ['label'=>'Payment',       'icon'=>'fa-credit-card',      'color'=>'#059669'],
        'technical'   => ['label'=>'Technical Bug', 'icon'=>'fa-bug',              'color'=>'#e11d48'],
        'account'     => ['label'=>'Account',       'icon'=>'fa-user-shield',      'color'=>'#d97706'],
        'assignment'  => ['label'=>'Assignment',    'icon'=>'fa-file-pen',         'color'=>'#4f46e5'],
        'other'       => ['label'=>'Other',         'icon'=>'fa-ellipsis',         'color'=>'#94a3b8'],
    ];

    public static array $statusMeta = [
        'open'        => ['label'=>'Open',        'badge'=>'badge-amber',  'icon'=>'fa-circle-dot'],
        'in_progress' => ['label'=>'In Progress', 'badge'=>'badge-blue',   'icon'=>'fa-spinner'],
        'resolved'    => ['label'=>'Resolved',    'badge'=>'badge-green',  'icon'=>'fa-circle-check'],
        'closed'      => ['label'=>'Closed',      'badge'=>'badge-gray',   'icon'=>'fa-lock'],
    ];

    public static array $priorityMeta = [
        'low'    => ['label'=>'Low',    'color'=>'#94a3b8'],
        'medium' => ['label'=>'Medium', 'color'=>'#f59e0b'],
        'high'   => ['label'=>'High',   'color'=>'#ef4444'],
        'urgent' => ['label'=>'Urgent', 'color'=>'#dc2626'],
    ];

    public function user()          { return $this->belongsTo(User::class); }
    public function assignedTo()    { return $this->belongsTo(User::class, 'assigned_to'); }
    public function transferredBy() { return $this->belongsTo(User::class, 'transferred_by'); }
    public function replies()       { return $this->hasMany(TicketReply::class, 'ticket_id')->orderBy('created_at'); }
    public function lastReply()  { return $this->hasOne(TicketReply::class, 'ticket_id')->latestOfMany(); }

    public function getCategoryMeta(): array
    {
        return self::$categories[$this->category] ?? self::$categories['other'];
    }
    public function getStatusMeta(): array
    {
        return self::$statusMeta[$this->status] ?? self::$statusMeta['open'];
    }
    public function getPriorityMeta(): array
    {
        return self::$priorityMeta[$this->priority] ?? self::$priorityMeta['medium'];
    }
}
