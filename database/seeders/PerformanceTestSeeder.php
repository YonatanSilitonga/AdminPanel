<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\Admin;
use App\Models\Role;
use App\Models\User;
use App\Models\Destination;
use App\Models\DestinationGallery;
use App\Models\Event;
use App\Models\Review;
use App\Models\Facility;
use App\Models\RecommendationLog;
use App\Models\Report;
use Carbon\Carbon;

class PerformanceTestSeeder extends Seeder
{
    /**
     * Run the database seeds for performance testing.
     * 
     * This seeder creates realistic data volumes to test system performance:
     * - 1000+ destinations
     * - 10,000+ reviews
     * - 50,000+ recommendation logs
     * - 100,000+ chatbot messages
     */
    public function run(): void
    {
        $this->command->info('🚀 Starting Performance Test Data Seeding...');
        
        // Ensure test admin exists
        $this->createTestAdmin();
        
        // Create test users
        $users = $this->createUsers(100);
        $this->command->info('✅ Created 100 test users');
        
        // Create facilities
        $facilities = $this->createFacilities();
        $this->command->info('✅ Created facilities');
        
        // Create destinations with galleries
        $destinations = $this->createDestinations(1000, $facilities);
        $this->command->info('✅ Created 1000 destinations with galleries');
        
        // Create events
        $this->createEvents(200);
        $this->command->info('✅ Created 200 events');
        
        // Create reviews
        $this->createReviews(10000, $users, $destinations);
        $this->command->info('✅ Created 10,000 reviews');
        
        // Create recommendation logs (MongoDB)
        $this->createRecommendationLogs(50000, $users, $destinations);
        $this->command->info('✅ Created 50,000 recommendation logs');
        
        // Create chatbot messages (MongoDB)
        $this->createChatbotMessages(100000, $users);
        $this->command->info('✅ Created 100,000 chatbot messages');
        
        // Create reports (MongoDB)
        $this->createReports(5000, $users, $destinations);
        $this->command->info('✅ Created 5,000 reports');
        
        // Create budaya
        $this->createBudaya(100);
        $this->command->info('✅ Created 100 budaya entries');
        
        // Create fasilitas umum
        $this->createFasilitasUmum(200);
        $this->command->info('✅ Created 200 fasilitas umum entries');
        
        $this->command->info('🎉 Performance test data seeding completed!');
    }
    
    /**
     * Create test admin user
     */
    private function createTestAdmin(): void
    {
        // Get or create super_admin role
        $role = Role::firstOrCreate(
            ['name' => 'super_admin'],
            [
                'display_name' => 'Super Administrator',
                'description' => 'Full system access',
            ]
        );
        
        // Create super admin if not exists
        Admin::firstOrCreate(
            ['email' => 'superadmin@smarttourism.local'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('SuperAdmin@123'),
                'role_id' => $role->_id,
                'is_active' => true,
            ]
        );
        
        $this->command->info('✅ Test admin user ready (superadmin@smarttourism.local / SuperAdmin@123)');
    }
    
    /**
     * Create test users
     */
    private function createUsers(int $count): array
    {
        $users = [];
        
        for ($i = 1; $i <= $count; $i++) {
            $users[] = User::create([
                'name' => "Test User {$i}",
                'email' => "testuser{$i}@example.com",
                'password' => Hash::make('password123'),
                'phone' => '+62812' . str_pad($i, 8, '0', STR_PAD_LEFT),
                'is_active' => true,
                'created_at' => Carbon::now()->subDays(rand(1, 365)),
            ]);
        }
        
        return $users;
    }
    
    /**
     * Create facilities
     */
    private function createFacilities(): array
    {
        $facilityNames = [
            'Parking Area', 'Restaurant', 'Toilet', 'Prayer Room', 'WiFi',
            'ATM', 'Souvenir Shop', 'Information Center', 'First Aid',
            'Children Playground', 'Gazebo', 'Camping Area', 'Swimming Pool',
            'Boat Rental', 'Bicycle Rental', 'Locker', 'Security', 'Guide Service',
        ];
        
        $facilities = [];
        
        foreach ($facilityNames as $name) {
            $facilities[] = Facility::firstOrCreate(
                ['name' => $name],
                ['icon' => 'fas fa-' . Str::slug($name)]
            );
        }
        
        return $facilities;
    }
    
    /**
     * Create destinations with galleries
     */
    private function createDestinations(int $count, array $facilities): array
    {
        $categories = ['wisata_alam', 'wisata_budaya', 'wisata_kuliner', 'wisata_religi', 'wisata_sejarah'];
        $admin = Admin::where('email', 'superadmin@smarttourism.local')->first();
        $destinations = [];
        
        for ($i = 1; $i <= $count; $i++) {
            $name = "Destination Test {$i}";
            $slug = Str::slug($name);
            
            $destination = Destination::create([
                'name' => $name,
                'slug' => $slug . '-' . $i,
                'description' => "This is a test destination {$i} for performance testing purposes.",
                'long_description' => "Long description for destination {$i}. " . str_repeat("Lorem ipsum dolor sit amet. ", 20),
                'category' => $categories[array_rand($categories)],
                'latitude' => 2.3 + (rand(-1000, 1000) / 1000),
                'longitude' => 99.0 + (rand(-1000, 1000) / 1000),
                'thumbnail_url' => "https://placehold.co/400x300?text=Destination+{$i}",
                'cover_url' => "https://placehold.co/1200x600?text=Destination+{$i}",
                'rating' => rand(30, 50) / 10,
                'admin_id' => $admin->_id,
                'is_active' => rand(0, 10) > 1, // 90% active
                'is_featured' => rand(0, 10) > 8, // 20% featured
                'is_trending' => rand(0, 10) > 7, // 30% trending
                'created_at' => Carbon::now()->subDays(rand(1, 365)),
            ]);
            
            // Add 3-7 gallery images per destination
            $galleryCount = rand(3, 7);
            for ($g = 1; $g <= $galleryCount; $g++) {
                DestinationGallery::create([
                    'destination_id' => $destination->id,
                    'image_url' => "https://placehold.co/800x600?text=Gallery+{$i}+{$g}",
                    'caption' => "Gallery image {$g} for destination {$i}",
                    'order' => $g,
                ]);
            }
            
            // Attach 2-5 random facilities
            $randomFacilities = array_rand($facilities, rand(2, min(5, count($facilities))));
            if (!is_array($randomFacilities)) {
                $randomFacilities = [$randomFacilities];
            }
            foreach ($randomFacilities as $index) {
                $destination->facilities()->attach($facilities[$index]->id);
            }
            
            $destinations[] = $destination;
            
            // Progress indicator every 100 destinations
            if ($i % 100 === 0) {
                $this->command->info("  → Created {$i} destinations...");
            }
        }
        
        return $destinations;
    }
    
    /**
     * Create events
     */
    private function createEvents(int $count): void
    {
        $categories = ['festival', 'concert', 'exhibition', 'workshop', 'conference'];
        $admin = Admin::where('email', 'superadmin@smarttourism.local')->first();
        
        for ($i = 1; $i <= $count; $i++) {
            $startDate = Carbon::now()->addDays(rand(-30, 180));
            
            Event::create([
                'name' => "Event Test {$i}",
                'slug' => Str::slug("Event Test {$i}") . '-' . $i,
                'description' => "Test event {$i} for performance testing.",
                'category' => $categories[array_rand($categories)],
                'location' => "Location {$i}, Lake Toba",
                'start_date' => $startDate,
                'end_date' => $startDate->copy()->addDays(rand(1, 7)),
                'thumbnail_url' => "https://placehold.co/400x300?text=Event+{$i}",
                'admin_id' => $admin->_id,
                'is_active' => rand(0, 10) > 2, // 80% active
                'created_at' => Carbon::now()->subDays(rand(1, 180)),
            ]);
            
            if ($i % 50 === 0) {
                $this->command->info("  → Created {$i} events...");
            }
        }
    }
    
    /**
     * Create reviews
     */
    private function createReviews(int $count, array $users, array $destinations): void
    {
        $statuses = ['pending', 'approved', 'rejected'];
        $admin = Admin::where('email', 'superadmin@smarttourism.local')->first();
        
        for ($i = 1; $i <= $count; $i++) {
            $user = $users[array_rand($users)];
            $destination = $destinations[array_rand($destinations)];
            $status = $statuses[array_rand($statuses)];
            
            Review::create([
                'user_id' => $user->id,
                'destination_id' => $destination->id,
                'rating' => rand(1, 5),
                'title' => "Review title {$i}",
                'content' => "This is test review content {$i}. " . str_repeat("Great place to visit! ", rand(5, 15)),
                'status' => $status,
                'reported_count' => rand(0, 100) > 95 ? rand(1, 5) : 0, // 5% reported
                'approved_by' => $status === 'approved' ? $admin->_id : null,
                'created_at' => Carbon::now()->subDays(rand(1, 365)),
            ]);
            
            if ($i % 1000 === 0) {
                $this->command->info("  → Created {$i} reviews...");
            }
        }
    }
    
    /**
     * Create recommendation logs (MongoDB)
     */
    private function createRecommendationLogs(int $count, array $users, array $destinations): void
    {
        $algorithms = ['content_based', 'collaborative', 'hybrid', 'popularity_based'];
        
        for ($i = 1; $i <= $count; $i++) {
            $user = $users[array_rand($users)];
            $recommendedDests = array_slice($destinations, rand(0, count($destinations) - 6), 5);
            
            RecommendationLog::create([
                'user_id' => (string)$user->id,
                'algorithm' => $algorithms[array_rand($algorithms)],
                'input_preferences' => [
                    'categories' => ['wisata_alam', 'wisata_budaya'],
                    'budget' => rand(100000, 1000000),
                    'duration' => rand(1, 7),
                ],
                'recommended_destinations' => array_map(function($dest) {
                    return [
                        'destination_id' => (string)$dest->id,
                        'destination_name' => $dest->name,
                        'score' => rand(70, 100) / 100,
                    ];
                }, $recommendedDests),
                'metadata' => [
                    'processing_time_ms' => rand(50, 500),
                    'model_version' => '1.0',
                ],
                'created_at' => Carbon::now()->subDays(rand(1, 365)),
            ]);
            
            if ($i % 5000 === 0) {
                $this->command->info("  → Created {$i} recommendation logs...");
            }
        }
    }
    
    /**
     * Create chatbot messages (MongoDB)
     */
    private function createChatbotMessages(int $count, array $users): void
    {
        $intents = ['greeting', 'destination_inquiry', 'booking_help', 'general_info', 'complaint'];
        
        for ($i = 1; $i <= $count; $i++) {
            $user = $users[array_rand($users)];
            
            DB::connection('mongodb')->collection('chat_sessions')->insert([
                'user_id' => (string)$user->id,
                'messages' => [
                    [
                        'role' => 'user',
                        'content' => "Test message {$i} from user",
                        'timestamp' => Carbon::now()->subMinutes(rand(1, 525600)),
                    ],
                    [
                        'role' => 'assistant',
                        'content' => "Test response {$i} from chatbot",
                        'timestamp' => Carbon::now()->subMinutes(rand(1, 525600)),
                        'intent' => $intents[array_rand($intents)],
                        'confidence' => rand(70, 100) / 100,
                    ],
                ],
                'status' => rand(0, 10) > 8 ? 'flagged' : 'active',
                'created_at' => Carbon::now()->subDays(rand(1, 365)),
                'updated_at' => Carbon::now()->subDays(rand(0, 365)),
            ]);
            
            if ($i % 10000 === 0) {
                $this->command->info("  → Created {$i} chatbot messages...");
            }
        }
    }
    
    /**
     * Create reports (MongoDB)
     */
    private function createReports(int $count, array $users, array $destinations): void
    {
        $types = ['inappropriate_content', 'spam', 'misinformation', 'harassment', 'other'];
        $statuses = ['pending', 'resolved', 'dismissed'];
        
        for ($i = 1; $i <= $count; $i++) {
            $user = $users[array_rand($users)];
            $destination = $destinations[array_rand($destinations)];
            
            Report::create([
                'user_id' => (string)$user->id,
                'destination_id' => (string)$destination->id,
                'type' => $types[array_rand($types)],
                'description' => "Test report {$i}. " . str_repeat("Report description. ", rand(3, 10)),
                'status' => $statuses[array_rand($statuses)],
                'priority' => rand(1, 5),
                'created_at' => Carbon::now()->subDays(rand(1, 365)),
            ]);
            
            if ($i % 1000 === 0) {
                $this->command->info("  → Created {$i} reports...");
            }
        }
    }
    
    /**
     * Create budaya entries (MongoDB)
     */
    private function createBudaya(int $count): void
    {
        $categories = ['traditional_dance', 'traditional_music', 'traditional_craft', 'traditional_ceremony', 'folklore'];
        
        for ($i = 1; $i <= $count; $i++) {
            DB::connection('mongodb')->collection('budaya')->insert([
                'name' => "Budaya Test {$i}",
                'slug' => Str::slug("Budaya Test {$i}") . '-' . $i,
                'category' => $categories[array_rand($categories)],
                'description' => "Test budaya entry {$i}. " . str_repeat("Cultural description. ", 10),
                'thumbnail_url' => "https://placehold.co/400x300?text=Budaya+{$i}",
                'is_active' => rand(0, 10) > 1,
                'created_at' => Carbon::now()->subDays(rand(1, 365)),
                'updated_at' => Carbon::now()->subDays(rand(0, 365)),
            ]);
        }
    }
    
    /**
     * Create fasilitas umum entries (MongoDB)
     */
    private function createFasilitasUmum(int $count): void
    {
        $types = ['hospital', 'police_station', 'gas_station', 'bank', 'post_office', 'market', 'hotel', 'restaurant'];
        
        for ($i = 1; $i <= $count; $i++) {
            DB::connection('mongodb')->collection('mongo_fasilitas_umums')->insert([
                'name' => "Fasilitas {$i}",
                'type' => $types[array_rand($types)],
                'address' => "Address {$i}, Lake Toba Region",
                'latitude' => 2.3 + (rand(-1000, 1000) / 1000),
                'longitude' => 99.0 + (rand(-1000, 1000) / 1000),
                'phone' => '+62812' . str_pad($i, 8, '0', STR_PAD_LEFT),
                'is_active' => rand(0, 10) > 1,
                'created_at' => Carbon::now()->subDays(rand(1, 365)),
                'updated_at' => Carbon::now()->subDays(rand(0, 365)),
            ]);
        }
    }
}
