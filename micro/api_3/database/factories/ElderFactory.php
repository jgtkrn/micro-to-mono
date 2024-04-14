<?php

namespace Database\Factories;

use App\Constants\Language;
use App\Models\District;
use App\Models\Elder;
use App\Models\Referral;
use App\Models\Zone;
use Illuminate\Database\Eloquent\Factories\Factory;
use Faker\Factory as faker;

class ElderFactory extends Factory
{
    protected $model = Elder::class;
    protected $newUid = '';


    public function definition()
    {
        $faker = faker::create('en_HK');

        // create array for relationship, limited mobility
        $listRelationship = array('Spouse','Sibling','Relative','Friend','Child','Grand-child','Parent','Others');
        $limitedMobility = array('Independent','Walking aids','Chair bound','Bed bound');
        // ========================================================================================================

        $operator = json_encode(['id' => 0, 'name' => 'programmatic']);
        $randomDistrict = District::all()->random()->id;
        $randomZone = Zone::all()->random()->id;
        $allowedLanguage = array_map(fn (Language $language) => $language->value(), Language::cases());
        $randomLanguage = $this->faker->randomElement($allowedLanguage);
        $randomReferral = Referral::all()->random()->id;

        // ============================================================================================================
        // then create data
        return [
            'uid' => $this->faker->numerify('UID######'),
            'name' => $faker->name(120),
            'name_en' => $faker->name(120),
            'gender' => $faker->randomElement(['male','female']),
            'birth_day' => $faker->numberBetween(1,31),
            'birth_month' => $faker->numberBetween(1,12),
            'birth_year' => $faker->numberBetween(1940,2000),
            'contact_number' => $faker->phoneNumber,
            'second_contact_number' => $faker->phoneNumber,
            'third_contact_number' => $faker->phoneNumber,
            'address' => $faker->address,
            'district_id' => $randomDistrict,
            'zone_id' => $randomZone,
            'language' => $randomLanguage,
            'relationship' => $faker->randomElement($listRelationship),
            'uid_connected_with' => $faker->randomElement(['NAAC0010','CSKT0011','WP0012']),
            'case_type' => $this->faker->randomElement(['BZN', 'CGA']),
            'elder_remark' => $faker->word,
            'referral_id' => $randomReferral,
            'emergency_contact_number' => $faker->phoneNumber,
            'emergency_contact_number_2' => $faker->phoneNumber,
            'emergency_contact_name' => $faker->name,
            'emergency_contact_relationship_other' => $faker->randomElement($listRelationship),
            'created_by' => $operator,
            'updated_by' => $operator,
        ];
    }

    public function createUidCskt($bzn_code): string{

            $code = $bzn_code;
            $uid = Elder::select('uid')->where('uid','LIKE','%'.$code.'%')->max('uid');
            $data = (int)substr($uid,4,4);
            $data++;
            return $this->newUid = $code. sprintf("%04s",$data);

    }

    public function createUidNaac($bzn_code): string{
            $code = $bzn_code;
            $uid = Elder::select('uid')->where('uid','LIKE','%'.$code.'%')->max('uid');
            $data = (int)substr($uid,4,4);
            $data++;
            return $this->newUid = $code. sprintf("%04s",$data);

    }
    public function createUidWp($bzn_code): string{
            $code = $bzn_code;
            $uid = Elder::select('uid')->where('uid','LIKE','%'.$code.'%')->max('uid');
            $data = (int)substr($uid,3,3);
            $data++;
            return $this->newUid = $code. sprintf("%04s",$data);

    }

}
