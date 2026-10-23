<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'topic_id',
        'question',
        'options', // stored as JSON
        'correct_option',
        'explanation',
        'order',
    ];

    protected $casts = [
        'options' => 'array',
    ];

    public function topic()
    {
        return $this->belongsTo(Topic::class);
    }

    public function attempts()
    {
        return $this->hasMany(QuestionAttempt::class);
    }
}
?>