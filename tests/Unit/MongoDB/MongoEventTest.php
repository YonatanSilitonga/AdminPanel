<?php

namespace Tests\Unit\MongoDB;

use Tests\TestCase;
use App\Models\MongoDB\MongoEvent;

class MongoEventTest extends TestCase
{
    public function test_can_instantiate_MongoEvent()
    {
        $model = new MongoEvent();
        $this->assertInstanceOf(MongoEvent::class, $model);
    }

    public function test_MongoEvent_uses_mongodb_connection()
    {
        $model = new MongoEvent();
        $this->assertEquals('mongodb', $model->getConnectionName());
    }
}