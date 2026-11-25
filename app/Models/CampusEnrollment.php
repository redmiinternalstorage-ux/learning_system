<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class CampusEnrollment extends Model
{
    protected $fillable = ['campus_student_id', 'course_id', 'progress', 'completed_at'];

    protected function casts(): array
    {
        return ['completed_at' => 'datetime'];
    }

    public function student()   { return $this->belongsTo(CampusStudent::class, 'campus_student_id'); }
    public function course()    { return $this->belongsTo(Course::class); }
}
