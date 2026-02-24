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
        // Create Roles
        $superAdminRole = Role::firstOrCreate(
            ['name' => 'super_admin'],
            ['description' => 'Full access to admin panel']
        );

        $adminRole = Role::firstOrCreate(
            ['name' => 'admin'],
            ['description' => 'Manage destinations and events']
        );

        $moderatorRole = Role::firstOrCreate(
            ['name' => 'moderator'],
            ['description' => 'Moderate reviews and reports']
        );

        // Create Permissions
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

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission['name']], $permission);
        }

        // Assign permissions to roles
        $allPermissions = Permission::all();
        $destinationPerms = Permission::where('group', 'destination')->get();
        $eventPerms = Permission::where('group', 'event')->get();
        $reviewPerms = Permission::where('group', 'review')->get();
        $reportPerms = Permission::where('group', 'report')->get();
        $userPerms = Permission::where('group', 'user')->get();
        $logPerms = Permission::where('group', 'log')->get();
        $analyticsPerms = Permission::where('group', 'analytics')->get();
        $systemPerms = Permission::where('group', 'system')->get();

        // Super Admin - All permissions
        foreach ($allPermissions as $permission) {
            $superAdminRole->givePermission($permission);
        }

        // Admin - Destination, Event, Reviews, Analytics
        foreach ($destinationPerms as $permission) {
            $adminRole->givePermission($permission);
        }
        foreach ($eventPerms as $permission) {
            $adminRole->givePermission($permission);
        }
        foreach ($reviewPerms as $permission) {
            $adminRole->givePermission($permission);
        }
        foreach ($userPerms as $permission) {
            $adminRole->givePermission($permission);
        }
        foreach ($logPerms as $permission) {
            $adminRole->givePermission($permission);
        }
        foreach ($analyticsPerms as $permission) {
            $adminRole->givePermission($permission);
        }

        // Moderator - Reviews, Reports, Logs
        foreach ($reviewPerms as $permission) {
            $moderatorRole->givePermission($permission);
        }
        foreach ($reportPerms as $permission) {
            $moderatorRole->givePermission($permission);
        }
        foreach ($logPerms as $permission) {
            $moderatorRole->givePermission($permission);
        }

        // Create sample admins
        Admin::firstOrCreate(
            ['email' => 'superadmin@smarttourism.local'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('SuperAdmin@123'),
                'role_id' => $superAdminRole->id,
                'is_active' => true,
                'phone' => '+6281234567890',
            ]
        );

        Admin::firstOrCreate(
            ['email' => 'admin@smarttourism.local'],
            [
                'name' => 'Admin',
                'password' => Hash::make('Admin@123'),
                'role_id' => $adminRole->id,
                'is_active' => true,
                'phone' => '+6281234567891',
            ]
        );

        Admin::firstOrCreate(
            ['email' => 'moderator@smarttourism.local'],
            [
                'name' => 'Moderator',
                'password' => Hash::make('Moderator@123'),
                'role_id' => $moderatorRole->id,
                'is_active' => true,
                'phone' => '+6281234567892',
            ]
        );

        $this->command->info('Admin users seeded successfully!');
        $this->command->info('');
        $this->command->info('Login credentials:');
        $this->command->info('Super Admin: superadmin@smarttourism.local / SuperAdmin@123');
        $this->command->info('Admin: admin@smarttourism.local / Admin@123');
        $this->command->info('Moderator: moderator@smarttourism.local / Moderator@123');
    }
}
