<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LessonCompletion extends Model
{
    protected $fillable = ['lesson_id','user_id','completed_at'];
    protected $casts = ['completed_at'=>'datetime'];

    public function lesson(){ return $this->belongsTo(CourseLesson::class, 'lesson_id'); }
    public function user(){ return $this->belongsTo(User::class); }
}
