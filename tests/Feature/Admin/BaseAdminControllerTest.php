<?php

namespace Tests\Feature\Admin;

use Tests\TestCase;
use App\Models\Admin;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BaseAdminControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();
        
        $role = Role::firstOrCreate(['name' => 'super_admin'], ['description' => 'Super Admin']);
        $this->admin = Admin::factory()->create([
            'role_id' => $role->id,
            'is_active' => true,
        ]);
    }

    public function test_can_authenticate_and_access_BaseAdminController_context()
    {
        $this->actingAs($this->admin, 'admin');
        $this->assertAuthenticatedAs($this->admin, 'admin');
        
        // This is a base test. 
        // Real tests for specific routes (e.g., $this->get(route('...'))) should be added here.
        $this->assertTrue(true);
    }
}