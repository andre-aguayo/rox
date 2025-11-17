<?php

namespace App\Http\Requests\Subject;

use Illuminate\Foundation\Http\FormRequest;

class StoreSubjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
            ],
            'code' => [
                'required',
                'string',
                'max:100',
                'unique:subjects,code',
            ],
            'description' => [
                'nullable',
                'string',
            ],
            'student_id' => [
                'nullable',
                'integer',
                'exists:students,id',
            ],
            'student_ids' => [
                'nullable',
                'array',
            ],
            'student_ids.*' => [
                'integer',
                'exists:students,id',
            ],
        ];
    }
}
