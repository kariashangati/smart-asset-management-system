<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class FixMissingPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $missingPermissions = [
            // Alerts - Missing individual action permissions
            'alerts.view',
            'alerts.create',
            'alerts.update',
            'alerts.delete',
            'alerts.mark_as_read',
            'alerts.mark_as_resolved',

            // Reports - Missing permissions
            'reports.view',
            'reports.create',
            'reports.export',

            // Audit Logs - Missing permissions
            'audit_logs.view',
        ];

        // Create permissions if they don't exist
        foreach ($missingPermissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
            ]);
        }

        // Get admin role and sync all permissions
        $adminRole = Role::where('name', 'admin')->first();
        if ($adminRole) {
            $adminRole->syncPermissions(Permission::all());
        }

        // Update asset_manager role with appropriate permissions
        $assetManagerRole = Role::where('name', 'asset_manager')->first();
        if ($assetManagerRole) {
            $assetManagerRole->syncPermissions([
                'dashboard.manager.view',

                'assets.view',
                'assets.create',
                'assets.update',

                'tracking.live_map.view',
                'tracking.history.view',

                'geofences.view',
                'geofences.create',
                'geofences.update',

                'alerts.view',
                'alerts.mark_as_read',
                'alerts.mark_as_resolved',
            ]);
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
