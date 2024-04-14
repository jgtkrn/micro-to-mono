<?php

namespace Tests\Feature;

use App\Models\District;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Tests\TestCase;

class DistrictTest extends TestCase
{
    use WithFaker, WithoutMiddleware, RefreshDatabase;

    public function test_get_district_list()
    {
        $districtCount = $this->faker->numberBetween(5, 10);
        District::factory()->count($districtCount)->create();
        $response = $this->getJson('/elderly-api/v1/districts');
        $response->assertOk();
        $response->assertJsonCount($districtCount, 'data');
    }

    public function test_get_district_detail()
    {
        $districtCount = $this->faker->numberBetween(5, 10);
        $districts = District::factory()->count($districtCount)->create();
        $district = $districts->random();

        $response = $this->getJson("/elderly-api/v1/districts/$district->id");
        $response->assertOk();
        $response->assertJsonPath('data.id', $district->id);
        $response->assertJsonPath('data.district_name', $district->district_name);
    }

    public function test_create_district_success()
    {
        $data = [
            'district_name' => $this->faker->city,
            'bzn_code' => $this->faker->lexify('???'),
        ];
        $response = $this->postJson('/elderly-api/v1/districts', $data);
        $response->assertCreated();
        $response->assertJsonPath('data.district_name', $data['district_name']);
        $response->assertJsonPath('data.bzn_code', $data['bzn_code']);

        $this->assertDatabaseHas('districts', $data);
    }

    public function test_create_district_failed()
    {
        $data = [];
        $response = $this->postJson('/elderly-api/v1/districts', $data);
        $response->assertStatus(422);
    }

    public function test_update_district_success()
    {
        $district = District::factory()->create();
        $data = [
            'district_name' => 'UPDATED district',
            'bzn_code' => 'NEW',
        ];
        $response = $this->putJson("/elderly-api/v1/districts/$district->id", $data);
        $response->assertOk();
        $response->assertJsonPath('data.district_name', $data['district_name']);
        $response->assertJsonPath('data.bzn_code', $data['bzn_code']);

        $district->refresh();
        $this->assertEquals($district->district_name, $data['district_name']);
        $this->assertEquals($district->bzn_code, $data['bzn_code']);
    }

    public function test_update_district_with_empty_failed()
    {
        $district = District::factory()->create();
        $data = [
            'district_name' => null,
            'bzn_code' => null,
        ];
        $response = $this->putJson("/elderly-api/v1/districts/$district->id", $data);
        $response->assertStatus(422);
    }

    public function test_update_nonexistence_district_failed()
    {
        $data = [
            'district_name' => $this->faker->city,
            'bzn_code' => $this->faker->lexify('???'),
        ];
        $response = $this->putJson('/elderly-api/v1/districts/101', $data);
        $response->assertNotFound();
    }

    public function test_delete_nonexistence_district_failed()
    {
        $response = $this->deleteJson('/elderly-api/v1/districts/101');
        $response->assertNotFound();
    }

    public function test_delete_district_success()
    {
        $district = District::factory()->create();
        $districtId = $district->id;
        $response = $this->deleteJson("/elderly-api/v1/districts/$districtId");
        $response->assertStatus(204);

        $this->assertDatabaseMissing('districts', ['id' => $districtId]);
    }
}
