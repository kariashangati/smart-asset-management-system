<?php

namespace App\Services;

use App\Models\Department;
use App\Models\User;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Role;

class DepartmentService
{
    /**
     * Validate manager can be assigned to department
     * 
     * @param string $role The role being assigned (admin or asset_manager)
     * @param int|null $departmentId The department ID
     * @return bool
     * @throws ValidationException
     */
    public function validateManagerDepartmentAssignment($role, $departmentId): bool
    {
        // If role is admin, department_id should be NULL
        if ($role === 'admin' && $departmentId !== null) {
            throw ValidationException::withMessages([
                'department_id' => 'Admin users cannot be assigned to a department. Admin users have access to all departments.'
            ]);
        }

        // If role is asset_manager, department_id is REQUIRED
        if ($role === 'asset_manager' && $departmentId === null) {
            throw ValidationException::withMessages([
                'department_id' => 'Asset managers must be assigned to a department.'
            ]);
        }

        // If department_id provided, verify it exists
        if ($departmentId !== null) {
            if (!Department::where('id', $departmentId)->exists()) {
                throw ValidationException::withMessages([
                    'department_id' => 'The selected department does not exist.'
                ]);
            }
        }

        return true;
    }

    /**
     * Assign a manager to a department
     * 
     * @param User $user
     * @param int $departmentId
     * @return User
     */
    public function assignManagerToDepartment(User $user, int $departmentId): User
    {
        // Verify user is a manager
        if (!$user->hasRole('asset_manager')) {
            throw ValidationException::withMessages([
                'department_id' => 'Only asset managers can be assigned to departments.'
            ]);
        }

        // Verify department exists
        if (!Department::where('id', $departmentId)->exists()) {
            throw ValidationException::withMessages([
                'department_id' => 'The selected department does not exist.'
            ]);
        }

        // Assign manager to department
        $user->update(['department_id' => $departmentId]);

        return $user;
    }

    /**
     * Remove manager from department (when role changes)
     * 
     * @param User $user
     * @return User
     */
    public function removeManagerFromDepartment(User $user): User
    {
        $user->update(['department_id' => null]);
        return $user;
    }

    /**
     * Get all managers in a department
     * 
     * @param int $departmentId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getManagersInDepartment(int $departmentId)
    {
        return User::where('department_id', $departmentId)
            ->whereHas('roles', function ($query) {
                $query->where('name', 'asset_manager');
            })
            ->get();
    }

    /**
     * Get department with all related data
     * 
     * @param int $departmentId
     * @return Department|null
     */
    public function getDepartmentWithStats(int $departmentId): ?Department
    {
        return Department::find($departmentId);
    }

    /**
     * Validate user has permission to manage this department
     * Used for authorization checks
     * 
     * @param User $currentUser The logged-in user
     * @param int|null $departmentId The department being managed
     * @return bool
     */
    public function userCanManageDepartment(User $currentUser, ?int $departmentId): bool
    {
        // Admins can manage all departments
        if ($currentUser->hasRole('admin')) {
            return true;
        }

        // Managers can only manage their own department
        if ($currentUser->isDepartmentManager()) {
            return $currentUser->department_id === $departmentId;
        }

        return false;
    }

    /**
     * Get departments accessible to user
     * 
     * @param User $user
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAccessibleDepartments(User $user)
    {
        // Admins see all departments
        if ($user->hasRole('admin')) {
            return Department::all();
        }

        // Managers see only their department
        if ($user->isDepartmentManager() && $user->department_id) {
            return Department::where('id', $user->department_id)->get();
        }

        return collect();
    }
}
