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
                $logoPath = $request->file('logo')->store('settings', 'public');
                AppSetting::set('logo', $logoPath);
            }

            // Handle favicon upload
            if ($request->hasFile('favicon')) {
                $faviconPath = $request->file('favicon')->store('settings', 'public');
                AppSetting::set('favicon', $faviconPath);
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

            $newValues = AppSetting::getAllSettings();
            $this->logActivity('update_general_settings', 'settings', 'general', $oldValues, $newValues);

            return redirect()->back()->with('success', 'Pengaturan berhasil diperbarui.');
        } catch (\Exception $e) {
            Log::error('Error updating general settings: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal memperbarui pengaturan: ' . $e->getMessage());
        }
    }
}
