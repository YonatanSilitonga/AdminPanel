<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Permission;

class PermissionTest extends TestCase
{
    public function test_can_instantiate_Permission()
    {
        $model = new Permission();
        $this->assertInstanceOf(Permission::class, $model);
    }

    public function test_Permission_uses_mongodb_connection()
    {
        $model = new Permission();
        $this->assertEquals('mongodb', $model->getConnectionName());
    }
}