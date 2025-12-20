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
        // Mendaftarkan alias untuk middleware kustom kita
        $middleware->alias([
            'role' => \App\Http\Middleware\CheckRole::class,
        ]);
        
        // Exclude route feedback dari CSRF verification
        // Karena tablet feedback berjalan tanpa auth dan session bisa expired
        $middleware->validateCsrfTokens(except: [
            'feedback',
            'feedback/*',
            'api/feedback/*',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })
    ->withCommands([
        \App\Console\Commands\CreateAdminUser::class,
    ])
    ->create();
