<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\FunctionMobilityForm>
 */
class FunctionMobilityFormFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'iadl' => $this->faker->numberBetween(1, 3),
            'total_iadl_score' => $this->faker->numberBetween(0, 18),
            'mobility' => $this->faker->numberBetween(1, 4),
            'walk_with_assistance' => $this->faker->numberBetween(1, 7),
            'mobility_tug' => $this->faker->word,
            'left_single_leg' => $this->faker->boolean,
            'right_single_leg' => $this->faker->boolean,
            'range_of_motion' => $this->faker->numberBetween(1, 2),
            'upper_limb_left' => $this->faker->numberBetween(0, 5),
            'upper_limb_right' => $this->faker->numberBetween(0, 5),
            'lower_limb_left' => $this->faker->numberBetween(0, 5),
            'lower_limb_right' => $this->faker->numberBetween(0, 5),
            'fall_history' => $this->faker->boolean,
            'number_of_major_fall' => $this->faker->randomNumber,
            'mi_independent' => $this->faker->boolean,
            'mi_walk_assisst' => $this->faker->boolean,
            'mi_wheelchair_bound' => $this->faker->boolean,
            'mi_bed_bound' => $this->faker->boolean,
            'mi_remark' => $this->faker->word,
            'mo_independent' => $this->faker->boolean,
            'mo_walk_assisst' => $this->faker->boolean,
            'mo_wheelchair_bound' => $this->faker->boolean,
            'mo_bed_bound' => $this->faker->boolean,
            'mo_remark' => $this->faker->word,
        ];
    }
}
