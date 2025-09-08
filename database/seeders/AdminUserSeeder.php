<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        // Create the admin role if it doesn't exist
        $adminRole = Role::firstOrCreate(['name' => 'admin']);

        // Create the admin user
        $admin = User::firstOrCreate(
            ['email' => 'arnobsec21@gmail.com'], // email to identify
            [
                'name' => 'Biplob Hossain',
                'password' => bcrypt('kilban13'), // default password
            ]
        );

        // Assign admin role to user
        if (!$admin->hasRole('admin')) {
            $admin->assignRole($adminRole);
        }

        $this->command->info('Admin user and role created successfully!');
    }
}
