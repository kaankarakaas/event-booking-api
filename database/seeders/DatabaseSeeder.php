<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Event;
use App\Models\Reservation;
use App\Models\Seat;
use App\Models\Ticket;
use App\Models\User;
use App\Models\Venue;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        $user = User::where('email', 'admin@example.com')->first();

        if (!$user) {
         User::factory()->create([
             'name' => 'Admin User',
             'email' => 'admin@example.com',
             'password' => '$2y$10$CLIyjULb3Z21id4/xF.iJejK4ZvqLmJKxGU8rfRsN2k3SDzKhYNp2',
             'is_admin' => 1,
         ]);
        }
        Event::factory(20)->create();
        Reservation::factory(20)->create();
        Seat::factory(20)->create();
        Venue::factory(20)->create();
        Ticket::factory(20)->create();
    }
}
