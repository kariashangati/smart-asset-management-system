<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permissions = [
            // Dashboards
            'dashboard.admin.view',
            'dashboard.manager.view',

            // Users
            'users.view',
            'users.create',
            'users.update',
            'users.delete',

            // Roles
            'roles.view',
            'roles.create',
            'roles.update',
            'roles.delete',
            'roles.assign',

            // Departments
            'departments.view',
            'departments.create',
            'departments.update',
            'departments.delete',

            // Asset Categories
            'asset_categories.view',
            'asset_categories.create',
            'asset_categories.update',
            'asset_categories.delete',

            // Assets
            'assets.view',
            'assets.create',
            'assets.update',
            'assets.delete',

            // Devices
            'devices.view',
            'devices.create',
            'devices.update',
            'devices.delete',
            'devices.assign',

            // Tracking
            'tracking.live_map.view',
            'tracking.history.view',

            // Geofences
            'geofences.view',
            'geofences.create',
            'geofences.update',
            'geofences.delete',

            // Alerts
            'alerts.view',
            'alerts.resolve',

            // Reports
            'reports.view',
            'reports.export',

            // Audit Logs
            'audit_logs.view',

            // Settings
            'settings.manage',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
            ]);
        }

        $adminRole = Role::firstOrCreate([
            'name' => 'admin',
            'guard_name' => 'web',
        ]);

        $assetManagerRole = Role::firstOrCreate([
            'name' => 'asset_manager',
            'guard_name' => 'web',
        ]);

        $adminRole->syncPermissions(Permission::all());

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
        ]);

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}