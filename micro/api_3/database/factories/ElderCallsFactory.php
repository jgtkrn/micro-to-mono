<?php

namespace Database\Factories;

use App\Models\Cases;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ElderCalls>
 */
class ElderCallsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $callStatuses = [
            'Success | Interested',
            'Pending | To Schedule Assesment',
            'Pending | No One Answer',
            'Fail | Wrong Number',
            'Fail | Nursery Home/Deceased/Travel',
            'Fail | Refused To Join'
        ];

        return [
            'caller_id' => $this->faker->randomDigit,
            'call_date' => $this->faker->dateTimeBetween('-1 day',now()),
            'call_status' => $this->faker->randomElement($callStatuses),
            'remark' => $this->faker->paragraph,
            'cases_id' => Cases::all()->random()->id,
        ];
    }
}
