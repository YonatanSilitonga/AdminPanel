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

        // Reset Mongo data so reseeding fully replaces dataset
        MongoReview::query()->delete();
        MongoReport::query()->delete();
        MongoEvent::query()->delete();
        MongoDestination::query()->delete();

        // 2. Create test users
        User::factory(15)->create();

        // 3. Create destinations
        $destinations = [];
        for ($i = 1; $i <= 10; $i++) {
            $destinations[] = MongoDestination::create([
                'name' => "Destinasi " . $i,
                'description' => fake()->paragraph(),
                'location' => fake()->city(),
                'price' => fake()->numberBetween(50000, 500000),
                'rating' => fake()->randomFloat(1, 1, 5),
            ]);
        }

        // 4. Create events for destinations
        foreach ($destinations as $destination) {
            for ($j = 0; $j < 2; $j++) {
                MongoEvent::create([
                    'destination_id' => $destination->_id,
                    'title' => fake()->sentence(),
                    'description' => fake()->paragraph(),
                    'start_date' => fake()->dateTimeBetween('+1 days', '+30 days'),
                    'end_date' => fake()->dateTimeBetween('+30 days', '+60 days'),
                ]);
            }
        }

        // 5. Create new curated reviews for sentiment testing
        $reviewSamples = [
            ['rating' => 5, 'review' => 'Pelayanan cepat, staf ramah, dan kamar sangat bersih. Saya puas.'],
            ['rating' => 5, 'review' => 'Pemandangan indah, fasilitas lengkap, dan proses check in sangat mudah.'],
            ['rating' => 4, 'review' => 'Lokasi strategis, akses mudah, harga masih masuk akal.'],
            ['rating' => 5, 'review' => 'Pengalaman liburan menyenangkan, tempat nyaman untuk keluarga.'],
            ['rating' => 4, 'review' => 'Makanan enak, area rapi, petugas responsif saat diminta bantuan.'],
            ['rating' => 5, 'review' => 'Kamar luas, kasur nyaman, AC dingin, dan suasana tenang.'],
            ['rating' => 4, 'review' => 'Sangat recommended untuk akhir pekan, tidak terlalu ramai.'],
            ['rating' => 5, 'review' => 'Pelayanan front office profesional dan informatif dari awal sampai selesai.'],
            ['rating' => 4, 'review' => 'Kebersihan area publik terjaga dengan baik.'],
            ['rating' => 5, 'review' => 'Semua fasilitas berfungsi, pengalaman saya memuaskan.'],
            ['rating' => 4, 'review' => 'Harga sesuai kualitas, saya ingin kembali lagi.'],
            ['rating' => 5, 'review' => 'Tempat wisata ini bagus sekali, aman dan nyaman.'],

            ['rating' => 3, 'review' => 'Tempat cukup baik, pengalaman standar, tidak istimewa.'],
            ['rating' => 3, 'review' => 'Fasilitas dasar tersedia, pelayanan biasa saja.'],
            ['rating' => 3, 'review' => 'Lokasi lumayan bagus, tetapi belum terlalu menarik.'],
            ['rating' => 3, 'review' => 'Kamar cukup bersih, ukuran sedang, cocok untuk singgah.'],
            ['rating' => 3, 'review' => 'Makanan cukup enak, pilihan menu terbatas.'],
            ['rating' => 3, 'review' => 'Waktu tunggu normal, tidak cepat juga tidak lambat.'],
            ['rating' => 3, 'review' => 'Suasana tenang, fasilitas standar sesuai kelasnya.'],
            ['rating' => 3, 'review' => 'Area parkir cukup luas, namun penunjuk arah kurang jelas.'],
            ['rating' => 3, 'review' => 'Tidak ada masalah besar, tapi juga tidak terlalu berkesan.'],
            ['rating' => 3, 'review' => 'Harga dan layanan terasa seimbang.'],
            ['rating' => 3, 'review' => 'Kondisi umum oke untuk kunjungan singkat.'],
            ['rating' => 3, 'review' => 'Saya menilai pengalaman ini biasa saja.'],

            ['rating' => 1, 'review' => 'Kamar kotor, bau lembap, dan sprei tidak layak pakai.'],
            ['rating' => 2, 'review' => 'Pelayanan lambat, staf kurang ramah, proses check in lama.'],
            ['rating' => 1, 'review' => 'Fasilitas tidak sesuai iklan dan banyak yang rusak.'],
            ['rating' => 2, 'review' => 'Makanan datang dingin dan rasanya tidak enak.'],
            ['rating' => 1, 'review' => 'Toilet kotor, air kecil, kebersihan sangat buruk.'],
            ['rating' => 1, 'review' => 'Lokasi sulit dijangkau, informasi arah membingungkan.'],
            ['rating' => 2, 'review' => 'Suasana bising dan tidak nyaman untuk istirahat.'],
            ['rating' => 1, 'review' => 'Pengalaman mengecewakan, saya tidak akan kembali.'],
            ['rating' => 2, 'review' => 'AC mati, kamar panas, komplain tidak cepat ditangani.'],
            ['rating' => 1, 'review' => 'Area tidak terawat, banyak sampah di sekitar lokasi.'],
            ['rating' => 2, 'review' => 'Harga mahal untuk kualitas layanan yang rendah.'],
            ['rating' => 1, 'review' => 'Sangat tidak rekomendasi karena pelayanan buruk.'],
        ];

        foreach ($reviewSamples as $index => $sample) {
            MongoReview::create([
                'destination_id' => $destinations[$index % count($destinations)]->_id,
                'user_id' => 'user_' . str_pad((string) ($index + 1), 3, '0', STR_PAD_LEFT),
                'rating' => $sample['rating'],
                'review' => $sample['review'],
                'status' => 'approved',
            ]);
        }

        // 6. Create reports
        for ($i = 0; $i < 5; $i++) {
            MongoReport::create([
                'destination_id' => $destinations[array_rand($destinations)]->_id,
                'user_id' => 'user_' . fake()->randomNumber(),
                'description' => fake()->paragraph(),
                'status' => fake()->randomElement(['pending', 'reviewed', 'resolved']),
                'assigned_to' => null,
                'action_taken' => null,
                'action_reason' => null,
            ]);
        }

        $this->command->info('Database seeded successfully!');
    }
}

