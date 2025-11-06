<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseChapter;
use Illuminate\Http\Request;

class CourseChapterController extends Controller
{
    public function index(Course $course, Request $request)
    {
        if ($request->wantsJson()) {
            $chapters = $course->chapters()->withCount('lessons')->get();
            return response()->json([
                'data' => $chapters->map(fn($c) => [
                    'id' => $c->id,
                    'title' => $c->title,
                    'sort_order' => $c->sort_order,
                    'status' => $c->status ? 'Active' : 'Inactive',
                    'status_bool' => (bool) $c->status,
                    'lessons_count' => $c->lessons_count,
                    'created_at' => $c->created_at->format('Y-m-d H:i'),
                ])
            ]);
        }
        // If you render a Blade that includes both chapters & lessons widgets
        return view('panel.pages.courses.structure', compact('course'));
    }

    public function store(Course $course, Request $request)
    {
        $data = $request->validate([
            'title' => ['required','string','max:255'],
            'sort_order' => ['nullable','integer','min:1'],
            'status' => ['required','boolean'],
        ]);

        CourseChapter::create([
            'course_id' => $course->id,
            'title' => $data['title'],
            'sort_order' => $data['sort_order'] ?? 1,
            'status' => $data['status'],
        ]);

        return response()->json(['message' => 'Chapter created']);
    }

    public function update(Course $course, CourseChapter $chapter, Request $request)
    {
        abort_unless($chapter->course_id === $course->id, 404);

        $data = $request->validate([
            'title' => ['required','string','max:255'],
            'sort_order' => ['nullable','integer','min:1'],
            'status' => ['required','boolean'],
        ]);

        $chapter->update([
            'title' => $data['title'],
            'sort_order' => $data['sort_order'] ?? $chapter->sort_order,
            'status' => $data['status'],
        ]);

        return response()->json(['message' => 'Chapter updated']);
    }

    public function destroy(Course $course, CourseChapter $chapter)
    {
        abort_unless($chapter->course_id === $course->id, 404);
        $chapter->delete(); // cascades lessons via FK
        return response()->json(['message' => 'Chapter deleted']);
    }
}
