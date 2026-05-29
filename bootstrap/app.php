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
        $middleware->alias([
            // Admin custom middleware
            'admin.auth' => \App\Http\Middleware\EnsureAdminAuthenticated::class,
            'admin.role' => \App\Http\Middleware\AdminRoleMiddleware::class,
            'admin.permission' => \App\Http\Middleware\AdminPermissionMiddleware::class,
            'admin.activity-log' => \App\Http\Middleware\AdminActivityLogMiddleware::class,
            'admin.maintenance' => \App\Http\Middleware\AdminMaintenanceMode::class,
            'admin.error-handler' => \App\Http\Middleware\AdminErrorHandler::class,
        ]);
       // $middleware->redirectGuestsTo(fn () => route('admin.login'));
       $middleware->redirectGuestsTo(function (Request $request) {
            if ($request->expectsJson()) {
                return null;
            }

            return route('admin.login');
        });
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Handle 404 Not Found
        $exceptions->render(function (Illuminate\Http\Exceptions\HttpResponseException $e, Request $request) {
            if ($request->is('admin/*') && $e->getResponse()->status() === 404) {
                return response()->view('admin.errors.404', [
                    'message' => 'The requested admin page was not found.',
                    'path' => $request->path(),
                ], 404);
            }
        });

        // Handle generic HTTP exceptions
        $exceptions->renderable(function (\Throwable $e, Request $request) {
            // Admin route error handling
            if ($request->is('admin/*')) {
                if ($e instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException) {
                    return response()->view('admin.errors.404', [
                        'message' => 'Page not found.',
                        'path' => $request->path(),
                    ], 404);
                }

                if ($e instanceof \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException) {
                    return response()->view('admin.errors.403', [
                        'message' => 'Access denied.',
                        'reason' => 'You do not have permission to perform this action.',
                    ], 403);
                }

                if ($e instanceof \Illuminate\Auth\AuthenticationException) {
                    return response()->view('admin.errors.401', [
                        'message' => 'Session expired. Please login again.',
                    ], 401);
                }

                if ($e instanceof \Illuminate\Validation\ValidationException) {
                    return response()->view('admin.errors.400', [
                        'message' => 'Validation failed. Please check your input.',
                        'details' => $e->errors(),
                    ], 400);
                }

                // Server errors
                if (config('app.debug')) {
                    return response()->view('admin.errors.500', [
                        'message' => 'An error occurred: ' . $e->getMessage(),
                        'exception' => $e,
                    ], 500);
                } else {
                    return response()->view('admin.errors.500', [
                        'message' => 'Something went wrong. Please try again later.',
                    ], 500);
                }
            }
        });
    })->create();
