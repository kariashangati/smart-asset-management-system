<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDepartmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('departments.create') ?? false;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:150', 'unique:departments,name'],
            'code' => ['nullable', 'string', 'max:40', 'unique:departments,code'],
            'description' => ['nullable', 'string', 'max:1000'],
        ];
    }
}