<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Course;
use App\Models\User;
use App\Models\CourseEnrollment;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $user = $request->user();
        $q    = trim($request->get('q', ''));

        if (isAdmin($user)) {
            // ===== Admin data =====
            $totalCourses     = Course::count();
            $totalStudents    = User::where('role', 'student')->count();
            $totalEnrollments = CourseEnrollment::count();

            // Simple revenue = sum of course price per enrollment (adjust if you have payments table)
            $revenue = DB::table('course_enrollments as ce')
                ->join('courses as c', 'c.id', '=', 'ce.course_id')
                ->where('c.price', '>', 0)
                ->sum('c.price');

            $latestEnrollments = CourseEnrollment::with([
                    'course:id,title,slug,image,price', // Add slug here
                    'user:id,name,email',
                ])
                ->orderByDesc(DB::raw('COALESCE(enrolled_at, created_at)'))
                ->limit(10)
                ->get();

            // needs Course::enrollments() relation (see below)
            $popularCourses = Course::withCount('enrollments')
                ->orderByDesc('enrollments_count')
                ->limit(6)
                ->get();

            return view('panel.pages.home', compact(
                'totalCourses', 'totalStudents', 'totalEnrollments',
                'revenue', 'latestEnrollments', 'popularCourses'
            ))->with('isAdmin', true);
        }

        // ===== Student data (My Enrolled Courses) =====
        $enrollments = CourseEnrollment::with(['course' => function ($q2) {
                $q2->select('id', 'title', 'slug', 'price', 'image', 'status', 'created_at', 'updated_at');
            }])
            ->where('user_id', $user->id)
            ->when($q !== '', function ($query) use ($q) {
                $query->whereHas('course', function ($c) use ($q) {
                    $c->where('title', 'like', "%{$q}%");
                });
            })
            ->orderByDesc(DB::raw('COALESCE(enrolled_at, created_at)'))
            ->paginate(12)
            ->withQueryString();

        return view('panel.pages.home', compact('enrollments', 'q'))
            ->with('isAdmin', false);
    }
}
