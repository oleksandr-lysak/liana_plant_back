<?php

namespace Database\Factories;

use App\Models\Master;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Review>
 */
class ReviewFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'user_id' => User::inRandomOrder()->first()->id,
            'rating' => rand(1, 5),
            'review' => fake()->text(250),
            'master_id' => Master::inRandomOrder()->first()->id,
        ];
    }
}
