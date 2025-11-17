<?php

namespace App\Contracts\Subject;

use App\Models\Subject;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface SubjectServiceInterface
{
    /**
     * Return a paginated list of subjects with their relations.
     *
     * @return LengthAwarePaginator<Subject>
     */
    public function list(int $perPage = 15): LengthAwarePaginator;

    /**
     * Find a subject by id or fail.
     */
    public function find(int $id): Subject;

    /**
     * Create a new subject, optionally attaching students.
     *
     * @param  array<string,mixed>  $data
     */
    public function create(array $data): Subject;

    /**
     * Update a subject and optionally sync students.
     *
     * @param  array<string,mixed>  $data
     */
    public function update(int $id, array $data): Subject;

    /**
     * Delete a subject by id.
     */
    public function delete(int $id): void;
}
