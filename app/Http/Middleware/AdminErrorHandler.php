<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Admin Error Handler Middleware
 * Provides comprehensive error handling untuk admin panel
 */
class AdminErrorHandler
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            $response = $next($request);

            // Handle various HTTP response codes
            switch ($response->getStatusCode()) {
                case 200:
                    // Success - add success flag to session
                    if ($request->method() !== 'GET') {
                        session()->flash('response_code', 200);
                    }
                    break;

                case 201:
                    // Created
                    session()->flash('response_code', 201);
                    session()->flash('success_message', 'Resource created successfully');
                    break;

                case 204:
                    // No Content
                    session()->flash('response_code', 204);
                    break;

                case 301:
                case 302:
                case 303:
                case 307:
                case 308:
                    // Redirects handled by Laravel
                    break;

                case 400:
                    // Bad Request
                    session()->flash('error_message', 'Bad request. Please check your input.');
                    break;

                case 401:
                    // Unauthorized
                    return redirect()->route('admin.login')
                        ->with('error', 'Please authenticate first.');

                case 403:
                    // Forbidden
                    return response()->view('admin.errors.403', [
                        'message' => 'You do not have permission to perform this action.',
                    ], 403);

                case 404:
                    // Not Found
                    return response()->view('admin.errors.404', [
                        'message' => 'The requested resource was not found.',
                        'path' => $request->path(),
                    ], 404);

                case 422:
                    // Unprocessable Entity (Validation)
                    session()->flash('error_message', 'Validation failed.');
                    break;

                case 500:
                case 502:
                case 503:
                case 504:
                    // Server Errors
                    \Log::error('Server Error', [
                        'status' => $response->getStatusCode(),
                        'path' => $request->path(),
                        'method' => $request->method(),
                        'user' => auth('admin')->id(),
                    ]);

                    if ($request->expectsJson()) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Server error occurred. Please try again later.',
                            'code' => $response->getStatusCode(),
                        ], $response->getStatusCode());
                    }

                    return response()->view('admin.errors.500', [
                        'message' => 'An unexpected error occurred.',
                        'code' => $response->getStatusCode(),
                        'errors' => new \Illuminate\Support\ViewErrorBag(),
                    ], 500);
            }

            return $response;
        } catch (\Throwable $e) {
            // Log unexpected errors
            \Log::critical('Admin Error Handler Exception', [
                'exception' => get_class($e),
                'message' => $e->getMessage(),
                'path' => $request->path(),
                'user' => auth('admin')->id() ?? 'unauthenticated',
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred while processing your request.',
                    'code' => 500,
                ], 500);
            }

            return response()->view('admin.errors.500', [
                'message' => 'An unexpected error occurred.',
                'errors' => new \Illuminate\Support\ViewErrorBag(),
            ], 500);
        }
    }
}
