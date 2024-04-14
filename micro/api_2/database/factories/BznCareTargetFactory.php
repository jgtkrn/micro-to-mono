<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BznCareTarget>
 */
class BznCareTargetFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'intervention' => $this->faker->text,
            'target_type' => $this->faker->randomNumber(),
            'plan' => $this->faker->text
        ];
    }
}
