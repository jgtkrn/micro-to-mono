<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MajorFallTable>
 */
class MajorFallTableFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'location' => $this->faker->randomNumber,
            'injury_sustained' => $this->faker->randomNumber,
            'fall_mechanism' => $this->faker->word,
            'fall_mechanism_other' => $this->faker->word,
            'fracture' => $this->faker->boolean,
            'fracture_text' => $this->faker->word,
        ];
    }
}
