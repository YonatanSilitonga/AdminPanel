<?php

namespace Tests\Unit\MongoDB;

use Tests\TestCase;
use App\Models\MongoDB\MongoDestination;

class MongoDestinationTest extends TestCase
{
    public function test_can_instantiate_MongoDestination()
    {
        $model = new MongoDestination();
        $this->assertInstanceOf(MongoDestination::class, $model);
    }

    public function test_MongoDestination_uses_mongodb_connection()
    {
        $model = new MongoDestination();
        $this->assertEquals('mongodb', $model->getConnectionName());
    }
}