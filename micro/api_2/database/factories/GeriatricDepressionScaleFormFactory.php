<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\GeriatricDepressionScaleForm>
 */
class GeriatricDepressionScaleFormFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $assessment_date = new Carbon($this->faker->dateTime);
        return [
            'elderly_central_ref_number' => $this->faker->word,
            'assessment_date'=>Carbon::parse($assessment_date)->format('Y-m-d'),
            'assessor_name' => $this->faker->word,
            'assessment_kind' => $this->faker->numberBetween(0, 2),
            'is_satisfied' => $this->faker->numberBetween(0, 1),
            'is_given_up' => $this->faker->numberBetween(0, 1),
            'is_feel_empty' => $this->faker->numberBetween(0, 1),
            'is_often_bored' => $this->faker->numberBetween(0, 1),
            'is_happy_a_lot' => $this->faker->numberBetween(0, 1),
            'is_affraid' => $this->faker->numberBetween(0, 1),
            'is_happy_all_day' => $this->faker->numberBetween(0, 1),
            'is_feel_helpless' => $this->faker->numberBetween(0, 1),
            'is_prefer_stay' => $this->faker->numberBetween(0, 1),
            'is_memory_problem' => $this->faker->numberBetween(0, 1),
            'is_good_to_alive' => $this->faker->numberBetween(0, 1),
            'is_feel_useless' => $this->faker->numberBetween(0, 1),
            'is_feel_energic' => $this->faker->numberBetween(0, 1),
            'is_hopeless' => $this->faker->numberBetween(0, 1),
            'is_people_better' => $this->faker->numberBetween(0, 1),
            'gds15_score' => $this->faker->numberBetween(0, 15),
        ];
    }
}
