<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MedicationAdherenceForm>
 */
class MedicationAdherenceFormFactory extends Factory
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
            'assessment_date' => Carbon::parse($assessment_date)->format('Y-m-d'),
            'assessor_name' => $this->faker->word,
            'assessment_kind' => $this->faker->numberBetween(0, 2),
            'is_forget_sometimes' => $this->faker->boolean,
            'is_missed_meds' => $this->faker->boolean,
            'is_reduce_meds' => $this->faker->boolean,
            'is_forget_when_travel' => $this->faker->boolean,
            'is_meds_yesterday' => $this->faker->boolean,
            'is_stop_when_better' => $this->faker->boolean,
            'is_annoyed' => $this->faker->boolean,
            'forget_sometimes' => $this->faker->text,
            'missed_meds' => $this->faker->text,
            'reduce_meds' => $this->faker->text,
            'forget_when_travel' => $this->faker->text,
            'meds_yesterday' => $this->faker->text,
            'stop_when_better' => $this->faker->text,
            'annoyed' => $this->faker->text,
            'forget_frequency' => $this->faker->randomNumber,
            'total_mmas_score' => $this->faker->randomFloat(0,10,2)
        ];
    }
}
