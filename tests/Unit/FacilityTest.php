<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Facility;

class FacilityTest extends TestCase
{
    public function test_can_instantiate_Facility()
    {
        $model = new Facility();
        $this->assertInstanceOf(Facility::class, $model);
    }

    public function test_Facility_uses_mongodb_connection()
    {
        $model = new Facility();
        $this->assertEquals('mongodb', $model->getConnectionName());
    }
}