<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\LubbenSocialNetworkScaleForm>
 */
class LubbenSocialNetworkScaleFormFactory extends Factory
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
            'relatives_sum' => $this->faker->numberBetween(0, 5),
            'relatives_to_talk' => $this->faker->numberBetween(0, 5),
            'relatives_to_help' => $this->faker->numberBetween(0, 5),
            'friends_sum' => $this->faker->numberBetween(0, 5),
            'friends_to_talk' => $this->faker->numberBetween(0, 5),
            'friends_to_help' => $this->faker->numberBetween(0, 5),
            'lubben_total_score' => $this->faker->numberBetween(0, 30),
        ];
    }
}
