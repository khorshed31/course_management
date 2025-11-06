<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CustomPage extends Model
{
    protected $fillable = [
        'title','slug','icon','is_published','position','content','attachment'
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'attachments'  => 'array',
    ];

    // Auto-slug on creating if missing
    protected static function booted() {
        static::creating(function ($model) {
            if (empty($model->slug)) {
                $base = Str::slug($model->title);
                $slug = $base;
                $i = 2;
                while (static::where('slug', $slug)->exists()) {
                    $slug = $base.'-'.$i++;
                }
                $model->slug = $slug;
            }
        });
    }

    public function url(): string
    {
        return route('pages.show', $this->slug);
    }
}