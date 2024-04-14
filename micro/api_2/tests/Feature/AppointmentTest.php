<?php

namespace Tests\Feature;

use App\Models\Appointment;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AppointmentTest extends TestCase
{
    use RefreshDatabase, WithoutMiddleware;
    
    # Appointments Search
    public function test_search_by_name_success() 
    {
        $inputs = [
            [
                'cluster' => 'Hong Kong East Cluster (港島東聯網)',
                'type' => 'Hospital',
                'name_en' => 'Pamela Youde Nethersole Eastern Hospital (PYNEH)',
                'name_sc' => '東區尤德夫人那打素醫院',
            ],
            [
                'cluster' => 'Hong Kong East Cluster (港島東聯網)',
                'type' => 'Hospital',
                'name_en' => 'Ruttonjee Hospital (RH)',
                'name_sc' => '律敦治醫院',
            ],
        ];

        foreach ($inputs as $input) {
            Appointment::factory()->create($input);
        }

        $response = $this->json('POST', 'assessments-api/v1/appointments/search', [
            'query' => 'east',
        ]);
        
        $response->assertOk();
    }
    
    public function test_search_by_name_notfound() 
    {
        $inputs = [
            [
                'cluster' => 'Hong Kong East Cluster (港島東聯網)',
                'type' => 'Hospital',
                'name_en' => 'Pamela Youde Nethersole Eastern Hospital (PYNEH)',
                'name_sc' => '東區尤德夫人那打素醫院',
            ],
            [
                'cluster' => 'Hong Kong East Cluster (港島東聯網)',
                'type' => 'Hospital',
                'name_en' => 'Ruttonjee Hospital (RH)',
                'name_sc' => '律敦治醫院',
            ],
        ];

        foreach ($inputs as $input) {
            Appointment::factory()->create($input);
        }

        $response = $this->json('POST', 'assessments-api/v1/appointments/search', [
            'query' => 'notfound',
        ]);

        $response->assertNotFound();
    }
    
    public function test_search_by_bad_request() 
    {
        $inputs = [
            [
                'cluster' => 'Hong Kong East Cluster (港島東聯網)',
                'type' => 'Hospital',
                'name_en' => 'Pamela Youde Nethersole Eastern Hospital (PYNEH)',
                'name_sc' => '東區尤德夫人那打素醫院',
            ],
            [
                'cluster' => 'Hong Kong East Cluster (港島東聯網)',
                'type' => 'Hospital',
                'name_en' => 'Ruttonjee Hospital (RH)',
                'name_sc' => '律敦治醫院',
            ],
        ];

        foreach ($inputs as $input) {
            Appointment::factory()->create($input);
        }
        
        $response = $this->json('POST', 'assessments-api/v1/appointments/search', []);

        $response->assertStatus(400);
        
        $response->assertJson([
            'error' => [
                'code' => 400,
                'message' => "query key parameter is required",
                'success' => false
            ]
        ]);
    }

    # CRUD API Appointments
    public function test_get_appointment_list() 
    {
        $appointment = Appointment::factory()->count(5)->create();
        $response = $this->get('assessments-api/v1/appointments');
        $response->assertOk();
        $response->assertJsonCount($appointment->count(), 'data');
    }

    public function test_create_appointment() 
    {
        $create_response = $this->json('POST', 'assessments-api/v1/appointments', [
            'cluster' => 'Hong Kong East Cluster (港島東聯網)',
            'type' => 'Hospital',
            'name_en' => 'Pamela Youde Nethersole Eastern Hospital (PYNEH)',
            'name_sc' => '東區尤德夫人那打素醫院',
          ]);
          $create_response->assertOk();
    }

    public function test_create_appointment_invalid_data() 
    {
        $create_response = $this->json('POST', 'assessments-api/v1/appointments', [
                'cluster' => 'Hong Kong East Cluster (港島東聯網)', // required
                'type' => '', // required
                'name_en' => 'Pamela Youde Nethersole Eastern Hospital (PYNEH)', // required
                'name_sc' => '東區尤德夫人那打素醫院', // required
        ]);

        $create_response->assertStatus(422);
        $create_response->assertJson([
            "status" => [
                "code" => 422,
                "message" => "",
                "errors" => [
                    [
                        "field" => "type",
                        "message" => "The type field is required.",
                    ],
                ],
            ]
        ]);
    }

    public function test_get_appointment_detail() 
    {
        $appointment = Appointment::factory()->create();
        $response = $this->getJson("assessments-api/v1/appointments/{$appointment->id}");
        $response->assertOK();

    }

    public function test_get_notfound_appointment_detail() 
    {
        $response = $this->getJson("assessments-api/v1/appointments/404");
        $response->assertNotFound();

    }

    public function test_update_appointment() 
    {
        $appointment = Appointment::factory()->create();
        $appointment->name = "{$appointment->name} - updated";
        $response = $this->putJson("assessments-api/v1/appointments/{$appointment->id}", $appointment->toArray());
        $response->assertOK();
    }
    
    public function test_update_notfound_appointment() 
    {
        $appointment = Appointment::factory()->create();
        $appointment->name = "{$appointment->name} - updated";
        $response = $this->putJson("assessments-api/v1/appointments/404", $appointment->toArray());
        $response->assertNotFound();
    }

    public function test_delete_appointment() 
    {
        $appointment = Appointment::factory()->create();
        $response = $this->deleteJson("assessments-api/v1/appointments/{$appointment->id}");
        $response->assertOk();
    }
}

