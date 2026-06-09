<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;

class UserTest extends TestCase
{
    public function test_can_instantiate_User()
    {
        $model = new User();
        $this->assertInstanceOf(User::class, $model);
    }

    public function test_User_uses_mongodb_connection()
    {
        $model = new User();
        $this->assertEquals('mongodb', $model->getConnectionName());
    }
}