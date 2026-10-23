<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Subject;
use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    use HasFactory;

    protected $fillable = [
        'topic_id',
        'type',      // pdf, ppt, docx, zip
        'title',
        'filename', // stored filename on disk
        'description',
        'order',
    ];

    public function topic()
    {
        return $this->belongsTo(Topic::class);
    }

    // New relationship to Subject
    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }
}
?>