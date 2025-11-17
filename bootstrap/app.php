<?php

use App\Exceptions\ApiAuthenticationException;
use App\Exceptions\ApiValidationException;
use App\Http\Middleware\ApiResponseMiddleware;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        api: __DIR__.'/../routes/api.php',
        apiPrefix: 'api',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->redirectGuestsTo(function (Request $request): ?string {
            if ($request->is('api/*')) {
                return null;
            }

            return route(name: 'login');
        });
        $middleware->api(append: [
            ApiResponseMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->map(
            function (AuthenticationException $e): ApiAuthenticationException {
                return new ApiAuthenticationException(
                    message: $e->getMessage(),
                    code: $e->getCode(),
                    previous: $e,
                );
            }
        );

        $exceptions->map(
            function (ValidationException $e): ApiValidationException {
                return new ApiValidationException(
                    message: $e->getMessage(),
                    errors: $e->errors(),
                    code: $e->getCode(),
                    previous: $e,
                );
            }
        );

        $exceptions->shouldRenderJsonWhen(function (Request $request, Throwable $e): bool {
            if ($request->is('api/*')) {
                return true;
            }

            return $request->expectsJson();
        });
    })->create();
