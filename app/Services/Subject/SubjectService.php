<?php

namespace App\Services\Subject;

use App\Contracts\Subject\SubjectServiceInterface;
use App\Models\Subject;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class SubjectService implements SubjectServiceInterface
{
    public function list(int $perPage = 15): LengthAwarePaginator
    {
        return Subject::query()
            ->with('students')
            ->paginate($perPage);
    }

    public function find(int $id): Subject
    {
        return Subject::query()
            ->with('students')
            ->findOrFail($id);
    }

    public function create(array $data): Subject
    {
        $studentIds = $data['student_ids'] ?? ($data['student_id'] ?? null);

        unset($data['student_ids'], $data['student_id']);

        /** @var Subject $subject */
        $subject = Subject::query()->create($data);

        if ($studentIds !== null) {
            if (! is_array($studentIds)) {
                $studentIds = [$studentIds];
            }

            $subject->students()->sync($studentIds);
        }

        return $subject->load('students');
    }

    public function update(int $id, array $data): Subject
    {
        $subject = $this->find($id);

        $studentIds = $data['student_ids'] ?? ($data['student_id'] ?? null);

        unset($data['student_ids'], $data['student_id']);

        $subject->fill($data);
        $subject->save();

        if ($studentIds !== null) {
            if (! is_array($studentIds)) {
                $studentIds = [$studentIds];
            }

            $subject->students()->sync($studentIds);
        }

        return $subject->load('students');
    }

    public function delete(int $id): void
    {
        $subject = $this->find($id);

        $subject->delete();
    }
}
