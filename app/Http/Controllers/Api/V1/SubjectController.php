<?php

namespace App\Http\Controllers\Api\V1;

use App\Contracts\Subject\SubjectServiceInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\Subject\StoreSubjectRequest;
use App\Http\Requests\Subject\UpdateSubjectRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SubjectController extends Controller
{
    private SubjectServiceInterface $subjectService;

    public function __construct(SubjectServiceInterface $subjectService)
    {
        $this->subjectService = $subjectService;
    }

    public function index(Request $request): JsonResponse
    {
        $perPage = (int) $request->get('per_page', 15);

        $subjects = $this->subjectService->list($perPage);

        return response()->json($subjects);
    }

    public function store(StoreSubjectRequest $request): JsonResponse
    {
        $subject = $this->subjectService->create($request->validated());

        return response()->json($subject, Response::HTTP_CREATED);
    }

    public function show(int $id): JsonResponse
    {
        $subject = $this->subjectService->find($id);

        return response()->json($subject);
    }

    public function update(UpdateSubjectRequest $request, int $id): JsonResponse
    {
        $subject = $this->subjectService->update($id, $request->validated());

        return response()->json($subject);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->subjectService->delete($id);

        return response()->json([], Response::HTTP_NO_CONTENT);
    }
}
