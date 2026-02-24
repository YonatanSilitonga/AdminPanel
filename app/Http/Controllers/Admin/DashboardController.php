<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\DB;

class DashboardController extends BaseAdminController
{
    /**
     * Show dashboard
     */
    public function index()
    {
        // Get statistics
        $stats = $this->getDashboardStats();

        // Get recent activity
        $recentActivity = $this->getRecentActivity(10);

        // Get featured destinations
        $featuredDestinations = DB::table('destinations')
            ->where('is_featured', true)
            ->where('is_active', true)
            ->orderBy('rating', 'desc')
            ->limit(5)
            ->get();

        // Get pending items
        $pendingReviews = DB::table('reviews')
            ->where('status', 'pending')
            ->count();

        $pendingReports = DB::table('reports')
            ->where('status', 'pending')
            ->count();

        // Get chart data
        $chartData = $this->getMonthlyChartData();

        return view('admin.dashboard.index', [
            'stats' => $stats,
            'recentActivity' => $recentActivity,
            'featuredDestinations' => $featuredDestinations,
            'pendingReviews' => $pendingReviews,
            'pendingReports' => $pendingReports,
            'chartData' => $chartData,
        ]);
    }

    /**
     * Get monthly chart data (AJAX)
     */
    public function getChartData()
    {
        return response()->json($this->getMonthlyChartData());
    }

    /**
     * Calculate monthly statistics for charts
     */
    private function getMonthlyChartData()
    {
        $months = now()->subMonths(11)->monthsUntil(now()->addMonth());
        $data = [];

        foreach ($months as $month) {
            $startDate = $month->startOfMonth();
            $endDate = $month->endOfMonth();

            $data[] = [
                'month' => $month->format('M'),
                'destinations' => DB::table('destinations')
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->count(),
                'events' => DB::table('events')
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->count(),
                'reviews' => DB::table('reviews')
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->count(),
                'reports' => DB::table('reports')
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->count(),
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
