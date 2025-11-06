<?php


namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BookRequest extends FormRequest
{
    public function authorize(): bool { return auth()->check(); }

    public function rules(): array
    {
        $isCreate = $this->isMethod('post');

        return [
            'title'        => ['required','string','max:255'],
            'author'       => ['nullable','string','max:255'],
            'description'  => ['nullable','string'],
            'pages'        => ['nullable','integer','min:1','max:20000'],
            'price'        => ['required','numeric','min:0'],
            'status'       => ['required','in:draft,published'],
            'published_at' => ['nullable','date'],

            'cover'        => [$isCreate ? 'nullable' : 'nullable', 'file', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'pdf'          => [$isCreate ? 'required' : 'nullable', 'file', 'mimes:pdf', 'max:51200'],
        ];
    }
}
