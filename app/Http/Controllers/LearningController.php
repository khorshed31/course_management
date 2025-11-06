<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\{Course, CourseLesson, CourseEnrollment, CourseChapter, LessonCompletion};

class LearningController extends Controller
{
    /** Continue: jump to last valid lesson, else first playable */
    public function show(Course $course, Request $request)
    {
        $user = $request->user();

        $enrollment = CourseEnrollment::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->first();

        if (!$enrollment) {
            return redirect()->route('courses.show', $course->id)
                ->with('error', 'Please enroll to start learning this course.');
        }

        // 1) If we have a last_lesson_id that is valid & active → go there
        if ($enrollment->last_lesson_id) {
            $candidate = CourseLesson::where('id', $enrollment->last_lesson_id)
                ->where('course_id', $course->id)
                ->where(function($q){ $q->where('status',1)->orWhereNull('status'); })
                ->first();
            if ($candidate) {
                return redirect()->route('learn.lesson', [$course->id, $candidate->id]);
            }
        }

        // 2) Else the first playable lesson by chapter/lesson sort
        $firstLesson = CourseLesson::query()
            ->leftJoin('course_chapters as ch', 'ch.id', '=', 'course_lessons.chapter_id')
            ->where('course_lessons.course_id', $course->id)
            ->where(function ($q) { $q->where('course_lessons.status', 1)->orWhereNull('course_lessons.status'); })
            ->where(function ($q) { $q->where('ch.status', 1)->orWhereNull('ch.status'); })
            ->orderBy(DB::raw('COALESCE(ch.sort_order, 999999)'))
            ->orderBy(DB::raw('COALESCE(course_lessons.sort_order, 999999)'))
            ->orderBy('course_lessons.id')
            ->select('course_lessons.*')
            ->first();

        // 3) Fallback to any lesson if none marked active yet
        if (!$firstLesson) {
            $firstLesson = CourseLesson::where('course_id', $course->id)
                ->orderBy(DB::raw('COALESCE(sort_order, 999999)'))
                ->orderBy('id')
                ->first();
        }

        if (!$firstLesson) {
            return redirect()->route('home', $course->id)
                ->with('error', 'No lessons available yet.');
        }

        return redirect()->route('learn.lesson', [$course->id, $firstLesson->id]);
    }

    /** Lesson page with curriculum + resume seconds (NO SEQUENCE LOCKS) */
    public function lesson(Course $course, CourseLesson $lesson, Request $request)
    {
        $user = $request->user();

        abort_if((int)$lesson->course_id !== (int)$course->id, 404);

        $enrollment = CourseEnrollment::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->first();

        if (!$enrollment) {
            return redirect()->route('courses.show', $course->id)
                ->with('error', 'Please enroll to access this course.');
        }

        // ----- Ordered lessons (active) for curriculum & Prev/Next; NO gating checks
        $ordered = CourseLesson::query()
            ->leftJoin('course_chapters as ch', 'ch.id', '=', 'course_lessons.chapter_id')
            ->where('course_lessons.course_id', $course->id)
            ->where(function($q){ $q->where('course_lessons.status',1)->orWhereNull('course_lessons.status'); })
            ->where(function($q){ $q->where('ch.status',1)->orWhereNull('ch.status'); })
            ->orderBy(DB::raw('COALESCE(ch.sort_order, 999999)'))
            ->orderBy(DB::raw('COALESCE(course_lessons.sort_order, 999999)'))
            ->orderBy('course_lessons.id')
            ->select('course_lessons.*')
            ->get();

        // If current lesson isn’t in the "active" set (e.g., unpublished), 404
        $idx = $ordered->search(fn($l) => (int)$l->id === (int)$lesson->id);
        if ($idx === false) {
            abort(404);
        }

        // Curriculum (active chapters + lessons)
        $chapters = CourseChapter::with(['lessons' => function ($q) {
                $q->where(function($q2){ $q2->where('status',1)->orWhereNull('status'); })
                  ->orderBy('sort_order');
            }])
            ->where('course_id', $course->id)
            ->where(function($q){ $q->where('status',1)->orWhereNull('status'); })
            ->orderBy('sort_order')
            ->get();

        // Prev/Next from ordered list (no gating)
        $prev = ($idx > 0) ? $ordered[$idx - 1] : null;
        $next = ($idx < $ordered->count() - 1) ? $ordered[$idx + 1] : null;

        // Completed flag for this lesson
        $completed = LessonCompletion::where('user_id', $user->id)
            ->where('lesson_id', $lesson->id)
            ->exists();

        // Resume seconds (from enrollment if the last_lesson_id matches current)
        $previousLastLessonId = (int)($enrollment->last_lesson_id ?? 0);
        $seekSeconds = ($previousLastLessonId === (int)$lesson->id)
            ? (int)($enrollment->last_position_seconds ?? 0)
            : 0;

        // Update enrollment "last lesson" pointer if user opened a different lesson
        if ($previousLastLessonId !== (int)$lesson->id) {
            $enrollment->update(['last_lesson_id' => $lesson->id]);
        }

        // Render view (no orderedIds/unlockMaxIdx needed anymore)
        return view('panel.pages.courses.learn', compact(
            'course',
            'lesson',
            'chapters',
            'enrollment',
            'completed',
            'prev',
            'next',
            'seekSeconds'
        ));
    }

    /** Mark complete and recalc progress */
    public function complete(CourseLesson $lesson, Request $request)
    {
        $user = $request->user();
        $courseId = (int)$lesson->course_id;

        $enrollment = CourseEnrollment::where('user_id', $user->id)
            ->where('course_id', $courseId)->first();

        if (!$enrollment) {
            return back()->with('error', 'Enroll first to track progress.');
        }

        LessonCompletion::firstOrCreate(
            ['lesson_id' => $lesson->id, 'user_id' => $user->id],
            ['completed_at' => now()]
        );

        $total = CourseLesson::where('course_id', $courseId)
            ->where(function($q){ $q->where('status',1)->orWhereNull('status'); })
            ->count();

        $done = LessonCompletion::where('user_id', $user->id)
            ->whereIn('lesson_id', function ($q) use ($courseId) {
                $q->select('id')->from('course_lessons')
                  ->where('course_id', $courseId)
                  ->where(function($q2){ $q2->where('status',1)->orWhereNull('status'); });
            })->count();

        $percent = $total > 0 ? round(($done / $total) * 100) : 0;

        $enrollment->update([
            'progress_percent' => $percent,
            'completed_at'     => $percent >= 100 ? now() : null,
        ]);

        // 🔔 If course finished, flash a special flag for the UI modal
        if ($percent >= 100) {
            return back()
                ->with('success', 'Course completed! 🎉')
                ->with('course_completed', true);
        }

        return back()->with('success', 'Marked as complete.');
    }

    /** Save playback position to enrollment (AJAX) */
    public function saveProgress(Request $request)
    {
        $data = $request->validate([
            'course_id' => 'required|integer|exists:courses,id',
            'lesson_id' => 'required|integer|exists:course_lessons,id',
            'position'  => 'required|numeric|min:0',
            'duration'  => 'nullable|numeric|min:0',
        ]);

        $user = $request->user();

        $enrollment = CourseEnrollment::where('user_id', $user->id)
            ->where('course_id', $data['course_id'])
            ->firstOrFail();

        $pos = max(0, (int)floor($data['position']));

        $enrollment->update([
            'last_lesson_id'        => (int)$data['lesson_id'],
            'last_position_seconds' => $pos,
        ]);

        return response()->json(['ok' => true]);
    }
}
