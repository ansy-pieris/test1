<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Register enhanced API middleware for outstanding Sanctum integration
        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
            'enhanced.sanctum' => \App\Http\Middleware\Api\EnhancedSanctumAuth::class,
        ]);

        // API-specific middleware groups
        $middleware->group('api', [
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
            \Illuminate\Routing\Middleware\ThrottleRequests::class.':api',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ]);

        // Enhanced API rate limiting
        $middleware->throttle([
            'api' => \Illuminate\Routing\Middleware\ThrottleRequests::class.':60,1',
            'auth' => \Illuminate\Routing\Middleware\ThrottleRequests::class.':5,15', // 5 attempts per 15 minutes
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
