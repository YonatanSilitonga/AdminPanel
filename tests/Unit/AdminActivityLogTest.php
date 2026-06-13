<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\AdminActivityLog;

class AdminActivityLogTest extends TestCase
{
    public function test_can_instantiate_AdminActivityLog()
    {
        $model = new AdminActivityLog();
        $this->assertInstanceOf(AdminActivityLog::class, $model);
    }

    public function test_AdminActivityLog_uses_mongodb_connection()
    {
        $model = new AdminActivityLog();
        $this->assertEquals('mongodb', $model->getConnectionName());
    }
}