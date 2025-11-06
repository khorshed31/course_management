<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseEnrollment extends Model {
    protected $guarded = [];
    protected $casts = ['enrolled_at'=>'datetime','completed_at'=>'datetime'];
    public function course()
    { 
        return $this->belongsTo(Course::class); 
    }
    public function user()
    { 
        return $this->belongsTo(User::class); 
    }

    public function assignedBy() { return $this->belongsTo(\App\Models\User::class, 'assigned_by'); }

}
