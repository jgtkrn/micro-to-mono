<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MedicationHistory>
 */
class MedicationHistoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'case_id' => $this->faker->randomNumber(),
            'medication_category' => $this->faker->word(),
            'medication_name' => $this->faker->word(),
            'dosage' => $this->faker->bothify('?? ##'),
            'number_of_intake' => $this->faker->bothify('? ###'),
            'frequency' => $this->faker->words(),
            'route' => $this->faker->regexify('[A-Z]{2}'),
            'remarks' => $this->faker->word(),
            'qi_data' => $this->faker->word(),
            'frequency_other' => $this->faker->word(),
            'sign_off' => $this->faker->boolean(),
            'routes_other' => $this->faker->word(),
            'created_by' => 'test',
            'updated_by' => 'test',
            'updated_by_name' => 'test',
            'created_by_name' => 'test'
        ];
    }
}
