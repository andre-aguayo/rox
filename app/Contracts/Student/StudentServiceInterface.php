<?php

namespace App\Contracts\Student;

use App\Models\Student;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface StudentServiceInterface
{
    /**
     * Return a paginated list of students with their relations.
     *
     * @return LengthAwarePaginator<Student>
     */
    public function list(int $perPage = 15): LengthAwarePaginator;

    /**
     * Find a student by id or fail.
     */
    public function find(int $id): Student;

    /**
     * Create a new student, optionally attaching subjects.
     *
     * @param  array<string,mixed>  $data
     */
    public function create(array $data): Student;

    /**
     * Update a student and optionally sync subjects.
     *
     * @param  array<string,mixed>  $data
     */
    public function update(int $id, array $data): Student;

    /**
     * Delete a student by id.
     */
    public function delete(int $id): void;
}
