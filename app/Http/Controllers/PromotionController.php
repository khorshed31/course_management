<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Promotion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PromotionController extends Controller
{
    

    public function index(Request $request)
    {
        $sql = Promotion::with(['get_course'])->latest();

        // Handle Search Filters
        if ($request->filled('course_name')) {
            $sql->whereHas('get_course', function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->course_name . '%');
            });
        }

        if ($request->filled('discount_type')) {
            $sql->where('discount_type', $request->discount_type);
        }

        $lists = 1;
        $perPage = 10;
        $records = $sql->paginate($perPage);
        $serial = (!empty($input['page'])) ? (($perPage * ($input['page'] - 1)) + 1) : 1;
        return view('panel.pages.promotion.index', compact('lists', 'serial', 'records'));
    }


    public function create()
    {
        $data['courses'] = Course::where('status','1')->get();
        return view('panel.pages.promotion.create', $data);
    }



    public function store(Request $request)
    {
        // Validate the request data
        $request->validate([
            'course_id' => 'required|integer',
            'discount_type' => 'nullable|string|in:null,timer,first_some_student,special_day',
            'start_time' => 'nullable|date',
            'end_time' => 'nullable|date|after_or_equal:start_time',
            'student_limit' => 'nullable|integer|min:1',
            'day_title' => 'nullable|string|max:255',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'discount_value_type' => 'required|string|in:percentage,fixed',
            'discount_value' => 'required|numeric|min:0',
            'status' => 'required|boolean',
        ]);

        try {

            if ($request->course_id == 0) {
                Promotion::query()->update(['status' => 0]);
            }
            // Create the promotion record
            $insert = Promotion::create([
                'course_id' => $request->course_id,
                'discount_type' => $request->discount_type,
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'student_limit' => $request->student_limit,
                'day_title' => $request->day_title,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'discount_value_type' => $request->discount_value_type,
                'discount_value' => $request->discount_value,
                'status' => $request->status,
            ]);
    
            if ($insert) :
                $request->session()->flash('message', ['status' => 1, 'text' => 'Promotion add successfully']);
            else :
                $request->session()->flash('message', ['status' => 0,  'text' => 'Promotion add failed.']);
            endif;
        } catch (\Exception $e) {
            $request->session()->flash('message', ['status' => 0, 'text' => $e->getMessage()]);
        }

        return redirect()->back();



    }

    public function edit($id)
    {
        $data['promotion'] = Promotion::where('id', $id)->first();
        $data['courses'] = Course::where('status','1')->get();
        $data['id'] = $id;
        return view('panel.pages.promotion.edit', $data);
    }




    public function update(Request $request, $id)
    {
        // Validate the incoming request
        $request->validate([
            'course_id' => 'required|integer',
            'discount_type' => 'nullable|string',
            'start_time' => 'nullable|date',
            'end_time' => 'nullable|date|after_or_equal:start_time',
            'student_limit' => 'nullable|integer|min:1',
            'day_title' => 'nullable|string|max:255',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'discount_value_type' => 'required|string|in:percentage,fixed',
            'discount_value' => 'required|numeric|min:0',
            'status' => 'required|boolean',
        ]);

        try {
            // Find the promotion by its ID
            $promotion = Promotion::findOrFail($id);

            // Update promotion details
            $promotion->course_id = $request->input('course_id');
            $promotion->discount_type = $request->input('discount_type', 'null');
            $promotion->start_time = $request->input('discount_type') === 'timer' ? $request->input('start_time') : null;
            $promotion->end_time = $request->input('discount_type') === 'timer' ? $request->input('end_time') : null;
            $promotion->student_limit = $request->input('discount_type') === 'first_some_student' ? $request->input('student_limit') : null;
            $promotion->day_title = $request->input('discount_type') === 'special_day' ? $request->input('day_title') : null;
            $promotion->start_date = $request->input('discount_type') === 'special_day' ? $request->input('start_date') : null;
            $promotion->end_date = $request->input('discount_type') === 'special_day' ? $request->input('end_date') : null;
            $promotion->discount_value_type = $request->input('discount_value_type');
            $promotion->discount_value = $request->input('discount_value');
            $promotion->status = $request->input('status');

            // Save changes
            $promotion->save();

            if ($promotion) :
                $request->session()->flash('message', ['status' => 1, 'text' => 'Promotion update successfully']);
            else :
                $request->session()->flash('message', ['status' => 0,  'text' => 'Promotion update failed.']);
            endif;
        } catch (\Exception $e) {
            $request->session()->flash('message', ['status' => 0, 'text' => $e->getMessage()]);
        }

        return redirect()->back();
    }



    public function destroy($id, Request $request)
    {
        $p = Promotion::find($id);
        if (!$p) {
            return back()->with('error', 'Promotion not found.');
        }
        $p->delete();
        return back()->with('success', 'Promotion deleted successfully.');
    }



    public function updateStatus(Request $request, $id)
    {
        try {
            $promotion = Promotion::findOrFail($id);
            $promotion->update(['status' => $request->status]);

            return response()->json(['success' => true, 'message' => 'Status updated successfully!']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to update status.']);
        }
    }


    public function checkPromotion($course_id)
    {
        $promotion = Promotion::where('course_id', $course_id)
                            ->where('status', '1')
                            ->first();

        return response()->json(['promotion' => $promotion]);
    }


    public function deactivateOldPromotions(Request $request)
    {
        try {
            $courseId = $request->course_id;

            // Deactivate all promotions for the selected course
            Promotion::where('course_id', $courseId)->update(['status' => '0']);

            return response()->json(['success' => true, 'message' => 'All old promotions deactivated successfully!']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to deactivate promotions.']);
        }
    }



}
