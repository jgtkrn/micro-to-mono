<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CrossDisciplinary>
 */
class CrossDisciplinaryFactory extends Factory
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
            'role' => $this->faker->word,
            'comments' => $this->faker->word,
            'name' => $this->faker->word,
            'date' => $this->faker->dateTime
        ];
    }
}
