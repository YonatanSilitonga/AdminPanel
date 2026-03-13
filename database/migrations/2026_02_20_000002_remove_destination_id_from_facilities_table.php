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
        Schema::table('facilities', function (Blueprint $table) {
            if (Schema::hasColumn('facilities', 'destination_id')) {
                // Drop index if it exists
                try {
                    $table->dropIndex(['destination_id']);
                } catch (\Exception $e) {
                    // Ignore if index doesn't exist
                }
                
                $table->dropForeign(['destination_id']);
                $table->dropColumn('destination_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('facilities', function (Blueprint $table) {
            if (!Schema::hasColumn('facilities', 'destination_id')) {
                $table->foreignId('destination_id')->constrained()->onDelete('cascade');
                $table->index('destination_id');
            }
        });
    }
};
