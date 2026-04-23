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
        Schema::connection($this->connection)->create('favorites', function (Blueprint $collection) {
            $collection->index('user_id');
            $collection->index('destination_id');
        });

        Schema::connection($this->connection)->create('trip_plans', function (Blueprint $collection) {
            $collection->index('user_id');
        });

        Schema::connection($this->connection)->create('cache', function (Blueprint $collection) {
            $collection->index('key');
        });

        Schema::connection($this->connection)->create('sessions', function (Blueprint $collection) {
            $collection->index('user_id');
            $collection->index('last_activity');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection($this->connection)->drop('favorites');
        Schema::connection($this->connection)->drop('trip_plans');
        Schema::connection($this->connection)->drop('cache');
        Schema::connection($this->connection)->drop('sessions');
    }
};
