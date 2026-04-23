<?php

use Illuminate\Database\Migrations\Migration;
use MongoDB\Laravel\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'mongodb';

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::connection($this->connection)->create('ratings', function (Blueprint $collection) {
            $collection->index('destination_id');
            $collection->index('user_id');
            $collection->index('rating');
            $collection->index('status');
        });

        Schema::connection($this->connection)->create('reports', function (Blueprint $collection) {
            $collection->index('destination_id');
            $collection->index('user_id');
            $collection->index('status');
        });

        Schema::connection($this->connection)->create('chat_sessions', function (Blueprint $collection) {
            $collection->index('user_id');
            $collection->index('updated_at');
        });

        Schema::connection($this->connection)->create('carousel_banners', function (Blueprint $collection) {
            $collection->index('is_active');
            $collection->index('order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection($this->connection)->drop('ratings');
        Schema::connection($this->connection)->drop('reports');
        Schema::connection($this->connection)->drop('chat_sessions');
        Schema::connection($this->connection)->drop('carousel_banners');
    }
};
