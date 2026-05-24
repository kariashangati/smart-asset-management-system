<?php

namespace Database\Factories;

use App\Models\Asset;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\LocationLog>
 */
class LocationLogFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'asset_id' => Asset::factory(),
            'latitude' => $this->faker->latitude,
            'longitude' => $this->faker->longitude,
            'speed' => $this->faker->numberBetween(0, 120),
            'motion_detected' => $this->faker->boolean,
            'recorded_at' => $this->faker->dateTimeBetween('-30 days', 'now'),
            'received_at' => now(),
        ];
    }
}
