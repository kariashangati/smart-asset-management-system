<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTrackerDeviceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'device_code' => 'required|string|max:255|unique:tracker_devices,device_code,' . $this->tracker_device,
            'device_name' => 'required|string|max:255',
            'imei' => 'nullable|string|max:255|unique:tracker_devices,imei,' . $this->tracker_device,
            'sim_number' => 'nullable|string|max:255',
            'status' => ['required', Rule::in(['active', 'inactive', 'faulty'])],
            'battery_level' => 'nullable|integer|min:0|max:100',
            'firmware_version' => 'nullable|string|max:50',
        ];
    }
}