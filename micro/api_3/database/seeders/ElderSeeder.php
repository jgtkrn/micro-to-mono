<?php

namespace Database\Seeders;

use App\Models\Elder;
use App\Models\RecordUid;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ElderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for ($i=0; $i < 50 ; $i++) {
            Elder::factory()->create();
        }

        $listUid = Elder::select('UID','created_by','updated_by')->get();
        foreach($listUid as $data){
            RecordUid::create([
                'UID' => $data->UID,
                'created_by' => $data->created_by,
                'updated_by' => $data->updated_by,
            ]);
        }

    }
}
