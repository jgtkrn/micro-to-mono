<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PhysiologicalMeasurementForm>
 */
class PhysiologicalMeasurementFormFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'temperature' => $this->faker->randomFloat(2,25,48), //decimal
            'sitting_sbp' => $this->faker->numberBetween(10,20),
            'sitting_dbp' => $this->faker->numberBetween(10,20),
            'standing_sbp' => $this->faker->numberBetween(10,20),
            'standing_dbp' => $this->faker->numberBetween(10,20),
            'blood_oxygen' => $this->faker->numberBetween(1,2),
            'heart_rate' => $this->faker->numberBetween(1,2),
            'heart_rythm' => $this->faker->numberBetween(1,2),
            'kardia' => $this->faker->numberBetween(1,2),
            'blood_sugar' => $this->faker->randomFloat(2,10,20), //decimal
            'blood_sugar_time' => $this->faker->numberBetween(1,2),
            'waistline' => $this->faker->randomFloat(2,20,250), //decimal
            'weight' => $this->faker->randomFloat(2,10,200), //decimal
            'height' => $this->faker->randomFloat(2,2,3), //decimal
            'respiratory_rate' => $this->faker->numberBetween(1, 2),
            'blood_options' => $this->faker->numberBetween(1,2),
            'blood_text' => $this->faker->word,
            'meal_text' => $this->faker->word
        ];
    }
}
