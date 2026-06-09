<?php

namespace Tests\Unit\MongoDB;

use Tests\TestCase;
use App\Models\MongoDB\MongoRecommendation;

class MongoRecommendationTest extends TestCase
{
    public function test_can_instantiate_MongoRecommendation()
    {
        $model = new MongoRecommendation();
        $this->assertInstanceOf(MongoRecommendation::class, $model);
    }

    public function test_MongoRecommendation_uses_mongodb_connection()
    {
        $model = new MongoRecommendation();
        $this->assertEquals('mongodb', $model->getConnectionName());
    }
}