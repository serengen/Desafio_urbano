<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class OrderTokenMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $expectedToken = (string) config('app.order_api_token', '');

        if ($expectedToken !== '') {
            $providedToken = $request->bearerToken();

            if ($providedToken !== $expectedToken) {
                return new JsonResponse([
                    'message' => 'Unauthorized.',
                ], Response::HTTP_UNAUTHORIZED);
            }
        }

        return $next($request);
    }
}
