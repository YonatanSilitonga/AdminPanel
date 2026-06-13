<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\AppSetting;

class AppSettingTest extends TestCase
{
    public function test_can_instantiate_AppSetting()
    {
        $model = new AppSetting();
        $this->assertInstanceOf(AppSetting::class, $model);
    }

    public function test_AppSetting_uses_mongodb_connection()
    {
        $model = new AppSetting();
        $this->assertEquals('mongodb', $model->getConnectionName());
    }
}