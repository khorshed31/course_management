<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Promotion extends Model
{
    protected $guarded = [];
    protected $casts = [
        'start_time' => 'datetime',
        'end_time'   => 'datetime',
        'start_date' => 'date',
        'end_date'   => 'date',
        'status'     => 'boolean',
    ];


    public function get_course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    // Accessor to always get a properly formatted decimal string (e.g., "8.00")
    public function getDiscountValueFormattedAttribute(): string
    {
        return number_format((float)$this->attributes['discount_value'], 2, '.', '');
    }

    // Mutator ensures we store normalized decimal strings
    public function setDiscountValueAttribute($value): void
    {
        $this->attributes['discount_value'] = number_format((float)$value, 2, '.', '');
    }

    // === SCOPES ===
    public function scopeActive(Builder $q): Builder
    {
        return $q->where('status', 1);
    }

    public function scopeApplicableToCourse(Builder $q, $courseId = null): Builder
    {
        // 0 or null => site-wide; otherwise match course
        return $q->where(function ($qq) use ($courseId) {
            $qq->whereNull('course_id')
                ->orWhere('course_id', 0);
            if (!is_null($courseId)) {
                $qq->orWhere('course_id', $courseId);
            }
        });
    }

    public function scopeCurrentlyValid(Builder $q): Builder
    {
        $now = Carbon::now();

        return $q->where(function ($qq) use ($now) {
            // TIMER: start_time <= now <= end_time
            $qq->where('discount_type', 'timer')
                ->whereNotNull('start_time')
                ->whereNotNull('end_time')
                ->where('start_time', '<=', $now)
                ->where('end_time', '>=', $now);
        })->orWhere(function ($qq) use ($now) {
            // SPECIAL DAY: date range inclusive
            $qq->where('discount_type', 'special_day')
                ->whereNotNull('start_date')
                ->whereNotNull('end_date')
                ->whereDate('start_date', '<=', $now->toDateString())
                ->whereDate('end_date', '>=', $now->toDateString());
        })->orWhere(function ($qq) {
            // FIRST SOME STUDENTS (optional availability check happens in service)
            $qq->where('discount_type', 'first_some_student')
                ->whereNotNull('student_limit');
        });
    }

    // Scopes for filters
    public function scopeSearch($q, $term = null)
    {
        if ($term) {
            $q->where(function ($s) use ($term) {
                $s->where('day_title', 'like', "%{$term}%")
                  ->orWhere('discount_type', 'like', "%{$term}%");
            });
        }
        return $q;
    }

    public function scopeType($q, $type = null)
    {
        if ($type !== null && $type !== '') {
            $q->where('discount_type', $type);
        }
        return $q;
    }

    public function scopeStatus($q, $status = null)
    {
        if ($status !== null && $status !== '') {
            $q->where('status', (int)$status);
        }
        return $q;
    }

    // === HELPERS (for rendering) ===
    public function label(): string
    {
        return match ($this->discount_type) {
            'timer'               => 'Limited Time Offer',
            'special_day'         => $this->day_title ?: 'Today Only',
            'first_some_student'  => 'Early Bird',
            default               => 'Offer',
        };
    }

    public function isPercentage(): bool
    {
        return $this->discount_value_type === 'percentage';
    }


    public function isFixedAmount(): bool
    {
        return $this->discount_value_type === 'fixed';
    }
}
