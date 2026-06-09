<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\DestinationGallery;

class DestinationGalleryTest extends TestCase
{
    public function test_can_instantiate_DestinationGallery()
    {
        $model = new DestinationGallery();
        $this->assertInstanceOf(DestinationGallery::class, $model);
    }

    public function test_DestinationGallery_uses_mongodb_connection()
    {
        $model = new DestinationGallery();
        $this->assertEquals('mongodb', $model->getConnectionName());
    }
}