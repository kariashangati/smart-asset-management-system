<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTrackerDeviceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'device_code' => 'required|string|max:255|unique:tracker_devices,device_code',
            'device_name' => 'required|string|max:255',
            'imei' => 'nullable|string|max:255|unique:tracker_devices,imei',
            'sim_number' => 'nullable|string|max:255',
            'api_token_hash' => 'required|string|unique:tracker_devices,api_token_hash',
            'status' => ['required', Rule::in(['active', 'inactive', 'faulty'])],
            'battery_level' => 'nullable|integer|min:0|max:100',
            'firmware_version' => 'nullable|string|max:50',
        ];
    }
}