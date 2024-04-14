<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Appointment>
 */
class AppointmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'cluster' => $this->faker->word(),
            'type' => $this->faker->word(),
            'name_en' => $this->faker->word(),
            'name_sc' => $this->faker->word(),
        ];
    }
}
