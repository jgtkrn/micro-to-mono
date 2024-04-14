<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends\Illuminate\Database\Eloquent\Factories\Factory<\App\Models\RePhysiologicalMeasurementForm>
 */
class RePhysiologicalMeasurementFormFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            're_temperature' => $this->faker->randomFloat(2,25,48), //decimal
            're_sitting_sbp' => $this->faker->numberBetween(10,20),
            're_sitting_dbp' => $this->faker->numberBetween(10,20),
            're_standing_sbp' => $this->faker->numberBetween(10,20),
            're_standing_dbp' => $this->faker->numberBetween(10,20),
            're_blood_oxygen' => $this->faker->numberBetween(1,2),
            're_heart_rate' => $this->faker->numberBetween(1,2),
            're_heart_rythm' => $this->faker->numberBetween(1,2),
            're_kardia' => $this->faker->numberBetween(1,2),
            're_blood_sugar' => $this->faker->randomFloat(2,10,20), //decimal
            're_blood_sugar_time' => $this->faker->numberBetween(1,2),
            're_waistline' => $this->faker->randomFloat(2,20,250), //decimal
            're_weight' => $this->faker->randomFloat(2,10,200), //decimal
            're_height' => $this->faker->randomFloat(2,2,3), //decimal
            're_respiratory_rate' => $this->faker->numberBetween(1, 2),
            're_blood_options' => $this->faker->numberBetween(1,2),
            're_blood_text' => $this->faker->word,
            're_meal_text' => $this->faker->word
        ];
    }
}
