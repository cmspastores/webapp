<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        /* if (!auth()->check() || !auth()->user()->is_admin) {
            // If not logged in or not admin, redirect or abort
            abort(403, 'Unauthorized access.');
        } */

        return $next($request);
    }
}
