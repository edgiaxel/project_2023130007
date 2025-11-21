<?php
// database/seeders/PermissionSeeder.php (NEW)

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    public function run()
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. Define Permissions
        $permissions = [
            // RENTER PERMISSIONS (Base Functionality)
            'costume:create',
            'costume:edit-own',
            'costume:delete-own',
            'order:confirm-reject',
            'order:update-status',

            // ADMIN PERMISSIONS (Oversight and Moderation)
            'costume:approve-reject',
            'costume:edit-all',
            'costume:delete-all',
            'analytics:view-renter',
            'discount:manage-global',
            'user:edit-renter-user', // Can edit Renter/User profiles

            // OWNER PERMISSIONS (God Mode)
            'user:manage-roles', // Can change any user's role
            'platform:view-global-kpis',
            'platform:manage-banners',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // 2. Assign Permissions to Roles

        $owner = Role::where('name', 'owner')->first();
        $admin = Role::where('name', 'admin')->first();
        $renter = Role::where('name', 'renter')->first();
        $user = Role::where('name', 'user')->first();

        // OWNER: All Permissions (God Mode)
        $owner->givePermissionTo(Permission::all());

        // ADMIN: Moderation and Oversight
        $admin->givePermissionTo([
            'costume:approve-reject',
            'costume:edit-all',
            'costume:delete-all',
            'analytics:view-renter',
            'discount:manage-global',
            'user:edit-renter-user',
            'platform:view-global-kpis',
            'platform:manage-banners',
        ]);

        // RENTER: Costume Management
        $renter->givePermissionTo([
            'costume:create',
            'costume:edit-own',
            'costume:delete-own',
            'order:confirm-reject',
            'order:update-status',
        ]);

        // USER: No special permissions (can only view and order)
    }
}