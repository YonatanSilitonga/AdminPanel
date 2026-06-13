<?php

namespace Tests\Unit\MongoDB;

use Tests\TestCase;
use App\Models\MongoDB\MongoFasilitasUmum;

class MongoFasilitasUmumTest extends TestCase
{
    public function test_can_instantiate_MongoFasilitasUmum()
    {
        $model = new MongoFasilitasUmum();
        $this->assertInstanceOf(MongoFasilitasUmum::class, $model);
    }

    public function test_MongoFasilitasUmum_uses_mongodb_connection()
    {
        $model = new MongoFasilitasUmum();
        $this->assertEquals('mongodb', $model->getConnectionName());
    }
}