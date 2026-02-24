<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class SettingsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth('admin')->check() && 
               auth('admin')->user()->isSuperAdmin();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'app_name' => 'required|string|min:3|max:255',
            'app_version' => 'required|string|max:50',
            'maintenance_mode' => 'boolean',
            'maintenance_message' => 'required_if:maintenance_mode,1|string|max:500',
            'pagination_per_page' => 'required|integer|between:10,100',
            'max_upload_size' => 'required|integer|min:1|max:50',
            'analytics_enabled' => 'boolean',
            'ai_enabled' => 'boolean',
            'chatbot_enabled' => 'boolean',
        ];
    }

    /**
     * Get the validation messages.
     */
    public function messages(): array
    {
        return [
            'app_name.required' => 'The application name is required.',
            'app_name.min' => 'The application name must be at least 3 characters.',
            'pagination_per_page.required' => 'Pagination items per page is required.',
            'pagination_per_page.between' => 'Items per page must be between 10 and 100.',
            'max_upload_size.required' => 'Maximum upload size is required.',
            'max_upload_size.min' => 'Maximum upload size must be at least 1 MB.',
            'max_upload_size.max' => 'Maximum upload size must not exceed 50 MB.',
            'maintenance_message.required_if' => 'Maintenance message is required when maintenance mode is enabled.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'maintenance_mode' => $this->has('maintenance_mode'),
            'analytics_enabled' => $this->has('analytics_enabled'),
            'ai_enabled' => $this->has('ai_enabled'),
            'chatbot_enabled' => $this->has('chatbot_enabled'),
        ]);
    }
}
