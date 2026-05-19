<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::updateOrCreate(
            ['email' => 'admin@smartassets.test'],
            [
                'name' => 'System Administrator',
                'password' => Hash::make('Admin@12345'),
            ]
        );

        $admin->syncRoles(['admin']);

        $manager = User::updateOrCreate(
            ['email' => 'manager@smartassets.test'],
            [
                'name' => 'Asset Manager',
                'password' => Hash::make('Manager@12345'),
            ]
        );

        $manager->syncRoles(['asset_manager']);
    }
}