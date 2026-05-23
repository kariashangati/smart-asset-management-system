<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('users.create') ?? false;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:40'],
            'status' => ['required', Rule::in(User::STATUSES)],
            'role' => ['required', 'string', 'exists:roles,name'],
            'password' => ['required', 'confirmed', Password::defaults()],
            // NEW: Department assignment validation
            'department_id' => [
                Rule::requiredIf(fn () => $this->input('role') === 'asset_manager'),
                'nullable',
                'integer',
                'exists:departments,id',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'department_id.required' => 'Asset managers must be assigned to a department.',
            'department_id.exists' => 'The selected department does not exist.',
            'department_id.integer' => 'Department must be a valid selection.',
        ];
    }

    /**
     * Prepare the data for validation.
     * Admins should not have department_id set
     */
    protected function prepareForValidation(): void
    {
        // If role is admin, clear department_id
        if ($this->input('role') === 'admin') {
            $this->merge([
                'department_id' => null,
            ]);
        }
    }
}
