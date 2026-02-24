<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminPermissionMiddleware
{
    /**
     * Handle an incoming request.
     * 
     * Validates admin permission against required permission.
     * Usage: Route::middleware('admin.permission:delete.users')
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $permission  Required permission slug
     */
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        // Get auth guard with explicit type hint
        /** @var \Illuminate\Auth\SessionGuard $guard */
        $guard = auth('admin');
        
        // Check if admin is authenticated
        if (!$guard->check()) {
            return redirect()->route('admin.login')
                ->with('error', 'Authentication required');
        }

        /** @var \App\Models\Admin|null $admin */
        $admin = $guard->user();

        // Double-check admin exists
        if (!$admin) {
            $guard->logout();
            return redirect()->route('admin.login')
                ->with('error', 'Session expired. Please login again');
        }

        // Check if admin is active
        if (!$admin->is_active) {
            $guard->logout();
            return redirect()->route('admin.login')
                ->with('error', 'Your account has been deactivated');
        }

        // Validate permission (with null-safe checks)
        if (!$admin->hasPermission($permission)) {
            abort(403, "Permission denied: {$permission}");
        }

        return $next($request);
    }
}
