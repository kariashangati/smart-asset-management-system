<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Role;

class RoleService
{
    private array $protectedRoles = [
        'admin',
        'asset_manager',
    ];

    public function create(array $data): Role
    {
        $role = Role::create([
            'name' => $data['name'],
            'guard_name' => 'web',
        ]);

        $role->syncPermissions($data['permissions'] ?? []);

        return $role->load('permissions');
    }

    public function update(Role $role, array $data): Role
    {
        if (
            in_array($role->name, $this->protectedRoles, true) &&
            $role->name !== $data['name']
        ) {
            throw ValidationException::withMessages([
                'name' => 'The built-in role name cannot be changed.',
            ]);
        }

        $role->update([
            'name' => $data['name'],
        ]);

        $role->syncPermissions($data['permissions'] ?? []);

        return $role->refresh()->load('permissions');
    }

    public function delete(Role $role): void
    {
        if (in_array($role->name, $this->protectedRoles, true)) {
            throw ValidationException::withMessages([
                'role' => 'Built-in roles cannot be deleted.',
            ]);
        }

        if (User::role($role->name)->exists()) {
            throw ValidationException::withMessages([
                'role' => 'This role is assigned to users and cannot be deleted.',
            ]);
        }

        $role->delete();
    }
}