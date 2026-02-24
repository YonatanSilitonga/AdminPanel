<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class AdminMaintenanceMode
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if maintenance mode is enabled in settings
        $maintenanceEnabled = Cache::remember('app.maintenance_mode', 3600, function () {
            $setting = DB::table('app_settings')->where('key', 'maintenance_mode')->first();
            return $setting ? $setting->value === 'true' : false;
        });

        // Get auth guard with explicit type hint
        /** @var \Illuminate\Auth\SessionGuard $guard */
        $guard = auth('admin');
        
        /** @var \App\Models\Admin|null $admin */
        $admin = $guard->user();
        if ($maintenanceEnabled && !($admin && $admin->isSuperAdmin())) {
            return response()->view('errors.unavailable', [], 503);
        }

        return $next($request);
    }
}
