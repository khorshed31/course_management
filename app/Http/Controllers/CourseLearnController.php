<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\{Course, CourseChapter, CourseLesson, CourseEnrollment, LessonProgress};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CourseLearnController extends Controller
{
    // Enroll current user
    public function enroll(Course $course, Request $request)
    {
        $user = $request->user();
        CourseEnrollment::firstOrCreate(
            ['course_id' => $course->id, 'user_id' => $user->id],
            ['enrolled_at' => now()]
        );
        return redirect()->route('learn.course', $course);
    }

    // Course entry (redirect to first accessible lesson)
    public function index(Course $course, Request $request)
    {
        $user = $request->user();
        $enrolled = CourseEnrollment::where('course_id', $course->id)->where('user_id', $user->id)->exists();

        // Find first accessible lesson (free preview if not enrolled)
        $firstLesson = CourseLesson::where('course_id', $course->id)
            ->orderBy('sort_order')->orderBy('id')
            ->when(!$enrolled, fn($q) => $q->where('is_free_preview', true))
            ->first();

        // If enrolled and no lesson filtered, show first lesson normally
        if (!$firstLesson && $enrolled) {
            $firstLesson = CourseLesson::where('course_id', $course->id)->orderBy('sort_order')->orderBy('id')->first();
        }
        abort_if(!$firstLesson, 404, 'No lessons available');
        return redirect()->route('learn.lesson', [$course->id, $firstLesson->id]);
    }

    // Lesson player
    public function play(Course $course, CourseLesson $lesson, Request $request)
    {
        abort_if($lesson->course_id !== $course->id, 404);

        $user = $request->user();
        $enrolled = CourseEnrollment::where('course_id', $course->id)->where('user_id', $user->id)->exists();

        // Gate: allow if enrolled OR lesson is free preview
        if (!$enrolled && !$lesson->is_free_preview) {
            return redirect()->route('courses.show', $course->id)
                ->with('error', 'Please enroll to access this lesson.');
        }

        $chapters = CourseChapter::with(['lessons' => function ($q) {
            $q->orderBy('sort_order')->orderBy('id');
        }])->where('course_id', $course->id)
            ->orderBy('sort_order')->orderBy('id')->get();

        $progress = LessonProgress::firstOrCreate([
            'course_id' => $course->id,
            'chapter_id' => $lesson->chapter_id,
            'lesson_id' => $lesson->id,
            'user_id' => $user->id,
        ]);

        // Find next/prev lessons (for autoplay / navigation)
        $allLessons = CourseLesson::where('course_id', $course->id)->orderBy('sort_order')->orderBy('id')->get();
        $idx = $allLessons->search(fn($l) => $l->id === $lesson->id);
        $prevLesson = $idx > 0 ? $allLessons[$idx - 1] : null;
        $nextLesson = $idx < $allLessons->count() - 1 ? $allLessons[$idx + 1] : null;

        return view('student.learn.player', compact('course', 'chapters', 'lesson', 'progress', 'prevLesson', 'nextLesson', 'enrolled'));
    }

    // Save playback position (and optional partial progress)
    public function saveProgress(Course $course, CourseLesson $lesson, Request $request)
    {
        $user = $request->user();
        abort_if($lesson->course_id !== $course->id, 404);

        $data = $request->validate([
            'last_position_seconds' => ['nullable', 'integer', 'min:0'],
        ]);

        LessonProgress::updateOrCreate(
            ['course_id' => $course->id, 'chapter_id' => $lesson->chapter_id, 'lesson_id' => $lesson->id, 'user_id' => $user->id],
            ['last_position_seconds' => $data['last_position_seconds'] ?? 0]
        );

        return response()->json(['ok' => true]);
    }

    // Mark lesson complete and update course percent
    public function markComplete(Course $course, CourseLesson $lesson, Request $request)
    {
        $user = $request->user();
        abort_if($lesson->course_id !== $course->id, 404);

        LessonProgress::updateOrCreate(
            ['course_id' => $course->id, 'chapter_id' => $lesson->chapter_id, 'lesson_id' => $lesson->id, 'user_id' => $user->id],
            ['is_completed' => true]
        );

        // Compute course completion %
        $total = CourseLesson::where('course_id', $course->id)->count();
        $done  = LessonProgress::where('course_id', $course->id)->where('user_id', $user->id)->where('is_completed', true)->count();
        $pct = $total ? (int) floor(($done / $total) * 100) : 0;

        $enr = CourseEnrollment::firstOrCreate(['course_id' => $course->id, 'user_id' => $user->id], ['enrolled_at' => now()]);
        $enr->update(['progress_percent' => $pct, 'completed_at' => $pct >= 100 ? now() : null]);

        return response()->json(['ok' => true, 'progress_percent' => $pct]);
    }
}
