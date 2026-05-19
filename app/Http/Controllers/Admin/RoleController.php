<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use App\Services\RoleService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function index(): View
    {
        $roles = Role::query()
            ->with('permissions')
            ->latest()
            ->get();

        $permissions = Permission::query()
            ->orderBy('name')
            ->get();

        return view('admin.roles.index', compact('roles', 'permissions'));
    }

    public function store(
        StoreRoleRequest $request,
        RoleService $roleService
    ): RedirectResponse {
        $roleService->create($request->validated());

        return redirect()
            ->route('admin.roles.index')
            ->with('success', 'Role created successfully.');
    }

    public function update(
        UpdateRoleRequest $request,
        Role $role,
        RoleService $roleService
    ): RedirectResponse {
        $roleService->update($role, $request->validated());

        return redirect()
            ->route('admin.roles.index')
            ->with('success', 'Role updated successfully.');
    }

    public function destroy(
        Role $role,
        RoleService $roleService
    ): RedirectResponse {
        $roleService->delete($role);

        return redirect()
            ->route('admin.roles.index')
            ->with('success', 'Role deleted successfully.');
    }
}