<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ReportRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth('admin')->check() && 
               auth('admin')->user()->hasPermission('manage_reports');
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'status' => 'required|in:pending,investigating,resolved,dismissed',
            'action' => 'required_if:status,resolved|in:delete_content,warn_user,ignore',
            'action_reason' => 'required_if:status,resolved|string|max:500',
        ];
    }

    /**
     * Get the validation messages.
     */
    public function messages(): array
    {
        return [
            'status.required' => 'Please select a status for the report.',
            'status.in' => 'The selected status is invalid.',
            'action.required_if' => 'Please select an action for this resolved report.',
            'action.in' => 'The selected action is invalid.',
            'action_reason.required_if' => 'Please provide a reason for the action taken.',
            'action_reason.max' => 'The reason must not exceed 500 characters.',
        ];
    }
}
