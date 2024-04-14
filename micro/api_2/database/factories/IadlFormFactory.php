<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\IadlForm>
 */
class IadlFormFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $assessment_date = new Carbon($this->faker->dateTime);
        return [
            'elderly_central_ref_number' => $this->faker->text,
            'assessment_date'=>Carbon::parse($assessment_date)->format('Y-m-d'),
            'assessor_name' => $this->faker->text,
            'assessment_kind' => $this->faker->numberBetween(0, 2),
            'can_use_phone' => $this->faker->numberBetween(0, 2),
            'text_use_phone' => $this->faker->text,
            'can_take_ride' => $this->faker->numberBetween(0, 2),
            'text_take_ride' => $this->faker->text,
            'can_buy_food' => $this->faker->numberBetween(0, 2),
            'text_buy_food' => $this->faker->text,
            'can_cook' => $this->faker->numberBetween(0, 2),
            'text_cook' => $this->faker->text,
            'can_do_housework' => $this->faker->numberBetween(0, 2),
            'text_do_housework' => $this->faker->text,
            'can_do_repairment' => $this->faker->numberBetween(0, 2),
            'text_do_repairment' => $this->faker->text,
            'can_do_laundry' => $this->faker->numberBetween(0, 2),
            'text_do_laundry' => $this->faker->text,
            'can_take_medicine' => $this->faker->numberBetween(0, 2),
            'text_take_medicine' => $this->faker->text,
            'can_handle_finances' => $this->faker->numberBetween(0, 2),
            'text_handle_finances' => $this->faker->text,
            'iadl_total_score' => $this->faker->numberBetween(0, 18),
        ];
    }
}
