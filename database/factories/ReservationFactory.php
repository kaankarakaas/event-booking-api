<?php

namespace Database\Factories;

use App\Models\Reservation;
use App\Models\User;
use App\Models\Event;
use App\Models\Seat;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReservationFactory extends Factory
{
    protected $model = Reservation::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'event_id' => Event::factory(),
            'seat_id' => Seat::factory(),
            'status' => $this->faker->randomElement(['pending', 'confirmed', 'cancelled']),
            'expires_at' => $this->faker->dateTimeBetween('now', '+1 hour'),
            'total_amount' => $this->faker->randomFloat(2, 10, 100), // Add this line
        ];
    }
}
