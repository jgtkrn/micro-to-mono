<?php

namespace Database\Factories;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CgaConsultationNotes>
 */
class CgaConsultationNotesFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $consultation_time = new Carbon($this->faker->dateTime);
        return [
            // Assessor Information
            'assessor_1' => $this->faker->word,
            'assessor_2' => $this->faker->word,
            'visit_type' => $this->faker->word,
            'assessment_date' => Carbon::parse($consultation_time)->format('Y-m-d'),
            'assessment_time' => Carbon::parse($consultation_time)->format('H:I:S'),

            // Vital Sign
            'sbp' => $this->faker->randomNumber(1, 2),
            'dbp' => $this->faker->randomNumber(1, 2),
            'pulse' => $this->faker->randomNumber(1, 2),
            'pao' => $this->faker->randomNumber(1, 2),
            'hstix' => $this->faker->randomFloat(0,10,3),
            'body_weight' => $this->faker->randomNumber(1, 2),
            'waist' => $this->faker->randomNumber(1, 2),
            'circumference' => $this->faker->word,

            // Log
            'purpose' => $this->faker->word,
            'content' => $this->faker->word,
            'progress' => $this->faker->word,
            'case_summary' => $this->faker->word,
            'followup_options' => $this->faker->randomNumber(1, 2),
            'followup' => $this->faker->word,
            'personal_insight' => $this->faker->word,

            // Case Status
            'case_status' => $this->faker->randomNumber(1, 2),
            'case_remark' => $this->faker->word,
        ];
    }
}
