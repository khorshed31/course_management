<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\EnrollmentAssignRequest;
use App\Http\Requests\EnrollmentBulkAssignRequest;
use App\Models\User;
use App\Models\Course;
use App\Models\CourseEnrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\StreamedResponse;

class StudentController extends Controller
{
    /** Admin page with filters + pagination */
    public function create(Request $request)
    {
        $filters = [
            'course_id'   => $request->filled('course_id') ? (int) $request->input('course_id') : null,
            'status'      => $request->input('status', 'all'), // all|in-progress|completed
            'assigned_by' => $request->input('assigned_by', 'any'), // any|me|none|{adminId}
            'from'        => $request->filled('from') ? Carbon::parse($request->input('from')) : null,
            'to'          => $request->filled('to')   ? Carbon::parse($request->input('to'))   : null,
            'q'           => trim((string) $request->input('q')),
        ];

        $courses = Course::orderBy('title')->get(['id','title']);
        $students = User::where('role','student')->orderBy('name')->get(['id','name','email']);
        $admins = User::where('role','admin')->orderBy('name')->get(['id','name']);

        $query = CourseEnrollment::query()
            ->with(['course:id,title', 'user:id,name,email', 'assignedBy:id,name']);

        if ($filters['course_id']) $query->where('course_id', $filters['course_id']);

        if ($filters['status'] === 'completed') {
            $query->whereNotNull('completed_at');
        } elseif ($filters['status'] === 'in-progress') {
            $query->whereNull('completed_at');
        }

        if ($filters['assigned_by'] !== 'any') {
            if ($filters['assigned_by'] === 'me' && auth()->check()) {
                $query->where('assigned_by', auth()->id());
            } elseif ($filters['assigned_by'] === 'none') {
                $query->whereNull('assigned_by');
            } elseif (ctype_digit((string)$filters['assigned_by'])) {
                $query->where('assigned_by', (int) $filters['assigned_by']);
            }
        }

        if ($filters['from']) $query->where('enrolled_at', '>=', $filters['from']->startOfDay());
        if ($filters['to'])   $query->where('enrolled_at', '<=', $filters['to']->endOfDay());

        if ($filters['q']) {
            $q = $filters['q'];
            $query->where(function($w) use ($q) {
                $w->whereHas('course', fn($qq) => $qq->where('title','like',"%{$q}%"))
                  ->orWhereHas('user',   fn($qq) => $qq->where('name','like',"%{$q}%")->orWhere('email','like',"%{$q}%"))
                  ->orWhereHas('assignedBy', fn($qq) => $qq->where('name','like',"%{$q}%"));
            });
        }

        $enrollments = $query->latest('id')->paginate(15)->withQueryString();

        return view('panel.pages.students.assign', compact('courses','students','admins','enrollments','filters'));
    }

    /** Assign one student (idempotent) */
    public function store(EnrollmentAssignRequest $request)
    {
        $data = $request->validated();

        CourseEnrollment::firstOrCreate(
            ['course_id' => $data['course_id'], 'user_id' => $data['user_id']],
            [
                'enrolled_at'           => Carbon::now(),
                'progress_percent'      => 0,
                'last_position_seconds' => 0,
                'last_lesson_id'        => null,
                'completed_at'          => null,
                'assigned_by'           => auth()->id(),
            ]
        );

        return back()->with('success', 'Student assigned to course.');
    }

    /** Bulk assign with transaction; silently skips existing pairs */
    public function bulkStore(EnrollmentBulkAssignRequest $request)
    {
        $data = $request->validated();
        $now = Carbon::now();
        $adminId = auth()->id();

        DB::transaction(function () use ($data, $now, $adminId) {
            foreach ($data['user_ids'] as $uid) {
                CourseEnrollment::firstOrCreate(
                    ['course_id' => $data['course_id'], 'user_id' => $uid],
                    [
                        'enrolled_at'           => $now,
                        'progress_percent'      => 0,
                        'last_position_seconds' => 0,
                        'last_lesson_id'        => null,
                        'completed_at'          => null,
                        'assigned_by'           => $adminId,
                    ]
                );
            }
        });

        return back()->with('success', 'Bulk assignment completed.');
    }

    public function destroy(CourseEnrollment $enrollment)
    {
        $enrollment->delete();
        return back()->with('success', 'Enrollment removed.');
    }

    /** AJAX: student search (id, name, email) */
    public function ajaxStudentSearch(Request $request)
    {
        $q = trim((string) $request->get('q', ''));
        $rows = User::where('role','student')
            ->when($q, function($qr) use ($q){
                $qr->where(function($w) use ($q){
                    $w->where('name','like',"%{$q}%")
                      ->orWhere('email','like',"%{$q}%");
                });
            })
            ->orderBy('name')
            ->limit(20)
            ->get(['id','name','email'])
            ->map(fn($u)=>['id'=>$u->id,'text'=> "{$u->name} — {$u->email}"]);

        return response()->json(['results'=>$rows]);
    }

    /** AJAX: course search */
    public function ajaxCourseSearch(Request $request)
    {
        $q = trim((string) $request->get('q', ''));
        $rows = Course::when($q, fn($qr)=>$qr->where('title','like',"%{$q}%"))
            ->orderBy('title')->limit(20)->get(['id','title'])
            ->map(fn($c)=>['id'=>$c->id,'text'=>$c->title]);

        return response()->json(['results'=>$rows]);
    }


    /** Activity page (students list + enrollments UI) */
    public function page(Request $request)
    {
        $q = trim((string)$request->input('q'));

        $students = \App\Models\User::query()
            ->where('role','student')
            ->when($q !== '', function($qr) use ($q){
                $qr->where(function($s) use ($q){
                    $s->where('name','like',"%$q%")
                      ->orWhere('email','like',"%$q%");
                });
            })
            ->withCount('enrollments as enrollments_count')
            ->withAvg('enrollments as avg_progress_percent','progress_percent')
            ->orderBy('name')
            ->paginate(15)
            ->appends($request->query());

        return view('panel.pages.students.activity', compact('students','q'));
    }

    public function studentActivity(User $user)
    {
        $enrollments = CourseEnrollment::query()
            ->with([
                'course' => fn($c) => $c->select('id','title')->withCount('lessons')
            ])
            ->where('user_id', $user->id)
            ->select([
                'course_enrollments.*',
                DB::raw("
                    (
                      SELECT COUNT(DISTINCT lc.lesson_id)
                      FROM lesson_completions lc
                      JOIN course_lessons cl ON cl.id = lc.lesson_id
                      WHERE lc.user_id = course_enrollments.user_id
                        AND cl.course_id = course_enrollments.course_id
                    ) AS completed_lessons_count
                ")
            ])
            ->orderByDesc('id')
            ->get();

        $recentCompletions = DB::table('lesson_completions as lc')
            ->join('course_lessons as cl','cl.id','=','lc.lesson_id')
            ->join('courses as c','c.id','=','cl.course_id')
            ->where('lc.user_id',$user->id)
            ->orderByDesc('lc.completed_at')
            ->limit(10)
            ->get([
                'lc.lesson_id','lc.completed_at',
                'c.title as course_title',
            ]);

        return view('panel.pages.students.student_activity_rows', compact('user','enrollments','recentCompletions'));
    }
   
}
