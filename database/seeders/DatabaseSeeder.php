<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\MongoDB\MongoDestination;
use App\Models\MongoDB\MongoEvent;
use App\Models\MongoDB\MongoReview;
use App\Models\MongoDB\MongoReport;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed in proper order to avoid foreign key constraints

        // 1. Seed admin users with roles and permissions
        $this->call(AdminSeeder::class);

        // 2. Create test users (if using MongoDB for users, use mongo model, otherwise SQL works if configured)
        try {
            User::factory(15)->create();
        } catch (\Throwable $e) {
            $this->command->warn('User seeding skipped: ' . $e->getMessage());
        }

        // 3. Create destinations (using MongoDB model)
        $destinations = [];
        for ($i = 0; $i < 10; $i++) {
            $destination = MongoDestination::create([
                'name' => "Destination " . ($i + 1),
                'description' => "Description for destination " . ($i + 1),
                'category' => ['Alam', 'Budaya', 'Hiburan'][rand(0, 2)],
                'latitude' => -6.2 + (rand(-100, 100) / 1000),
                'longitude' => 106.8 + (rand(-100, 100) / 1000),
                'is_active' => true,
                'is_featured' => rand(0, 1) === 1,
                'average_rating' => rand(3, 5),
                'total_reviews' => rand(0, 100),
            ]);
            $destinations[] = $destination;
        }

        // 4. Create events for destinations (using MongoDB model)
        foreach ($destinations as $destination) {
            for ($i = 0; $i < 2; $i++) {
                MongoEvent::create([
                    'name' => "Event " . ($i + 1) . " at " . $destination->name,
                    'description' => "Description for event",
                    'destination_id' => $destination->_id ?? $destination->id,
                    'start_date' => now()->addDays(rand(1, 30))->toDateTimeString(),
                    'end_date' => now()->addDays(rand(31, 60))->toDateTimeString(),
                    'is_active' => true,
                ]);
            }
        }

        // 5. Create reviews (using MongoDB model)
        for ($i = 0; $i < 20; $i++) {
            MongoReview::create([
                'destination_id' => $destinations[rand(0, count($destinations) - 1)]->_id ?? $destinations[rand(0, count($destinations) - 1)]->id,
                'user_id' => rand(1, 15),
                'rating' => rand(1, 5),
                'title' => "Review Title " . ($i + 1),
                'content' => "Review content here",
                'status' => 'approved',
            ]);
        }

        // 6. Create reports (using MongoDB model)
        for ($i = 0; $i < 5; $i++) {
            MongoReport::create([
                'destination_id' => $destinations[rand(0, count($destinations) - 1)]->_id ?? $destinations[rand(0, count($destinations) - 1)]->id,
                'user_id' => rand(1, 15),
                'reason' => ['Spam', 'Inappropriate', 'Offensive'][rand(0, 2)],
                'description' => "Report description",
                'status' => 'pending',
            ]);
        }

        $this->command->info('Database seeded successfully!');
    }
}

