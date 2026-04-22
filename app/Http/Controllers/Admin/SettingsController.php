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
            'site_name' => 'required|string|max:255',
            'support_email' => 'required|email|max:255',
        ]);

        try {
            $oldValues = AppSetting::getAllSettings();
            
            AppSetting::set('site_name', $validated['site_name']);
            AppSetting::set('support_email', $validated['support_email']);

            $newValues = AppSetting::getAllSettings();
            $this->logActivity('update_settings', 'settings', 'general', $oldValues, $newValues);

            return redirect()->back()->with('success', 'General settings updated successfully.');
        } catch (\Exception $e) {
            Log::error('Error updating general settings: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to update settings: ' . $e->getMessage());
        }
    }

    /**
     * Display API keys form.
     */
    public function editApiKeys()
    {
        $settings = AppSetting::getAllSettings();
        return view('admin.settings.api-keys', compact('settings'));
    }

    /**
     * Update API keys.
     */
    public function updateApiKeys(Request $request)
    {
        $validated = $request->validate([
            'maps_api_key' => 'nullable|string',
            'ai_api_key' => 'nullable|string',
        ]);

        try {
            $oldValues = AppSetting::getAllSettings();
            
            AppSetting::set('maps_api_key', $validated['maps_api_key'] ?? '');
            AppSetting::set('ai_api_key', $validated['ai_api_key'] ?? '');

            $newValues = AppSetting::getAllSettings();
            $this->logActivity('update_api_keys', 'settings', 'api_keys', $oldValues, $newValues);

            return redirect()->back()->with('success', 'API keys updated successfully.');
        } catch (\Exception $e) {
            Log::error('Error updating API keys: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to update API keys.');
        }
    }

    /**
     * Display AI configuration form.
     */
    public function editAiConfig()
    {
        $settings = AppSetting::getAllSettings();
        return view('admin.settings.ai-config', compact('settings'));
    }

    /**
     * Update AI configuration.
     */
    public function updateAiConfig(Request $request)
    {
        $validated = $request->validate([
            'model_name' => 'required|string',
            'temperature' => 'required|numeric|min:0|max:2',
        ]);

        try {
            $oldValues = AppSetting::getAllSettings();
            
            AppSetting::set('model_name', $validated['model_name']);
            AppSetting::set('temperature', $validated['temperature'], 'float');

            $newValues = AppSetting::getAllSettings();
            $this->logActivity('update_ai_config', 'settings', 'ai_config', $oldValues, $newValues);

            return redirect()->back()->with('success', 'AI configuration updated successfully.');
        } catch (\Exception $e) {
            Log::error('Error updating AI config: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to update AI configuration.');
        }
    }

    /**
     * Toggle maintenance mode.
     */
    public function toggleMaintenance()
    {
        try {
            $currentStatus = AppSetting::get('maintenance_mode', false);
            AppSetting::set('maintenance_mode', !$currentStatus, 'boolean');
            
            $statusStr = !$currentStatus ? 'enabled' : 'disabled';
            $this->logActivity('toggle_maintenance', 'settings', 'maintenance', ['status' => $currentStatus], ['status' => !$currentStatus]);

            return redirect()->back()->with('success', "Maintenance mode {$statusStr} successfully.");
        } catch (\Exception $e) {
            Log::error('Error toggling maintenance mode: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to toggle maintenance mode.');
        }
    }
}
