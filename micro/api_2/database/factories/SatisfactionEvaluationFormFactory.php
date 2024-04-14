<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SatisfactionEvaluationForm>
 */
class SatisfactionEvaluationFormFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'case_id' => $this->faker->numberBetween(1,100),
            'elder_reference_number' => $this->faker->word,
            'assessor_name' => $this->faker->word,
            'clear_plan' => $this->faker->numberBetween(1,2),
            'enough_discuss_time' => $this->faker->numberBetween(1,2),
            'appropriate_plan' => $this->faker->numberBetween(1,2),
            'has_discussion_team' => $this->faker->numberBetween(1,2),
            'own_involved' => $this->faker->numberBetween(1,2),
            'enough_opportunities' => $this->faker->numberBetween(1,2),
            'enough_information' => $this->faker->numberBetween(1,2),
            'selfcare_improved' => $this->faker->numberBetween(1,2),
            'confidence_team' => $this->faker->numberBetween(1,2),
            'feel_respected' => $this->faker->numberBetween(1,2),
            'performance_rate' => $this->faker->numberBetween(1,2),
            'service_scale' => $this->faker->numberBetween(1,2),
            'recommend_service' => $this->faker->numberBetween(1,2),
        ];
    }
}
