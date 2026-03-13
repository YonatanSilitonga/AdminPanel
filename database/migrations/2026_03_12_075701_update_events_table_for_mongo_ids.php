<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            // Drop foreign key first if it exists
            $table->dropForeign(['destination_id']);
            $table->dropColumn('destination_id');
        });

        Schema::table('events', function (Blueprint $table) {
            // Re-add destination_id as string for Mongo IDs
            $table->string('destination_id')->after('id')->nullable()->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn('destination_id');
        });

        Schema::table('events', function (Blueprint $table) {
            $table->foreignId('destination_id')->after('id')->nullable()->constrained('destinations')->onDelete('cascade');
        });
    }
};
