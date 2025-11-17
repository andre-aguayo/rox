<?php

namespace App\Http\Requests\Student;

use App\Models\Student;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateStudentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $routeParam = $this->route('student') ?? $this->route('id');
        $studentId = $routeParam instanceof Student ? $routeParam->id : $routeParam;

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
                Rule::unique('students', 'email')->ignore($studentId),
            ],
            'enrollment_number' => [
                'required',
                'string',
                'max:255',
                Rule::unique('students', 'enrollment_number')->ignore($studentId),
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
