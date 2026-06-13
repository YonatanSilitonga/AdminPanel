<?php

namespace Tests\Unit\MongoDB;

use Tests\TestCase;
use App\Models\MongoDB\MongoReport;

class MongoReportTest extends TestCase
{
    public function test_can_instantiate_MongoReport()
    {
        $model = new MongoReport();
        $this->assertInstanceOf(MongoReport::class, $model);
    }

    public function test_MongoReport_uses_mongodb_connection()
    {
        $model = new MongoReport();
        $this->assertEquals('mongodb', $model->getConnectionName());
    }
}