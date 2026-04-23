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
        Schema::connection($this->connection)->create('destinations', function (Blueprint $collection) {
            $collection->index('name');
            $collection->index('category');
            $collection->index('is_active');
        });

        Schema::connection($this->connection)->create('events', function (Blueprint $collection) {
            $collection->index('name');
            $collection->unique('slug');
            $collection->index('category');
            $collection->index('start_date');
            $collection->index('is_active');
        });

        Schema::connection($this->connection)->create('budaya', function (Blueprint $collection) {
            $collection->index('name');
            $collection->index('category');
            $collection->index('is_active');
        });

        Schema::connection($this->connection)->create('mongo_fasilitas_umums', function (Blueprint $collection) {
            $collection->index('name');
            $collection->index('type');
            $collection->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection($this->connection)->drop('destinations');
        Schema::connection($this->connection)->drop('events');
        Schema::connection($this->connection)->drop('budaya');
        Schema::connection($this->connection)->drop('mongo_fasilitas_umums');
    }
};
