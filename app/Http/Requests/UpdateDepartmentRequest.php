<?php

namespace App\Http\Requests;

use App\Models\Department;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDepartmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('departments.update') ?? false;
    }

    public function rules(): array
    {
        /** @var Department $department */
        $department = $this->route('department');

        return [
            'name' => [
                'required',
                'string',
                'max:150',
                Rule::unique('departments', 'name')->ignore($department),
            ],
            'code' => [
                'nullable',
                'string',
                'max:40',
                Rule::unique('departments', 'code')->ignore($department),
            ],
            'description' => ['nullable', 'string', 'max:1000'],
        ];
    }
}