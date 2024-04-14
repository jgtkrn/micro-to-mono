<?php

namespace Database\Factories;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BznConsultationNotes>
 */
class BznConsultationNotesFactory extends Factory
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
            'assessor' => $this->faker->word,
            'meeting' => $this->faker->word,
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

            // Intervention Target 1
            'domain' => $this->faker->randomNumber(1, 2),
            'urgency' => $this->faker->randomNumber(1, 2),
            'category' => $this->faker->randomNumber(1, 2),
            'intervention_remark' => $this->faker->word,
            'consultation_remark' => $this->faker->word,
            'area' => $this->faker->word,
            'priority' => $this->faker->randomNumber(1, 2),
            'target' => $this->faker->word,
            'modifier' => $this->faker->randomNumber(1, 2),
            'ssa' => $this->faker->word,
            'knowledge' => $this->faker->randomNumber(1, 2),
            'behaviour' => $this->faker->randomNumber(1, 2),
            'status' => $this->faker->randomNumber(1, 2),

            // Case Status
            'case_status' => $this->faker->randomNumber(1, 2),
            'case_remark' => $this->faker->word,
        ];
    }
}
