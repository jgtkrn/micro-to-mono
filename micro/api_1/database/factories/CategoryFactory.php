<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $category = array(1 => 'Assessment', 2 => 'Consultation', 3 => 'Internal Meeting');
        $selected = rand(1, 2); //exclude 3 (internal meeting) so testing still needs elder_id
        return [
            'id' => $selected,
            'name' => $category[$selected]
        ];
    }
}
