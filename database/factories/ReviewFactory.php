<?php

namespace Database\Factories;

use App\Models\Review;
use App\Models\User;
use App\Models\Destination;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReviewFactory extends Factory
{
    protected $model = Review::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'destination_id' => Destination::factory(),
            'rating' => $this->faker->numberBetween(1, 5),
            'title' => $this->faker->sentence(),
            'content' => $this->faker->paragraphs(2, true),
            'status' => $this->faker->randomElement(['pending', 'approved', 'rejected']),
            'reported_count' => $this->faker->numberBetween(0, 5),
            'approved_by' => null,
        ];
    }
}
