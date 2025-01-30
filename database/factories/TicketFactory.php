<?php

namespace Database\Factories;

use App\Models\Ticket;
use App\Models\Event;
use App\Models\Seat;
use Illuminate\Database\Eloquent\Factories\Factory;

class TicketFactory extends Factory
{
    protected $model = Ticket::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'event_id' => Event::factory(),
            'seat_id' => Seat::factory(),
            'price' => $this->faker->randomFloat(2, 10, 100),
            'status' => $this->faker->randomElement(['available', 'sold', 'reserved']),
        ];
    }
}
