<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAssetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'asset_code' => 'required|string|max:255|unique:assets,asset_code,' . $this->asset,
            'name' => 'required|string|max:255',
            'serial_number' => 'nullable|string|max:255',
            'asset_category_id' => 'required|exists:asset_categories,id',
            'department_id' => 'required|exists:departments,id',
            'description' => 'nullable|string',
            'purchase_date' => 'nullable|date',
            'status' => 'required|in:active,inactive,missing,maintenance',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];
    }
}