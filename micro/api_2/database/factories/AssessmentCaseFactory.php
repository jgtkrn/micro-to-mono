<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AssessmentCase>
 */
class AssessmentCaseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $start_time = new Carbon($this->faker->dateTime);
        $end_time = clone $start_time;
        $assessment_date = clone $start_time;

        return [
            'case_id' => $this->faker->randomNumber,
            'case_type' => $this->faker->randomElement(['CGA','BZN']),
            'first_assessor' => $this->faker->numberBetween(1,9),
            'second_assessor' => $this->faker->numberBetween(1,9),
            'assessment_date' => Carbon::parse($assessment_date)->format('Y-m-d'),
            'start_time' => $start_time,
            'end_time' => $end_time->addHours(1),
            'priority_level' => $this->faker->numberBetween(1,3)
        ];
    }
}
