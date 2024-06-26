<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\District>
 */
class DistrictFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'district_name' => $this->faker->city,
            'bzn_code' => $this->faker->lexify('bzn???'),
            'cga_code' => $this->faker->lexify('cga???'),
        ];
    }
}
