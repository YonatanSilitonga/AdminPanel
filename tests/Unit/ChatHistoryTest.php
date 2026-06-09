<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\ChatHistory;

class ChatHistoryTest extends TestCase
{
    public function test_can_instantiate_ChatHistory()
    {
        $model = new ChatHistory();
        $this->assertInstanceOf(ChatHistory::class, $model);
    }

    public function test_ChatHistory_uses_mysql_connection()
    {
        $model = new ChatHistory();
        $this->assertEquals('mysql', $model->getConnectionName());
    }
}