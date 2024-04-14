<?php

namespace Tests\Feature;

use App\Models\MedicalHistory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Tests\TestCase;

class MedicalHistoryTest extends TestCase
{
    use RefreshDatabase, WithoutMiddleware, WithFaker;

    public function test_get_medical_history_by_case_id()
    {
        $medicalHistory = MedicalHistory::factory()->create();

        $response = $this->getJson("assessments-api/v1/medical-histories/case-id/{$medicalHistory->case_id}");
        $response->assertOk();
    }

    # Medical History Search
    public function test_search_by_category_name_or_diagnosis_name_success() 
    {
        $medicalHistories = [
            [
                'medical_category_name' => 'Blood problem',
                'medical_diagnosis_name' => 'Anemia',
            ],
            [
                'medical_category_name' => 'Blood problem',
                'medical_diagnosis_name' => 'Leukimia',
            ],
        ];

        foreach ($medicalHistories as $medicalHistory) {
            MedicalHistory::factory()->create($medicalHistory);
        }

        $response = $this->json('POST', 'assessments-api/v1/medical-histories/search', [
            'query' => 'Blood',
        ]);
        
        $response->assertOk();
    }
    
    public function test_search_by_category_or_diagnosis_name_notfound() 
    {
        $medicalHistories = [
            [
                'medical_category_name' => 'Blood problem',
                'medical_diagnosis_name' => 'Anemia',
            ],
            [
                'medical_category_name' => 'Blood problem',
                'medical_diagnosis_name' => 'Leukimia',
            ],
        ];

        foreach ($medicalHistories as $medicalHistory) {
            MedicalHistory::factory()->create($medicalHistory);
        }

        $response = $this->json('POST', 'assessments-api/v1/medical-histories/search', [
            'query' => 'notfound',
        ]);
        
        $response->assertNotFound();
    }
    
    public function test_search_by_category_or_diagnosis_name_bad_request() 
    {
        $medicalHistories = [
            [
                'medical_category_name' => 'Blood problem',
                'medical_diagnosis_name' => 'Anemia',
            ],
            [
                'medical_category_name' => 'Blood problem',
                'medical_diagnosis_name' => 'Leukimia',
            ],
        ];

        foreach ($medicalHistories as $medicalHistory) {
            MedicalHistory::factory()->create($medicalHistory);
        }

        $response = $this->json('POST', 'assessments-api/v1/medical-histories/search', []);

        $response->assertStatus(400);
        
        $response->assertJson([
            'error' => [
                'code' => 400,
                'message' => "query key parameter is required",
                'success' => false
            ]
        ]);
    }

    # Crud API Medical History 
    public function test_get_medical_history_list() 
    {
        $medicalHistory = MedicalHistory::factory()->count(5)->create();
        $response = $this->get('assessments-api/v1/medical-histories');
        $response->assertOk();
        $response->assertJsonCount($medicalHistory->count(), 'data');
    }

    public function test_create_medical_history() 
    {
        $create_response = $this->json('POST', 'assessments-api/v1/medical-histories', [
            'case_id' => 1,
            'medical_category_name' => 'Blood problem',
            'medical_diagnosis_name' => 'Anemia',
          ]);
          $create_response->assertOk();
    }

    public function test_create_medical_history_invalid_data() 
    {
        $create_response = $this->json('POST', 'assessments-api/v1/medical-histories', [
            'case_id' => 1, // required
            'medical_category_name' => 'Blood problem', // required
            'medical_diagnosis_name' => '', // required
        ]);

        $create_response->assertStatus(422);
        $create_response->assertJson([
            "status" => [
                "code" => 422,
                "message" => "",
                "errors" => [
                    [
                        "field" => "medical_diagnosis_name",
                        "message" => "The medical diagnosis name field is required.",
                    ],
                ],
            ]
        ]);
    }

    public function test_get_medical_history_detail() 
    {
        $medicalHistory = MedicalHistory::factory()->create();
        $response = $this->getJson("assessments-api/v1/medical-histories/{$medicalHistory->id}");
        $response->assertOK();

    }

    public function test_get_notfound_medical_history_detail() 
    {
        $response = $this->getJson("assessments-api/v1/medical-histories/404");
        $response->assertNotFound();

    }

    public function test_update_medical_history() 
    {
        $medicalHistory = MedicalHistory::factory()->create();
        $medicalHistory->medical_diagnosis_name = "{$medicalHistory->medical_diagnosis_name} - updated";
        $response = $this->putJson("assessments-api/v1/medical-histories/{$medicalHistory->id}", $medicalHistory->toArray());
        $response->assertOK();
    }
    
    public function test_update_notfound_medical_history() 
    {
        $medicalHistory = MedicalHistory::factory()->create();
        $medicalHistory->medical_diagnosis_name = "{$medicalHistory->medical_diagnosis_name} - updated";
        $response = $this->putJson("assessments-api/v1/medical-histories/404", $medicalHistory->toArray());
        $response->assertNotFound();
    }

    public function test_delete_medical_history() 
    {
        $medicalHistory = MedicalHistory::factory()->create();
        $response = $this->deleteJson("assessments-api/v1/medical-histories/{$medicalHistory->id}");
        $response->assertOk();
    }

}