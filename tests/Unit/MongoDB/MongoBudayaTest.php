<?php

namespace Tests\Unit\MongoDB;

use Tests\TestCase;
use App\Models\MongoDB\MongoBudaya;

class MongoBudayaTest extends TestCase
{
    public function test_can_instantiate_MongoBudaya()
    {
        $model = new MongoBudaya();
        $this->assertInstanceOf(MongoBudaya::class, $model);
    }

    public function test_MongoBudaya_uses_mongodb_connection()
    {
        $model = new MongoBudaya();
        $this->assertEquals('mongodb', $model->getConnectionName());
    }
}