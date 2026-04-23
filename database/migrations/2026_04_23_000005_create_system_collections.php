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
        Schema::connection($this->connection)->create('app_settings', function (Blueprint $collection) {
            $collection->unique('key');
        });

        Schema::connection($this->connection)->create('admin_activity_logs', function (Blueprint $collection) {
            $collection->index('admin_id');
            $collection->index('action');
            $collection->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection($this->connection)->drop('app_settings');
        Schema::connection($this->connection)->drop('admin_activity_logs');
    }
};
