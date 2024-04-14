<?php

namespace Database\Seeders;

use App\Models\District;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DistrictSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $listDistrict = array(
            [
                'district_name' => 'Kwun Tong',
                'bzn_code' => 'CSKT'
            ],
            [
                'district_name' => 'Kowloon Bay',
                'bzn_code' => 'CSKT'
            ],
            [
                'district_name' => 'Tuen Mun',
                'bzn_code' => 'NAAC',
            ],
            [
                'district_name' => 'Hung Hum',
                'bzn_code' => 'WP',
            ],
            [
                'district_name' => 'Whompoa',
                'bzn_code' => 'WP'
            ]
        );



        foreach ($listDistrict as $district) {
            District::create([
                'district_name' => $district['district_name'],
                'bzn_code' => $district['bzn_code'],
            ]);
        }
    }
}
