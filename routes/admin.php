<?php

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
use App\Http\Controllers\Admin\FasilitasUmumController;
use App\Http\Controllers\Admin\BudayaController;
use App\Http\Controllers\Admin\BeritaPromosiController;

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
Route::middleware(['auth:admin', 'admin.error-handler'])->prefix('admin')->group(function () {

    // ============ DASHBOARD ============
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('/dashboard/chart-data', [DashboardController::class, 'getChartData'])->name('admin.dashboard.chart-data');

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

    // ============ CONTENT MANAGEMENT (Fasilitas, Budaya, Berita) ============
    Route::middleware('admin.role:admin,super_admin')->group(function () {
        // Fasilitas Umum
        Route::get('fasilitas_umum', [FasilitasUmumController::class, 'index'])
            ->name('admin.fasilitas_umum.index');
        Route::post('fasilitas_umum', [FasilitasUmumController::class, 'store'])
            ->name('admin.fasilitas_umum.store');
        Route::get('fasilitas_umum/{id}/edit', [FasilitasUmumController::class, 'edit'])
            ->name('admin.fasilitas_umum.edit');
        Route::put('fasilitas_umum/{id}', [FasilitasUmumController::class, 'update'])
            ->name('admin.fasilitas_umum.update');
        Route::delete('fasilitas_umum/{id}', [FasilitasUmumController::class, 'destroy'])
            ->name('admin.fasilitas_umum.destroy');
        Route::patch('fasilitas_umum/{id}/status', [FasilitasUmumController::class, 'toggleStatus'])
            ->name('admin.fasilitas_umum.toggle-status');

        // Budaya
        Route::get('budaya', [BudayaController::class, 'index'])
            ->name('admin.budaya.index');
        Route::post('budaya', [BudayaController::class, 'store'])
            ->name('admin.budaya.store');
        Route::get('budaya/{id}/edit', [BudayaController::class, 'edit'])
            ->name('admin.budaya.edit');
        Route::put('budaya/{id}', [BudayaController::class, 'update'])
            ->name('admin.budaya.update');
        Route::delete('budaya/{id}', [BudayaController::class, 'destroy'])
            ->name('admin.budaya.destroy');
        Route::patch('budaya/{id}/status', [BudayaController::class, 'toggleStatus'])
            ->name('admin.budaya.toggle-status');

        // Berita Promosi
        Route::get('berita_promosi', [BeritaPromosiController::class, 'index'])
            ->name('admin.berita_promosi.index');
        Route::post('berita_promosi', [BeritaPromosiController::class, 'store'])
            ->name('admin.berita_promosi.store');
        Route::get('berita_promosi/{id}/edit', [BeritaPromosiController::class, 'edit'])
            ->name('admin.berita_promosi.edit');
        Route::put('berita_promosi/{id}', [BeritaPromosiController::class, 'update'])
            ->name('admin.berita_promosi.update');
        Route::delete('berita_promosi/{id}', [BeritaPromosiController::class, 'destroy'])
            ->name('admin.berita_promosi.destroy');
    });

    // ============ REVIEWS (Admin + Moderator) ============
    Route::middleware('admin.role:admin,moderator,super_admin')->group(function () {
        Route::get('reviews', [ReviewController::class, 'index'])->name('admin.reviews.index');
        Route::match(['get', 'post'], 'reviews/analytics/print', [ReviewController::class, 'printAnalytics'])->name('admin.reviews.print-analytics');
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
        Route::get('reports/{report}', [ReportController::class, 'show'])->name('admin.reports.show');
        Route::patch('reports/{report}/resolve', [ReportController::class, 'resolve'])->name('admin.reports.resolve');
        Route::post('reports/{report}/action', [ReportController::class, 'takeAction'])->name('admin.reports.action');
        Route::patch('reports/{report}/flag', [ReportController::class, 'flag'])->name('admin.reports.flag');
        Route::delete('reports/{report}', [ReportController::class, 'destroy'])->name('admin.reports.destroy');
    });

    // ============ USERS (Admin Role) ============
    Route::middleware('admin.role:admin,super_admin')->group(function () {
        Route::get('users', [UserController::class, 'index'])->name('admin.users.index');
        Route::get('users/{user}/activity', [UserController::class, 'showActivity'])->name('admin.users.activity');
        Route::patch('users/{user}/status', [UserController::class, 'toggleStatus'])->name('admin.users.toggle-status');
    });

    // ============ RECOMMENDATION LOGS (Admin Role) ============
    Route::middleware('admin.role:admin,super_admin')->group(function () {
        Route::get('recommendations', [RecommendationLogController::class, 'index'])
            ->name('admin.recommendations.index');
        Route::get('recommendations/export', [RecommendationLogController::class, 'export'])
            ->name('admin.recommendations.export');
        Route::get('recommendations/{log}', [RecommendationLogController::class, 'show'])
            ->name('admin.recommendations.show');
    });

    // ============ CHATBOT LOGS (Admin + Moderator) ============
    Route::middleware('admin.role:admin,moderator,super_admin')->group(function () {
        Route::get('chatbot-logs', [ChatbotLogController::class, 'index'])
            ->name('admin.chatbot-logs.index');
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
    Route::put('profile/password', [ProfileController::class, 'updatePassword'])
        ->name('admin.profile.password.update');
    Route::post('logout', [AdminAuthController::class, 'logout'])->name('admin.logout');

});

// Error pages
Route::get('/admin/permission-denied', function () {
    return response()->view('admin.errors.permission-denied', [], 403);
})->name('admin.permission-denied');
