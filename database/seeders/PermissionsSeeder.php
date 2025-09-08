<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Models\Permission;

class PermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Fetch all route names
        $routes = collect(Route::getRoutes())->map(function ($route) {
            return $route->getName();
        })->filter()->unique();

        foreach ($routes as $name) {
            // Skip routes that do not need permissions
            // if (str($name)->startsWith('admin') || str($name)->startsWith('profile') || str($name)->startsWith('leave')) {
            Permission::firstOrCreate(['name' => $name]);
            // }
        }

        $this->command->info('Permissions seeded successfully!');
    }
}
