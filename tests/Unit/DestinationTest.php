<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Destination;

class DestinationTest extends TestCase
{
    public function test_can_instantiate_Destination()
    {
        $model = new Destination();
        $this->assertInstanceOf(Destination::class, $model);
    }

    public function test_Destination_uses_mongodb_connection()
    {
        $model = new Destination();
        $this->assertEquals('mongodb', $model->getConnectionName());
    }
}