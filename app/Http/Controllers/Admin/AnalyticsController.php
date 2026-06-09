<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\MongoDB\MongoDestination;
use App\Models\MongoDB\MongoEvent;
use App\Models\MongoDB\MongoReview;
use App\Models\MongoDB\MongoReport;
use App\Models\MongoDB\MongoRecommendation;
use Illuminate\Support\Facades\Cache;

class AnalyticsController extends BaseAdminController
{
    /**
     * Analytics overview dashboard.
     * Route: GET /admin/analytics
     */
    public function dashboard()
    {
        $summary = Cache::remember('analytics_dashboard_summary', now()->addMinutes(10), function () {
            return [
                'total_views'    => MongoRecommendation::count(),
                'total_searches' => MongoRecommendation::where('is_clicked', true)->count(),
                'active_users'   => User::where('is_active', true)->count(),
            ];
        });

        return view('admin.analytics.dashboard', compact('summary'));
    }

    /**
     * Destinations analytics.
     * Route: GET /admin/analytics/destinations
     */
    public function destinations()
    {
        $data = Cache::remember('analytics_destinations', now()->addMinutes(10), function () {
            return [
                'total'    => MongoDestination::count(),
                'active'   => MongoDestination::where('is_active', true)->count(),
                'featured' => MongoDestination::where('is_featured', true)->count(),
                'trending' => MongoDestination::where('is_trending', true)->count(),
            ];
        });

        return view('admin.analytics.destinations', compact('data'));
    }

    /**
     * Events analytics.
     * Route: GET /admin/analytics/events
     */
    public function events()
    {
        $data = Cache::remember('analytics_events', now()->addMinutes(10), function () {
            return [
                'total'  => MongoEvent::count(),
                'active' => MongoEvent::where('is_active', true)->count(),
            ];
        });

        return view('admin.analytics.events', compact('data'));
    }

    /**
     * Reports analytics.
     * Route: GET /admin/analytics/reports
     */
    public function reports()
    {
        $data = Cache::remember('analytics_reports', now()->addMinutes(10), function () {
            return [
                'total'    => MongoReport::count(),
                'pending'  => MongoReport::where('status', 'pending')->count(),
                'resolved' => MongoReport::where('status', 'resolved')->count(),
            ];
        });

        return view('admin.analytics.reports', compact('data'));
    }
}
