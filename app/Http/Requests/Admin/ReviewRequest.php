<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ReviewRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth('admin')->check() && 
               auth('admin')->user()->hasPermission('moderate_reviews');
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'status' => 'required|in:pending,approved,rejected',
            'reason' => 'required_if:status,rejected|string|max:500',
        ];
    }

    /**
     * Get the validation messages.
     */
    public function messages(): array
    {
        return [
            'status.required' => 'Please select a status for the review.',
            'status.in' => 'The selected status is invalid.',
            'reason.required_if' => 'Please provide a reason for rejecting this review.',
            'reason.max' => 'The reason must not exceed 500 characters.',
        ];
    }
}
