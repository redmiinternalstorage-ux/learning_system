<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Note extends Model
{
    use HasFactory;

    protected $fillable = ['subject_id', 'title', 'file_path'];

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function bookmarks()
    {
        return $this->hasMany(Bookmark::class);
    }
}
