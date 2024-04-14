<?php

namespace Database\Seeders;

use App\Models\ElderCalls;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ElderCallsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for ($i=0; $i < 100 ; $i++) {
            ElderCalls::factory()->create();
        }
    }
}
