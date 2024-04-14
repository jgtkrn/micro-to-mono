<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PainSiteTable>
 */
class PainSiteTableFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            // Pain
            'provoking_factor' => $this->faker->word,
            'pain_location1' => $this->faker->word,
            'is_dull' => $this->faker->boolean,
            'is_achy' => $this->faker->boolean,
            'is_sharp' => $this->faker->boolean,
            'is_stabbing' => $this->faker->boolean,
            'stabbing_option' => $this->faker->randomElement(['constant', 'intermittent']),
            'pain_location2' => $this->faker->word,
            'is_relief' => $this->faker->boolean,
            'what_relief' => $this->faker->word,
            'have_relief_method' => $this->faker->numberBetween(1,2,3),
            'relief_method' => $this->faker->numberBetween(0, 6),
            'other_relief_method' => $this->faker->word,
            'pain_scale' => $this->faker->randomElement([0, 2, 4, 6, 8, 10]),
            'when_pain' => $this->faker->word,
            'affect_adl' => $this->faker->numberBetween(1,2),
            'adl_info' => $this->faker->word,
            'pain_remark' => $this->faker->word,
            'is_radiation' => $this->faker->randomElement(['1','2']),
            'other_radiation' => $this->faker->word
        ];
    }
}
