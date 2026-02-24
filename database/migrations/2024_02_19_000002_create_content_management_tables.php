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
        // Destinations table
        Schema::create('destinations', function (Blueprint $table) {
            $table->id();
            $table->string('name')->index();
            $table->string('slug')->unique()->index();
            $table->text('description');
            $table->longText('long_description')->nullable();
            $table->decimal('latitude', 10, 8)->nullable()->index();
            $table->decimal('longitude', 11, 8)->nullable()->index();
            $table->string('category')->index(); // park, beach, museum, historical, etc.
            $table->decimal('rating', 3, 2)->default(0);
            $table->integer('rating_count')->default(0);
            $table->boolean('is_featured')->default(false)->index();
            $table->boolean('is_trending')->default(false)->index();
            $table->boolean('is_active')->default(true)->index();
            $table->string('thumbnail_url')->nullable();
            $table->string('cover_url')->nullable();
            $table->foreignId('admin_id')->nullable()->constrained('admins');
            $table->softDeletes();
            $table->timestamps();
        });

        // Destination gallery table
        Schema::create('destination_galleries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('destination_id')->constrained()->onDelete('cascade');
            $table->string('image_url');
            $table->string('caption')->nullable();
            $table->integer('order')->default(0);
            $table->boolean('is_primary')->default(false);
            $table->timestamps();

            $table->index(['destination_id', 'order']);
        });

        // Facilities table
        Schema::create('facilities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('destination_id')->constrained()->onDelete('cascade');
            $table->string('name'); // parking, toilet, wifi, restaurant, etc.
            $table->string('icon_url')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();

            $table->index('destination_id');
        });

        // Events table
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('destination_id')->constrained()->onDelete('cascade');
            $table->string('name')->index();
            $table->string('slug')->unique();
            $table->text('description');
            $table->longText('long_description')->nullable();
            $table->dateTime('start_date')->index();
            $table->dateTime('end_date')->index();
            $table->string('banner_url')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->foreignId('admin_id')->nullable()->constrained('admins');
            $table->softDeletes();
            $table->timestamps();
        });

        // Reviews table
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('destination_id')->constrained()->onDelete('cascade');
            $table->integer('rating')->comment('1-5 stars');
            $table->string('title')->nullable();
            $table->text('content');
            $table->string('status')->default('pending')->index(); // pending, approved, rejected
            $table->integer('reported_count')->default(0);
            $table->foreignId('approved_by')->nullable()->constrained('admins');
            $table->softDeletes();
            $table->timestamps();

            $table->index(['user_id', 'destination_id']);
            $table->index(['status', 'created_at']);
        });

        // Reports table (Polymorphic)
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->string('reportable_type'); // App\Models\Destination, App\Models\Review, App\Models\Event
            $table->unsignedBigInteger('reportable_id');
            $table->string('reason')->index(); // spam, inappropriate, fake, harassment, etc.
            $table->text('description')->nullable();
            $table->string('attachment_path')->nullable();
            $table->string('status')->default('pending')->index(); // pending, investigating, resolved, dismissed
            $table->foreignId('assigned_to')->nullable()->constrained('admins');
            $table->string('action_taken')->nullable(); // delete_content, warn_user, ignore, etc.
            $table->text('action_reason')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->index(['reportable_type', 'reportable_id']);
            $table->index(['status', 'created_at']);
            $table->index('user_id');
        });

        // Chat History table
        Schema::create('chat_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->uuid('conversation_id')->index();
            $table->text('content');
            $table->string('role')->index(); // user, assistant
            $table->boolean('is_flagged')->default(false)->index();
            $table->string('flag_reason')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'conversation_id']);
            $table->index(['created_at']);
        });

        // Recommendation Logs table
        Schema::create('recommendation_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('recommended_destination_id')->constrained('destinations');
            $table->json('behavior_data'); // viewed, searched, saved, etc.
            $table->decimal('recommendation_score', 5, 3)->nullable();
            $table->boolean('is_clicked')->default(false);
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
            $table->index(['recommended_destination_id']);
        });

        // Settings table
        Schema::create('app_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->longText('value')->nullable();
            $table->string('type')->default('string'); // string, json, boolean, integer
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('app_settings');
        Schema::dropIfExists('recommendation_logs');
        Schema::dropIfExists('chat_histories');
        Schema::dropIfExists('reports');
        Schema::dropIfExists('reviews');
        Schema::dropIfExists('events');
        Schema::dropIfExists('facilities');
        Schema::dropIfExists('destination_galleries');
        Schema::dropIfExists('destinations');
    }
};
