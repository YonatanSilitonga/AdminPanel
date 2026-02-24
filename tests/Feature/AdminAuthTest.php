<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAuthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Seed roles and permissions
        $this->artisan('db:seed', ['--class' => 'AdminSeeder']);
    }

    public function test_admin_can_login_with_valid_credentials()
    {
        // Get super_admin role
        $superAdminRole = Role::where('name', 'super_admin')->first();
        
        // Create admin
        $admin = Admin::factory()->create([
            'role_id' => $superAdminRole->id,
            'is_active' => true,
        ]);

        $response = $this->post(route('admin.login.post'), [
            'email' => $admin->email,
            'password' => 'password', // Default factory password
        ]);

        $this->assertAuthenticated('admin');
    }

    public function test_admin_cannot_login_with_invalid_password()
    {
        $superAdminRole = Role::where('name', 'super_admin')->first();
        
        $admin = Admin::factory()->create([
            'role_id' => $superAdminRole->id,
        ]);

        $response = $this->post(route('admin.login.post'), [
            'email' => $admin->email,
            'password' => 'wrong-password',
        ]);

        $this->assertGuest('admin');
    }

    public function test_inactive_admin_cannot_login()
    {
        $superAdminRole = Role::where('name', 'super_admin')->first();
        
        $admin = Admin::factory()->create([
            'role_id' => $superAdminRole->id,
            'is_active' => false,
        ]);

        $response = $this->post(route('admin.login.post'), [
            'email' => $admin->email,
            'password' => 'password',
        ]);

        $this->assertGuest('admin');
    }

    public function test_admin_can_logout()
    {
        $superAdminRole = Role::where('name', 'super_admin')->first();
        
        $admin = Admin::factory()->create([
            'role_id' => $superAdminRole->id,
        ]);

        $this->actingAs($admin, 'admin')
            ->post(route('admin.logout'))
            ->assertRedirect(route('admin.login'));

        $this->assertGuest('admin');
    }
}
