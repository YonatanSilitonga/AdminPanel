<?php

namespace Tests\Unit\MongoDB;

use Tests\TestCase;
use App\Models\MongoDB\CarouselBanner;

class CarouselBannerTest extends TestCase
{
    public function test_can_instantiate_CarouselBanner()
    {
        $model = new CarouselBanner();
        $this->assertInstanceOf(CarouselBanner::class, $model);
    }

    public function test_CarouselBanner_uses_mongodb_connection()
    {
        $model = new CarouselBanner();
        $this->assertEquals('mongodb', $model->getConnectionName());
    }
}