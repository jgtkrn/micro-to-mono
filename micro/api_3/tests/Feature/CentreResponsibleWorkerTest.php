<?php

namespace Tests\Feature;

use App\Models\CentreResponsibleWorker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Tests\TestCase;

class CentreResponsibleWorkerTest extends TestCase
{
    use WithFaker, WithoutMiddleware, RefreshDatabase;

    public function test_get_centre_list_success()
    {
        $count = $this->faker->numberBetween(5, 10);
        CentreResponsibleWorker::factory()
            ->count($count)
            ->sequence(fn ($sequence) => ['name' => "Clinic 00$sequence->index"])
            ->create();
        $response = $this->getJson('/elderly-api/v1/centres');

        $response->assertOk();
        $response->assertJsonCount($count, 'data');
    }

    public function test_create_centre_success()
    {
        $data = [
            'name' => 'Air Nomad Monastery',
            'code' => 'air_nomad_monastery'
        ];
        $response = $this->postJson('/elderly-api/v1/centres', $data);

        $response->assertCreated();
        $response->assertJsonPath('data.name', $data['name']);
        $response->assertJsonPath('data.code', $data['code']);

        $this->assertDatabaseHas('centre_responsible_workers', $data);
    }

    public function test_create_centre_with_empty_data_failed()
    {
        $data = [];
        $response = $this->postJson('/elderly-api/v1/centres', $data);

        $response->assertStatus(422);
    }

    public function test_get_centre_detail_success()
    {
        $count = $this->faker->numberBetween(5, 10);
        $centres = CentreResponsibleWorker::factory()
            ->count($count)
            ->sequence(fn ($sequence) => ['name' => "Clinic 00$sequence->index"])
            ->create();
        $randomCentre = $centres->random();
        $response = $this->getJson("/elderly-api/v1/centres/$randomCentre->id");

        $response->assertOk();
        $response->assertJsonPath('data.id', $randomCentre->id);
        $response->assertJsonPath('data.name', $randomCentre->name);
        $response->assertJsonPath('data.code', $randomCentre->code);
    }

    public function test_get_nonexistence_centre_detail_failed()
    {
        $response = $this->getJson('/elderly-api/v1/centres/101');

        $response->assertNotFound();
    }

    public function test_update_centre_success()
    {
        $count = $this->faker->numberBetween(5, 10);
        $centres = CentreResponsibleWorker::factory()
            ->count($count)
            ->sequence(fn ($sequence) => ['name' => "Clinic 00$sequence->index"])
            ->create();
        $randomCentre = $centres->random();
        $data = [
            'name' => 'Updated Centre Name',
            'code' => 'updated_centre_name',
        ];
        $response = $this->putJson("/elderly-api/v1/centres/$randomCentre->id", $data);

        $response->assertOk();
        $response->assertJsonPath('data.id', $randomCentre->id);
        $response->assertJsonPath('data.name', $data['name']);
        $response->assertJsonPath('data.code', $data['code']);

        $this->assertDatabaseHas('centre_responsible_workers', array_merge(['id' => $randomCentre->id], $data));
    }

    public function test_update_centre_with_empty_data_failed()
    {
        $count = $this->faker->numberBetween(5, 10);
        $centres = CentreResponsibleWorker::factory()
            ->count($count)
            ->sequence(fn ($sequence) => ['name' => "Clinic 00$sequence->index"])
            ->create();
        $randomCentre = $centres->random();
        $data = [
            'name' => '',
            'code' => '',
        ];
        $response = $this->putJson("/elderly-api/v1/centres/$randomCentre->id", $data);

        $response->assertStatus(422);
    }

    public function test_update_nonexistence_centre_detail_failed()
    {
        $data = [
            'name' => 'Updated Centre Name',
            'code' => 'updated_centre_name',
        ];
        $response = $this->putJson('/elderly-api/v1/centres/101', $data);

        $response->assertNotFound();
    }

    public function test_delete_centre_success()
    {
        $count = $this->faker->numberBetween(5, 10);
        $centres = CentreResponsibleWorker::factory()
            ->count($count)
            ->sequence(fn ($sequence) => ['name' => "Clinic 00$sequence->index"])
            ->create();
        $centreId = $centres->random()->id;
        $response = $this->deleteJson("/elderly-api/v1/centres/$centreId");

        $response->assertNoContent();
        $this->assertDatabaseMissing('centre_responsible_workers', ['id' => $centreId]);
    }

    public function test_delete_nonexistence_centre_detail_failed()
    {
        $response = $this->deleteJson('/elderly-api/v1/centres/101');

        $response->assertNotFound();
    }
}
