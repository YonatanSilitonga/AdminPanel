<?php

namespace Tests\Unit\MongoDB;

use Tests\TestCase;
use App\Models\MongoDB\MongoReview;

class MongoReviewTest extends TestCase
{
    public function test_can_instantiate_MongoReview()
    {
        $model = new MongoReview();
        $this->assertInstanceOf(MongoReview::class, $model);
    }

    public function test_MongoReview_uses_mongodb_connection()
    {
        $model = new MongoReview();
        $this->assertEquals('mongodb', $model->getConnectionName());
    }
}