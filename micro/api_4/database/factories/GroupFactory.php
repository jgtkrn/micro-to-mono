<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Group>
 */
class GroupFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $userData = json_encode([
            'id' => 0,
            'name' => 'Programmatic'
        ]);
        return [
            'name' => $this->faker->word,
            'code' => $this->faker->bothify('???#####'),
            'created_by' => $userData,
            'updated_by' => $userData,
        ];
    }
}
