<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\Department;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index(): View
    {
        $users = User::query()
            ->with('roles', 'department')
            ->latest()
            ->get();

        $roles = Role::query()
            ->orderBy('name')
            ->get();

        $departments = Department::query()
            ->orderBy('name')
            ->get();

        return view('admin.users.index', compact('users', 'roles', 'departments'));
    }

    public function show(User $user): View
    {
        $user->load('roles', 'permissions', 'department');

        return view('admin.users.show', compact('user'));
    }

    public function store(
        StoreUserRequest $request,
        UserService $userService
    ): RedirectResponse {
        $userService->create($request->validated());

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User created successfully.');
    }

    public function update(
        UpdateUserRequest $request,
        User $user,
        UserService $userService
    ): RedirectResponse {
        $userService->update($user, $request->validated());

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User updated successfully.');
    }

    public function toggleStatus(
        User $user,
        UserService $userService
    ): RedirectResponse {
        $updatedUser = $userService->toggleStatus($user);

        $message = $updatedUser->isActive()
            ? 'User activated successfully.'
            : 'User deactivated successfully.';

        return redirect()
            ->route('admin.users.index')
            ->with('success', $message);
    }
}
