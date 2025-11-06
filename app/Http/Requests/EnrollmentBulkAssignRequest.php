<?php


namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EnrollmentBulkAssignRequest extends FormRequest
{
    //public function authorize(): bool { return $this->user()?->can('manage-enrollments') ?? false; }

    public function rules(): array {
        return [
            'course_id'   => ['required','integer', Rule::exists('courses','id')],
            'user_ids'    => ['required','array','min:1'],
            'user_ids.*'  => ['integer', Rule::exists('users','id')],
        ];
    }
}