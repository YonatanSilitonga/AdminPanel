<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Event;

class EventTest extends TestCase
{
    public function test_can_instantiate_Event()
    {
        $model = new Event();
        $this->assertInstanceOf(Event::class, $model);
    }

    public function test_Event_uses_mongodb_connection()
    {
        $model = new Event();
        $this->assertEquals('mongodb', $model->getConnectionName());
    }
}