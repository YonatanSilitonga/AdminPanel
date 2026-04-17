<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MongoDB\MongoBudaya;

class BudayaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Hapus data budaya lama jika ada (agar tidak double ketika dijalankan berulang)
        MongoBudaya::truncate();

        $budayas = [
            [
                'name' => 'Makam Raja Sidabutar',
                'category' => 'Sejarah',
                'category_mobile' => 'SEJARAH BATAK',
                'location' => 'Pulau Samosir',
                'description' => 'Situs pemakaman megalitik kuno di Desa Tomok yang menyimpan sejarah kepemimpinan Raja Batak ratusan tahun silam.',
                'image_url' => 'https://images.unsplash.com/photo-1596422846543-75c6fa195c11?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=60',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Huta Siallagan',
                'category' => 'Tradisi',
                'category_mobile' => 'DESA TRADISIONAL',
                'location' => 'Ambarita',
                'description' => 'Desa adat dengan rumah Bolon dan kursi batu persidangan tempat dilaksanakan hukum adat suku Batak Toba.',
                'image_url' => 'https://images.unsplash.com/photo-1549474759-4bac3485ceae?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=60',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Rumah Adat Batak (Ruma Gorga)',
                'category' => 'Rumah Adat',
                'category_mobile' => 'RUMAH ADAT',
                'location' => 'Toba Samosir',
                'description' => 'Rumah tradisional suku batak dengan ukiran indah (gorga) peninggalan budaya nenek moyang.',
                'image_url' => 'https://images.unsplash.com/photo-1518599904199-0ca897819ddb?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=60',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Tradisi dan Upacara Adat',
                'category' => 'Tradisi',
                'category_mobile' => 'TRADISI',
                'location' => 'Seluruh Kawasan',
                'description' => 'Berbagai upacara dan acara adat tradisional masyarakat Batak seperti Mangokal Holi dan lainnya.',
                'image_url' => 'https://images.unsplash.com/photo-1533158326339-7f3cf2404354?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=60',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Asal-Usul Danau Toba',
                'category' => 'Cerita Rakyat',
                'category_mobile' => 'LEGENDA',
                'location' => 'Cerita Rakyat',
                'description' => 'Pelajari kisah rakyat tentang seorang pemuda bernama Toba dan ikan emas ajaib yang melahirkan danau vulkanik raksasa.',
                'image_url' => 'https://images.unsplash.com/photo-1526768393529-6500d02464bc?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=60',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Mie Gomak & Arsik',
                'category' => 'Kuliner',
                'category_mobile' => 'KULINER BATAK',
                'location' => 'Kuliner Lokal',
                'description' => 'Nikmati cita rasa khas bumbu andaliman dalam masakan tradisional Batak yang menggugah selera.',
                'image_url' => 'https://images.unsplash.com/photo-1604908176997-125f25cc6f3d?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=60',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($budayas as $budaya) {
            MongoBudaya::create($budaya);
        }
    }
}
