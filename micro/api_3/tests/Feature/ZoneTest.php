<?php

namespace Tests\Feature;

use App\Models\Zone;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Support\Str;
use Tests\TestCase;

class ZoneTest extends TestCase
{
    use WithFaker, WithoutMiddleware, RefreshDatabase;

    public function test_get_zone_list_success()
    {
        $zoneCount = $this->faker->numberBetween(5, 10);
        Zone::factory()
            ->count($zoneCount)
            ->sequence(fn ($sequence) => ['name' => "Zendless Zone $sequence->index"])
            ->create();
        $response = $this->getJson('/elderly-api/v1/zones');

        $response->assertOk();
        $response->assertJsonCount($zoneCount, 'data');
    }

    public function test_get_zone_detail_success()
    {
        $zoneCount = $this->faker->numberBetween(5, 10);
        $zones = Zone::factory()
            ->count($zoneCount)
            ->sequence(fn ($sequence) => ['name' => "Zendless Zone $sequence->index"])
            ->create();
        $zone = $zones->random();
        $response = $this->getJson("/elderly-api/v1/zones/$zone->id");

        $response->assertOk();
        $response->assertJsonPath('data.id', $zone->id);
        $response->assertJsonPath('data.name', $zone->name);
        $response->assertJsonPath('data.code', $zone->code);
    }

    public function test_get_nonexistence_zone_failed()
    {
        $response = $this->getJson('/elderly-api/v1/zones/101');

        $response->assertNotFound();
    }

    public function test_create_zone_success()
    {
        $name = $this->faker->word . ' ' . time();
        $data = [
            'name' => $name,
            'code' => Str::slug($name),
        ];
        $response = $this->postJson('/elderly-api/v1/zones', $data);

        $response->assertCreated();
        $response->assertJsonPath('data.name', $data['name']);
        $response->assertJsonPath('data.code', $data['code']);

        $this->assertDatabaseHas('zones', $data);
    }

    public function test_create_zone_with_empty_data_failed()
    {
        $data = [];
        $response = $this->postJson('/elderly-api/v1/zones', $data);

        $response->assertStatus(422);
    }

    public function test_update_zone_success()
    {
        $zone = Zone::factory()->create();
        $name = 'Updated name';
        $data = [
            'name' => $name,
            'code' => Str::slug($name),
        ];
        $response = $this->putJson("/elderly-api/v1/zones/$zone->id", $data);

        $response->assertOk();
        $response->assertJsonPath('data.id', $zone->id);
        $response->assertJsonPath('data.name', $data['name']);
        $response->assertJsonPath('data.code', $data['code']);

        $this->assertDatabaseHas('zones', array_merge(['id' => $zone->id], $data));
    }

    public function test_update_with_invalid_data_failed()
    {
        $zone = Zone::factory()->create();
        $data = [
            'name' => '',
            'code' => '',
        ];
        $response = $this->putJson("/elderly-api/v1/zones/$zone->id", $data);

        $response->assertStatus(422);
    }

    public function test_update_nonexistence_zone_failed()
    {
        $data = [];
        $response = $this->putJson('/elderly-api/v1/zones/101', $data);

        $response->assertNotFound();
    }

    public function test_delete_nonexistence_zone_failed()
    {
        $response = $this->deleteJson('/elderly-api/v1/zones/101');

        $response->assertNotFound();
    }

    public function test_delete_zone_success()
    {
        $zone = Zone::factory()->create();
        $zoneId = $zone->id;
        $response = $this->deleteJson("/elderly-api/v1/zones/$zoneId");

        $response->assertNoContent();

        $this->assertDatabaseMissing('zones', ['id' => $zoneId]);
    }
}
