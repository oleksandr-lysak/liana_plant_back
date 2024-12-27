<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Client>
 */
class ClientFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'phone' => fake()->unique()->phoneNumber(),
            'verified_at' => fake()->dateTime(),
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (Client $client) {
            $user = User::factory()->create();
            $user->name = $client->name;
            $user->save();
            $client->update([
                'user_id' => $user->id,
            ]);
        });
    }
}
