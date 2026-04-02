<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Admin;
use App\Models\Role;
use App\Models\Permission;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Create Single Role: super_admin
        $superAdminRole = Role::updateOrCreate(
            ['name' => 'super_admin'],
            ['description' => 'Full access to admin panel']
        );

        // 2. Create Permissions
        $permissions = [
            // Destination Management
            ['name' => 'view_destinations', 'group' => 'destination'],
            ['name' => 'create_destination', 'group' => 'destination'],
            ['name' => 'edit_destination', 'group' => 'destination'],
            ['name' => 'delete_destination', 'group' => 'destination'],
            ['name' => 'manage_gallery', 'group' => 'destination'],
            ['name' => 'manage_facilities', 'group' => 'destination'],
            ['name' => 'mark_featured', 'group' => 'destination'],

            // Event Management
            ['name' => 'view_events', 'group' => 'event'],
            ['name' => 'create_event', 'group' => 'event'],
            ['name' => 'edit_event', 'group' => 'event'],
            ['name' => 'delete_event', 'group' => 'event'],
            ['name' => 'toggle_event_status', 'group' => 'event'],

            // Review Management
            ['name' => 'view_reviews', 'group' => 'review'],
            ['name' => 'approve_review', 'group' => 'review'],
            ['name' => 'reject_review', 'group' => 'review'],
            ['name' => 'delete_review', 'group' => 'review'],

            // Report Management
            ['name' => 'view_reports', 'group' => 'report'],
            ['name' => 'resolve_report', 'group' => 'report'],
            ['name' => 'take_report_action', 'group' => 'report'],

            // User Management
            ['name' => 'view_users', 'group' => 'user'],
            ['name' => 'edit_user', 'group' => 'user'],
            ['name' => 'delete_user', 'group' => 'user'],
            ['name' => 'disable_user_account', 'group' => 'user'],

            // Log Viewing
            ['name' => 'view_recommendation_logs', 'group' => 'log'],
            ['name' => 'view_chatbot_logs', 'group' => 'log'],
            ['name' => 'export_logs', 'group' => 'log'],

            // Analytics
            ['name' => 'view_analytics', 'group' => 'analytics'],
            ['name' => 'export_analytics', 'group' => 'analytics'],

            // System
            ['name' => 'access_settings', 'group' => 'system'],
            ['name' => 'manage_api_keys', 'group' => 'system'],
            ['name' => 'toggle_maintenance', 'group' => 'system'],
            ['name' => 'view_audit_logs', 'group' => 'system'],
        ];

        $permissionIds = [];
        foreach ($permissions as $permission) {
            $p = Permission::updateOrCreate(['name' => $permission['name']], $permission);
            $permissionIds[] = $p->_id;
        }

        // 3. Assign All Permissions to Super Admin
        $superAdminRole->permissions()->sync($permissionIds);

        // 4. Create Single Super Admin Account
        Admin::updateOrCreate(
            ['email' => 'superadmin@smarttourism.local'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('SuperAdmin@123'),
                'role_id' => $superAdminRole->_id,
                'is_active' => true,
                'phone' => '+6281234567890',
            ]
        );

        $this->command->info('Admin system seeded successfully in MongoDB!');
        $this->command->info('Role: super_admin');
        $this->command->info('Credentials: superadmin@smarttourism.local / SuperAdmin@123');
    }
}
