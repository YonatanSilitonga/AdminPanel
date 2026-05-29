<?php

if (!function_exists('admin_error')) {
    /**
     * Render admin error page
     */
    function admin_error(int $code, string $message = '', array $data = [])
    {
        $errorMessages = [
            400 => 'Bad Request',
            401 => 'Unauthorized',
            403 => 'Access Denied',
            404 => 'Not Found',
            500 => 'Server Error',
        ];

        $viewName = "admin.errors.{$code}";

        return response()->view($viewName, array_merge([
            'message' => $message ?: $errorMessages[$code] ?? 'Error',
        ], $data), $code);
    }
}

if (!function_exists('admin_success')) {
    /**
     * Flash success message
     */
    function admin_success(string $message, array $data = [])
    {
        return back()->with(array_merge([
            'success' => true,
            'message' => $message,
        ], $data));
    }
}

if (!function_exists('admin_error_flash')) {
    /**
     * Flash error message
     */
    function admin_error_flash(string $message, array $data = [])
    {
        return back()->with(array_merge([
            'error' => true,
            'message' => $message,
        ], $data));
    }
}

if (!function_exists('admin_validation_error')) {
    /**
     * Validation error response
     */
    function admin_validation_error(array $errors = [])
    {
        return back()
            ->withInput()
            ->withErrors($errors);
    }
}

if (!function_exists('is_admin_api_request')) {
    /**
     * Check if request expects JSON
     */
    function is_admin_api_request()
    {
        $request = app('request');
        return $request->expectsJson() || $request->ajax();
    }
}

if (!function_exists('admin_can')) {
    /**
     * Check admin permission
     */
    function admin_can(string $ability, $resource = null)
    {
        $admin = auth('admin')->user();
        
        if (!$admin) {
            return false;
        }

        return $admin->can($ability, $resource);
    }
}

if (!function_exists('admin_cannot')) {
    /**
     * Check admin cannot perform action
     */
    function admin_cannot(string $ability, $resource = null)
    {
        return !admin_can($ability, $resource);
    }
}

if (!function_exists('admin_authorize')) {
    /**
     * Authorize admin action
     */
    function admin_authorize(string $ability, $resource = null, string $message = 'Not authorized')
    {
        if (!admin_can($ability, $resource)) {
            abort(403, $message);
        }
    }
}

if (!function_exists('admin_is_role')) {
    /**
     * Check if admin has specific role
     */
    function admin_is_role(string $role)
    {
        $admin = auth('admin')->user();
        
        if (!$admin) {
            return false;
        }

        return $admin->role?->name === $role;
    }
}

if (!function_exists('admin_is_roles')) {
    /**
     * Check if admin has any of the roles
     */
    function admin_is_roles(...$roles)
    {
        $admin = auth('admin')->user();
        
        if (!$admin) {
            return false;
        }

        $adminRole = $admin->role?->name;
        
        return in_array($adminRole, $roles, true);
    }
}

if (!function_exists('admin_log_action')) {
    /**
     * Log admin action
     */
    function admin_log_action(string $action, string $module, array $details = [])
    {
        $admin = auth('admin')->user();
        
        if (!$admin) {
            return;
        }

        \App\Models\AdminActivityLog::create([
            'admin_id' => $admin->id,
            'action' => $action,
            'module' => $module,
            'details' => json_encode($details),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}

if (!function_exists('admin_json_error')) {
    /**
     * Return JSON error response
     */
    function admin_json_error(string $message, int $code = 400, array $data = [])
    {
        return response()->json(array_merge([
            'success' => false,
            'message' => $message,
            'code' => $code,
        ], $data), $code);
    }
}

if (!function_exists('admin_json_success')) {
    /**
     * Return JSON success response
     */
    function admin_json_success(string $message, array $data = [], int $code = 200)
    {
        return response()->json(array_merge([
            'success' => true,
            'message' => $message,
            'code' => $code,
        ], $data), $code);
    }
}
