<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsNotBlocked
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // If the user is logged in AND blocked
        if (auth()->check() && auth()->user()->is_blocked) {
            auth()->logout();

            return redirect()->route('login')
                ->withErrors(['email' => 'Your account has been blocked.']);
        }

        return $next($request);
    }
}
