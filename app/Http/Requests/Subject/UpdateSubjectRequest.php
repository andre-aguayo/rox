<?php

namespace App\Http\Requests\Subject;

use App\Models\Subject;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSubjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $routeParam = $this->route('subject') ?? $this->route('id');
        $subjectId = $routeParam instanceof Subject ? $routeParam->id : $routeParam;

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
                Rule::unique('subjects', 'code')->ignore($subjectId),
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
