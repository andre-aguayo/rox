<?php

namespace App\Http\Controllers\Api\V1;

use App\Contracts\Student\StudentServiceInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\Student\StoreStudentRequest;
use App\Http\Requests\Student\UpdateStudentRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class StudentController extends Controller
{
    private StudentServiceInterface $studentService;

    public function __construct(StudentServiceInterface $studentService)
    {
        $this->studentService = $studentService;
    }

    public function index(Request $request): JsonResponse
    {
        $perPage = (int) $request->get('per_page', 15);

        $students = $this->studentService->list($perPage);

        return response()->json($students);
    }

    public function store(StoreStudentRequest $request): JsonResponse
    {
        $student = $this->studentService->create($request->validated());

        return response()->json($student, Response::HTTP_CREATED);
    }

    public function show(int $id): JsonResponse
    {
        $student = $this->studentService->find($id);

        return response()->json($student);
    }

    public function update(UpdateStudentRequest $request, int $id): JsonResponse
    {
        $student = $this->studentService->update($id, $request->validated());

        return response()->json($student);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->studentService->delete($id);

        return response()->json([], Response::HTTP_NO_CONTENT);
    }
}
