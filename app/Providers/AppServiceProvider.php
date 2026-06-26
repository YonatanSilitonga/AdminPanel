<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Cache;
use App\Models\MongoDB\MongoReview as Review;
use App\Models\MongoDB\MongoReport as Report;
use App\Models\AdminActivityLog;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        require_once app_path('helpers.php');
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->registerViewComposers();

        // Apply default language locale from settings
        try {
            $lang = \App\Models\AppSetting::get('default_language', 'id');
            \Illuminate\Support\Facades\App::setLocale($lang);
        } catch (\Exception $e) {
            // Silence exceptions in case DB connection is not initialized during console/migration runs
        }
    }

    /**
     * Register view composers for admin panel layout data
     */
    private function registerViewComposers(): void
    {
        // Ensure $errors is always available in ALL admin views
        // Blade @error directive & $errors->any() depend on it
        View::composer('admin.*', function ($view) {
            if (!$view->offsetExists('errors')) {
                $view->with('errors', session()->get('errors', new \Illuminate\Support\ViewErrorBag()));
            }
        });

        // Compose data for admin layout with fallback for empty data
        View::composer(['admin.layouts.app', 'admin.layouts.navbar', 'admin.layouts.sidebar'], function ($view) {
            // Ensure $errors is always available — Blade @error directive & $errors->any() depend on it
            if (!$view->offsetExists('errors')) {
                $view->with('errors', session()->get('errors', new \Illuminate\Support\ViewErrorBag()));
            }

            try {
                // Cache navbar data for 5 minutes to avoid N+1 queries
                $cacheKey = 'admin.navbar.counts';
                $cacheMinutes = 5;

                $navbarData = Cache::remember($cacheKey, now()->addMinutes($cacheMinutes), function () {
                    $pendingReviews = (int) (Review::where('status', 'pending')->count() ?? 0);
                    $pendingReports = (int) (Report::where('status', 'pending')->count() ?? 0);
                    
                    return [
                        'pendingReviews' => $pendingReviews,
                        'pendingReports' => $pendingReports,
                        'pendingNotificationsCount' => $pendingReviews + $pendingReports,
                        'approvedReviews' => (int) (Review::where('status', 'approved')->count() ?? 0),
                    ];
                });

                $view->with($navbarData);
            } catch (\Exception $e) {
                // Fallback if database query fails
                $view->with([
                    'pendingReviews' => 0,
                    'pendingReports' => 0,
                    'pendingNotificationsCount' => 0,
                    'approvedReviews' => 0,
                ]);
            }
        });

        // Compose data for dashboard with empty state handling
        View::composer('admin.dashboard.index', function ($view) {
            try {
                $cacheKey = 'admin.dashboard.data';
                $cacheMinutes = 5;

                $dashboardData = Cache::remember($cacheKey, now()->addMinutes($cacheMinutes), function () {
                    return [
                        'pendingReviews' => (int) (Review::where('status', 'pending')->count() ?? 0),
                        'pendingReports' => (int) (Report::where('status', 'pending')->count() ?? 0),
                        'totalReports' => (int) (Report::count() ?? 0),
                        'totalReviews' => (int) (Review::count() ?? 0),
                    ];
                });

                $view->with($dashboardData);
            } catch (\Exception $e) {
                // Fallback for empty state
                $view->with([
                    'pendingReviews' => 0,
                    'pendingReports' => 0,
                    'totalReports' => 0,
                    'totalReviews' => 0,
                ]);
            }
        });

        // Compose recent activities for sidebar with caching
        View::composer('admin.layouts.sidebar', function ($view) {
            try {
                // Cache recent activities for 2 minutes to reduce DB load
                $cacheKey = 'admin.sidebar.activities';
                
                $recentActivities = Cache::remember($cacheKey, now()->addMinutes(2), function () {
                    return AdminActivityLog::with('admin')
                        ->latest()
                        ->limit(5)
                        ->get(['id', 'admin_id', 'action', 'description', 'created_at']);
                }) ?? collect();

                $view->with('recentActivities', $recentActivities);
            } catch (\Exception $e) {
                // Fallback to empty collection
                $view->with('recentActivities', collect());
            }
        });
    }
}

