<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Referral>
 */
class ReferralFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $label = $this->faker->word;
        $operator = json_encode(['id' => 0, 'name' => 'programmatic']);
        return [
            'label' => $label,
            'code' => Str::slug($label),
            'bzn_code' => $this->faker->lexify('B???'),
            'cga_code' => $this->faker->lexify('C???'),
            'created_by' => $operator,
            'updated_by' => $operator,
        ];
    }
}
