<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\HospitalizationTables>
 */
class HospitalizationTablesFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'hosp_month' => $this->faker->word,
            'hosp_year' => $this->faker->word,
            'hosp_hosp' => $this->faker->randomElement([1, 2, 3, 4, 5, 6, 7, 8, 9, 10]),
            'hosp_hosp_other' => $this->faker->word,
            'hosp_way' => $this->faker->randomElement([1, 2, 3, 4]),
            'hosp_home' => $this->faker->randomElement([1, 2, 3, 4, 5]),
            'hosp_home_else' => $this->faker->word,
            'hosp_reason' => $this->faker->randomElement([1, 2, 3, 4, 5]),
            'hosp_reason_other' => $this->faker->word,
        ];
    }
}
