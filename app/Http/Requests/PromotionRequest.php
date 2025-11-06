<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PromotionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // add policy/permission checks if needed
    }

    public function rules(): array
    {
        $rules = [
            'course_id' => ['nullable','integer','min:0'], // 0 = All Courses
            'discount_type' => ['nullable', Rule::in(['null','timer','first_some_student','special_day'])],
            'start_time' => ['nullable','date'],
            'end_time' => ['nullable','date','after_or_equal:start_time'],
            'student_limit' => ['nullable','integer','min:1'],
            'day_title' => ['nullable','string','max:255'],
            'start_date' => ['nullable','date'],
            'end_date' => ['nullable','date','after_or_equal:start_date'],
            'discount_value_type' => ['required', Rule::in(['percentage','fixed'])],
            'discount_value' => ['required','numeric','min:0'],
            'status' => ['required','boolean'],
        ];

        $type = $this->input('discount_type');

        if ($type === 'timer') {
            $rules['start_time'] = ['required','date'];
            $rules['end_time']   = ['required','date','after_or_equal:start_time'];
        }

        if ($type === 'first_some_student') {
            $rules['student_limit'] = ['required','integer','min:1'];
        }

        if ($type === 'special_day') {
            $rules['day_title']  = ['required','string','max:255'];
            $rules['start_date'] = ['required','date'];
            $rules['end_date']   = ['required','date','after_or_equal:start_date'];
        }

        return $rules;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            // keep 0 as 0 (All courses); only empty string -> null
            'course_id' => $this->course_id === '' ? null : $this->course_id,
            'student_limit' => $this->student_limit === '' ? null : $this->student_limit,
            'status' => $this->status ?? 1,
        ]);
    }
}
