<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdminAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth('admin')->check()) {
            return redirect()->route('admin.login')->with('error', 'Please login first');
        }

        // Check if admin is active
        /** @var \App\Models\Admin|null $admin */
        $admin = auth('admin')->user();
        if ($admin && !$admin->isActive()) {
            /** @var \Illuminate\Auth\SessionGuard $guard */
            $guard = auth('admin');
            $guard->logout();
            return redirect()->route('admin.login')->with('error', 'Your account is inactive');
        }

        return $next($request);
    }
}
