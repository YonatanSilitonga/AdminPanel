<?php

namespace Database\Factories;

use App\Models\Report;
use App\Models\User;
use App\Models\Destination;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReportFactory extends Factory
{
    protected $model = Report::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'reportable_type' => 'App\Models\Destination',
            'reportable_id' => Destination::factory()->create()->id,
            'reason' => $this->faker->randomElement(['spam', 'inappropriate', 'fake', 'harassment', 'other']),
            'description' => $this->faker->paragraph(),
            'attachment_path' => null,
            'status' => $this->faker->randomElement(['pending', 'investigating', 'resolved', 'dismissed']),
            'assigned_to' => null,
            'action_taken' => null,
            'action_reason' => null,
        ];
    }
}
