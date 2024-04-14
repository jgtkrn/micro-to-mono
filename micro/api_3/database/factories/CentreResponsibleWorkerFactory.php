<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CentreResponsibleWorker>
 */
class CentreResponsibleWorkerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $name = $this->faker->word;
        $operator = json_encode(['id' => 0, 'name' => 'programmatic']);

        return [
            'name' => $name,
            'code' => Str::slug($name),
            'created_by' => $operator,
            'updated_by' => $operator,
        ];
    }
}
