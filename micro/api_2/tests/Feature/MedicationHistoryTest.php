<?php

namespace Tests\Feature;

use App\Models\AssessmentCase;
use App\Models\MedicationHistory;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

use function PHPUnit\Framework\isNull;

class MedicationHistoryTest extends TestCase
{
    use RefreshDatabase, WithoutMiddleware, WithFaker;

    public function test_get_medication_history_by_case_id_notfound()
    {
        $elderCasesId = -1;
        $medicationHistory = MedicationHistory::where('case_id', $elderCasesId)->first();
        $medicationHistoryId = (isNull($medicationHistory) ? 0 : $medicationHistory->id);
        $medication_histories_response = $this->getJson("assessments-api/v1/medication-histories/{$medicationHistoryId}");
        $medication_histories_response->assertNotFound();
    }

    public function test_get_medication_history_list() 
    {
        $medication_histories = MedicationHistory::factory()->count(5)->create();
        $response = $this->get('assessments-api/v1/medication-histories');
        $response->assertOk();
        $response->assertJsonCount($medication_histories->count(), 'data');
    }

    public function test_create_medication_history() 
    {
        $create_response = $this->json('POST', 'assessments-api/v1/medication-histories', [
            'case_id' => 204,
            'medication_category' => 'hyspepsia',
            'medication_name' => 'Mylanta',
            'dosage' => '15mg',
            'number_of_intake' => '1 tab',
            'frequency' => ["Daily", "BD", "TDS", "QID", "Q_H", "Nocte", "prn", "Others"],
            'route' => 'PO',
            'remarks' => 'remarks',
            'created_by' => 'test',
            'updated_by' => 'test',
            'updated_by_name' => 'test',
            'created_by_name' => 'test'
          ]);
          $create_response->assertOk();
    }

    public function test_create_medication_history_invalid_data() 
    {
        $create_response = $this->json('POST', 'assessments-api/v1/medication-histories', [
            'case_id' => 1,
            'medication_category' => 'hyspepsia',
            'medication_name' => 'Mylanta',
            'dosage' => '15mg',
            'number_of_intake' => '1 tab',
            'frequency' => ["Daily", "BD", "TDS", "QID", "Q_H", "Nocte", "prn", "Others"],
            'route' => '', // required 
            'remarks' => '', // optional
            'created_by' => 'test',
            'updated_by' => 'test',
            'updated_by_name' => 'test',
            'created_by_name' => 'test'
        ]);

        $create_response->assertStatus(422);
        $create_response->assertJson([
            "status" => [
                "code" => 422,
                "message" => "",
                "errors" => [
                    [
                        "field" => "route",
                        "message" => "The route field is required.",
                    ],
                ],
            ]
        ]);
    }
    
    public function test_create_medication_history_notfound() 
    {
        // Elder cases id join table at Elder service is not exits with value -1
        $caseId = -1;
        $create_response = $this->json('POST', 'assessments-api/v1/medication-histories', [
            'case_id' => $caseId, 
            'medication_category' => 'hyspepsia',
            'medication_name' => 'Mylanta',
            'dosage' => '15mg',
            'number_of_intake' => '1 tab',
            'frequency' => ["Daily", "BD", "TDS", "QID", "Q_H", "Nocte", "prn", "Others"],
            'route' => 'PO',
            'remarks' => 'remarks',
            'created_by' => 'test',
            'updated_by' => 'test',
            'updated_by_name' => 'test',
            'created_by_name' => 'test'
        ]);
        $create_response->assertNotFound();
    }

    public function test_get_medication_history_detail() 
    {
        $medication_histories = MedicationHistory::factory()->create();
        $response = $this->getJson("assessments-api/v1/medication-histories/{$medication_histories->id}");
        $response->assertOK();

    }

    public function test_get_notfound_medication_history_detail() 
    {
        $response = $this->getJson("assessments-api/v1/medication-histories/404");
        $response->assertNotFound();

    }

    public function test_update_medication_history() 
    {
        $medication_histories = MedicationHistory::factory()->create();
        $medication_histories->medication_name = "{$medication_histories->medication_name} - updated";
        $response = $this->putJson("assessments-api/v1/medication-histories/{$medication_histories->id}", $medication_histories->toArray());
        $response->assertOK();
    }
    
    public function test_update_notfound_medication_history() 
    {
        $medication_histories = MedicationHistory::factory()->create();
        $medication_histories->medication_name = "{$medication_histories->medication_name} - updated";
        $response = $this->putJson("assessments-api/v1/medication-histories/404", $medication_histories->toArray());
        $response->assertNotFound();
    }

    public function test_delete_medication_history() 
    {
        $medication_histories = MedicationHistory::factory()->create();
        $response = $this->deleteJson("assessments-api/v1/medication-histories/{$medication_histories->id}");
        $response->assertOk();
    }
    
}
