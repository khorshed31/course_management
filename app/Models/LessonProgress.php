<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LessonProgress extends Model {
    protected $table = 'lesson_progress';
    protected $fillable = ['course_id','chapter_id','lesson_id','user_id','is_completed','last_position_seconds'];
    protected $casts = ['is_completed'=>'boolean'];
    public function lesson()
    { 
        return $this->belongsTo(CourseLesson::class, 'lesson_id'); 
    }
}
