<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;
use App\Traits\FileSaver;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;


class CourseController extends Controller
{
    use FileSaver;

    public function index(Request $request)
    {
        // ===== Filters =====
        $q         = trim((string)$request->get('q'));
        $status    = $request->get('status');         // '1'|'0'|null
        $dateFrom  = $request->get('date_from');      // Y-m-d
        $dateTo    = $request->get('date_to');        // Y-m-d
        $pmin      = $request->filled('price_min') ? (float)$request->get('price_min') : null;
        $pmax      = $request->filled('price_max') ? (float)$request->get('price_max') : null;

        $courses = Course::query()
            ->when($q !== '', fn($qr) => $qr->where('title','like',"%{$q}%"))
            ->when($status !== null && $status !== '',
                fn($qr) => $qr->where('status', (int)$status))
            ->when($dateFrom, fn($qr) => $qr->whereDate('created_at','>=',$dateFrom))
            ->when($dateTo,   fn($qr) => $qr->whereDate('created_at','<=',$dateTo))
            ->when($pmin !== null, fn($qr) => $qr->where('price','>=',$pmin))
            ->when($pmax !== null, fn($qr) => $qr->where('price','<=',$pmax))
            ->latest('id')
            ->paginate(12)
            ->appends($request->query());

        return view('panel.pages.courses.index', compact('courses','q','status','dateFrom','dateTo','pmin','pmax'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'       => ['required','string','max:255'],
            'price'       => ['required','numeric','min:0'],
            'description' => ['nullable','string'],
            'status'      => ['required','boolean'],
            'image'       => ['nullable','image','mimes:jpg,jpeg,png,webp','max:2048'],
            'program'     => ['nullable', 'integer'],
        ]);

        $slug = Str::slug($data['title'], '-');

        $slugExists = Course::where('slug', $slug)->first();
        if ($slugExists) {
            $slug = $slug . '-' . Str::random(4); // Append a random string if the slug already exists
        }

        $course = Course::create([
            'title'       => $data['title'],
            'price'       => $data['price'],
            'description' => $data['description'] ?? null,
            'status'      => $data['status'],
            'program'     => $data['program'] ?? null,
            'slug'        => $slug,
        ]);

        if ($request->hasFile('image')) {
            $this->upload_file($request->file('image'), $course, 'image', 'courses');
        }

        return response()->json([
            'message' => 'Course created successfully',
            'course'  => [
                'id'          => $course->id,
                'title'       => $course->title,
                'slug'        => $course->slug,
                'price'       => (float) $course->price,
                'program'     => $course->program,
                'image'       => $course->image ? asset($course->image) : null,
                'status'      => $course->status ? 'Active' : 'Inactive',
                'status_bool' => (bool)$course->status,
                'description' => $course->description,
                'created_at'  => $course->created_at->format('Y-m-d H:i'),
            ],
        ]);
    }

    public function update(Request $request, Course $course)
    {
        $data = $request->validate([
            'title'       => ['required','string','max:255'],
            'price'       => ['required','numeric','min:0'],
            'description' => ['nullable','string'],
            'status'      => ['required','boolean'],
            'image'       => ['nullable','image','mimes:jpg,jpeg,png,webp','max:2048'],
            'program'     => ['nullable', 'integer'],
        ]);

        // Generate the slug
        $slug = Str::slug($data['title'], '-');

        // Ensure unique slug (optional, in case there are duplicate titles)
        if ($course->slug !== $slug) {
            $slugExists = Course::where('slug', $slug)->first();
            if ($slugExists) {
                $slug = $slug . '-' . Str::random(4); // Append a random string if the slug already exists
            }
        }

        $course->update([
            'title'       => $data['title'],
            'price'       => $data['price'],
            'description' => $data['description'] ?? null,
            'status'      => $data['status'],
            'program'     => $data['program'] ?? null,
            'slug'        => $slug,
        ]);

        if ($request->hasFile('image')) {
            $this->upload_file($request->file('image'), $course, 'image', 'courses');
        }

        return response()->json(['message' => 'Course updated successfully']);
    }

    public function destroy(Course $course)
    {
        // Manually remove the current image (since we’re deleting the row)
        if ($course->image) {
            $abs = public_path($course->image);
            if (is_file($abs)) @unlink($abs);
        }

        $course->delete();

        return response()->json(['message' => 'Course deleted successfully']);
    }


}