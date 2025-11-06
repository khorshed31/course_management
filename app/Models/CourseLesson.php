<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseLesson extends Model
{
    protected $fillable = [
        'course_id','chapter_id','title','type',
        'video_provider','video_url','video_file_path','video_mime','video_size',
        'file_path','mime_type','file_size','toils','rounds','notes','others',
        'content_text','duration_seconds','sort_order','is_free_preview','status'
    ];

    public function course()  { return $this->belongsTo(Course::class); }
    public function chapter() { return $this->belongsTo(CourseChapter::class, 'chapter_id'); }

    public function getFileUrlAttribute(): ?string
    {
        return $this->file_path ? asset($this->file_path) : null;
    }

    public function getVideoFileUrlAttribute(): ?string
    {
        return $this->video_file_path ? asset($this->video_file_path) : null;
    }
}
