<?php

namespace App\Services;

use App\Models\Promotion;
use Carbon\Carbon;

class PromotionService
{
    /**
     * Get the active promotion banner (no cache).
     */
    public function currentBannerPromo(?int $courseId = null): ?Promotion
    {
        $query = Promotion::query()
            ->active()
            ->applicableToCourse($courseId)
            ->currentlyValid()
            ->orderByRaw("
                CASE
                    WHEN course_id IS NOT NULL AND course_id <> 0 THEN 0
                    ELSE 1
                END
            ")
            ->orderByRaw("
                CASE discount_type
                    WHEN 'timer' THEN 0
                    WHEN 'special_day' THEN 1
                    WHEN 'first_some_student' THEN 2
                    ELSE 3
                END
            ")
            ->latest();

        $promo = $query->first();

        // ✅ Optional: Check for "first_some_student" limit
        if ($promo && $promo->discount_type === 'first_some_student') {
            if ($this->countEligibleEnrollments($promo) >= (int) $promo->student_limit) {
                return null;
            }
        }

        return $promo;
    }

    /**
     * Example method — you can connect your orders/enrollments later.
     */
    protected function countEligibleEnrollments(Promotion $promo): int
    {
        // For now, always allow
        return 0;
    }

    /**
     * Calculate end time for countdown.
     */
    public function endsAt(?Promotion $promo): ?Carbon
    {
        if (!$promo) return null;

        return match ($promo->discount_type) {
            'timer'       => $promo->end_time,
            'special_day' => $promo->end_date?->endOfDay(),
            default       => null,
        };
    }

    /**
     * Format discount text for display.
     */
    public function saveText(Promotion $promo, string $currencySymbol = '$'): string
    {
        if ($promo->discount_value_type === 'percentage') {
            return 'SAVE ' . rtrim(rtrim($promo->discount_value, '0'), '.') . ' %';
        }

        return 'SAVE ' . number_format((float)$promo->discount_value, 2) . ' ' . $currencySymbol;
    }


    public function bestForCourse(int $courseId): ?Promotion
    {
        // Prefer course-specific > site-wide, then timer > special_day > first_some_student, newest first
        return Promotion::query()
            ->active()
            ->applicableToCourse($courseId)   // allows courseId or 0/NULL
            ->currentlyValid()
            ->orderByRaw("
                CASE
                WHEN course_id = ? THEN 0
                WHEN course_id IS NULL OR course_id = 0 THEN 1
                ELSE 2
                END
            ", [$courseId])
            ->orderByRaw("
                CASE discount_type
                WHEN 'timer' THEN 0
                WHEN 'special_day' THEN 1
                WHEN 'first_some_student' THEN 2
                ELSE 3
                END
            ")
            ->latest()
            ->first();
    }

    /** Calculate the discounted price (keeps >= 0). */
    public function applyDiscount(float $price, Promotion $promo): float
    {
        if ($price <= 0) return 0.0;

        if ($promo->discount_value_type === 'percentage') {
            $off = ($price * ((float)$promo->discount_value)) / 100.0;
            return max(0.0, round($price - $off, 2));
        }

        // fixed amount
        return max(0.0, round($price - (float)$promo->discount_value, 2));
    }

    /** Short “save” text for badges on cards. */
    public function shortSaveText(Promotion $promo, string $currencySymbol = '$'): string
    {
        return $promo->discount_value_type === 'percentage'
            ? (rtrim(rtrim($promo->discount_value, '0'), '.') . '% OFF')
            : ($currencySymbol . number_format((float)$promo->discount_value, 2) . ' OFF');
    }
}
