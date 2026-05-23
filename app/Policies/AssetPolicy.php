<?php

namespace App\Policies;

use App\Models\Asset;
use App\Models\User;

class AssetPolicy
{
    /**
     * Determine whether the user can view any assets
     */
    public function viewAny(User $user): bool
    {
        return $user->can('assets.view');
    }

    /**
     * Determine whether the user can view the asset
     * Managers can only view assets in their department
     * Admins can view all assets
     */
    public function view(User $user, Asset $asset): bool
    {
        // Must have permission
        if (!$user->can('assets.view')) {
            return false;
        }

        // Admins can view all assets
        if ($user->hasRole('admin')) {
            return true;
        }

        // Managers can only view assets in their department
        if ($user->isDepartmentManager()) {
            return $asset->department_id === $user->department_id;
        }

        return false;
    }

    /**
     * Determine whether the user can create assets
     * Managers can only create in their department
     * Admins can create in any department
     */
    public function create(User $user): bool
    {
        return $user->can('assets.create');
    }

    /**
     * Determine whether the user can update the asset
     * Managers can only update assets in their department
     * Admins can update any asset
     */
    public function update(User $user, Asset $asset): bool
    {
        // Must have permission
        if (!$user->can('assets.update')) {
            return false;
        }

        // Admins can update any asset
        if ($user->hasRole('admin')) {
            return true;
        }

        // Managers can only update assets in their department
        if ($user->isDepartmentManager()) {
            return $asset->department_id === $user->department_id;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the asset
     * Managers can only delete assets in their department
     * Admins can delete any asset
     */
    public function delete(User $user, Asset $asset): bool
    {
        // Must have permission
        if (!$user->can('assets.delete')) {
            return false;
        }

        // Admins can delete any asset
        if ($user->hasRole('admin')) {
            return true;
        }

        // Managers can only delete assets in their department
        if ($user->isDepartmentManager()) {
            return $asset->department_id === $user->department_id;
        }

        return false;
    }

    /**
     * Determine whether the user can restore the asset
     */
    public function restore(User $user, Asset $asset): bool
    {
        return $this->delete($user, $asset);
    }

    /**
     * Determine whether the user can permanently delete the asset
     */
    public function forceDelete(User $user, Asset $asset): bool
    {
        return $this->delete($user, $asset);
    }

    /**
     * Determine whether the user can transfer asset to another department
     * Only admins can transfer, managers are restricted to their own
     */
    public function transfer(User $user, Asset $asset): bool
    {
        // Must have update permission
        if (!$user->can('assets.update')) {
            return false;
        }

        // Only admins can transfer between departments
        if ($user->hasRole('admin')) {
            return true;
        }

        // Managers cannot transfer (cannot change department)
        if ($user->isDepartmentManager()) {
            return false;
        }

        return false;
    }
}
