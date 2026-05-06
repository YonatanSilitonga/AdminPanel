<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class EventRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth('admin')->check() && 
               auth('admin')->user()->hasPermission('manage_events');
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $eventId = $this->route('event')?->id;

        return [
            'destination_id' => 'required|exists:destinations,id',
            'name' => [
                'required',
                'string',
                'min:3',
                'max:200',
                'unique:events,name,' . $eventId,
            ],
            'slug' => [
                'string',
                'max:255',
                'unique:events,slug,' . $eventId,
            ],
            'description' => 'required|string|min:10|max:500',
            'long_description' => 'nullable|string|max:5000',
            'start_date' => 'required|date_format:Y-m-d H:i|after_or_equal:now',
            'end_date' => 'required|date_format:Y-m-d H:i|after:start_date',
            'banner' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get the validation messages.
     */
    public function messages(): array
    {
        return [
            'destination_id.required' => 'Please select a destination for this event.',
            'destination_id.exists' => 'The selected destination does not exist.',
            'name.required' => 'The event name is required.',
            'name.unique' => 'An event with this name already exists.',
            'name.min' => 'The event name must be at least 3 characters.',
            'name.max' => 'The event name must not exceed 200 characters.',
            'description.required' => 'The description is required.',
            'description.min' => 'The description must be at least 10 characters.',
            'start_date.required' => 'The start date and time are required.',
            'start_date.after_or_equal' => 'The start date must be in the future.',
            'end_date.required' => 'The end date and time are required.',
            'end_date.after' => 'The end date must be after the start date.',
            'banner.image' => 'The banner must be an image file.',
            'banner.mimes' => 'The banner must be a JPEG, PNG, or WebP image.',
            'banner.max' => 'The banner must not exceed 2 MB.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_active' => $this->has('is_active'),
        ]);
    }
}
