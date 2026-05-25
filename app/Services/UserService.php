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
        $this->departmentService->validateManagerDepartmentAssignment(
            $data['role'],
            $data['department_id'] ?? null
        );

        $user = User::create([
            'name'          => $data['name'],
            'email'         => $data['email'],
            'phone'         => $data['phone'] ?? null,
            'status'        => $data['status'],
            'password'      => $data['password'],
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

        // For admin role, department_id must be null
        // For asset_manager role, department_id is required
        // We call validation only when role is changing or department_id is being set
        $role          = $data['role'];
        $departmentId  = isset($data['department_id']) && $data['department_id'] !== ''
            ? (int) $data['department_id']
            : null;

        $this->departmentService->validateManagerDepartmentAssignment($role, $departmentId);

        $payload = [
            'name'          => $data['name'],
            'email'         => $data['email'],
            'phone'         => $data['phone'] ?? null,
            'status'        => $data['status'],
            'department_id' => $departmentId,
        ];

        // Only update password when a new one is supplied
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
