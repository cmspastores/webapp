<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Register middleware alias
        $middleware->alias([
        'not_blocked' => \App\Http\Middleware\EnsureUserIsNotBlocked::class,
            'admin' => \App\Http\Middleware\AdminMiddleware::class,              // ← alias admin middleware
        ]);

        // Apply middleware globally to all web routes
        $middleware->web(append: [
            \App\Http\Middleware\EnsureUserIsNotBlocked::class,
            \App\Http\Middleware\AdminMiddleware::class,                         // ← apply to web group
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
