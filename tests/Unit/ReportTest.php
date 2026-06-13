<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Report;

class ReportTest extends TestCase
{
    public function test_can_instantiate_Report()
    {
        $model = new Report();
        $this->assertInstanceOf(Report::class, $model);
    }

    public function test_Report_uses_mongodb_connection()
    {
        $model = new Report();
        $this->assertEquals('mongodb', $model->getConnectionName());
    }
}