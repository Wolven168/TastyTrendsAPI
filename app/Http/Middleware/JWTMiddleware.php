<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class JWTMiddleware
{
    
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $message = '';

        try {
            // Checks token validation
            JWTAuth::parseToken()->authenticate();
            return $next($request);
        }
        catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
            // When token expires
            $message = 'Token expired';
        }
        catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            // When token is invalid
            $message = 'Token invalid';
        }
        catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
            // When token is not present
            $message = 'Provide token';
        }
        return response()->json([
            'success' => false,
            'message' => $message
        ]);
    }
}
