<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BarthelIndexForm>
 */
class BarthelIndexFormFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'bowels' => $this->faker->numberBetween(0, 2),
            'bladder' => $this->faker->numberBetween(0, 2),
            'grooming' => $this->faker->numberBetween(0, 1),
            'toilet_use' => $this->faker->numberBetween(0, 2),
            'feeding' => $this->faker->numberBetween(0, 2),
            'transfer' => $this->faker->numberBetween(0, 3),
            'mobility' => $this->faker->numberBetween(0, 3),
            'dressing' => $this->faker->numberBetween(0, 2),
            'stairs' => $this->faker->numberBetween(0, 2),
            'bathing' => $this->faker->numberBetween(0, 1),
            'barthel_total_score' => $this->faker->numberBetween(0, 20),
        ];
    }
}
