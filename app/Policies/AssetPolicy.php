<?php

namespace App\Policies;

use App\Models\Asset;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class AssetPolicy
{
    /**
     * Determine if the user can view any assets
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isDepartmentManager();
    }

    /**
     * Determine if the user can view the asset
     * Managers can only view assets in their department
     */
    public function view(User $user, Asset $asset): bool
    {
        // Admins can view all assets
        if ($user->isAdmin()) {
            return true;
        }

        // Managers can only view assets in their department
        if ($user->isDepartmentManager()) {
            return $asset->department_id === $user->department_id;
        }

        return false;
    }

    /**
     * Determine if the user can create assets
     */
    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isDepartmentManager();
    }

    /**
     * Determine if the user can update the asset
     * Managers can only update assets in their department
     */
    public function update(User $user, Asset $asset): bool
    {
        // Admins can update all assets
        if ($user->isAdmin()) {
            return true;
        }

        // Managers can only update assets in their department
        if ($user->isDepartmentManager()) {
            return $asset->department_id === $user->department_id;
        }

        return false;
    }

    /**
     * Determine if the user can delete the asset
     * Managers can only delete assets in their department
     */
    public function delete(User $user, Asset $asset): bool
    {
        // Admins can delete all assets
        if ($user->isAdmin()) {
            return true;
        }

        // Managers can only delete assets in their department
        if ($user->isDepartmentManager()) {
            return $asset->department_id === $user->department_id;
        }

        return false;
    }

    /**
     * Determine if the user can restore the asset
     */
    public function restore(User $user, Asset $asset): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine if the user can permanently delete the asset
     */
    public function forceDelete(User $user, Asset $asset): bool
    {
        return $user->isAdmin();
    }
}
