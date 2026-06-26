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
        // Report system exceptions by sending email if configured
        $exceptions->report(function (\Throwable $e) {
            if ($e instanceof \Illuminate\Validation\ValidationException || 
                $e instanceof \Illuminate\Auth\AuthenticationException || 
                $e instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException) {
                return;
            }

            if (\App\Models\AppSetting::get('notify_system_error', false)) {
                try {
                    $adminEmail = config('mail.from.address', 'admin@toba.id');
                    $errorMessage = $e->getMessage();
                    $errorTrace = $e->getTraceAsString();
                    $requestUrl = request()?->fullUrl() ?? 'Console/Queue';
                    
                    \Illuminate\Support\Facades\Mail::to($adminEmail)->send(
                        new \App\Mail\SystemErrorNotification($errorMessage, $errorTrace, $requestUrl)
                    );
                } catch (\Exception $mailEx) {
                    \Illuminate\Support\Facades\Log::error('Failed to send error notification email: ' . $mailEx->getMessage());
                }
            }
        });

        // Handle 404 Not Found
        $exceptions->render(function (Illuminate\Http\Exceptions\HttpResponseException $e, Request $request) {
            if ($request->is('admin/*') && $e->getResponse()->status() === 404) {
                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'The requested admin page was not found.'
                    ], 404);
                }
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
                    if ($request->expectsJson() || $request->ajax()) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Page not found.'
                        ], 404);
                    }
                    return response()->view('admin.errors.404', [
                        'message' => 'Page not found.',
                        'path' => $request->path(),
                    ], 404);
                }

                if ($e instanceof \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException || 
                    ($e instanceof \Symfony\Component\HttpKernel\Exception\HttpException && $e->getStatusCode() === 403)) {
                    if ($request->expectsJson() || $request->ajax()) {
                        return response()->json([
                            'success' => false,
                            'message' => $e->getMessage() ?: 'Access denied. You do not have permission to perform this action.'
                        ], 403);
                    }
                    return response()->view('admin.errors.403', [
                        'message' => 'Access denied.',
                        'reason' => $e->getMessage() ?: 'You do not have permission to perform this action.',
                    ], 403);
                }

                if ($e instanceof \Illuminate\Auth\AuthenticationException) {
                    if ($request->expectsJson() || $request->ajax()) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Session expired. Please login again.'
                        ], 401);
                    }
                    return response()->view('admin.errors.401', [
                        'message' => 'Session expired. Please login again.',
                    ], 401);
                }

                if ($e instanceof \Illuminate\Validation\ValidationException) {
                    if ($request->expectsJson() || $request->ajax()) {
                        return response()->json([
                            'success' => false,
                            'message' => implode(' ', \Illuminate\Support\Arr::flatten($e->errors())),
                            'errors' => $e->errors(),
                        ], 422);
                    }
                    return response()->view('admin.errors.400', [
                        'message' => 'Validation failed. Please check your input.',
                        'details' => $e->errors(),
                    ], 400);
                }

                // Server errors
                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'An error occurred: ' . $e->getMessage()
                    ], 500);
                }

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
