<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreLocationRequest extends FormRequest
{
    /**
     * No authentication required for API endpoints
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'api_token_hash' => [
                'required',
                'string',
                'min:20', // Reasonable length for hash
            ],
            'latitude' => [
                'required',
                'numeric',
                'between:-90,90',
            ],
            'longitude' => [
                'required',
                'numeric',
                'between:-180,180',
            ],
            'speed' => [
                'nullable',
                'numeric',
                'min:0',
                'max:300', // Reasonable max speed in km/h
            ],
            'motion_detected' => [
                'nullable',
                'boolean',
            ],
            'battery_level' => [
                'nullable',
                'integer',
                'between:0,100',
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'api_token_hash.required' => 'API token is required to authenticate the device.',
            'latitude.required' => 'Latitude coordinate is required.',
            'latitude.between' => 'Latitude must be between -90 and 90 degrees.',
            'longitude.required' => 'Longitude coordinate is required.',
            'longitude.between' => 'Longitude must be between -180 and 180 degrees.',
            'speed.numeric' => 'Speed must be a valid number.',
            'battery_level.between' => 'Battery level must be between 0 and 100 percent.',
        ];
    }
}
