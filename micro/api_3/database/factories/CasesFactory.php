<?php

namespace Database\Factories;

use App\Models\Cases;
use App\Models\Elder;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Cases>
 */
class CasesFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $randomElder = Elder::all()->random()->id;
        return [
            'elder_id' => $randomElder,
            'case_name' => $this->faker->randomElement(['BZN','CGA']),
            'caller_name' => $this->faker->word,
            'case_number' => $this->faker->numberBetween(2,20),
            'case_status' => $this->faker->randomElement(['Assessment','Completed','Follow up','Ongoing']),
            'created_by' => $this->faker->numberBetween(2,20),
            'updated_by' => $this->faker->numberBetween(2,20),
            'created_by_name' => $this->faker->randomElement(['Nurse A','Nurse B','Administrator']),
            'updated_by_name' => $this->faker->randomElement(['Nurse A','Nurse B','Administrator']),
        ];
    }
}
