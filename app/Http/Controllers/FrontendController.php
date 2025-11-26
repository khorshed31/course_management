<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\CourseEnrollment;
use Illuminate\Support\Facades\DB;
use App\Services\SessionCart;

class FrontendController extends Controller
{
    public function index(SessionCart $cart)
    {
        // Get the 3 specific courses by program
        $firstCourse  = Course::where('program', '1')->first();
        $secondCourse = Course::where('program', '2')->first();
        $thirdCourse  = Course::where('program', '3')->first();

        $featuredBook = Book::query()
            ->where('status', 'published')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->latest('published_at')
            ->first();

        // Cart data
        $cartData   = $cart->get();
        $cartItems  = $cartData['items'] ?? [];
        $cartCount  = count($cartItems);
        $cartTotal  = $cart->totalPrice();          // uses your existing method
        $currency   = $cartData['currency'] ?? 'د.ك';

        return view('frontend.pages.index', compact(
            'firstCourse',
            'secondCourse',
            'thirdCourse',
            'featuredBook',
            'cartItems',
            'cartCount',
            'cartTotal',
            'currency'
        ));
    }



    public function courses(Request $request, SessionCart $cart)
    {
        $q     = trim((string) $request->input('q'));
        $sort  = $request->input('sort', 'latest'); // latest|popular|price_asc|price_desc
        $per   = (int) ($request->input('per_page') ?? 12);
        $per   = $per > 0 && $per <= 48 ? $per : 12;

        // Get courses based on search and sort options
        $courses = Course::query()
            ->when($q, fn($qry) =>
                $qry->where(fn($x) =>
                    $x->where('title', 'like', "%{$q}%")
                    ->orWhere('description', 'like', "%{$q}%")
                )
            )
            ->withCount('enrollments') // Count enrollments for popularity sorting
            ->when($sort === 'latest', fn($qry) => $qry->orderByDesc('created_at'))
            ->when($sort === 'popular', fn($qry) => $qry->orderByDesc('enrollments_count'))
            ->when($sort === 'price_asc', fn($qry) => $qry->orderBy('price'))
            ->when($sort === 'price_desc', fn($qry) => $qry->orderByDesc('price'))
            ->paginate($per)
            ->withQueryString();

        // Enrolled map
        $enrolledMap = [];
        if (auth()->check()) {
            $userId = auth()->id();
            $ids = $courses->getCollection()->pluck('id')->all();

            $enrolled = CourseEnrollment::whereIn('course_id', $ids)
                ->where('user_id', $userId)
                ->get(['course_id', 'progress_percent'])
                ->keyBy('course_id');

            $enrolledMap = $enrolled->toArray();
        }

        // Cart data (for cards + sticky bar)
        $cartData  = $cart->get();
        $cartItems = $cartData['items'] ?? [];
        $cartCount = count($cartItems);
        $cartTotal = $cart->totalPrice();               // uses your SessionCart::totalPrice()
        $currency  = $cartData['currency'] ?? 'د.ك';

        // View
        return view('frontend.pages.courses', compact(
            'courses',
            'q',
            'sort',
            'per',
            'enrolledMap',
            'cartItems',
            'cartCount',
            'cartTotal',
            'currency'
        ));
    }

    public function show(Course $course, Request $request, SessionCart $cart)
    {
        // Ensure course is found by slug
        $course = Course::where('slug', $course->slug)->firstOrFail();

        // Check if the user is enrolled in the course
        $enrolled = auth()->check()
            ? DB::table('course_enrollments')
                ->where('user_id', auth()->id())
                ->where('course_id', $course->id)
                ->exists()
            : false;

        // Optional: progress tracking (can be customized further)
        $progress = $enrolled ? null : null;

        // Optional: related courses based on the same status
        $relatedCourses = Course::where('status', $course->status ?? 1)
            ->where('id', '!=', $course->id)
            ->latest()
            ->take(6)
            ->get();

        $cartItems = $cart->get()['items'] ?? [];
        // Return the view with course details and related courses
        return view('frontend.pages.course_details', compact('course', 'enrolled', 'progress', 'relatedCourses', 'cartItems'));
    }


    public function enroll(Course $course, Request $request)
    {
        $user = $request->user();

        // Optional: block enroll if course inactive
        if (!($course->status ?? 1)) {
            return back()->with('error', 'This course is not available for enrollment.');
        }

        // Create if not exists; do not duplicate
        $enrollment = CourseEnrollment::firstOrCreate(
            ['course_id' => $course->id, 'user_id' => $user->id],
            [
                'enrolled_at'      => now(),
                'progress_percent' => 0,
                // 'completed_at'   => null, // optional
            ]
        );

        return redirect()
            ->route('home')
            ->with('success', 'Enrolled successfully!');
    }

}
