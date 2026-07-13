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
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'role' => \App\Http\Middleware\CheckRole::class,
        ]);
        // Tangkap error saat file melebihi post_max_size server
        $middleware->web(prepend: [
            \App\Http\Middleware\HandlePostTooLarge::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();