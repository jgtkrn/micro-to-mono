<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MedicalHistory>
 */
class MedicalHistoryFactory extends Factory
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
            'medical_category_name' => $this->faker->word(),
            'medical_diagnosis_name' => $this->faker->word(),
        ];
    }
}
