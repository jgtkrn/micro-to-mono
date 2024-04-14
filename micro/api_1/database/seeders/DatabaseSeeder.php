<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        //categories
        $categories = array(
            1 => 'Assessment',
            2 => 'Face-to-face Consultation',
            3 => 'Internal Meeting',
            4 => 'Tele-consultation',
            5 => 'On Leave'
        );

        foreach ($categories as $id => $name) {
            Category::updateOrCreate(
                ['id' => $id],
                ['name' => $name]
            );
        }
    }
}
