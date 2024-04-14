<?php

namespace Tests\Feature;

use App\Models\FollowUpHistory;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Tests\TestCase;

class FollowUpHistoryTest extends TestCase
{
    use RefreshDatabase, WithoutMiddleware, WithFaker;

    # CRUD API FollowUpHistories
    public function test_get_follow_up_history_by_case_id()
    {
        $followUpHistory = FollowUpHistory::factory()->create();

        $response = $this->getJson("assessments-api/v1/follow-up-histories/case-id/{$followUpHistory->case_id}");
        $response->assertOk();
    }
    
    public function test_get_follow_up_history_list() 
    {
        $followUpHistory = FollowUpHistory::factory()->count(5)->create();
        $response = $this->get('assessments-api/v1/follow-up-histories');
        $response->assertOk();
    }

    
    public function test_create_follow_up_history() 
    {
        $create_response = $this->json('POST', 'assessments-api/v1/follow-up-histories', [
            'case_id' => 1, // required
            'date' => '2022-10-29', // required
            'time' => '2022-10-29 01:13:22', // required
            'appointment_id' =>  '1',// required
            'type' => 'yes'    
        ]);
          $create_response->assertOk();
    }

    public function test_create_follow_up_history_invalid_data() 
    {
        $create_response = $this->json('POST', 'assessments-api/v1/follow-up-histories', [
            'case_id' => 1, // required
            'date' => '', // required
            'appointment_id' => 1, // required
        ]);

        $create_response->assertStatus(422);
        $create_response->assertJson([
            "status" => [
                "code" => 422,
                "message" => "",
                "errors" => [
                    [
                        "field" => "date",
                        "message" => "The date field is required.",
                    ],
                ],
            ]
        ]);
    }

    public function test_get_follow_up_history_detail() 
    {
        $followUpHistory = FollowUpHistory::factory()->create();
        $response = $this->getJson("assessments-api/v1/follow-up-histories/{$followUpHistory->id}");
        $response->assertOK();

    }

    public function test_get_notfound_follow_up_history_detail() 
    {
        $response = $this->getJson("assessments-api/v1/follow-up-histories/404");
        $response->assertNotFound();

    }

    public function test_update_follow_up_history() 
    {
        $followUpHistory = FollowUpHistory::factory()->create();
        $time = Carbon::parse($followUpHistory->time);
        $time = $time->addMinute();
        $response = $this->putJson("assessments-api/v1/follow-up-histories/{$followUpHistory->id}", [
            'case_id' => $followUpHistory->case_id,
            'date' => $followUpHistory->date,
            'time' => $time->format('Y-m-d H:i:s'),
            'appointment_id' => $followUpHistory->appointment_id,
            'type' => $followUpHistory->type
        ]);
        $response->assertOK();
    }

    public function test_update_notfound_follow_up_history() 
    {
        $followUpHistory = FollowUpHistory::factory()->create();
        $time = Carbon::parse($followUpHistory->time);
        $time = $time->addMinute();
        $response = $this->putJson("assessments-api/v1/follow-up-histories/404", [
            'case_id' => $followUpHistory->case_id,
            'date' => $followUpHistory->date,
            'time' => $time->format('Y-m-d H:i:s'),
            'appointment_id' => $followUpHistory->appointment_id,
            'type' => $followUpHistory->type
        ]);
        $response->assertNotFound();
    }

    public function test_delete_follow_up_history() 
    {
        $followUpHistory = FollowUpHistory::factory()->create();
        $response = $this->deleteJson("assessments-api/v1/follow-up-histories/{$followUpHistory->id}");
        $response->assertCreated();
    }
}
