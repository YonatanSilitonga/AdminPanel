<?php

namespace Database\Factories;

use App\Models\Destination;
use Illuminate\Database\Eloquent\Factories\Factory;

class DestinationFactory extends Factory
{
    protected $model = Destination::class;

    public function definition(): array
    {
        $name = $this->faker->unique()->words(3, true);
        
        return [
            'name' => ucfirst($name),
            'slug' => str($name)->slug(),
            'description' => $this->faker->sentence(10),
            'long_description' => $this->faker->paragraphs(3, true),
            'latitude' => $this->faker->latitude(-90, 90),
            'longitude' => $this->faker->longitude(-180, 180),
            'category' => $this->faker->randomElement(['park', 'beach', 'museum', 'historical', 'nature', 'cultural', 'religi']),
            'rating' => $this->faker->randomFloat(2, 0, 5),
            'rating_count' => $this->faker->numberBetween(0, 500),
            'is_featured' => $this->faker->boolean(20),
            'is_trending' => $this->faker->boolean(30),
            'is_active' => true,
            'thumbnail_url' => $this->faker->imageUrl(300, 200, 'travel'),
            'cover_url' => $this->faker->imageUrl(1200, 400, 'nature'),
            'admin_id' => null,
        ];
    }
}
