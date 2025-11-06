<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    protected $fillable = ['title','slug','price','program','image','description','status'];

    // Optional helper
    public function getImageUrlAttribute(): ?string
    {
        return $this->image ? asset($this->image) : null; 
    }

    public function chapters() {
        return $this->hasMany(CourseChapter::class)->where('status', 1)->orderBy('sort_order');
    }
    public function lessons() {
        return $this->hasMany(CourseLesson::class)->where('status', 1)->orderBy('sort_order');
    }

    public function enrollments(){ return $this->hasMany(CourseEnrollment::class); }
    public function enrolledUsers(){ return $this->belongsToMany(User::class, 'course_enrollments'); }

}
