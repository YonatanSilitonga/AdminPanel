<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Review;

class ReviewTest extends TestCase
{
    public function test_can_instantiate_Review()
    {
        $model = new Review();
        $this->assertInstanceOf(Review::class, $model);
    }

    public function test_Review_uses_mongodb_connection()
    {
        $model = new Review();
        $this->assertEquals('mongodb', $model->getConnectionName());
    }
}