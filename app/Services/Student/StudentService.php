<?php

namespace App\Services\Student;

use App\Contracts\Student\StudentServiceInterface;
use App\Models\Student;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class StudentService implements StudentServiceInterface
{
    public function list(int $perPage = 15): LengthAwarePaginator
    {
        return Student::query()
            ->with('subjects')
            ->paginate($perPage);
    }

    public function find(int $id): Student
    {
        return Student::query()
            ->with('subjects')
            ->findOrFail($id);
    }

    public function create(array $data): Student
    {
        $subjectIds = $data['subject_ids'] ?? ($data['subject_id'] ?? null);

        unset($data['subject_ids'], $data['subject_id']);

        $student = Student::query()->create($data);

        if ($subjectIds !== null) {
            if (! is_array($subjectIds)) {
                $subjectIds = [$subjectIds];
            }

            $student->subjects()->sync($subjectIds);
        }

        return $student->load('subjects');
    }

    public function update(int $id, array $data): Student
    {
        $student = $this->find($id);

        $subjectIds = $data['subject_ids'] ?? ($data['subject_id'] ?? null);

        unset($data['subject_ids'], $data['subject_id']);

        $student->fill($data);
        $student->save();

        if ($subjectIds !== null) {
            if (! is_array($subjectIds)) {
                $subjectIds = [$subjectIds];
            }

            $student->subjects()->sync($subjectIds);
        }

        return $student->load('subjects');
    }

    public function delete(int $id): void
    {
        $student = $this->find($id);

        $student->delete();
    }
}
