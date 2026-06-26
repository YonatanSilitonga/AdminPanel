<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Models\AppSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SettingsController extends BaseAdminController
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Display general settings form.
     */
    public function editGeneral()
    {
        $settings = AppSetting::getAllSettings();
        return view('admin.settings.general', compact('settings'));
    }

    /**
     * Update general settings.
     */
    public function updateGeneral(Request $request)
    {
        $validated = $request->validate([
            'logo' => 'nullable|image|mimes:png,jpg,jpeg,svg|max:2048',
            'favicon' => 'nullable|image|mimes:png,jpg,jpeg,ico|max:512',
            'primary_color' => 'required|string|max:7',
            'secondary_color' => 'required|string|max:7',
            'default_language' => 'required|in:id,en',
            'enable_reviews' => 'boolean',
            'enable_reports' => 'boolean',
            'moderate_reviews' => 'boolean',
            'notify_new_review' => 'boolean',
            'notify_new_report' => 'boolean',
            'notify_new_user' => 'boolean',
            'notify_system_error' => 'boolean',
            'dark_mode' => 'boolean',
        ]);

        try {
            $oldValues = AppSetting::getAllSettings();
            
            // Handle logo upload
            if ($request->hasFile('logo')) {
                $oldLogo = AppSetting::get('logo');
                $logoPath = $this->uploadFile($request->file('logo'), 'settings', [
                    'mimes' => ['image/png', 'image/jpeg', 'image/svg+xml', 'image/svg', 'image/webp'],
                    'max_size' => 2
                ]);
                if ($logoPath) {
                    if ($oldLogo) {
                        $this->deleteFile($oldLogo);
                    }
                    AppSetting::set('logo', $logoPath);
                }
            }

            // Handle favicon upload
            if ($request->hasFile('favicon')) {
                $oldFavicon = AppSetting::get('favicon');
                $faviconPath = $this->uploadFile($request->file('favicon'), 'settings', [
                    'mimes' => ['image/png', 'image/jpeg', 'image/x-icon', 'image/vnd.microsoft.icon', 'image/webp', 'image/ico'],
                    'max_size' => 0.5
                ]);
                if ($faviconPath) {
                    if ($oldFavicon) {
                        $this->deleteFile($oldFavicon);
                    }
                    AppSetting::set('favicon', $faviconPath);
                }
            }

            AppSetting::set('primary_color', $validated['primary_color']);
            AppSetting::set('secondary_color', $validated['secondary_color']);
            AppSetting::set('default_language', $validated['default_language']);
            
            AppSetting::set('enable_reviews', $request->has('enable_reviews'), 'boolean');
            AppSetting::set('enable_reports', $request->has('enable_reports'), 'boolean');
            AppSetting::set('moderate_reviews', $request->has('moderate_reviews'), 'boolean');
            
            AppSetting::set('notify_new_review', $request->has('notify_new_review'), 'boolean');
            AppSetting::set('notify_new_report', $request->has('notify_new_report'), 'boolean');
            AppSetting::set('notify_new_user', $request->has('notify_new_user'), 'boolean');
            AppSetting::set('notify_system_error', $request->has('notify_system_error'), 'boolean');
            AppSetting::set('dark_mode', $request->has('dark_mode'), 'boolean');

            // ── Clear navbar counts cache so notification settings apply immediately ──
            \Illuminate\Support\Facades\Cache::forget('admin.navbar.counts');

            // ── Apply locale immediately for this request ──────────────────────────
            \Illuminate\Support\Facades\App::setLocale($validated['default_language']);

            $newValues = AppSetting::getAllSettings();
            $this->logActivity('update_general_settings', 'settings', 'general', $oldValues, $newValues);

            return redirect()->back()->with('success', 'Pengaturan berhasil diperbarui.');
        } catch (\Exception $e) {
            Log::error('Error updating general settings: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal memperbarui pengaturan: ' . $e->getMessage());
        }
    }

    /**
     * Show API keys settings page (Stub).
     */
    public function editApiKeys()
    {
        return redirect()->back()->with('info', 'Fitur Pengaturan API Keys akan segera hadir.');
    }

    /**
     * Update API keys (Stub).
     */
    public function updateApiKeys(Request $request)
    {
        return redirect()->back()->with('info', 'Fitur Pengaturan API Keys akan segera hadir.');
    }

    /**
     * Show AI configuration settings page (Stub).
     */
    public function editAiConfig()
    {
        return redirect()->back()->with('info', 'Fitur Konfigurasi AI akan segera hadir.');
    }

    /**
     * Update AI configuration (Stub).
     */
    public function updateAiConfig(Request $request)
    {
        return redirect()->back()->with('info', 'Fitur Konfigurasi AI akan segera hadir.');
    }

    /**
     * Toggle Maintenance mode.
     */
    public function toggleMaintenance(Request $request)
    {
        try {
            $enabled = $request->has('enabled') || $request->input('enabled') === '1' || $request->input('enabled') === 'true';
            
            $oldValues = AppSetting::getAllSettings();
            
            // Toggle maintenance mode setting
            AppSetting::set('maintenance_mode', $enabled, 'boolean');
            
            $message = $request->input('maintenance_message', 'Sistem sedang dalam pemeliharaan.');
            AppSetting::set('maintenance_message', $message, 'string');
            
            $statusStr = $enabled ? 'aktif' : 'nonaktif';
            
            // Try sending email if notify setting is enabled
            if (AppSetting::get('notify_system_error', false)) {
                try {
                    $adminEmail = config('mail.from.address', 'admin@toba.id');
                    \Illuminate\Support\Facades\Mail::to($adminEmail)->send(new \App\Mail\MaintenanceMode($enabled, $message));
                } catch (\Exception $mailEx) {
                    Log::error('Failed to send Maintenance mode email: ' . $mailEx->getMessage());
                }
            }

            $newValues = AppSetting::getAllSettings();
            $this->logActivity('toggle_maintenance_mode', 'settings', 'maintenance', $oldValues, $newValues);

            return redirect()->back()->with('success', "Mode Pemeliharaan berhasil diubah menjadi {$statusStr}.");
        } catch (\Exception $e) {
            Log::error('Error toggling maintenance mode: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal mengubah status mode pemeliharaan: ' . $e->getMessage());
        }
    }
}
