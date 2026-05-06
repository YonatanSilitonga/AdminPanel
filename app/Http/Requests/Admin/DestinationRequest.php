<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class DestinationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth('admin')->check() && 
               auth('admin')->user()->hasPermission('manage_destinations');
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $destinationId = $this->route('destination')?->id;

        return [
            'name' => [
                'required',
                'string',
                'min:3',
                'max:200',
                'unique:destinations,name,' . $destinationId,
            ],
            'slug' => [
                'string',
                'max:255',
                'unique:destinations,slug,' . $destinationId,
            ],
            'description' => 'required|string|min:10|max:500',
            'long_description' => 'nullable|string|max:5000',
            'category' => 'required|in:park,beach,museum,historical,nature,cultural,religi',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240',
            'cover' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get the validation messages.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'The destination name is required.',
            'name.unique' => 'A destination with this name already exists.',
            'name.min' => 'The destination name must be at least 3 characters.',
            'name.max' => 'The destination name must not exceed 200 characters.',
            'description.required' => 'The description is required.',
            'description.min' => 'The description must be at least 10 characters.',
            'category.required' => 'Please select a destination category.',
            'category.in' => 'The selected category is invalid.',
            'latitude.required' => 'Latitude is required.',
            'latitude.numeric' => 'Latitude must be a valid number.',
            'latitude.between' => 'Latitude must be between -90 and 90.',
            'longitude.required' => 'Longitude is required.',
            'longitude.numeric' => 'Longitude must be a valid number.',
            'longitude.between' => 'Longitude must be between -180 and 180.',
            'thumbnail.image' => 'The thumbnail must be an image file.',
            'thumbnail.mimes' => 'The thumbnail must be a JPEG, PNG, or WebP image.',
            'thumbnail.max' => 'The thumbnail must not exceed 10 MB.',
            'cover.image' => 'The cover must be an image file.',
            'cover.mimes' => 'The cover must be a JPEG, PNG, or WebP image.',
            'cover.max' => 'The cover must not exceed 10 MB.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_featured' => $this->has('is_featured'),
            'is_active' => $this->has('is_active'),
        ]);
    }
}
