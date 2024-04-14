<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CgaCareTarget>
 */
class CgaCareTargetFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'target' => $this->faker->word,
            'health_vision' => $this->faker->word,
            'long_term_goal' => $this->faker->word,
            'short_term_goal' => $this->faker->word,
            'motivation' => $this->faker->numberBetween(1, 9),
            'early_change_stage' => $this->faker->numberBetween(1, 9),
            'later_change_stage' => $this->faker->numberBetween(1, 9),
        ];
    }
}
