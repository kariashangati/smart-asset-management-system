<?php

namespace App\Http\Requests;

use App\Models\AssetCategory;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAssetCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('asset_categories.update') ?? false;
    }

    public function rules(): array
    {
        /** @var AssetCategory $assetCategory */
        $assetCategory = $this->route('assetCategory');

        return [
            'name' => [
                'required',
                'string',
                'max:150',
                Rule::unique('asset_categories', 'name')->ignore($assetCategory),
            ],
            'description' => ['nullable', 'string', 'max:1000'],
        ];
    }
}