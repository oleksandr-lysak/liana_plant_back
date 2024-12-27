<?php

namespace Database\Factories;

use App\Enums\SourceTimeslotsEnum;
use App\Models\Client;
use App\Models\Master;
use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TimeSlot>
 */
class TimeSlotFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {

            return [
                'master_id' => Master::inRandomOrder()->first()->id,
                'client_id' => Client::inRandomOrder()->first()->id,
                'date' => now(),
                'time' => $this->faker->time('H:i:s', '18:00'),
                'is_booked' => true,
                'service_id' => Service::inRandomOrder()->first()->id,
                'source' => fake()->randomElement(SourceTimeslotsEnum::cases()),
                'duration' => rand(30,180),
                'comment' => fake()->sentence(),
            ];
    }
}
