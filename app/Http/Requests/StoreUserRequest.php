<?php

namespace App\Http\Requests;

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
            'name'     => ['required', 'string', 'max:150'],
            'email'    => ['required', 'email', 'max:255', Rule::unique('users', 'email')],
            'phone'    => ['nullable', 'string', 'max:40'],
            'status'   => ['required', Rule::in(\App\Models\User::STATUSES)],
            'role'     => ['required', 'string', 'exists:roles,name'],
            'department_id' => ['nullable', 'exists:departments,id'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ];
    }
}
