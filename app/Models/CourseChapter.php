<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseChapter extends Model
{
    protected $fillable = ['course_id','title','sort_order','status'];

    public function course() { return $this->belongsTo(Course::class); }

    public function lessons() {
        return $this->hasMany(CourseLesson::class, 'chapter_id')
            ->orderBy('sort_order')->orderBy('id');
    }
}
