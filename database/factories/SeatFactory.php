<?php

namespace Database\Factories;

use App\Models\Seat;
use App\Models\Venue;
use Illuminate\Database\Eloquent\Factories\Factory;

class SeatFactory extends Factory
{
    protected $model = Seat::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'number' => $this->faker->unique()->numberBetween(1, 100),
            'row' => $this->faker->randomLetter,
            'section' => $this->faker->randomElement(['A', 'B', 'C', 'D']),
            'status' => $this->faker->randomElement(['available', 'reserved']),
            'venue_id' => Venue::factory(),
            'price' => $this->faker->randomFloat(2, 10, 100), // Add price field
        ];
    }
}
