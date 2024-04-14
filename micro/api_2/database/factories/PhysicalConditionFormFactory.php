<?php

namespace Database\Factories;
use Carbon\Carbon;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PhysicalConditionForm>
 */
class PhysicalConditionFormFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {

        return [
            // General Condition
            'general_condition' => $this->faker->numberBetween(1, 4),
            'eye_opening_response' => $this->faker->numberBetween(1, 4),
            'verbal_response' => $this->faker->numberBetween(1, 5),
            'motor_response' => $this->faker->numberBetween(1, 6),
            'glasgow_score' => $this->faker->numberBetween(0, 15),

            // Mental State
            'mental_state' => $this->faker->numberBetween(1, 4),
            'edu_percentile' => $this->faker->numberBetween(1, 4),
            'moca_score' => $this->faker->numberBetween(0, 30),

            // Emotional State
            'emotional_state' => $this->faker->numberBetween(1, 3),
            'geriatric_score' => $this->faker->numberBetween(0, 30),

            // Sensory
            'is_good' => $this->faker->boolean,
            'is_deaf' => $this->faker->boolean,
            'dumb_left' => $this->faker->boolean,
            'dumb_right' => $this->faker->boolean,
            'non_verbal' => $this->faker->boolean,
            'is_visual_impaired' => $this->faker->boolean,
            'blind_left' => $this->faker->boolean,
            'blind_right' => $this->faker->boolean,
            'no_vision' => $this->faker->boolean,
            'is_assistive_devices' => $this->faker->boolean,
            'denture' => $this->faker->boolean,
            'hearing_aid' => $this->faker->boolean,
            'glasses' => $this->faker->boolean,

            // Nutrition
            'dat_special_diet' => $this->faker->numberBetween(1,2),
            'special_diet' => $this->faker->word,
            'is_special_feeding' => $this->faker->numberBetween(1,2),
            'special_feeding' => $this->faker->numberBetween(1, 2),
            'thickener_formula' => $this->faker->word,
            'fluid_restriction' => $this->faker->word,
            'tube_next_change' => $this->faker->word,
            'milk_formula' => $this->faker->word,
            'milk_regime' => $this->faker->word,
            'feeding_person' => $this->faker->numberBetween(1, 2),
            'feeding_person_text' => $this->faker->word,
            'feeding_technique' => $this->faker->numberBetween(1, 4),
            'ng_tube' => $this->faker->word,

            // Skin Condition
            'intact_abnormal' => $this->faker->numberBetween(1,2),
            'is_napkin_associated' => $this->faker->boolean,
            'is_dry' => $this->faker->boolean,
            'is_cellulitis' => $this->faker->boolean,
            'cellulitis_desc' => $this->faker->word,
            'is_eczema' => $this->faker->boolean,
            'eczema_desc' => $this->faker->word,
            'is_scalp' => $this->faker->boolean,
            'scalp_desc' => $this->faker->word,
            'is_itchy' => $this->faker->boolean,
            'itchy_desc' => $this->faker->word,
            'is_wound' => $this->faker->boolean,
            'wound_desc' => $this->faker->word,
            'wound_size' => $this->faker->randomFloat(2, 0, 100),
            'tunneling_time' => '02:15',
            'wound_bed' => $this->faker->randomFloat(2, 0, 100),
            'granulating_tissue' => $this->faker->randomFloat(2, 0, 100),
            'necrotic_tissue' => $this->faker->randomFloat(2, 0, 100),
            'sloughy_tissue' => $this->faker->randomFloat(2, 0, 100),
            'other_tissue' => $this->faker->randomFloat(2, 0, 100),
            'exudate_amount' => $this->faker->numberBetween(1, 3),
            'exudate_type' => $this->faker->numberBetween(1, 4),
            'other_exudate' => $this->faker->word,
            'surrounding_skin' => $this->faker->numberBetween(1, 6),
            'other_surrounding' => $this->faker->word,
            'odor' => $this->faker->numberBetween(1,2),
            'pain' => $this->faker->numberBetween(1,2),

            // Elimination
            'bowel_habit' => $this->faker->numberBetween(1,2),
            'abnormal_option' => $this->faker->numberBetween(1, 3),
            'fi_bowel' => $this->faker->numberBetween(1, 2),
            'urinary_habit' => $this->faker->numberBetween(1, 3),
            'fi_urine' => $this->faker->numberBetween(1, 2),
            'urine_device' => $this->faker->numberBetween(1, 2),
            'catheter_type' => $this->faker->numberBetween(1, 3),
            'catheter_next_change' => $this->faker->word,
            'catheter_size_fr' => $this->faker->numberBetween(1, 3),

            // Pain
            'is_pain' => $this->faker->numberBetween(1,2),
            'other_emotional_state' => $this->faker->word,
            'deaf_right' => $this->faker->boolean,
            'deaf_left' => $this->faker->boolean,
            'sensory_remark' => $this->faker->word,
            'other_radiation' => $this->faker->word,
            'nutrition_remark' => $this->faker->word,
            'skin_rash' => $this->faker->boolean,
            'other_skin_rash' => $this->faker->word,
            'bowel_remark' => $this->faker->word,
            'urine_remark' => $this->faker->word,
            'visual_impaired_left' => $this->faker->boolean,
            'visual_impaired_right' => $this->faker->boolean,
            'visual_impaired_both' => $this->faker->boolean,

            'napkin_associated_desc' => $this->faker->word,
        ];
    }
}
