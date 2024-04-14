<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CarePlan>
 */
class CarePlanFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'case_id' => $this->faker->randomNumber,
            'case_type' => $this->faker->randomElement(['BZN', 'CGA']),
            'case_manager' => $this->faker->name,
            'handler' => $this->faker->name,
            'manager_id' => $this->faker->randomNumber,
            'handler_id' => $this->faker->randomNumber,
        ];
    }
}
