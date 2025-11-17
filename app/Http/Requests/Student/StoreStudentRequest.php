<?php

namespace App\Http\Requests\Student;

use Illuminate\Foundation\Http\FormRequest;

class StoreStudentRequest extends FormRequest
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
            'email' => [
                'required',
                'email',
                'max:255',
                'unique:students,email',
            ],
            'enrollment_number' => [
                'required',
                'string',
                'max:255',
                'unique:students,enrollment_number',
            ],
            'birth_date' => [
                'required',
                'date',
            ],
            'subject_id' => [
                'nullable',
                'integer',
                'exists:subjects,id',
            ],
            'subject_ids' => [
                'nullable',
                'array',
            ],
            'subject_ids.*' => [
                'integer',
                'exists:subjects,id',
            ],
        ];
    }
}
