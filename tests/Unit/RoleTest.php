<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Role;

class RoleTest extends TestCase
{
    public function test_can_instantiate_Role()
    {
        $model = new Role();
        $this->assertInstanceOf(Role::class, $model);
    }

    public function test_Role_uses_mongodb_connection()
    {
        $model = new Role();
        $this->assertEquals('mongodb', $model->getConnectionName());
    }
}