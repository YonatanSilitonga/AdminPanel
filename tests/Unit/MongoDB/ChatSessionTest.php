<?php

namespace Tests\Unit\MongoDB;

use Tests\TestCase;
use App\Models\MongoDB\ChatSession;

class ChatSessionTest extends TestCase
{
    public function test_can_instantiate_ChatSession()
    {
        $model = new ChatSession();
        $this->assertInstanceOf(ChatSession::class, $model);
    }

    public function test_ChatSession_uses_mongodb_connection()
    {
        $model = new ChatSession();
        $this->assertEquals('mongodb', $model->getConnectionName());
    }
}