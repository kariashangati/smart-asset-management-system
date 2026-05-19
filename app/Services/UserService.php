<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Validation\ValidationException;

class UserService
{
    public function create(array $data): User
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'status' => $data['status'],
            'password' => $data['password'],
        ]);

        $user->syncRoles([$data['role']]);

        return $user->load('roles');
    }

    public function update(User $user, array $data): User
    {
        $currentRole = $user->getRoleNames()->first();

        if (
            auth()->id() === $user->id &&
            $currentRole !== $data['role']
        ) {
            throw ValidationException::withMessages([
                'role' => 'You cannot change your own role while signed in.',
            ]);
        }

        $payload = [
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'status' => $data['status'],
        ];

        if (! empty($data['password'])) {
            $payload['password'] = $data['password'];
        }

        $user->update($payload);
        $user->syncRoles([$data['role']]);

        return $user->refresh()->load('roles');
    }

    public function toggleStatus(User $user): User
    {
        if (auth()->id() === $user->id) {
            throw ValidationException::withMessages([
                'status' => 'You cannot deactivate your own account.',
            ]);
        }

        $user->update([
            'status' => $user->status === User::STATUS_ACTIVE
                ? User::STATUS_INACTIVE
                : User::STATUS_ACTIVE,
        ]);

        return $user->refresh();
    }
}