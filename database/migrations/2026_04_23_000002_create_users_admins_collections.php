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
        Schema::connection($this->connection)->create('users', function (Blueprint $collection) {
            $collection->unique('email');
            $collection->index('is_active');
        });

        Schema::connection($this->connection)->create('admins', function (Blueprint $collection) {
            $collection->unique('email');
            $collection->index('role_id');
            $collection->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection($this->connection)->drop('users');
        Schema::connection($this->connection)->drop('admins');
    }
};
