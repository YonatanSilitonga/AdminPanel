<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\RecommendationLog;

class RecommendationLogTest extends TestCase
{
    public function test_can_instantiate_RecommendationLog()
    {
        $model = new RecommendationLog();
        $this->assertInstanceOf(RecommendationLog::class, $model);
    }

    public function test_RecommendationLog_uses_mongodb_connection()
    {
        $model = new RecommendationLog();
        $this->assertEquals('mongodb', $model->getConnectionName());
    }
}