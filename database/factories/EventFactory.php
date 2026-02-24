<?php

namespace Database\Factories;

use App\Models\Event;
use App\Models\Destination;
use Illuminate\Database\Eloquent\Factories\Factory;

class EventFactory extends Factory
{
    protected $model = Event::class;

    public function definition(): array
    {
        $name = $this->faker->unique()->words(3, true);
        $startDate = $this->faker->dateTimeBetween('+1 day', '+30 days');
        
        return [
            'destination_id' => Destination::factory(),
            'name' => ucfirst($name),
            'slug' => str($name)->slug(),
            'description' => $this->faker->sentence(10),
            'long_description' => $this->faker->paragraphs(3, true),
            'start_date' => $startDate,
            'end_date' => $this->faker->dateTimeBetween($startDate, $startDate->modify('+7 days')),
            'banner_url' => $this->faker->imageUrl(1200, 400, 'business'),
            'is_active' => true,
            'admin_id' => null,
        ];
    }
}
