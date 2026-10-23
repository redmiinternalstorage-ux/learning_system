<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DataExport extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'export_type',
        'title',
        'target_email',
        'status',
        'progress_percent',
        'file_path',
        'file_size',
        'error_message',
        'completed_at',
    ];

    protected $casts = [
        'progress_percent' => 'integer',
        'completed_at'     => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            'completed'   => 'badge-green',
            'in_progress' => 'badge-amber',
            'pending'     => 'badge-blue',
            'failed'      => 'badge-rose',
            default       => 'badge-gray',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'completed'   => 'Done',
            'in_progress' => 'Processing (' . $this->progress_percent . '%)',
            'pending'     => 'Queued',
            'failed'      => 'Failed',
            default       => ucfirst($this->status),
        };
    }
}
