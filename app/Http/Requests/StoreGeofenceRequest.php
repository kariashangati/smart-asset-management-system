<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreGeofenceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'asset_id' => 'required|exists:assets,id',
            'name' => 'required|string|max:255',
            'center_latitude' => 'required|numeric|between:-90,90',
            'center_longitude' => 'required|numeric|between:-180,180',
            'radius_meters' => 'required|integer|min:1|max:10000',
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ];
    }
}