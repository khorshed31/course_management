<?php


namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EnrollmentAssignRequest extends FormRequest
{
    //public function authorize(): bool { return $this->user()?->can('manage-enrollments') ?? false; }

    public function rules(): array {
        return [
            'course_id' => ['required','integer', Rule::exists('courses','id')],
            'user_id'   => ['required','integer', Rule::exists('users','id')],
        ];
    }
}
