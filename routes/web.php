<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DestinationController;
use App\Http\Controllers\Admin\DestinationGalleryController;
use App\Http\Controllers\Admin\EventController;
use App\Http\Controllers\Admin\ReviewController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\RecommendationLogController;
use App\Http\Controllers\Admin\ChatbotLogController;
use App\Http\Controllers\Admin\AnalyticsController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\AuditLogController;
use App\Http\Controllers\Admin\FacilityController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\CarouselBannerController;
use App\Http\Controllers\Admin\FasilitasUmumController;
use App\Http\Controllers\Admin\BudayaController;
use App\Http\Controllers\Admin\BeritaPromosiController;
use App\Http\Controllers\Admin\TrendingDestinationController;

Route::get('/', function () {
    return redirect()->route('admin.login');
});

// Redirect /dashboard to /admin/dashboard
Route::get('/dashboard', function () {
    return redirect()->route('admin.dashboard');
});

// Redirect semua rute sidebar utama tanpa prefix /admin
Route::middleware('guest')->group(function () {
    $sidebarRoutes = [
        'destinations', 'trending-destinations', 'events', 'carousel-banners',
        'fasilitas-umum', 'berita-promosi', 'budaya', 'users',
        'chatbot-logs', 'recommendations', 'reviews', 'reports', 'settings', 'profile'
    ];

    foreach ($sidebarRoutes as $route) {
        Route::get("/$route", function () use ($route) {
            return redirect("/admin/$route");
        });
    }
});

// PUBLIC ROUTES (No auth required)
Route::prefix('admin')->group(function () {
    Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('admin.login');
    Route::post('/login', [AdminAuthController::class, 'login'])->name('admin.login.post');
    Route::get('/forgot-password', [AdminAuthController::class, 'showForgotForm'])->name('admin.forgot-password');
    Route::post('/forgot-password', [AdminAuthController::class, 'sendResetLink'])->name('admin.forgot-password.post');
    Route::get('/reset-password/{token}', [AdminAuthController::class, 'showResetForm'])->name('admin.reset-password');
    Route::post('/reset-password', [AdminAuthController::class, 'resetPassword'])->name('admin.reset-password.post');
});

// PROTECTED ROUTES (Require auth:admin)
Route::middleware('auth:admin')->prefix('admin')->group(function () {

    // ============ DASHBOARD & GLOBAL SEARCH ============
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('/dashboard/chart-data', [DashboardController::class, 'getChartData'])->name('admin.dashboard.chart-data');
    Route::get('/search', [\App\Http\Controllers\Admin\GlobalSearchController::class, 'index'])->name('admin.search');

    // ============ DESTINATIONS (Admin Role) ============
    Route::middleware('admin.role:admin,super_admin')->group(function () {
        Route::resource('destinations', DestinationController::class, [
            'names' => [
                'index' => 'admin.destinations.index',
                'create' => 'admin.destinations.create',
                'store' => 'admin.destinations.store',
                'edit' => 'admin.destinations.edit',
                'update' => 'admin.destinations.update',
                'destroy' => 'admin.destinations.destroy',
            ]
        ]);

        // Destination additional routes
        Route::patch('destinations/{destination}/featured', [DestinationController::class, 'toggleFeatured'])
            ->name('admin.destinations.toggle-featured');
        Route::patch('destinations/{destination}/status', [DestinationController::class, 'toggleStatus'])
            ->name('admin.destinations.toggle-status');

        // Gallery management
        Route::post('destinations/{destination}/gallery', [DestinationGalleryController::class, 'store'])
            ->name('admin.gallery.store');
        Route::delete('destinations/{destination}/gallery/{gallery}', [DestinationGalleryController::class, 'destroy'])
            ->name('admin.gallery.destroy');
        Route::patch('destinations/{destination}/gallery/order', [DestinationGalleryController::class, 'updateOrder'])
            ->name('admin.gallery.order');

        // Facility management
        Route::post('destinations/{destination}/facilities', [FacilityController::class, 'store'])
            ->name('admin.facilities.store');
        Route::delete('destinations/{destination}/facilities/{facility}', [FacilityController::class, 'destroy'])
            ->name('admin.facilities.destroy');

        // Trending Destinations (Integrated into DestinationController)
        Route::post('trending-destinations/mode', [DestinationController::class, 'updateTrendingMode'])
            ->name('admin.trending.update-mode');
        Route::post('trending-destinations/order', [DestinationController::class, 'updateTrendingOrder'])
            ->name('admin.trending.update-order');
        Route::post('trending-destinations/add', [DestinationController::class, 'addTrendingDestination'])
            ->name('admin.trending.add');
        Route::delete('trending-destinations/remove/{id}', [DestinationController::class, 'removeTrendingDestination'])
            ->name('admin.trending.remove');
        Route::post('trending-destinations/reset', [DestinationController::class, 'resetTrendingToAutomatic'])
            ->name('admin.trending.reset');
        Route::get('trending-destinations/search', [DestinationController::class, 'searchTrendingDestinations'])
            ->name('admin.trending.search');
    });

    // ============ EVENTS (Admin Role) ============
    Route::middleware('admin.role:admin,super_admin')->group(function () {
        Route::resource('events', EventController::class, [
            'names' => [
                'index' => 'admin.events.index',
                'create' => 'admin.events.create',
                'store' => 'admin.events.store',
                'edit' => 'admin.events.edit',
                'update' => 'admin.events.update',
                'destroy' => 'admin.events.destroy',
            ]
        ]);

        Route::patch('events/{event}/status', [EventController::class, 'toggleStatus'])
            ->name('admin.events.toggle-status');
    });

    // ============ CAROUSEL & BANNER ============
    Route::middleware('admin.role:admin,super_admin')->group(function () {
        Route::patch('carousel-banners/order', [CarouselBannerController::class, 'updateOrder'])->name('admin.carousel_banners.order');
        Route::patch('carousel-banners/{id}/toggle-active', [CarouselBannerController::class, 'toggleActive'])->name('admin.carousel_banners.toggle-active');
        Route::get('carousel-banners/contents-by-category', [CarouselBannerController::class, 'getContentsByCategory'])->name('admin.carousel_banners.contents-by-category');
        Route::resource('carousel-banners', CarouselBannerController::class, [
            'names' => [
                'index' => 'admin.carousel_banners.index',
                'create' => 'admin.carousel_banners.create',
                'store' => 'admin.carousel_banners.store',
                'edit' => 'admin.carousel_banners.edit',
                'update' => 'admin.carousel_banners.update',
                'destroy' => 'admin.carousel_banners.destroy',
            ]
        ])->except(['show', 'create']);
        Route::delete('carousel-banners/{id}/force-delete', [CarouselBannerController::class, 'forceDestroy'])
            ->name('admin.carousel_banners.force-destroy');
    });

    // ============ FASILITAS UMUM ============
    Route::middleware('admin.role:admin,super_admin')->group(function () {
        Route::patch('fasilitas-umum/{id}/toggle-status', [FasilitasUmumController::class, 'toggleStatus'])
            ->name('admin.fasilitas_umum.toggle-status');
        Route::resource('fasilitas-umum', FasilitasUmumController::class, [
            'names' => [
                'index' => 'admin.fasilitas_umum.index',
                'store' => 'admin.fasilitas_umum.store',
                'edit' => 'admin.fasilitas_umum.edit',
                'update' => 'admin.fasilitas_umum.update',
                'destroy' => 'admin.fasilitas_umum.destroy',
            ]
        ])->only(['index', 'store', 'edit', 'update', 'destroy']);
    });

    // ============ BUDAYA & HERITAGE ============
    Route::middleware('admin.role:admin,super_admin')->group(function () {
        Route::patch('budaya/{budaya}/status', [BudayaController::class, 'toggleStatus'])->name('admin.budaya.toggle-status');
        Route::resource('budaya', BudayaController::class, [
            'names' => [
                'index' => 'admin.budaya.index',
                'store' => 'admin.budaya.store',
                'edit' => 'admin.budaya.edit',
                'update' => 'admin.budaya.update',
                'destroy' => 'admin.budaya.destroy',
            ]
        ])->only(['index', 'store', 'edit', 'update', 'destroy']);
    });

    // ============ BERITA & PROMOSI ============
    Route::middleware('admin.role:admin,super_admin')->group(function () {
        Route::resource('berita-promosi', BeritaPromosiController::class, [
            'names' => [
                'index' => 'admin.berita_promosi.index',
                'store' => 'admin.berita_promosi.store',
                'edit' => 'admin.berita_promosi.edit',
                'update' => 'admin.berita_promosi.update',
                'destroy' => 'admin.berita_promosi.destroy',
            ]
        ])->except(['create', 'show']);
    });

    // ============ REVIEWS (Admin + Moderator) ============
    Route::middleware('admin.role:admin,moderator,super_admin')->group(function () {
        Route::get('reviews', [ReviewController::class, 'index'])->name('admin.reviews.index');
        Route::get('reviews/export', [ReviewController::class, 'export'])->name('admin.reviews.export');
        Route::get('reviews/{review}', [ReviewController::class, 'show'])->name('admin.reviews.show');
        Route::post('reviews/{review}/analyze', [ReviewController::class, 'analyze'])->name('admin.reviews.analyze');
        Route::post('reviews/analyze-batch', [ReviewController::class, 'analyzeBatch'])->name('admin.reviews.analyze-batch');
        Route::patch('reviews/{review}/approve', [ReviewController::class, 'approve'])->name('admin.reviews.approve');
        Route::patch('reviews/{review}/reject', [ReviewController::class, 'reject'])->name('admin.reviews.reject');
        Route::delete('reviews/{review}', [ReviewController::class, 'destroy'])->name('admin.reviews.destroy');
    });

    // ============ REPORTS (Moderator + Admin) ============
    Route::middleware('admin.role:moderator,admin,super_admin')->group(function () {
        Route::get('reports', [ReportController::class, 'index'])->name('admin.reports.index');
        Route::get('reports/export', [ReportController::class, 'export'])->name('admin.reports.export');
        Route::get('reports/{report}', [ReportController::class, 'show'])->name('admin.reports.show');
        Route::patch('reports/{report}/resolve', [ReportController::class, 'resolve'])->name('admin.reports.resolve');
        Route::post('reports/{report}/action', [ReportController::class, 'takeAction'])->name('admin.reports.action');
        Route::patch('reports/{report}/flag', [ReportController::class, 'flag'])->name('admin.reports.flag');
        Route::post('reports/{report}/status', [ReportController::class, 'updateStatus'])->name('admin.reports.status');
        Route::delete('reports/{report}', [ReportController::class, 'destroy'])->name('admin.reports.destroy');
    });

    // ============ USERS (Admin Role) ============
    Route::middleware('admin.role:admin,super_admin')->group(function () {
        Route::get('users', [UserController::class, 'index'])->name('admin.users.index');
        Route::get('users/export', [UserController::class, 'export'])->name('admin.users.export');
        Route::get('users/{user}/activity', [UserController::class, 'showActivity'])->name('admin.users.activity');
        Route::patch('users/{user}/status', [UserController::class, 'toggleStatus'])->name('admin.users.toggle-status');
    });

    // ============ RECOMMENDATION LOGS (Admin Role) ============
    Route::middleware('admin.role:admin,super_admin')->group(function () {
        Route::get('recommendations', [RecommendationLogController::class, 'index'])
            ->name('admin.recommendations.index');
        Route::get('recommendations/{log}', [RecommendationLogController::class, 'show'])
            ->name('admin.recommendations.show');
        Route::get('recommendations/export', [RecommendationLogController::class, 'export'])
            ->name('admin.recommendations.export');
    });

    // ============ CHATBOT LOGS (Admin + Moderator) ============
    Route::middleware('admin.role:admin,moderator,super_admin')->group(function () {
        Route::get('chatbot-logs', [ChatbotLogController::class, 'index'])
            ->name('admin.chatbot-logs.index');
        Route::get('chatbot-logs/export', [ChatbotLogController::class, 'export'])
            ->name('admin.chatbot-logs.export');
        Route::get('chatbot-logs/{log}', [ChatbotLogController::class, 'show'])
            ->name('admin.chatbot-logs.show');
        Route::patch('chatbot-logs/{log}/flag', [ChatbotLogController::class, 'flag'])
            ->name('admin.chatbot-logs.flag');
    });

    // ============ ANALYTICS (Admin Role) ============
    Route::middleware('admin.role:admin,super_admin')->group(function () {
        Route::get('analytics', [AnalyticsController::class, 'dashboard'])
            ->name('admin.analytics.dashboard');
        Route::get('analytics/destinations', [AnalyticsController::class, 'destinations'])
            ->name('admin.analytics.destinations');
        Route::get('analytics/events', [AnalyticsController::class, 'events'])
            ->name('admin.analytics.events');
        Route::get('analytics/reports', [AnalyticsController::class, 'reports'])
            ->name('admin.analytics.reports');
    });

    // ============ SETTINGS (Super Admin Only) ============
    Route::middleware('admin.role:super_admin')->group(function () {
        // General Settings
        Route::get('settings/general', [SettingsController::class, 'editGeneral'])
            ->name('admin.settings.general');
        Route::put('settings/general', [SettingsController::class, 'updateGeneral'])
            ->name('admin.settings.general.update');

        // API Keys
        Route::get('settings/api-keys', [SettingsController::class, 'editApiKeys'])
            ->name('admin.settings.api-keys');
        Route::put('settings/api-keys', [SettingsController::class, 'updateApiKeys'])
            ->name('admin.settings.api-keys.update');

        // AI Configuration
        Route::get('settings/ai-config', [SettingsController::class, 'editAiConfig'])
            ->name('admin.settings.ai-config');
        Route::put('settings/ai-config', [SettingsController::class, 'updateAiConfig'])
            ->name('admin.settings.ai-config.update');

        // Maintenance Mode
        Route::patch('settings/maintenance', [SettingsController::class, 'toggleMaintenance'])
            ->name('admin.settings.maintenance');

        // Audit Logs
        Route::get('settings/audit-logs', [AuditLogController::class, 'index'])
            ->name('admin.settings.audit-logs');
        Route::get('settings/audit-logs/{log}', [AuditLogController::class, 'show'])
            ->name('admin.settings.audit-logs.show');
    });

    // ============ PROFILE (All authenticated) ============
    Route::get('profile', [ProfileController::class, 'edit'])->name('admin.profile');
    Route::put('profile', [ProfileController::class, 'update'])->name('admin.profile.update');
    Route::put('profile/password', [ProfileController::class, 'updatePassword'])
        ->name('admin.profile.password.update');
    Route::post('logout', [AdminAuthController::class, 'logout'])->name('admin.logout');

});

// Error pages
Route::get('/admin/permission-denied', function () {
    return response()->view('admin.errors.permission-denied', [], 403);
})->name('admin.permission-denied');
