<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Destination;
use App\Models\Event;
use App\Models\Review;
use App\Models\Report;
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

        // 2. Create test users
        User::factory(15)->create();

        // 3. Create destinations
        $destinations = Destination::factory(10)->create();

        // 4. Create events for destinations
        $destinations->each(function ($destination) {
            Event::factory(2)->create(['destination_id' => $destination->id]);
        });

        // 5. Create reviews for destinations
        Review::factory(20)->create();

        // 6. Create reports
        Report::factory(5)->create();

        $this->command->info('Database seeded successfully!');
    }
}
