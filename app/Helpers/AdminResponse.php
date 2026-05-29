<?php

namespace App\Helpers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class AdminResponse
{
    /**
     * Success response (200 OK)
     */
    public static function success(
        string $message = 'Operation successful',
        array $data = [],
        string $redirectTo = null,
        int $statusCode = 200
    ): JsonResponse|RedirectResponse {
        $response = [
            'success' => true,
            'status' => 'success',
            'message' => $message,
            'code' => $statusCode,
        ];

        if (!empty($data)) {
            $response['data'] = $data;
        }

        // If redirect provided, use session flash
        if ($redirectTo) {
            return redirect($redirectTo)->with($response);
        }

        return response()->json($response, $statusCode);
    }

    /**
     * Created response (201 Created)
     */
    public static function created(
        string $message = 'Resource created successfully',
        array $data = [],
        string $redirectTo = null
    ): JsonResponse|RedirectResponse {
        return self::success($message, $data, $redirectTo, 201);
    }

    /**
     * Accepted response (202 Accepted)
     */
    public static function accepted(
        string $message = 'Request accepted',
        array $data = [],
        string $redirectTo = null
    ): JsonResponse|RedirectResponse {
        return self::success($message, $data, $redirectTo, 202);
    }

    /**
     * Bad Request (400)
     */
    public static function badRequest(
        string $message = 'Bad request',
        array $errors = [],
        int $statusCode = 400
    ): JsonResponse {
        return response()->json([
            'success' => false,
            'status' => 'error',
            'message' => $message,
            'code' => $statusCode,
            'errors' => $errors,
        ], $statusCode);
    }

    /**
     * Unauthorized (401)
     */
    public static function unauthorized(
        string $message = 'Unauthorized'
    ): JsonResponse {
        return response()->json([
            'success' => false,
            'status' => 'unauthorized',
            'message' => $message,
            'code' => 401,
        ], 401);
    }

    /**
     * Forbidden (403)
     */
    public static function forbidden(
        string $message = 'Access denied'
    ): JsonResponse {
        return response()->json([
            'success' => false,
            'status' => 'forbidden',
            'message' => $message,
            'code' => 403,
        ], 403);
    }

    /**
     * Not Found (404)
     */
    public static function notFound(
        string $message = 'Resource not found'
    ): JsonResponse {
        return response()->json([
            'success' => false,
            'status' => 'not_found',
            'message' => $message,
            'code' => 404,
        ], 404);
    }

    /**
     * Conflict (409)
     */
    public static function conflict(
        string $message = 'Conflict',
        array $data = []
    ): JsonResponse {
        return response()->json([
            'success' => false,
            'status' => 'conflict',
            'message' => $message,
            'code' => 409,
            'data' => $data,
        ], 409);
    }

    /**
     * Validation Failed (422)
     */
    public static function validationFailed(
        string $message = 'Validation failed',
        array $errors = []
    ): JsonResponse {
        return response()->json([
            'success' => false,
            'status' => 'validation_error',
            'message' => $message,
            'code' => 422,
            'errors' => $errors,
        ], 422);
    }

    /**
     * Server Error (500)
     */
    public static function serverError(
        string $message = 'Internal server error',
        array $debug = []
    ): JsonResponse {
        $response = [
            'success' => false,
            'status' => 'server_error',
            'message' => $message,
            'code' => 500,
        ];

        if (config('app.debug') && !empty($debug)) {
            $response['debug'] = $debug;
        }

        return response()->json($response, 500);
    }

    /**
     * Service Unavailable (503)
     */
    public static function serviceUnavailable(
        string $message = 'Service temporarily unavailable'
    ): JsonResponse {
        return response()->json([
            'success' => false,
            'status' => 'service_unavailable',
            'message' => $message,
            'code' => 503,
        ], 503);
    }

    /**
     * Redirect with success message
     */
    public static function redirectSuccess(
        string $route,
        string $message = 'Operation successful',
        array $params = []
    ): RedirectResponse {
        return redirect()->route($route, $params)->with([
            'success' => true,
            'message' => $message,
        ]);
    }

    /**
     * Redirect with error message
     */
    public static function redirectError(
        string $route = null,
        string $message = 'An error occurred',
        array $params = [],
        array $errors = []
    ): RedirectResponse {
        $redirect = $route ? redirect()->route($route, $params) : back();

        return $redirect->with([
            'success' => false,
            'message' => $message,
            'errors' => $errors,
        ]);
    }

    /**
     * Flash success message to session
     */
    public static function flash(
        string $message,
        string $type = 'success'
    ): array {
        return [
            'type' => $type,
            'message' => $message,
            'timestamp' => now(),
        ];
    }
}
