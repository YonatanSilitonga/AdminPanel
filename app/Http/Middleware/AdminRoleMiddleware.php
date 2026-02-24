<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminRoleMiddleware
{
    /**
     * Handle an incoming request.
     * 
     * Validates admin role against allowed roles from route parameters.
     * Usage: Route::middleware('admin.role:admin,super_admin')
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles  Allowed roles (from route middleware parameter)
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
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

        // Validate that admin has a role relationship loaded
        if (!$admin->relationLoaded('role') && $admin->role_id) {
            $admin->load('role');
        }

        // Check if role exists (null-safe access)
        $adminRole = $admin->role?->name ?? null;
        
        if (!$adminRole) {
            abort(500, 'Admin role not configured. Contact administrator.');
        }

        // Check if admin's role is in allowed roles
        if (!in_array($adminRole, $roles, true)) {
            abort(403, 'Insufficient privileges. This action is not permitted for your role.');
        }

        return $next($request);
    }
}
