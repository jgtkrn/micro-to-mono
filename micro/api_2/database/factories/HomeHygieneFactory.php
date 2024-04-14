<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\HomeHygiene>
 */
class HomeHygieneFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'elder_home_hygiene' => $this->faker->randomElement([1, 2, 3, 4, 5]),
        ];
    }
}
