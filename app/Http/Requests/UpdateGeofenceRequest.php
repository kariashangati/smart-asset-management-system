<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateGeofenceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => 'sometimes|required|string|max:255|unique:geofences,name,' . $this->geofence->id,
            'description' => 'nullable|string|max:1000',
            'center_latitude' => 'sometimes|required|numeric|between:-90,90',
            'center_longitude' => 'sometimes|required|numeric|between:-180,180',
            'radius_meters' => 'sometimes|required|numeric|min:10|max:50000',
            'status' => 'sometimes|required|string|in:active,inactive',
            'alert_on_breach' => 'sometimes|required|boolean',
        ];
    }
}
