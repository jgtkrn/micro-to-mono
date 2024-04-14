<?php

namespace Tests\Feature;

use App\Constants\Language;
use App\Models\District;
use App\Models\Elder;
use App\Models\Referral;
use App\Models\Zone;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

class ElderTest extends TestCase
{
    use WithoutMiddleware, RefreshDatabase, WithFaker;

    public function test_get_elder_list()
    {
        District::factory()->count(5)->create();
        Zone::factory()->create();
        Referral::factory()->create();
        $size = $this->faker->numberBetween(5, 10);
        Elder::factory()
            ->count($size)
            ->sequence(fn ($sequence) => [
                'uid' => "ID000$sequence->index"
            ])
            ->create();
        $response = $this->getJson('/elderly-api/v1/elders');

        $response->assertStatus(200);
        $response->assertJsonCount($size, 'data');
    }

    public function test_filter_elder_list_by_name()
    {
        District::factory()->create();
        Zone::factory()->create();
        Referral::factory()->create();
        $size = $this->faker->numberBetween(5, 10);
        $elders = Elder::factory()
            ->count($size)
            ->sequence(fn ($sequence) => [
                'uid' => "ID000$sequence->index",
                'name' => "Elder-$sequence->index",
            ])
            ->create();

        $nameFilter = $elders->random()->name;
        $response = $this->getJson("/elderly-api/v1/elders?name=$nameFilter");

        $response->assertStatus(200);
        $response->assertJsonPath('data.0.name', $nameFilter);
    }

    public function test_filter_elder_list_by_uid()
    {
        District::factory()->create();
        Zone::factory()->create();
        Referral::factory()->create();
        $size = $this->faker->numberBetween(5, 10);
        $elders = Elder::factory()
            ->count($size)
            ->sequence(fn ($sequence) => [
                'uid' => "ID000$sequence->index",
                'name' => "Elder-$sequence->index",
            ])
            ->create();

        $uidFilter = $elders->random()->uid;
        $response = $this->getJson("/elderly-api/v1/elders?uid=$uidFilter");

        $response->assertStatus(200);
        $response->assertJsonCount(1, 'data');
        $response->assertJsonPath('data.0.uid', $uidFilter);
    }

    public function test_filter_elder_list_by_ids()
    {
        District::factory()->create();
        Zone::factory()->create();
        Referral::factory()->create();
        $size = $this->faker->numberBetween(5, 10);
        $elders = Elder::factory()
            ->count($size)
            ->sequence(fn ($sequence) => [
                'uid' => "ID000$sequence->index",
                'name' => "Elder-$sequence->index",
            ])
            ->create();

        $takenNumber = $this->faker->numberBetween(1, $size);
        $selectedIds = $elders->take($takenNumber)
            ->map(fn ($item) => $item->id);
        $idsFilter = $selectedIds->join(',');
        $response = $this->getJson("/elderly-api/v1/elders?ids=$idsFilter");

        $response->assertStatus(200);
        $response->assertJsonCount($selectedIds->count(), 'data');
    }

    public function test_get_elder_autocomplete()
    {
        District::factory()->create();
        Zone::factory()->create();
        Referral::factory()->create();
        $size = $this->faker->numberBetween(5, 10);
        Elder::factory()
            ->count($size)
            ->sequence(fn ($sequence) => [
                'uid' => "ID000$sequence->index"
            ])
            ->create();
            
        $response = $this->getJson('/elderly-api/v1/elders-autocomplete');

        $response->assertOk();
        $response->assertJsonCount($size, 'data');
        $response->assertJsonStructure([
            'data' => [
                [
                    'id',
                    'uid',
                    'name',
                ],
            ],
            'links' => [],
            'meta' => [],
        ]);
    }

    public function test_filter_elder_autocomplete_by_name()
    {
        District::factory()->create();
        Zone::factory()->create();
        Referral::factory()->create();
        $size = $this->faker->numberBetween(5, 10);
        $elders = Elder::factory()
            ->count($size)
            ->sequence(fn ($sequence) => [
                'uid' => "ID000$sequence->index",
                'name' => "elder 0000$sequence->index",
            ])
            ->create();
        $nameFilter = $elders->random()->name;
        $response = $this->getJson("/elderly-api/v1/elders-autocomplete?name=$nameFilter");

        $response->assertOk();
        $response->assertJsonCount(1, 'data');
        $response->assertJsonPath('data.0.name', $nameFilter);
    }

    public function test_filter_elder_autocomplete_by_uid()
    {
        District::factory()->create();
        Zone::factory()->create();
        Referral::factory()->create();
        $size = $this->faker->numberBetween(5, 10);
        $elders = Elder::factory()
            ->count($size)
            ->sequence(fn ($sequence) => [
                'uid' => "ID000$sequence->index",
                'name' => "elder 0000$sequence->index",
            ])
            ->create();
        $uidFilter = $elders->random()->uid;
        $response = $this->getJson("/elderly-api/v1/elders-autocomplete?uid=$uidFilter");

        $response->assertOk();
        $response->assertJsonCount(1, 'data');
        $response->assertJsonPath('data.0.uid', $uidFilter);
    }

    public function test_filter_elder_autocomplete_by_ids()
    {
        District::factory()->create();
        Zone::factory()->create();
        Referral::factory()->create();
        $size = $this->faker->numberBetween(5, 10);
        $elders = Elder::factory()
            ->count($size)
            ->sequence(fn ($sequence) => [
                'uid' => "ID000$sequence->index",
                'name' => "elder 0000$sequence->index",
            ])
            ->create();
        $takeSize = $this->faker->numberBetween(1, $size);
        $idsFilter = $elders->random($takeSize)
            ->map(fn ($elder) => $elder->id)
            ->join(',');
        $response = $this->getJson("/elderly-api/v1/elders-autocomplete?ids=$idsFilter");

        $response->assertOk();
        $response->assertJsonCount($takeSize, 'data');
    }

    public function test_create_elder_without_relation_success()
    {
        $district = District::factory()->create();
        $zone = Zone::factory()->create();
        $referral = Referral::factory()->create();
        $allowedLanguage = array_map(fn (Language $language) => $language->value(), Language::cases());
        $randomLanguage = $this->faker->randomElement($allowedLanguage);
        $data = [
            'name' => 'Toph Bei Fong',
            'name_en' => 'Sung Kang',
            'gender' => 'female',
            'birth_day' => 1,
            'birth_month' => 1,
            'birth_year' => 1920,
            'contact_number' => '12345678',
            'second_contact_number' => '081234564',
            'third_contact_number' => '081234565',
            'address' => 'Street Unknown State Zero six 2',
            'district' => $district->district_name,
            'zone' => $zone->name,
            'language' => $randomLanguage,
            'emergency_contact_number' => '081234567890',
            'emergency_contact_number_2' => '081234567891',
            'emergency_contact_name' => 'Ling Bei Fong',
            'emergency_contact_relationship_other' => 'Child',
            'emergency_contact_2_number' => '081234567892',
            'emergency_contact_2_number_2' => '081234567893',
            'emergency_contact_2_name' => 'Suyin Bei Fong',
            'emergency_contact_2_relationship_other' => 'Child',
            'relationship' => null,
            'uid_connected_with' => null,
            'case_type' => 'CGA',
            'source_of_referral' => $referral->label,
        ];

        $response = $this->postJson('/elderly-api/v1/elders', $data);

        $response->assertCreated();
        $response->assertJsonPath('data.name', $data['name']);
        $response->assertJsonPath('data.name_en', $data['name_en']);
        $response->assertJsonPath('data.gender', $data['gender']);
        $response->assertJsonPath('data.birth_day', $data['birth_day']);
        $response->assertJsonPath('data.birth_month', $data['birth_month']);
        $response->assertJsonPath('data.birth_year', $data['birth_year']);
        $response->assertJsonPath('data.contact_number', $data['contact_number']);
        $response->assertJsonPath('data.second_contact_number', $data['second_contact_number']);
        $response->assertJsonPath('data.third_contact_number', $data['third_contact_number']);
        $response->assertJsonPath('data.address', $data['address']);
        $response->assertJsonPath('data.district', $data['district']);
        $response->assertJsonPath('data.zone', $data['zone']);
        $response->assertJsonPath('data.language', $data['language']);
        $response->assertJsonPath('data.emergency_contact_number', $data['emergency_contact_number']);
        $response->assertJsonPath('data.emergency_contact_number_2', $data['emergency_contact_number_2']);
        $response->assertJsonPath('data.emergency_contact_name', $data['emergency_contact_name']);
        $response->assertJsonPath('data.emergency_contact_relationship_other', $data['emergency_contact_relationship_other']);
        $response->assertJsonPath('data.emergency_contact_2_number', $data['emergency_contact_2_number']);
        $response->assertJsonPath('data.emergency_contact_2_number_2', $data['emergency_contact_2_number_2']);
        $response->assertJsonPath('data.emergency_contact_2_name', $data['emergency_contact_2_name']);
        $response->assertJsonPath('data.emergency_contact_2_relationship_other', $data['emergency_contact_2_relationship_other']);
        $response->assertJsonPath('data.relationship', $data['relationship']);
        $response->assertJsonPath('data.uid_connected_with', $data['uid_connected_with']);
        $response->assertJsonPath('data.case_type', $data['case_type']);
        $response->assertJsonPath('data.source_of_referral', $data['source_of_referral']);

        $cleanData = collect($data)->except(['district', 'zone', 'source_of_referral'])->all();
        $this->assertDatabaseHas('elders', $cleanData);

        $elderId = $response->json()['data']['id'];
        $this->assertDatabaseHas('cases', ['elder_id' => $elderId]);
    }

    public function test_create_elder_with_first_uid_success()
    {
        $district = District::factory()->create();
        $zone = Zone::factory()->create();
        $referral = Referral::factory()->create();
        $allowedLanguage = array_map(fn (Language $language) => $language->value(), Language::cases());
        $randomLanguage = $this->faker->randomElement($allowedLanguage);
        $data = [
            'name' => 'Toph Bei Fong',
            'name_en' => 'Sung Kang',
            'gender' => 'female',
            'birth_day' => 1,
            'birth_month' => 1,
            'birth_year' => 1920,
            'contact_number' => '12345678',
            'second_contact_number' => '12345678',
            'third_contact_number' => '12345678',
            'address' => 'Street Unknown State Zero six 2',
            'district' => $district->district_name,
            'zone' => $zone->name,
            'language' => $randomLanguage,
            'emergency_contact_number' => '081234567890',
            'emergency_contact_number_2' => '081234567891',
            'emergency_contact_name' => 'Ling Bei Fong',
            'emergency_contact_relationship_other' => 'Child',
            'emergency_contact_2_number' => '081234567892',
            'emergency_contact_2_number_2' => '081234567893',
            'emergency_contact_2_name' => 'Suyin Bei Fong',
            'emergency_contact_2_relationship_other' => 'Child',
            'relationship' => null,
            'uid_connected_with' => null,
            'case_type' => $this->faker->randomElement(['BZN', 'CGA']),
            'source_of_referral' => $referral->label,
        ];

        $response = $this->postJson('/elderly-api/v1/elders', $data);

        $response->assertCreated();
        $uidCode = $data['case_type'] === 'CGA' ? $referral->cga_code : $referral->bzn_code;
        $response->assertJsonPath('data.uid', $uidCode . '0001');
    }

    public function test_create_elder_with_n_number_of_uid_success()
    {
        $district = District::factory()->create();
        $zone = Zone::factory()->create();
        $referral = Referral::factory()->create();
        $elderCount = $this->faker->numberBetween(1, 10);
        $casetype = $this->faker->randomElement(['BZN', 'CGA']);
        $uidCode = $casetype === 'CGA' ? $referral->cga_code : $referral->bzn_code;
        Elder::factory()
            ->count($elderCount)
            ->sequence(fn ($sequence) => ['uid' => $uidCode . Str::padLeft($sequence->index + 1, 4, '0')])
            ->create();
        $allowedLanguage = array_map(fn (Language $language) => $language->value(), Language::cases());
        $randomLanguage = $this->faker->randomElement($allowedLanguage);
        $data = [
            'name' => 'Toph Bei Fong',
            'name_en' => 'Sung Kang',
            'gender' => 'female',
            'birth_day' => 1,
            'birth_month' => 1,
            'birth_year' => 1920,
            'contact_number' => '12345678',
            'second_contact_number' => '12345678',
            'third_contact_number' => '12345678',
            'address' => 'Street Unknown State Zero six 2',
            'district' => $district->district_name,
            'zone' => $zone->name,
            'language' => $randomLanguage,
            'emergency_contact_number' => '081234567890',
            'emergency_contact_number_2' => '081234567891',
            'emergency_contact_name' => 'Ling Bei Fong',
            'emergency_contact_relationship_other' => 'Child',
            'emergency_contact_2_number' => '081234567892',
            'emergency_contact_2_number_2' => '081234567893',
            'emergency_contact_2_name' => 'Suyin Bei Fong',
            'emergency_contact_2_relationship_other' => 'Child',
            'relationship' => null,
            'uid_connected_with' => null,
            'case_type' => $casetype,
            'source_of_referral' => $referral->label,
        ];

        $response = $this->postJson('/elderly-api/v1/elders', $data);

        $response->assertCreated();
        $correctUid = $uidCode . Str::padLeft($elderCount + 1, 4, '0');
        $response->assertJsonPath('data.uid', $correctUid);
    }

    public function test_create_elder_with_n_number_of_random_uid_success()
    {
        $district = District::factory()->create();
        $zone = Zone::factory()->create();
        $referral = Referral::factory()->create();
        $elderCount = $this->faker->numberBetween(1, 10);
        Elder::factory()
            ->count($elderCount)
            ->sequence(function ($sequence) use ($referral) {
                $casetype = ($sequence->index + 1) % 2 === 0 ? 'CGA' : 'BZN';
                $code = $casetype === 'CGA' ? $referral->cga_code : $referral->bzn_code;
                return ['uid' => $code . Str::padLeft($sequence->index + 1, 4, '0')];
            })
            ->create();
        $allowedLanguage = array_map(fn (Language $language) => $language->value(), Language::cases());
        $randomLanguage = $this->faker->randomElement($allowedLanguage);
        $data = [
            'name' => 'Toph Bei Fong',
            'name_en' => 'Sung Kang',
            'gender' => 'female',
            'birth_day' => 1,
            'birth_month' => 1,
            'birth_year' => 1920,
            'contact_number' => '12345678',
            'second_contact_number' => '12345678',
            'third_contact_number' => '12345678',
            'address' => 'Street Unknown State Zero six 2',
            'district' => $district->district_name,
            'zone' => $zone->name,
            'language' => $randomLanguage,
            'emergency_contact_number' => '081234567890',
            'emergency_contact_number_2' => '081234567891',
            'emergency_contact_name' => 'Ling Bei Fong',
            'emergency_contact_relationship_other' => 'Child',
            'emergency_contact_2_number' => '081234567892',
            'emergency_contact_2_number_2' => '081234567893',
            'emergency_contact_2_name' => 'Suyin Bei Fong',
            'emergency_contact_2_relationship_other' => 'Child',
            'relationship' => null,
            'uid_connected_with' => null,
            'case_type' => $this->faker->randomElement(['BZN', 'CGA']),
            'source_of_referral' => $referral->label,
        ];

        // UID check
        $uidCode = $data['case_type'] === 'CGA' ? $referral->cga_code : $referral->bzn_code;
        $lastIndex = 0;
        $lastUid = DB::table('elders')->select('uid')->where('uid', 'like', "$uidCode%")->max('uid');
        if ($lastUid) {
            $lastIndex = (int) str_replace($uidCode, '', $lastUid);
        }
        $correctUid = $uidCode . Str::padLeft($lastIndex + 1, 4, '0');

        $response = $this->postJson('/elderly-api/v1/elders', $data);

        $response->assertCreated();

        $response->assertJsonPath('data.uid', $correctUid);
    }

    public function test_create_elder_without_data_failed()
    {
        $data = [];
        $response = $this->postJson('/elderly-api/v1/elders', $data);
        $response->assertStatus(422);
    }

    public function test_create_elder_with_relation_success()
    {
        $district = District::factory()->create();
        $zone = Zone::factory()->create();
        $referral = Referral::factory()->create();
        $elder = Elder::factory()->create();
        $allowedLanguage = array_map(fn (Language $language) => $language->value(), Language::cases());
        $randomLanguage = $this->faker->randomElement($allowedLanguage);
        $data = [
            'name' => 'Toph Bei Fong',
            'name_en' => 'Sung Kang',
            'gender' => 'female',
            'birth_day' => 1,
            'birth_month' => 1,
            'birth_year' => 1920,
            'contact_number' => '12345678',
            'second_contact_number' => '12345678',
            'third_contact_number' => '12345678',
            'address' => 'Street Unknown State Zero six 2',
            'district' => $district->district_name,
            'zone' => $zone->name,
            'language' => $randomLanguage,
            'emergency_contact_number' => '081234567890',
            'emergency_contact_number_2' => '081234567891',
            'emergency_contact_name' => 'Suyin Bei Fong',
            'emergency_contact_relationship_other' => 'Child',
            'emergency_contact_2_number' => '081234567892',
            'emergency_contact_2_number_2' => '081234567893',
            'emergency_contact_2_name' => 'Ling Bei Fong',
            'emergency_contact_2_relationship_other' => 'Child',
            'relationship' => 'spouse',
            'uid_connected_with' => $elder->uid,
            'case_type' => 'CGA',
            'source_of_referral' => $referral->label,
        ];

        $response = $this->postJson('/elderly-api/v1/elders', $data);

        $response->assertCreated();
        $response->assertJsonPath('data.name', $data['name']);
        $response->assertJsonPath('data.name_en', $data['name_en']);
        $response->assertJsonPath('data.gender', $data['gender']);
        $response->assertJsonPath('data.birth_day', $data['birth_day']);
        $response->assertJsonPath('data.birth_month', $data['birth_month']);
        $response->assertJsonPath('data.birth_year', $data['birth_year']);
        $response->assertJsonPath('data.contact_number', $data['contact_number']);
        $response->assertJsonPath('data.second_contact_number', $data['contact_number']);
        $response->assertJsonPath('data.third_contact_number', $data['contact_number']);
        $response->assertJsonPath('data.address', $data['address']);
        $response->assertJsonPath('data.district', $data['district']);
        $response->assertJsonPath('data.zone', $data['zone']);
        $response->assertJsonPath('data.language', $data['language']);
        $response->assertJsonPath('data.emergency_contact_number', $data['emergency_contact_number']);
        $response->assertJsonPath('data.emergency_contact_number_2', $data['emergency_contact_number_2']);
        $response->assertJsonPath('data.emergency_contact_name', $data['emergency_contact_name']);
        $response->assertJsonPath('data.emergency_contact_relationship_other', $data['emergency_contact_relationship_other']);
        $response->assertJsonPath('data.emergency_contact_2_number', $data['emergency_contact_2_number']);
        $response->assertJsonPath('data.emergency_contact_2_number_2', $data['emergency_contact_2_number_2']);
        $response->assertJsonPath('data.emergency_contact_2_name', $data['emergency_contact_2_name']);
        $response->assertJsonPath('data.emergency_contact_2_relationship_other', $data['emergency_contact_2_relationship_other']);
        $response->assertJsonPath('data.relationship', $data['relationship']);
        $response->assertJsonPath('data.uid_connected_with', $data['uid_connected_with']);
        $response->assertJsonPath('data.case_type', $data['case_type']);
        $response->assertJsonPath('data.source_of_referral', $data['source_of_referral']);
    }

    public function test_create_elder_without_birth_date_success()
    {
        $district = District::factory()->create();
        $zone = Zone::factory()->create();
        $referral = Referral::factory()->create();
        $allowedLanguage = array_map(fn (Language $language) => $language->value(), Language::cases());
        $randomLanguage = $this->faker->randomElement($allowedLanguage);
        $data = [
            'name' => 'Toph Bei Fong',
            'name_en' => 'Sung Kang',
            'gender' => 'female',
            'birth_month' => 1,
            'birth_year' => 1920,
            'contact_number' => '12345678',
            'second_contact_number' => '12345678',
            'third_contact_number' => '12345678',
            'address' => 'Street Unknown State Zero six 2',
            'district' => $district->district_name,
            'zone' => $zone->name,
            'language' => $randomLanguage,
            'emergency_contact_number' => '081234567890',
            'emergency_contact_number_2' => '081234567890',
            'emergency_contact_name' => 'Ling Bei Fong',
            'emergency_contact_relationship_other' => 'Child',
            'relationship' => null,
            'uid_connected_with' => null,
            'case_type' => 'CGA',
            'source_of_referral' => $referral->label,
        ];

        $response = $this->postJson('/elderly-api/v1/elders', $data);

        $response->assertCreated();
        $response->assertJsonPath('data.name', $data['name']);
        $response->assertJsonPath('data.name_en', $data['name_en']);
        $response->assertJsonPath('data.gender', $data['gender']);
        $response->assertJsonPath('data.birth_day', null);
        $response->assertJsonPath('data.birth_month', $data['birth_month']);
        $response->assertJsonPath('data.birth_year', $data['birth_year']);
        $response->assertJsonPath('data.contact_number', $data['contact_number']);
        $response->assertJsonPath('data.second_contact_number', $data['contact_number']);
        $response->assertJsonPath('data.third_contact_number', $data['contact_number']);
        $response->assertJsonPath('data.address', $data['address']);
        $response->assertJsonPath('data.district', $data['district']);
        $response->assertJsonPath('data.zone', $data['zone']);
        $response->assertJsonPath('data.language', $data['language']);
        $response->assertJsonPath('data.emergency_contact_number', $data['emergency_contact_number']);
        $response->assertJsonPath('data.emergency_contact_number_2', $data['emergency_contact_number']);
        $response->assertJsonPath('data.emergency_contact_name', $data['emergency_contact_name']);
        $response->assertJsonPath('data.emergency_contact_relationship_other', $data['emergency_contact_relationship_other']);
        $response->assertJsonPath('data.relationship', $data['relationship']);
        $response->assertJsonPath('data.uid_connected_with', $data['uid_connected_with']);
        $response->assertJsonPath('data.case_type', $data['case_type']);
        $response->assertJsonPath('data.source_of_referral', $data['source_of_referral']);
    }

    public function test_create_elder_without_language_success()
    {
        $district = District::factory()->create();
        $zone = Zone::factory()->create();
        $referral = Referral::factory()->create();
        $data = [
            'name' => 'Toph Bei Fong',
            'name_en' => 'Sung Kang',
            'gender' => 'female',
            'birth_month' => 1,
            'birth_year' => 1920,
            'contact_number' => '12345678',
            'second_contact_number' => '12345678',
            'third_contact_number' => '12345678',
            'address' => 'Street Unknown State Zero six 2',
            'district' => $district->district_name,
            'zone' => $zone->name,
            'emergency_contact_number' => '081234567890',
            'emergency_contact_number_2' => '081234567890',
            'emergency_contact_name' => 'Ling Bei Fong',
            'emergency_contact_relationship_other' => 'Child',
            'relationship' => null,
            'uid_connected_with' => null,
            'case_type' => 'CGA',
            'source_of_referral' => $referral->label,
        ];

        $response = $this->postJson('/elderly-api/v1/elders', $data);

        $response->assertCreated();
        $response->assertJsonPath('data.name', $data['name']);
        $response->assertJsonPath('data.name_en', $data['name_en']);
        $response->assertJsonPath('data.gender', $data['gender']);
        $response->assertJsonPath('data.birth_day', null);
        $response->assertJsonPath('data.birth_month', $data['birth_month']);
        $response->assertJsonPath('data.birth_year', $data['birth_year']);
        $response->assertJsonPath('data.contact_number', $data['contact_number']);
        $response->assertJsonPath('data.second_contact_number', $data['contact_number']);
        $response->assertJsonPath('data.third_contact_number', $data['contact_number']);
        $response->assertJsonPath('data.address', $data['address']);
        $response->assertJsonPath('data.district', $data['district']);
        $response->assertJsonPath('data.zone', $data['zone']);
        $response->assertJsonPath('data.language', null);
        $response->assertJsonPath('data.emergency_contact_number', $data['emergency_contact_number']);
        $response->assertJsonPath('data.emergency_contact_number_2', $data['emergency_contact_number']);
        $response->assertJsonPath('data.emergency_contact_name', $data['emergency_contact_name']);
        $response->assertJsonPath('data.emergency_contact_relationship_other', $data['emergency_contact_relationship_other']);
        $response->assertJsonPath('data.relationship', $data['relationship']);
        $response->assertJsonPath('data.uid_connected_with', $data['uid_connected_with']);
        $response->assertJsonPath('data.case_type', $data['case_type']);
        $response->assertJsonPath('data.source_of_referral', $data['source_of_referral']);
    }

    public function test_check_elder_uid_generation_with_almost_similar_ref_code()
    {
        $district = District::factory()->create();
        $zone = Zone::factory()->create();
        $usertype = 'CGA';

        $firstReferral = Referral::factory()->create([
            'cga_code' => 'CSKT',
        ]);
        $count = $this->faker->numberBetween(1, 10);
        Elder::factory()
            ->count($count)
            ->sequence(fn ($sequence) => ['uid' => $firstReferral->cga_code . '000' . $sequence->index + 1])
            ->create([
                'referral_id' => $firstReferral->id,
                'case_type' => $usertype,
            ]);

        $secondReferral = Referral::factory()->create([
            'cga_code' => 'CS',
        ]);

        Elder::factory()->create([
            'referral_id' => $secondReferral->id,
            'case_type' => $usertype,
            'uid' => Elder::generateUID($usertype, $secondReferral),
        ]);

        $this->assertEquals('CS0002', Elder::generateUID('CGA', $secondReferral));

        $data = [
            'name' => 'Toph Bei Fong',
            'name_en' => 'Sung Kang',
            'gender' => 'female',
            'birth_month' => 1,
            'birth_year' => 1920,
            'contact_number' => '12345678',
            'second_contact_number' => '12345678',
            'third_contact_number' => '12345678',
            'address' => 'Street Unknown State Zero six 2',
            'district' => $district->district_name,
            'zone' => $zone->name,
            'emergency_contact_number' => '081234567890',
            'emergency_contact_number_2' => '081234567890',
            'emergency_contact_name' => 'Ling Bei Fong',
            'emergency_contact_relationship_other' => 'Child',
            'relationship' => null,
            'uid_connected_with' => null,
            'case_type' => $usertype,
            'source_of_referral' => $secondReferral->label,
        ];

        $response = $this->postJson('/elderly-api/v1/elders', $data);

        $response->assertCreated();
    }

    // public function test_create_elder_with_duplicate_contact_number_failed()
    // {
    //     $district = District::factory()->create();
    //     $zone = Zone::factory()->create();
    //     $referral = Referral::factory()->create();
    //     $elder = Elder::factory()->create(
    //         [
    //             'name' => 'Toph Bei Fong',
    //             'contact_number' => "081234567892",
    //             'gender' => 'female',
    //             'birth_year' => 1920,
    //         ]
    //     );
    //     $allowedLanguage = array_map(fn (Language $language) => $language->value(), Language::cases());
    //     $randomLanguage = $this->faker->randomElement($allowedLanguage);
    //     $data = [
    //         'name' => 'Toph Bei Fong',
    //         'name_en' => 'Sung Kang',
    //         'gender' => 'female',
    //         'birth_day' => 1,
    //         'birth_month' => 1,
    //         'birth_year' => 1920,
    //         'contact_number' => $elder->contact_number,
    //         'address' => 'Street Unknown State Zero six 2',
    //         'district' => $district->district_name,
    //         'zone' => $zone->name,
    //         'language' => $randomLanguage,
    //         'emergency_contact_number' => '081234567890',
    //         'emergency_contact_number_2' => '081234567890',
    //         'emergency_contact_name' => 'Ling Bei Fong',
    //         'emergency_contact_relationship_other' => 'Child',
    //         'relationship' => null,
    //         'uid_connected_with' => null,
    //         'case_type' => 'CGA',
    //         'source_of_referral' => $referral->label,
    //     ];

    //     $response = $this->postJson('/elderly-api/v1/elders', $data);

    //     $response->assertStatus(422);
    //     $response->assertJsonPath('status.errors.0.field', 'name');
    //     $response->assertJsonPath('status.errors.0.message', 'Combination of name, gender, contact_number, and birth_year already taken');
    // }

    public function test_check_is_contact_number_available_success()
    {
        $response = $this->getJson('/elderly-api/v1/is-contact-number-available?contact_number=081234567890');
        $response->assertJsonPath('data.status', true);
    }

    public function test_check_is_contact_number_available_failed()
    {
        District::factory()->create();
        Zone::factory()->create();
        Referral::factory()->create();
        $elder = Elder::factory()->create();
        $response = $this->getJson("/elderly-api/v1/is-contact-number-available?contact_number=$elder->contact_number");
        $response->assertJsonPath('data.status', false);
    }

    // public function test_update_elder_success()
    // {
    //     $district = District::factory()->create();
    //     $zone = Zone::factory()->create();
    //     $referral = Referral::factory()->create();
    //     $elder = Elder::factory()->create(
    //         [
    //             'name' => 'Toph Beifong',
    //             'name_en' => 'Sung Kang',
    //             'contact_number' => "12345678",
    //             'gender' => 'female',
    //             'birth_year' => 1920,
    //         ]
    //     );
    //     $allowedLanguage = array_map(fn (Language $language) => $language->value(), Language::cases());
    //     $randomLanguage = $this->faker->randomElement($allowedLanguage);
    //     $data = [
    //         'name' => 'Toph Beifong',
    //         'name_en' => 'Sung Kang',
    //         'district' => $district->district_name,
    //         'contact_number' => "12345678",
    //         'address' => 'Beifong Estate, Earth kingdom',
    //         'gender' => 'female',
    //         'birth_day' => 1,
    //         'birth_month' => 1,
    //         'birth_year' => 1920,
    //         'emergency_contact_number' => '081234567891',
    //         'emergency_contact_name' => 'Suyin Beifong',
    //         'emergency_contact_relationship_other' => 'Child',
    //         'zone' => $zone->name,
    //         'language' => $randomLanguage,
    //     ];
    //     $response = $this->putJson("/elderly-api/v1/elders/$elder->id", $data);
    //     $response->assertOk();

    //     $cleanData = collect($data)->except(['district', 'zone', 'source_of_referral'])->all();
    //     $this->assertDatabaseHas('elders', array_merge(['id' => $elder->id, 'district_id' => $district->id], $cleanData));
    // }

    // public function test_update_nonexistence_elder_failed()
    // {
    //     $district = District::factory()->create();
    //     $zone = Zone::factory()->create();
    //     $referral = Referral::factory()->create();
    //     $allowedLanguage = array_map(fn (Language $language) => $language->value(), Language::cases());
    //     $randomLanguage = $this->faker->randomElement($allowedLanguage);
    //     $data = [
    //         'name' => 'Toph Beifong',
    //         'name_en' => 'Sung Kang',
    //         'case_type' => 'CGA',
    //         'district' => $district->district_name,
    //         'contact_number' => "12345678",
    //         'address' => 'Beifong Estate, Earth kingdom',
    //         'gender' => 'female',
    //         'birth_day' => 1,
    //         'birth_month' => 1,
    //         'birth_year' => 1920,
    //         'emergency_contact_number' => '081234567891',
    //         'emergency_contact_name' => 'Suyin Beifong',
    //         'emergency_contact_relationship_other' => 'Parent',
    //         'source_of_referral' => $referral->label,
    //         'zone' => $zone->name,
    //         'language' => $randomLanguage,
    //     ];
    //     $response = $this->putJson('/elderly-api/v1/elders/101', $data);
    //     $response->assertNotFound();
    // }

    public function test_get_elder_detail_success()
    {
        District::factory()->create();
        Zone::factory()->create();
        Referral::factory()->create();
        $elder = Elder::factory()->create();

        $response = $this->getJson("/elderly-api/v1/elders/$elder->id");
        $response->assertOk();
        $response->assertJsonStructure([
            'data' => [
                'id',
                'uid',
                'name',
                'name_en',
                'gender',
                'contact_number',
                'second_contact_number',
                'third_contact_number',
                'address',
                'birth_day',
                'birth_month',
                'birth_year',
                'district_id',
                'district',
                'zone_id',
                'zone',
                'language',
                'centre_case_id',
                'centre_responsible_worker_id',
                'centre_responsible_worker',
                'responsible_worker_contact',
                'case_type',
                'referral_id',
                'source_of_referral',
                'relationship',
                'uid_connected_with',
                'emergency_contact_number',
                'emergency_contact_number_2',
                'emergency_contact_name',
                'emergency_contact_relationship_other',
                'emergency_contact_2_number',
                'emergency_contact_2_number_2',
                'emergency_contact_2_name',
                'emergency_contact_2_relationship_other',
            ],
        ]);
    }

    public function test_get_elder_detail_of_nonexistence_data()
    {
        $response = $this->getJson('/elderly-api/v1/elders/101');
        $response->assertNotFound();
    }

    public function test_delete_elder_success()
    {
        District::factory()->create();
        Zone::factory()->create();
        Referral::factory()->create();
        $elder = Elder::factory()->create();
        $elderId = $elder->id;

        $response = $this->deleteJson("/elderly-api/v1/elders/$elderId");
        $response->assertNoContent();
        $this->assertDatabaseMissing('elders', ['id' => $elderId]);
    }

    public function test_delete_nonexistence_elder()
    {
        $response = $this->deleteJson('/elderly-api/v1/elders/101');
        $response->assertNotFound();
    }

    public function test_post_bulk_create_elder_success()
    {
        $district = District::factory()->create();
        $zone = Zone::factory()->create();
        $referral = Referral::factory()->create();
        $allowedLanguage = array_map(fn (Language $language) => $language->value(), Language::cases());
        $randomLanguage = $this->faker->randomElement($allowedLanguage);

        $first_data = [
            'name' => 'Toph Bei Fong',
            'name_en' => 'Sung Kang',
            'gender' => 'female',
            'birth_day' => 1,
            'birth_month' => 1,
            'birth_year' => 1920,
            'contact_number' => '12345678',
            'second_contact_number' => '12345678',
            'third_contact_number' => '12345678',
            'address' => 'Street Unknown State Zero six 2',
            'district' => $district->district_name,
            'zone' => $zone->name,
            'language' => $randomLanguage,
            'emergency_contact_number' => '081234567890',
            'emergency_contact_number_2' => '081234567891',
            'emergency_contact_name' => 'Ling Bei Fong',
            'emergency_contact_relationship_other' => 'Child',
            'emergency_contact_2_number' => '081234567892',
            'emergency_contact_2_number_2' => '081234567893',
            'emergency_contact_2_name' => 'Suyin Bei Fong',
            'emergency_contact_2_relationship_other' => 'Child',
            'relationship' => null,
            'uid_connected_with' => null,
            'case_type' => $this->faker->randomElement(['BZN', 'CGA']),
            'source_of_referral' => $referral->label,
        ];

        $second_data = [
            'name' => 'Second User', //different name
            'name_en' => 'Sung Go Kong',
            'gender' => 'female',
            'birth_day' => 1,
            'birth_month' => 1,
            'birth_year' => 1920,
            'contact_number' => '12345679', //different contact
            'second_contact_number' => '12345678',
            'third_contact_number' => '12345678',
            'address' => 'Street Unknown State Zero six 2',
            'district' => $district->district_name,
            'zone' => $zone->name,
            'language' => $randomLanguage,
            'emergency_contact_number' => '081234567890',
            'emergency_contact_number_2' => '081234567891',
            'emergency_contact_name' => 'Ling Bei Fong',
            'emergency_contact_relationship_other' => 'Child',
            'emergency_contact_2_number' => '081234567892',
            'emergency_contact_2_number_2' => '081234567893',
            'emergency_contact_2_name' => 'Suyin Bei Fong',
            'emergency_contact_2_relationship_other' => 'Child',
            'relationship' => null,
            'uid_connected_with' => null,
            'case_type' => $this->faker->randomElement(['BZN', 'CGA']),
            'source_of_referral' => $referral->label,
        ];

        $data = [
            'elders' => [
                $first_data, $second_data
            ]
        ];

        $response = $this->postJson('/elderly-api/v1/elders-bulk-create', $data);

        $response->assertOk();
        $response->assertJsonCount(count($data['elders']), 'data.success');
    }

    // public function test_post_bulk_create_elder_partial_success()
    // {
    //     $district = District::factory()->create();
    //     $zone = Zone::factory()->create();
    //     $referral = Referral::factory()->create();
    //     $allowedLanguage = array_map(fn (Language $language) => $language->value(), Language::cases());
    //     $randomLanguage = $this->faker->randomElement($allowedLanguage);

    //     $first_data = [
    //         'name' => 'Toph Bei Fong',
    //         'name_en' => 'Sung Kang',
    //         'gender' => 'female',
    //         'birth_day' => 1,
    //         'birth_month' => 1,
    //         'birth_year' => 1920,
    //         'contact_number' => '12345678',
    //         'second_contact_number' => '12345678',
    //         'third_contact_number' => '12345678',
    //         'address' => 'Street Unknown State Zero six 2',
    //         'district' => $district->district_name,
    //         'zone' => $zone->name,
    //         'language' => $randomLanguage,
    //         'emergency_contact_number' => '081234567890',
    //         'emergency_contact_number_2' => '081234567891',
    //         'emergency_contact_name' => 'Ling Bei Fong',
    //         'emergency_contact_relationship_other' => 'Child',
    //         'emergency_contact_2_number' => '081234567892',
    //         'emergency_contact_2_number_2' => '081234567893',
    //         'emergency_contact_2_name' => 'Suyin Bei Fong',
    //         'emergency_contact_2_relationship_other' => 'Child',
    //         'relationship' => null,
    //         'uid_connected_with' => null,
    //         'case_type' => $this->faker->randomElement(['BZN', 'CGA']),
    //         'source_of_referral' => $referral->label,
    //     ];

    //     $second_data = [
    //         'name' => 'Toph Bei Fong',
    //         'name_en' => 'Sung Kang',
    //         'gender' => 'female',
    //         'birth_day' => 1,
    //         'birth_month' => 1,
    //         'birth_year' => 1920,
    //         'contact_number' => '12345678',
    //         'second_contact_number' => '12345678',
    //         'third_contact_number' => '12345678',
    //         'address' => 'Street Unknown State Zero six 2',
    //         'district' => $district->district_name,
    //         'zone' => $zone->name,
    //         'language' => $randomLanguage,
    //         'emergency_contact_number' => '081234567890',
    //         'emergency_contact_number_2' => '081234567891',
    //         'emergency_contact_name' => 'Ling Bei Fong',
    //         'emergency_contact_relationship_other' => 'Child',
    //         'emergency_contact_2_number' => '081234567892',
    //         'emergency_contact_2_number_2' => '081234567893',
    //         'emergency_contact_2_name' => 'Suyin Bei Fong',
    //         'emergency_contact_2_relationship_other' => 'Child',
    //         'relationship' => null,
    //         'uid_connected_with' => null,
    //         'case_type' => $this->faker->randomElement(['BZN', 'CGA']),
    //         'source_of_referral' => $referral->label,
    //     ];

    //     $data = [
    //         'elders' => [
    //             $first_data, $second_data
    //         ]
    //     ];

    //     $response = $this->postJson('/elderly-api/v1/elders-bulk-create', $data);

    //     $response->assertOk();
    //     $response->assertJsonCount(1, 'data.success');
    //     $response->assertJsonCount(1, 'data.failed');
    // }

    public function test_export_invalid_data() 
    {
        $district = District::factory()->create();
        $referral = Referral::factory()->create();

        $response = $this->json('POST', 'elderly-api/v1/elders-export-invalid-data', [
            'failed' => [
                [
                    'uid' => 'Invalid',
                    'name' => '唐子敏',
                    'name_en' => 'Testing',
                    'gender' => 'f',
                    'birth_day' => 1,
                    'birth_month' => 1,
                    'birth_year' => 1982,
                    'contact_number' => 96278597,
                    'second_contact_number' => 'Invalid',
                    'third_contact_number' => null,
                    'address' => '彩福邨彩喜樓2410室',
                    'district' => $district->district_name,
                    'zone' => '彩福邨',
                    'language' => null,
                    'centre_case_id' => '91-03837',
                    'centre_responsible_worker' => null,
                    'responsible_worker_contact' => null,
                    'related_uid' => null,
                    'relationship' => null,
                    'case_type' => 'CGA',
                    'source_of_referral' => $referral->label,
                    'emergency_contact_name' => '何小姐',
                    'emergency_contact_number' => 2458338,
                    'emergency_contact_number_2' => null,
                    'emergency_contact_relationship_other' => '孫女',
                    'emergency_contact_2_name' => null,
                    'emergency_contact_2_number' => null,
                    'emergency_contact_2_number_2' => null,
                    'emergency_contact_2_relationship_other' => null,
                ],
            ]
        ]);
        
        
        $response->assertOK();
        
        $now = date('d-m-Y_H-i-s'); 
        $filename = "elders_invalid_datas_$now.xlsx";
        $header = $response->headers->get('content-disposition');
        
        $this->assertEquals($header, "attachment; filename=$filename");
    }
}
