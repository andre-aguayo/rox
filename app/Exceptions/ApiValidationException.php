<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

class ApiValidationException extends Exception
{
    /**
     * @var array<string, array<int, string>>
     */
    private array $errors;

    /**
     * @param  array<string, array<int, string>>  $errors
     */
    public function __construct(
        string $message = 'The given data was invalid.',
        array $errors = [],
        int $code = 0,
        ?Throwable $previous = null,
    ) {
        parent::__construct($message, $code, $previous);

        $this->errors = $errors;
    }

    public function render(Request $request): JsonResponse
    {
        return response()->json(
            [
                'success' => false,
                'status' => 422,
                'data' => [
                    'message' => $this->getMessage(),
                    'errors' => $this->errors,
                ],
            ],
            422,
        );
    }
}
