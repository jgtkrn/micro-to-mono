<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SocialBackgroundForm>
 */
class SocialBackgroundFormFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'marital_status' => $this->faker->randomElement([1, 2, 3, 4]),
            'safety_alarm' => $this->faker->boolean,
            'has_carer' => $this->faker->boolean,
            'carer_option' => $this->faker->randomNumber,
            'carer' => $this->faker->text,
            'employment_status' => $this->faker->randomElement([1, 2, 3, 4]),
            'has_community_resource' => $this->faker->boolean,
            'education_level' => $this->faker->randomElement([1, 2, 3, 4]),
            // 'financial_state' => $this->faker->randomElement([1, 2, 3, 4, 5, 6]),
            'smoking_option' => $this->faker->randomElement([1, 2, 3]),
            'smoking' => $this->faker->randomNumber,
            'drinking_option' => $this->faker->randomElement([1, 2, 3]),
            'drinking' => $this->faker->randomNumber,
            'has_religion' => $this->faker->boolean,
            'religion' => $this->faker->text,
            'has_social_activity' => $this->faker->boolean,
            'social_activity' => $this->faker->text,
            'lubben_total_score' => $this->faker->numberBetween(0, 30),
            'other_living_status' => $this->faker->word,
            'relationship_other' => $this->faker->word,
            'financial_state_other' => $this->faker->word,
            'religion_remark' => $this->faker->word,
            'employment_remark' => $this->faker->word,
        ];
    }
}
