<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Admin;

class AdminTest extends TestCase
{
    public function test_can_instantiate_Admin()
    {
        $model = new Admin();
        $this->assertInstanceOf(Admin::class, $model);
    }

    public function test_Admin_uses_mongodb_connection()
    {
        $model = new Admin();
        $this->assertEquals('mongodb', $model->getConnectionName());
    }
}