@extends('panel.layouts.app')

@section('title', 'Promotion Edit')

@section('content')

<style>
    .hide_field {
        background-color: darkgray;
        padding: 20px;
    }
</style>

<!-- start page title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0"></h4>
            <div class="page-title-right">
                <a class="btn btn-primary" href="{{ route('admin.promotions.index') }}">
                        <em class="icon ni ni-arrow-left"></em> Back to List
                    </a>
            </div>
        </div>
    </div>
</div>
<!-- end page title -->

<div class="row">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-header align-items-center d-flex">
                <h4 class="card-title mb-0 flex-grow-1">Promotion Edit</h4>
            </div>

            <div class="card-body">

                <div class="control-group">
                    @if (!empty(Session::get('message')) && Session::get('message')['status'] == '1')
                        <div class="control-group">
                            <div class="alert alert-success inline">
                                {{ Session::get('message')['text'] }}
                            </div>
                        </div>
                    @elseif (!empty(Session::get('message')) && Session::get('message')['status'] == '0')
                        <div class="control-group">
                            <div class="alert alert-danger inline">
                                {{ Session::get('message')['text'] }}
                            </div>
                        </div>
                    @endif
                </div>

                <!--PAGE CONTENT BEGINS-->
                <form action="{{ route('admin.promotions.update', $promotion->id) }}" accept-charset="utf-8" method="post" class="form-horizontal" enctype="multipart/form-data">
                    @csrf

                    <div class="row mb-3">
                        <div class="col-lg-2 custom-text-align">
                            <label for="title" class="form-label">Course *</label>
                        </div>
                        <div class="col-lg-6">
                            <select class="form-select" id="course_id" name="course_id" required>
                                <option value="" disabled>Select a course</option>
                                <option value="0" {{ $promotion->course_id == 0 ? 'selected' : '' }}>All Courses</option>
                                @foreach ($courses as $course)
                                    <option value="{{ $course->id }}" {{ $promotion->course_id == $course->id ? 'selected' : '' }}>{{ $course->title }}</option>
                                @endforeach
                            </select>
                            @if ($errors->has('course_id'))
                                <strong>{{ $errors->first('course_id') }}</strong>
                            @endif
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-lg-2 custom-text-align">
                            <label class="control-label" for="form-field-1">Discount Type</label>
                        </div>
                        <div class="col-lg-6">
                            <select class="form-select" id="discount_type" name="discount_type">
                                <option value="" disabled>Select discount type</option>
                                <option value="null" {{ $promotion->discount_type == 'null' ? 'selected' : '' }}>None</option>
                                <option value="timer" {{ $promotion->discount_type == 'timer' ? 'selected' : '' }}>Timer Discount</option>
                                <option value="first_some_student" {{ $promotion->discount_type == 'first_some_student' ? 'selected' : '' }}>First Number of Student</option>
                                <option value="special_day" {{ $promotion->discount_type == 'special_day' ? 'selected' : '' }}>Special Day</option>
                            </select>
                            @if ($errors->has('discount_type'))
                                <strong>{{ $errors->first('discount_type') }}</strong>
                            @endif
                        </div>
                    </div>

                    <div class="row mb-3" id="timer_fields" style="display: {{ $promotion->discount_type == 'timer' ? '' : 'none' }}">
                        <div class="col-lg-2 custom-text-align hide_field" style="border-radius: 20px 0px 0px 20px;">
                            <label class="control-label" for="form-field-1">Time Duration</label>
                        </div>
                        <div class="col-lg-3 hide_field">
                            <label for="startTime" class="form-label">Start Time</label>
                            <input type="datetime-local" class="form-control" id="start_time" name="start_time" value="{{ $promotion->start_time }}">
                            @if ($errors->has('start_time'))
                                <strong>{{ $errors->first('start_time') }}</strong>
                            @endif
                        </div>
                        <div class="col-lg-3 hide_field" style="border-radius: 0px 20px 20px 0px;">
                            <label for="endTime" class="form-label mt-2">End Time</label>
                            <input type="datetime-local" class="form-control" id="end_time" name="end_time" value="{{ $promotion->end_time }}">
                            @if ($errors->has('end_time'))
                                <strong>{{ $errors->first('end_time') }}</strong>
                            @endif
                        </div>
                    </div>

                    <div class="row mb-3" id="student_limit_fields" style="display: {{ $promotion->discount_type == 'first_some_student' ? '' : 'none' }}">
                        <div class="col-lg-2 custom-text-align hide_field" style="border-radius: 20px 0px 0px 20px;">
                            <label class="control-label" for="form-field-1">Student Limit</label>
                        </div>
                        <div class="col-lg-6 hide_field" style="border-radius: 0px 20px 20px 0px;">
                            <input type="number" class="form-control" id="student_limit" name="student_limit" value="{{ $promotion->student_limit }}">
                            @if ($errors->has('student_limit'))
                                <strong>{{ $errors->first('student_limit') }}</strong>
                            @endif
                        </div>
                    </div>

                    <div class="row mb-3" id="special_day_fields" style="display: {{ $promotion->discount_type == 'special_day' ? '' : 'none' }}">
                        <div class="col-lg-2 custom-text-align hide_field" style="border-radius: 20px 0px 0px 20px;">
                            <label class="control-label" for="form-field-1">Special Day</label>
                        </div>
                        <div class="col-lg-2 hide_field">
                            <label for="startTime" class="form-label">Day Title</label>
                            <input type="text" class="form-control" id="day_title" name="day_title" value="{{ $promotion->day_title }}">
                            @if ($errors->has('day_title'))
                                <strong>{{ $errors->first('day_title') }}</strong>
                            @endif
                        </div>
                        <div class="col-lg-2 hide_field">
                            <label for="endTime" class="form-label mt-2">Start Date</label>
                            <input type="date" class="form-control" id="start_date" name="start_date" value="{{ $promotion->start_date }}">
                            @if ($errors->has('start_date'))
                                <strong>{{ $errors->first('start_date') }}</strong>
                            @endif
                        </div>
                        <div class="col-lg-2 hide_field" style="border-radius: 0px 20px 20px 0px;">
                            <label for="endTime" class="form-label mt-2">End Date</label>
                            <input type="date" class="form-control" id="end_date" name="end_date" value="{{ $promotion->end_date }}">
                            @if ($errors->has('end_date'))
                                <strong>{{ $errors->first('end_date') }}</strong>
                            @endif
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-lg-2 custom-text-align">
                            <label class="control-label" for="form-field-1">Discount Value Type *</label>
                        </div>
                        <div class="col-lg-6">
                            <select class="form-select" id="discount_value_type" name="discount_value_type" required>
                                <option value="percentage" {{ $promotion->discount_value_type == 'percentage' ? 'selected' : '' }}>Percentage (%)</option>
                                <option value="fixed" {{ $promotion->discount_value_type == 'fixed' ? 'selected' : '' }}>Fixed Amount</option>
                            </select>
                            @if ($errors->has('discount_value_type'))
                                <strong>{{ $errors->first('discount_value_type') }}</strong>
                            @endif
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-lg-2 custom-text-align">
                            <label class="control-label" for="form-field-1">Discount Value</label>
                        </div>
                        <div class="col-lg-6">
                            <input type="text" class="form-control" id="discount_value" name="discount_value" value="{{ $promotion->discount_value }}" required>
                            @if ($errors->has('discount_value'))
                                <strong>{{ $errors->first('discount_value') }}</strong>
                            @endif
                        </div>
                    </div>
                        
                    <div class="row mb-3">
                        <div class="col-lg-2 custom-text-align">
                            <label for="status" class="form-label">Status </label>
                        </div>
                        <div class="col-lg-4">
                            <select class="form-select" name="status">
                                <option value="1" {{ $promotion->status == 1 ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ $promotion->status == 0 ? 'selected' : '' }}>Inactive</option>
                            </select>
                            @error('status')
                                <strong>{{ $message }}</strong>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-lg-2 custom-text-align">
                            <label for="status" class="form-label">&nbsp; </label>
                        </div>
                        <div class="col-lg-4">
                            <button type="submit" class="btn btn-success">
                                <i class="icon-ok bigger-110"></i>
                                UPDATE
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>




    <script>
        const discountValueType = document.getElementById('discount_value_type');
        const discountValue = document.getElementById('discount_value');
    
        discountValueType.addEventListener('change', function () {
            if (this.value === 'percentage') {
                discountValue.placeholder = 'Enter discount percentage (e.g., 20 for 20%)';
                discountValue.min = 1;
                discountValue.max = 100;
            } else if (this.value === 'fixed') {
                discountValue.placeholder = 'Enter fixed discount amount (e.g., 500)';
                discountValue.removeAttribute('max'); // Fixed amounts don't have a max limit
                discountValue.min = 1; // Ensures positive numbers
            }
        });
    </script>

<script>
    // Show/Hide Discount Type Fields
    document.getElementById('discount_type').addEventListener('change', function () {
        const type = this.value;
        // Get all discount-related fields
        const timerFields = document.getElementById('timer_fields');
        const studentLimitFields = document.getElementById('student_limit_fields');
        const specialDayFields = document.getElementById('special_day_fields');

        // Hide/Show fields based on the selected type
        timerFields.style.display = type === 'timer' ? '' : 'none';
        studentLimitFields.style.display = type === 'first_some_student' ? '' : 'none';
        specialDayFields.style.display = type === 'special_day' ? '' : 'none';

    });

</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>


@endsection