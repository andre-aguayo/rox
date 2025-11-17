<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class ApiResponseMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $accept = $request->headers->get('Accept');

        if ($accept === null || stripos($accept, 'application/json') === false) {
            $request->headers->set('Accept', 'application/json');
        }

        $response = $next($request);

        if (! $response instanceof JsonResponse) {
            Log::info('Non-JSON response detected on API route', [
                'uri' => $request->getRequestUri(),
                'method' => $request->getMethod(),
                'response_class' => $response::class,
                'status' => $response->getStatusCode(),
                'location' => $response->headers->get('Location'),
                'content_type' => $response->headers->get('Content-Type'),
            ]);

            return $response;
        }

        $original = $response->getData(true);

        $formatted = [
            'success' => $response->isSuccessful(),
            'status' => $response->getStatusCode(),
            'data' => $original,
        ];

        return new JsonResponse(
            data: $formatted,
            status: $response->getStatusCode(),
        );
    }
}
