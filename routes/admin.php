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

    // ============ REVIEWS (Admin + Moderator) ============
    Route::middleware('admin.role:admin,moderator,super_admin')->group(function () {
        Route::get('reviews', [ReviewController::class, 'index'])->name('admin.reviews.index');
        Route::get('reviews/{review}', [ReviewController::class, 'show'])->name('admin.reviews.show');
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
    });

    // ============ USERS (Admin Role) ============
    Route::middleware('admin.role:admin,super_admin')->group(function () {
        Route::get('users', [UserController::class, 'index'])->name('admin.users.index');
        Route::get('users/{user}/activity', [UserController::class, 'showActivity'])->name('admin.users.activity');
        Route::patch('users/{user}/status', [UserController::class, 'toggleStatus'])->name('admin.users.toggle-status');
        Route::delete('users/{user}', [UserController::class, 'destroy'])->name('admin.users.destroy');
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
