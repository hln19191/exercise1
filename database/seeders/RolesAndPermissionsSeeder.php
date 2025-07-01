<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Clear cache
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            'read-users',
            'create-users',
            'update-users',
            'delete-users',
            'read-roles',
            'create-roles',
            'update-roles',
            'delete-roles',
            'read-user-groups',
            'create-user-groups',
            'update-user-groups',
            'delete-user-groups',
            'read-settings',
            'update-settings'
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles and assign permissions
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $admin->syncPermissions(Permission::all());

        $manager = Role::firstOrCreate(['name' => 'manager']);
        $manager->syncPermissions([
            'read-users',
            'update-users',
        ]);

        Role::firstOrCreate(['name' => 'user']); // No permissions by default

        // Optional: Assign the "admin" role to the first user
        $user = User::first();
        if ($user && !$user->hasRole('admin')) {
            $user->assignRole('admin');
        }

        $this->command->info('Roles and permissions seeded successfully.');
    }
}
