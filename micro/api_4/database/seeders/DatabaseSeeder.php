<?php

namespace Database\Seeders;

use App\Models\AccessRole;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        //access roles
        $access_roles = array(
            1 => 'admin',
            2 => 'manager',
            3 => 'user'
        );

        foreach($access_roles as $id => $name){
            AccessRole::updateOrCreate(
                ['id' => $id],
                ['name' => $name]
            );
        }
    }
}
