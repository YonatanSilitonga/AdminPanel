<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->redirectGuestsTo(function (Request $request) {
            if ($request->expectsJson()) {
                return null;
            }

            return route('admin.login');
        });

        $middleware->alias([
            // Admin custom middleware
            'admin.auth' => \App\Http\Middleware\EnsureAdminAuthenticated::class,
            'admin.role' => \App\Http\Middleware\AdminRoleMiddleware::class,
            'admin.permission' => \App\Http\Middleware\AdminPermissionMiddleware::class,
            'admin.activity-log' => \App\Http\Middleware\AdminActivityLogMiddleware::class,
            'admin.maintenance' => \App\Http\Middleware\AdminMaintenanceMode::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
