<?php

namespace Database\Factories;
use Carbon\Carbon;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MontrealCognitiveAssessmentForm>
 */
class MontrealCognitiveAssessmentFormFactory extends Factory
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
            'elderly_central_ref_number' => $this->faker->text,
            'assessment_date'=>Carbon::parse($assessment_date)->format('Y-m-d'),
            'assessor_name' => $this->faker->text,
            'assessment_kind' => $this->faker->randomNumber,
            'memory_c11' => $this->faker->boolean,
            'memory_c12' => $this->faker->boolean,
            'memory_c13' => $this->faker->boolean,
            'memory_c14' => $this->faker->boolean,
            'memory_c15' => $this->faker->boolean,
            'memory_c21' => $this->faker->boolean,
            'memory_c22' => $this->faker->boolean,
            'memory_c23' => $this->faker->boolean,
            'memory_c24' => $this->faker->boolean,
            'memory_c25' => $this->faker->boolean,
            'memory_score' => $this->faker->randomFloat(2, 0, 5),
            'language_fluency1' => $this->faker->word,
            'language_fluency2' => $this->faker->word,
            'language_fluency3' => $this->faker->word,
            'language_fluency4' => $this->faker->word,
            'language_fluency5' => $this->faker->word,
            'language_fluency6' => $this->faker->word,
            'language_fluency7' => $this->faker->word,
            'language_fluency8' => $this->faker->word,
            'language_fluency9' => $this->faker->word,
            'language_fluency10' => $this->faker->word,
            'language_fluency11' => $this->faker->word,
            'language_fluency12' => $this->faker->word,
            'language_fluency13' => $this->faker->word,
            'language_fluency14' => $this->faker->word,
            'language_fluency15' => $this->faker->word,
            'language_fluency16' => $this->faker->word,
            'language_fluency17' => $this->faker->word,
            'language_fluency18' => $this->faker->word,
            'language_fluency19' => $this->faker->word,
            'language_fluency20' => $this->faker->word,
            'all_words' => $this->faker->randomFloat(2, 0, 9),
            'repeat_words' => $this->faker->randomFloat(2, 0, 9),
            'non_animal_words' => $this->faker->randomFloat(2, 0, 9),
            'language_fluency_score' => $this->faker->randomFloat(2, 0, 9),
            'orientation_day' => $this->faker->numberBetween(1, 31),
            'orientation_month' => $this->faker->numberBetween(1, 12),
            'orientation_year' => $this->faker->numberBetween(1900, 2100),
            'orientation_week' => $this->faker->numberBetween(1, 4),
            'orientation_place' => $this->faker->word,
            'orientation_area' => $this->faker->word,
            'orientation_score' => $this->faker->randomFloat(2, 0, 6),
            'face_word' => $this->faker->randomElement([1, 2, 3]),
            'velvet_word' => $this->faker->randomElement([1, 2, 3]),
            'church_word' => $this->faker->randomElement([1, 2, 3]),
            'daisy_word' => $this->faker->randomElement([1, 2, 3]),
            'red_word' => $this->faker->randomElement([1, 2, 3]),
            'delayed_memory_score' => $this->faker->randomFloat(2, 0, 10),
            'category_percentile' => $this->faker->randomElement([1, 2, 3, 4]),
            'total_moca_score' => $this->faker->randomFloat(2, 0, 30),
            'education_level' => $this->faker->word,
        ];
    }
}
