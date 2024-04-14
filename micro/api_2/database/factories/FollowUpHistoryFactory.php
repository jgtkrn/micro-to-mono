<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\FollowUpHistory>
 */
class FollowUpHistoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'case_id' => $this->faker->randomNumber(),
            'date' => $this->faker->date(),
            'time' => $this->faker->dateTime(),
            'appointment_id' => $this->faker->randomNumber(),
            'appointment_other_text' => $this->faker->word(),
            'type' => $this->faker->word
        ];
    }
}
