<?php

namespace Tests\Unit\MongoDB;

use Tests\TestCase;
use App\Models\MongoDB\MongoBeritaPromosi;

class MongoBeritaPromosiTest extends TestCase
{
    public function test_can_instantiate_MongoBeritaPromosi()
    {
        $model = new MongoBeritaPromosi();
        $this->assertInstanceOf(MongoBeritaPromosi::class, $model);
    }

    public function test_MongoBeritaPromosi_uses_mongodb_connection()
    {
        $model = new MongoBeritaPromosi();
        $this->assertEquals('mongodb', $model->getConnectionName());
    }
}