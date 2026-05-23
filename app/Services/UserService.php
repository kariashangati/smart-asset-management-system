<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Validation\ValidationException;

class UserService
{
    protected DepartmentService $departmentService;

    public function __construct(DepartmentService $departmentService)
    {
        $this->departmentService = $departmentService;
    }

    public function create(array $data): User
    {
        // Validate department assignment based on role
        $this->departmentService->validateManagerDepartmentAssignment(
            $data['role'],
            $data['department_id'] ?? null
        );

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'status' => $data['status'],
            'password' => $data['password'],
            'department_id' => $data['department_id'] ?? null,
        ]);

        $user->syncRoles([$data['role']]);

        return $user->load('roles', 'department');
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

        // Validate department assignment based on role
        $this->departmentService->validateManagerDepartmentAssignment(
            $data['role'],
            $data['department_id'] ?? null
        );

        $payload = [
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'status' => $data['status'],
            'department_id' => $data['department_id'] ?? null,
        ];

        if (! empty($data['password'])) {
            $payload['password'] = $data['password'];
        }

        $user->update($payload);
        $user->syncRoles([$data['role']]);

        return $user->refresh()->load('roles', 'department');
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
