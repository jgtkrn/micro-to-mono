<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PreCoachingPam>
 */
class PreCoachingPamFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'section' => $this->faker->numberBetween(1,2),
            'intervention_group' => $this->faker->numberBetween(1,2),
            'gender' => $this->faker->numberBetween(1,2),
            'health_manage' => $this->faker->numberBetween(1,2),
            'active_role' => $this->faker->numberBetween(1,2),
            'self_confidence' => $this->faker->numberBetween(1,2),
            'drug_knowledge' => $this->faker->numberBetween(1,2),
            'self_understanding' => $this->faker->numberBetween(1,2),
            'self_health' => $this->faker->numberBetween(1,2),
            'self_discipline' => $this->faker->numberBetween(1,2),
            'issue_knowledge' => $this->faker->numberBetween(1,2),
            'other_treatment' => $this->faker->numberBetween(1,2),
            'change_treatment' => $this->faker->numberBetween(1,2),
            'issue_prevention' => $this->faker->numberBetween(1,2),
            'find_solutions' => $this->faker->numberBetween(1,2),
            'able_maintain' => $this->faker->numberBetween(1,2),
            'remarks' => $this->faker->word
        ];
    }
}
