<?php

namespace Tests\Feature;

use App\Models\MedicationDrug;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class MedicationDrugTest extends TestCase
{
    use RefreshDatabase, WithoutMiddleware, WithFaker;

    # MedicationDrugs Search
    public function test_search_by_name_success()
    {
        $medicationDrug = MedicationDrug::factory()->create([
            'name' => 'GASTRO-INTESTINAL SYSTEM 腸胃科藥物',
            'parent_id' => 1,
        ]);

        $response = $this->json('POST', 'assessments-api/v1/medication-drugs/search', [
            'query' => 'GASTRO',
        ]);

        $response->assertOk();
    }

    public function test_search_by_name_notfound()
    {
        $medicationDrug = MedicationDrug::factory()->create([
            'name' => 'GASTRO-INTESTINAL SYSTEM 腸胃科藥物',
            'parent_id' => 1,
        ]);

        $response = $this->json('POST', 'assessments-api/v1/medication-drugs/search', [
            'query' => 'notfound',
        ]);

        $response->assertNotFound();
    }

    public function test_search_by_bad_request()
    {
        $medicationDrug = MedicationDrug::factory()->create([
            'name' => 'GASTRO-INTESTINAL SYSTEM 腸胃科藥物',
            'parent_id' => 1,
        ]);

        $response = $this->json('POST', 'assessments-api/v1/medication-drugs/search', []);

        $response->assertStatus(400);

        $response->assertJson([
            'error' => [
                'code' => 400,
                'message' => "query key parameter is required",
                'success' => false
            ]
        ]);
    }

    # CRUD API MedicationDrugs
    public function test_get_medication_drug_list()
    {
        $medicationDrug = MedicationDrug::factory()->count(5)->create()->where('parent_id', 0);
        $response = $this->get('assessments-api/v1/medication-drugs');
        $response->assertOk();
        $response->assertJsonCount($medicationDrug->count(), 'data');
    }

    public function test_create_medication_drug()
    {
        $create_response = $this->json('POST', 'assessments-api/v1/medication-drugs', [
            'name' => 'GASTRO-INTESTINAL SYSTEM 腸胃科藥物',
            'parent_id' => 1,
        ]);
        $create_response->assertOk();
    }

    public function test_create_medication_drug_invalid_data()
    {
        $create_response = $this->json('POST', 'assessments-api/v1/medication-drugs', [
            'parent_id' => 1,
            'name' => '', // required
        ]);

        $create_response->assertStatus(422);
        $create_response->assertJson([
            "status" => [
                "code" => 422,
                "message" => "",
                "errors" => [
                    [
                        "field" => "name",
                        "message" => "The name field is required.",
                    ],
                ],
            ]
        ]);
    }

    public function test_get_medication_drug_detail()
    {
        $medicationDrug = MedicationDrug::factory()->create();
        $response = $this->getJson("assessments-api/v1/medication-drugs/{$medicationDrug->id}");
        $response->assertOK();
    }

    public function test_get_notfound_medication_drug_detail()
    {
        $response = $this->getJson("assessments-api/v1/medication-drugs/404");
        $response->assertNotFound();
    }

    public function test_update_medication_drug()
    {
        $medicationDrug = MedicationDrug::factory()->create();
        $medicationDrug->name = "{$medicationDrug->name} - updated";
        $response = $this->putJson("assessments-api/v1/medication-drugs/{$medicationDrug->id}", $medicationDrug->toArray());
        $response->assertOK();
    }

    public function test_update_notfound_medication_drug()
    {
        $medicationDrug = MedicationDrug::factory()->create();
        $medicationDrug->name = "{$medicationDrug->name} - updated";
        $response = $this->putJson("assessments-api/v1/medication-drugs/404", $medicationDrug->toArray());
        $response->assertNotFound();
    }

    public function test_delete_medication_drug()
    {
        $medicationDrug = MedicationDrug::factory()->create();
        $response = $this->deleteJson("assessments-api/v1/medication-drugs/{$medicationDrug->id}");
        $response->assertOk();
    }
}
