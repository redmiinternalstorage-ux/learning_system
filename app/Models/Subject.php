<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    use HasFactory;

    protected $fillable = ['semester_id', 'name', 'description', 'order'];

    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }

    public function units()
    {
        return $this->hasMany(Unit::class);
    }
}
?>