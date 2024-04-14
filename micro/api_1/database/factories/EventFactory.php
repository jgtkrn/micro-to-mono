<?php

namespace Database\Factories;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Event>
 */
class EventFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $start_time = new Carbon($this->faker->dateTime);
        $end_time = clone $start_time;

        return [
            'title' => $this->faker->title,
            'start' => $start_time,
            'end' => $end_time->addHours(1),
            'elder_id' => rand(1, 20),
            'case_id' => rand(1, 20),
            'remark' => $this->faker->sentence
        ];
    }
}
