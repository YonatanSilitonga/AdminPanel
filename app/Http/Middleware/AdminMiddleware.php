<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * DEPRECATED: This file previously contained multiple middleware classes.
 * 
 * Each middleware has been moved to its own file:
 * - EnsureAdminAuthenticated.php
 * - AdminRoleMiddleware.php
 * - AdminPermissionMiddleware.php
 * - AdminActivityLogMiddleware.php
 * - AdminMaintenanceMode.php
 * 
 * This class is kept for backward compatibility only.
 */
class AdminMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        return $next($request);
    }
}

