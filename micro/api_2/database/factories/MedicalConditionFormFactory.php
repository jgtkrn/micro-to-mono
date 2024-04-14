<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MedicalConditionForm>
 */
class MedicalConditionFormFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'has_medical_history' => $this->faker->boolean,
            'premorbid' => $this->faker->text,
            'followup_appointment' => $this->faker->text,
            'has_food_allergy' => $this->faker->boolean,
            'food_allergy_description' => $this->faker->text,
            'has_drug_allergy' => $this->faker->boolean,
            'drug_allergy_description' => $this->faker->text,
            'has_medication' => $this->faker->boolean,
            'medication_description' => $this->faker->text,
            'other_complaint' => $this->faker->word,
            'other_medical_history' => $this->faker->word,
            'premorbid_condition' => $this->faker->word,
            'ra_part' => $this->faker->text,
            'fracture_part' => $this->faker->text,
            'arthritis_part' => $this->faker->text,
        ];
    }
}
