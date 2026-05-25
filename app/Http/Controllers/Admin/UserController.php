<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\Department;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\ValidationException;
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
        try {
            $userService->create($request->validated());

            return redirect()
                ->route('admin.users.index')
                ->with('success', 'User created successfully.');
        } catch (ValidationException $e) {
            return redirect()
                ->route('admin.users.index')
                ->withErrors($e->errors())
                ->withInput()
                ->with('_modal', 'create-user-modal');
        }
    }

    public function update(
        UpdateUserRequest $request,
        User $user,
        UserService $userService
    ): RedirectResponse {
        try {
            $userService->update($user, $request->validated());

            return redirect()
                ->route('admin.users.index')
                ->with('success', 'User updated successfully.');
        } catch (ValidationException $e) {
            return redirect()
                ->route('admin.users.index')
                ->withErrors($e->errors())
                ->withInput()
                ->with('_modal', 'edit-user-modal-' . $user->id);
        }
    }

    public function toggleStatus(
        User $user,
        UserService $userService
    ): RedirectResponse {
        try {
            $updatedUser = $userService->toggleStatus($user);

            $message = $updatedUser->isActive()
                ? 'User activated successfully.'
                : 'User deactivated successfully.';

            return redirect()
                ->route('admin.users.index')
                ->with('success', $message);
        } catch (ValidationException $e) {
            return redirect()
                ->route('admin.users.index')
                ->with('error', $e->getMessage());
        }
    }
}
