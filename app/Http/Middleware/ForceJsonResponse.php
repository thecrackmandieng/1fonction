<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ForceJsonResponse
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Force JSON response for API routes
        $request->headers->set('Accept', 'application/json');

        $response = $next($request);

        // Ensure JSON response
        if ($request->is('api/*')) {
            $response->headers->set('Content-Type', 'application/json');
        }

        return $response;
    }
}
