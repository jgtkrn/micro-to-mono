<?php

namespace Tests\Feature;

use App\Models\Cases;
use App\Models\District;
use App\Models\Elder;
use App\Models\ElderCalls;
use App\Models\Referral;
use App\Models\Zone;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Tests\TestCase;

class CasesTest extends TestCase
{
    use WithFaker, WithoutMiddleware, RefreshDatabase;

    public function test_get_cases_list_success()
    {
        District::factory()->create();
        Zone::factory()->create();
        Referral::factory()->create();
        Elder::factory()->create();
        $casesCount = $this->faker->numberBetween(5, 10);
        Cases::factory()->count($casesCount)->create();

        $response = $this->getJson('/elderly-api/v1/cases');
        $response->assertOk();
        $response->assertJsonCount($casesCount, 'data');
    }

    public function test_get_cases_list_correct_structure()
    {
        Zone::factory()->create();
        Referral::factory()->create();
        District::factory()->create();
        Elder::factory()->create();
        $case = Cases::factory()->create();

        $response = $this->getJson('/elderly-api/v1/cases');
        $response->assertOk();
        $response->assertJsonPath('data.0.id', $case->id);
        $response->assertJsonPath('data.0.case_number', $case->case_number);
        $response->assertJsonPath('data.0.case_status', $case->case_status);
        $response->assertJsonPath('data.0.elder_uid', $case->elder->uid);
        $response->assertJsonPath('data.0.elder_name', $case->elder->name);
        $response->assertJsonPath('data.0.district', $case->elder->district->district_name);
    }

    public function test_filter_cases_list_by_district_success()
    {
        District::factory()->create();
        Zone::factory()->create();
        Referral::factory()->create();
        Elder::factory()->create();
        $casesCount = $this->faker->numberBetween(5, 10);
        Cases::factory()->count($casesCount)->create();

        $response = $this->getJson('/elderly-api/v1/cases');
        $response->assertOk();
        $response->assertJsonCount($casesCount, 'data');
    }

    public function test_filter_cases_list_by_user_type_success()
    {
        District::factory()->create();
        Zone::factory()->create();
        Referral::factory()->create();
        $userType = $this->faker->randomElement(['BZN', 'CGA']);
        $count1 = $this->faker->numberBetween(5, 10);
        $elders1 = Elder::factory()
            ->count($count1)
            ->sequence(fn ($sequence) => ['uid' => 'UID001' . $sequence->index+1])
            ->create([
                'case_type' => $userType,
            ]);
        $count2 = $this->faker->numberBetween(5, 10);
        $elders2 = Elder::factory()
            ->count($count2)
            ->sequence(fn ($sequence) => ['uid' => 'UID002' . $sequence->index+1])
            ->create([
                'case_type' => $userType === 'CGA' ? 'BZN' : 'CGA',
            ]);
        Cases::factory()
            ->count($count1)
            ->create([
                'elder_id' => $elders1->random()->id,
                'case_name' => $userType,
            ]);
        Cases::factory()
            ->count($count2)
            ->create([
                'elder_id' => $elders2->random()->id,
                'case_name' => $userType === 'CGA' ? 'BZN' : 'CGA',
            ]);

        $response = $this->getJson("/elderly-api/v1/cases?user_type=$userType");
        $response->assertOk();
        $response->assertJsonCount($count1, 'data');
    }

    public function test_filter_cases_list_by_case_status_success()
    {
        District::factory()->create();
        Zone::factory()->create();
        Referral::factory()->create();
        Elder::factory()->create();
        $casesCount1 = $this->faker->numberBetween(5, 10);
        $casesCount2 = $this->faker->numberBetween(5, 10);
        $status1 = 'Enrolled - CGA';
        $status2 = 'Enrolled - BZN';
        Cases::factory()->count($casesCount1)->create([
            'case_status' => $status1,
        ]);
        Cases::factory()->count($casesCount2)->create([
            'case_status' => $status2,
        ]);

        $response = $this->getJson("/elderly-api/v1/cases?case_status=$status1");
        $response->assertOk();
        $response->assertJsonCount($casesCount1, 'data');
    }

    public function test_search_cases_list_by_name_success()
    {
        District::factory()->create();
        Zone::factory()->create();
        Referral::factory()->create();
        $elderCount = $this->faker->numberBetween(1, 10);
        $elders = Elder::factory()
            ->count($elderCount)
            ->sequence(fn ($sequence) => [
                'uid' => "UID000$sequence->index",
                'name' => "Elder $sequence->index",
            ])
            ->create();
        $casesCount = $this->faker->numberBetween(5, 10);
        Cases::factory()->count($casesCount)->create([
            'elder_id' => $elders->random()->id,
        ]);

        $selectedElder = $elders->random();

        $response = $this->getJson("/elderly-api/v1/cases?search=$selectedElder->name");
        $response->assertOk();

        $expectedCount = Cases::where('elder_id', $selectedElder->id)->count();
        $response->assertJsonCount($expectedCount, 'data');
    }

    public function test_search_cases_list_by_contact_number_success()
    {
        District::factory()->create();
        Zone::factory()->create();
        Referral::factory()->create();
        $elderCount = $this->faker->numberBetween(1, 10);
        $elders = Elder::factory()
            ->count($elderCount)
            ->sequence(fn ($sequence) => [
                'uid' => "UID000$sequence->index",
                'contact_number' => "08123456789$sequence->index",
            ])
            ->create();
        $casesCount = $this->faker->numberBetween(5, 10);
        Cases::factory()->count($casesCount)->create([
            'elder_id' => $elders->random()->id,
        ]);

        $selectedElder = $elders->random();

        $response = $this->getJson("/elderly-api/v1/cases?search=$selectedElder->contact_number");
        $response->assertOk();

        $expectedCount = Cases::where('elder_id', $selectedElder->id)->count();
        $response->assertJsonCount($expectedCount, 'data');
    }

    public function test_search_cases_list_by_uid_success()
    {
        District::factory()->create();
        Zone::factory()->create();
        Referral::factory()->create();
        $elderCount = $this->faker->numberBetween(1, 10);
        $elders = Elder::factory()
            ->count($elderCount)
            ->sequence(fn ($sequence) => [
                'uid' => "UID000$sequence->index",
            ])
            ->create();
        $casesCount = $this->faker->numberBetween(5, 10);
        Cases::factory()->count($casesCount)->create([
            'elder_id' => $elders->random()->id,
        ]);

        $selectedElder = $elders->random();

        $response = $this->getJson("/elderly-api/v1/cases?search=$selectedElder->uid");
        $response->assertOk();

        $expectedCount = Cases::where('elder_id', $selectedElder->id)->count();
        $response->assertJsonCount($expectedCount, 'data');
    }

    public function test_filter_cases_list_by_user_type_and_search_success()
    {
        District::factory()->create();
        Zone::factory()->create();
        Referral::factory()->create();
        $userType = $this->faker->randomElement(['BZN', 'CGA']);
        $count1 = $this->faker->numberBetween(5, 10);
        $elderName1 = $this->faker->randomElement(['Amoy', 'Acong']);
        $elders1 = Elder::factory()
            ->count($count1)
            ->sequence(fn ($sequence) => [
                'uid' => 'UID001' . $sequence->index+1,
                'name' => $elderName1 . $sequence->index+1
            ])
            ->create([
                'case_type' => $userType,
            ]);
        $count2 = $this->faker->numberBetween(5, 10);
        $elderName2 = $this->faker->randomElement(['Amoy', 'Acong']);
        $elders2 = Elder::factory()
            ->count($count2)
            ->sequence(fn ($sequence) => [
                'uid' => 'UID002' . $sequence->index+1,
                'name' => $elderName2 . $sequence->index+1,
            ])
            ->create([
                'case_type' => $userType === 'CGA' ? 'BZN' : 'CGA',
            ]);
        Cases::factory()
            ->count($count1)
            ->create([
                'elder_id' => $elders1->random()->id,
                'case_name' => $userType,
            ]);
        Cases::factory()
            ->count($count2)
            ->create([
                'elder_id' => $elders2->random()->id,
                'case_name' => $userType === 'CGA' ? 'BZN' : 'CGA',
            ]);

        $response = $this->getJson("/elderly-api/v1/cases?user_type=$userType&search=$elderName1");
        $response->assertOk();

        $elderIds = Elder::where('name', 'like', "$elderName1%")->pluck('id')->all();
        $expectedCount = Cases::where('case_name', $userType)->whereIn('elder_id', $elderIds)->count();
        $response->assertJsonCount($expectedCount, 'data');
    }

    public function test_filter_cases_list_by_district_and_search_success()
    {
        $district1 = District::factory()->create(['district_name' => 'Kowloon']);
        $district2 = District::factory()->create(['district_name' => 'Tuen Mun']);
        Zone::factory()->create();
        Referral::factory()->create();
        $count1 = $this->faker->numberBetween(5, 10);
        $elderName1 = $this->faker->randomElement(['Amoy', 'Acong']);
        $elders1 = Elder::factory()
            ->count($count1)
            ->sequence(fn ($sequence) => [
                'uid' => 'UID001' . $sequence->index+1,
                'name' => $elderName1 . $sequence->index+1
            ])
            ->create([
                'district_id' => $district1->id,
            ]);
        $count2 = $this->faker->numberBetween(5, 10);
        $elderName2 = $this->faker->randomElement(['Amoy', 'Acong']);
        $elders2 = Elder::factory()
            ->count($count2)
            ->sequence(fn ($sequence) => [
                'uid' => 'UID002' . $sequence->index+1,
                'name' => $elderName2 . $sequence->index+1,
            ])
            ->create([
                'district_id' => $district2->id,
            ]);
        Cases::factory()
            ->count($count1)
            ->create([
                'elder_id' => $elders1->random()->id,
            ]);
        Cases::factory()
            ->count($count2)
            ->create([
                'elder_id' => $elders2->random()->id,
            ]);

        $response = $this->getJson("/elderly-api/v1/cases?district=$district1->district_name&search=$elderName1");
        $response->assertOk();

        $elderIds = Elder::where('district_id', $district1->id)->pluck('id')->all();
        $expectedCount = Cases::whereIn('elder_id', $elderIds)->count();
        $response->assertJsonCount($expectedCount, 'data');
    }

    // public function test_get_case_detail_success()
    // {
    //     District::factory()->create();
    //     Zone::factory()->create();
    //     Referral::factory()->create();
    //     $elder = Elder::factory()->create();
    //     $casesCount = $this->faker->numberBetween(5, 10);
    //     $cases = Cases::factory()->count($casesCount)->create();
    //     $randomCase = $cases->random();

    //     $response = $this->getJson("/elderly-api/v1/cases/$randomCase->id");
    //     $response->assertOk();
    //     $response->assertJsonPath('data.id', $randomCase->id);
    //     $response->assertJsonPath('data.user_type', $randomCase->case_name);
    //     $response->assertJsonPath('data.case_number', $randomCase->case_number);
    //     $response->assertJsonPath('data.case_status', $randomCase->case_status);
    //     $response->assertJsonPath('data.elder.id', $elder->id);
    //     $response->assertJsonPath('data.elder.uid', $elder->uid);
    //     $response->assertJsonPath('data.elder.name', $elder->name);
    //     $response->assertJsonCount(0, 'data.calls');
    // }

    // public function test_get_case_detail_with_calls_success()
    // {
    //     District::factory()->create();
    //     Zone::factory()->create();
    //     Referral::factory()->create();
    //     $elder = Elder::factory()->create();
    //     $casesCount = $this->faker->numberBetween(5, 10);
    //     $cases = Cases::factory()->count($casesCount)->create();
    //     $randomCase = $cases->random();

    //     $callsCount = $this->faker->numberBetween(5, 10);
    //     ElderCalls::factory()->count($callsCount)->create([
    //         'cases_id' => $randomCase->id,
    //     ]);

    //     $response = $this->getJson("/elderly-api/v1/cases/$randomCase->id");
    //     $response->assertOk();
    //     $response->assertJsonPath('data.id', $randomCase->id);
    //     $response->assertJsonPath('data.user_type', $randomCase->case_name);
    //     $response->assertJsonPath('data.case_number', $randomCase->case_number);
    //     $response->assertJsonPath('data.case_status', $randomCase->case_status);
    //     $response->assertJsonPath('data.elder.id', $elder->id);
    //     $response->assertJsonPath('data.elder.uid', $elder->uid);
    //     $response->assertJsonPath('data.elder.name', $elder->name);
    //     $response->assertJsonCount($callsCount, 'data.calls');
    // }

    public function test_create_case_with_empty_data_failed()
    {
        $response = $this->postJson('/elderly-api/v1/cases', []);
        $response->assertStatus(422);
    }

    public function test_create_case_success()
    {
        District::factory()->create();
        Zone::factory()->create();
        Referral::factory()->create();
        $elder = Elder::factory()->create();
        $data = [
            'case_name' => $this->faker->randomElement(['BZN', 'CGA']),
            'caller_name' => $this->faker->word,
            'case_status' => $this->faker->randomElement(['Ongoing', 'Follow up', 'Completed']),
            'case_number' => $this->faker->randomDigit(),
            'elder_id' => $elder->id,
        ];
        $response = $this->postJson('/elderly-api/v1/cases', $data);
        $response->assertCreated();
        $response->assertJsonPath('data.user_type', $data['case_name']);
        $response->assertJsonPath('data.case_number', $data['case_number']);
        $response->assertJsonPath('data.case_status', $data['case_status']);
        $response->assertJsonPath('data.elder.id', $elder->id);
        $response->assertJsonPath('data.elder.uid', $elder->uid);
        $response->assertJsonPath('data.elder.name', $elder->name);
    }

    // public function test_update_case_with_empty_data_failed()
    // {
    //     District::factory()->create();
    //     Zone::factory()->create();
    //     Referral::factory()->create();
    //     Elder::factory()->create();
    //     $casesCount = $this->faker->numberBetween(5, 10);
    //     $cases = Cases::factory()->count($casesCount)->create();
    //     $randomCase = $cases->random();

    //     $response = $this->putJson("/elderly-api/v1/cases/$randomCase->id", []);
    //     $response->assertStatus(422);
    // }

    public function test_update_nonexistence_case_failed()
    {
        District::factory()->create();
        Zone::factory()->create();
        Referral::factory()->create();
        $elder = Elder::factory()->create();
        $data = [
            'case_name' => $this->faker->randomElement(['BZN', 'CGA']),
            'caller_name' => $this->faker->word,
            'case_status' => $this->faker->randomElement(['Ongoing', 'Follow up', 'Completed']),
            'case_number' => $this->faker->randomDigit(),
            'elder_id' => $elder->id,
        ];
        $response = $this->putJson('/elderly-api/v1/cases/101', $data);
        $response->assertNotFound();
    }

    public function test_update_case_success()
    {
        District::factory()->create();
        Zone::factory()->create();
        Referral::factory()->create();
        Elder::factory()->create();
        $casesCount = $this->faker->numberBetween(5, 10);
        $cases = Cases::factory()->count($casesCount)->create();
        $randomCase = $cases->random();

        $elder = Elder::factory()->create([
            'uid' => $this->faker->numerify('uid####'),
        ]);
        $data = [
            'case_name' => $this->faker->randomElement(['BZN', 'CGA']),
            'caller_name' => $this->faker->word,
            'case_status' => $this->faker->randomElement(['Ongoing', 'Follow up', 'Completed']),
            'case_number' => $this->faker->randomDigit(),
            'elder_id' => $elder->id,
        ];

        $response = $this->putJson("/elderly-api/v1/cases/$randomCase->id", $data);
        $response->assertOk();

        $randomCase->refresh();
        $response->assertJsonPath('data.id', $randomCase->id);
        $response->assertJsonPath('data.user_type', $randomCase->case_name);
        $response->assertJsonPath('data.case_number', $randomCase->case_number);
        $response->assertJsonPath('data.case_status', $randomCase->case_status);
        $response->assertJsonPath('data.elder.id', $elder->id);
        $response->assertJsonPath('data.elder.uid', $elder->uid);
        $response->assertJsonPath('data.elder.name', $elder->name);
        $response->assertJsonCount(0, 'data.calls');
    }

    public function test_delete_nonexistence_case_failed()
    {
        $response = $this->deleteJson('/elderly-api/v1/cases/101');
        $response->assertNotFound();
    }

    public function test_delete_case_success()
    {
        District::factory()->create();
        Zone::factory()->create();
        Referral::factory()->create();
        Elder::factory()->create();
        $casesCount = $this->faker->numberBetween(5, 10);
        $cases = Cases::factory()->count($casesCount)->create();
        $caseId = $cases->random()->id;

        $response = $this->deleteJson("/elderly-api/v1/cases/$caseId");
        $response->assertNoContent();

        $this->assertDatabaseMissing('cases', ['id' => $caseId]);
    }
}
