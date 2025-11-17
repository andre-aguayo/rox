<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

class ApiAuthenticationException extends Exception
{
    public function __construct(
        string $message = 'Unauthenticated.',
        int $code = 0,
        ?Throwable $previous = null,
    ) {
        parent::__construct($message, $code, $previous);
    }

    public function render(Request $request): JsonResponse
    {
        return response()->json(
            [
                'success' => false,
                'status' => 401,
                'data' => [
                    'message' => $this->getMessage(),
                ],
            ],
            401,
        );
    }
}
