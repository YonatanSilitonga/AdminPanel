<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Models\MongoDB\MongoEvent;
use App\Models\MongoDB\MongoDestination;
use App\Models\MongoDB\MongoReview;
use App\Models\MongoDB\MongoReport;
use App\Models\MongoDB\MongoBeritaPromosi;
use App\Models\MongoDB\MongoBudaya;
use App\Models\MongoDB\MongoFasilitasUmum;
use App\Models\MongoDB\MongoRecommendation;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends BaseAdminController
{
    /**
     * Show dashboard
     */
    public function index()
    {
        // Get statistics (Basic stats are faster)
        $stats = $this->getDashboardStats();

        // Get recent activity
        $recentActivity = $this->getRecentActivity(10);

        // Get pending items (use 0 or basic count)
        $pendingReviews = $stats['pending_reviews'] ?? 0;
        $pendingReports = $stats['pending_reports'] ?? 0;

        // Top 5 Destinasi (Real Data dari MongoDB)
        $topDestinations = MongoDestination::orderBy('average_rating', 'desc')->limit(5)->get();

        // Trip Statistics (Real Data dari RecommendationLog) - Cached for 10 minutes
        $tripStats = \Illuminate\Support\Facades\Cache::remember('admin.dashboard.trip_stats', now()->addMinutes(10), function() {
            $today = Carbon::now()->startOfDay();
            $weekStart = Carbon::now()->startOfWeek();
            $weekEnd = Carbon::now()->endOfWeek();
            $monthStart = Carbon::now()->startOfMonth();
            $monthEnd = Carbon::now()->endOfMonth();

            return [
                'today' => MongoRecommendation::where('created_at', '>=', $today)->count(),
                'week' => MongoRecommendation::whereBetween('created_at', [$weekStart, $weekEnd])->count(),
                'month' => MongoRecommendation::whereBetween('created_at', [$monthStart, $monthEnd])->count(),
            ];
        });

        // Chart data is now handled via AJAX to speed up page load
        $chartData = []; 

        return view('admin.dashboard.index', [
            'stats' => $stats,
            'recentActivity' => $recentActivity,
            'pendingReviews' => $pendingReviews,
            'pendingReports' => $pendingReports,
            'topDestinations' => $topDestinations,
            'tripStats' => $tripStats,
            'chartData' => $chartData,
        ]);
    }

    /**
     * Get monthly chart data (AJAX) with caching
     */
    public function getChartData()
    {
        $cacheKey = 'admin.dashboard.monthly_chart';
        
        $data = \Illuminate\Support\Facades\Cache::remember($cacheKey, now()->addHours(1), function () {
            return $this->getMonthlyChartData();
        });

        return response()->json($data);
    }

    /**
     * Calculate monthly statistics for charts
     */
    private function getMonthlyChartData()
    {
        $months = now()->subMonths(11)->monthsUntil(now());
        $data = [];

        foreach ($months as $month) {
            $startDate = $month->copy()->startOfMonth();
            $endDate = $month->copy()->endOfMonth();

            $data[] = [
                'month' => $month->format('M'),
                'destinations' => MongoDestination::whereBetween('created_at', [$startDate, $endDate])->count(),
                'events' => MongoEvent::whereBetween('created_at', [$startDate, $endDate])->count(),
                'reviews' => MongoReview::whereBetween('created_at', [$startDate, $endDate])->count(),
                'reports' => MongoReport::whereBetween('created_at', [$startDate, $endDate])->count(),
                'berita' => MongoBeritaPromosi::whereBetween('created_at', [$startDate, $endDate])->count(),
                'budaya' => MongoBudaya::whereBetween('created_at', [$startDate, $endDate])->count(),
                'fasilitas' => MongoFasilitasUmum::whereBetween('created_at', [$startDate, $endDate])->count(),
            ];
        }

        return $data;
    }

    /**
     * Get summary by role
     */
    private function getSummaryByRole()
    {
        $adminRole = $this->admin->role->name;

        if ($adminRole === 'super_admin') {
            return $this->getSuperAdminSummary();
        } elseif ($adminRole === 'admin') {
            return $this->getAdminSummary();
        } elseif ($adminRole === 'moderator') {
            return $this->getModeratorSummary();
        }

        return [];
    }

    /**
     * Super Admin summary
     */
    private function getSuperAdminSummary()
    {
        return [
            'total_admins' => DB::table('admins')->count(),
            'active_admins' => DB::table('admins')->where('is_active', true)->count(),
            'total_roles' => DB::table('roles')->count(),
        ];
    }

    /**
     * Admin summary
     */
    private function getAdminSummary()
    {
        return [
            'destinations_created' => DB::table('destinations')->where('admin_id', $this->admin->id)->count(),
            'events_created' => DB::table('events')->where('admin_id', $this->admin->id)->count(),
        ];
    }

    /**
     * Moderator summary
     */
    private function getModeratorSummary()
    {
        return [
            'reports_assigned' => DB::table('reports')
                ->where('assigned_to', $this->admin->id)
                ->count(),
            'reports_resolved' => DB::table('reports')
                ->where('assigned_to', $this->admin->id)
                ->where('status', 'resolved')
                ->count(),
        ];
    }
}
