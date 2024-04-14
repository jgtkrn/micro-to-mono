<?php

namespace Tests\Feature;

use App\Models\CrossDisciplinary;
use App\Models\CarePlan;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Tests\TestCase;

class CrossDisciplinaryTest extends TestCase
{
    use RefreshDatabase, WithoutMiddleware, WithFaker;
    
    public function test_get_cross_disciplinary_list() 
    {
        $crossDisciplinary = CrossDisciplinary::factory()->count(5)->create([
            'case_id' => 1
        ]);
        $response = $this->get('assessments-api/v1/cross-disciplinary?case_id=1');
        $response->assertOk();
    }

    
    public function test_create_cross_disciplinary() 
    {
        $care_plan = CarePlan::factory()->create();
        $create_response = $this->json('POST', 'assessments-api/v1/cross-disciplinary', [
            'case_id' => $care_plan->case_id,
            'role' => $this->faker->word,
            'comments' => $this->faker->word,
            'user_id' => $care_plan->manager_id
        ]);
          $create_response->assertCreated();
    }

    public function test_get_cross_disciplinary_detail() 
    {
        $crossDisciplinary = CrossDisciplinary::factory()->create();
        $response = $this->getJson("assessments-api/v1/cross-disciplinary/{$crossDisciplinary->id}");
        $response->assertOK();

    }

    public function test_get_notfound_cross_disciplinary_detail() 
    {
        $response = $this->getJson("assessments-api/v1/cross-disciplinary/404");
        $response->assertNotFound();

    }

    public function test_update_cross_disciplinary() 
    {
        $care_plan = CarePlan::factory()->create();
        $crossDisciplinary = CrossDisciplinary::factory()->create([
            'case_id' => $care_plan->case_id
        ]);
        $response = $this->putJson("assessments-api/v1/cross-disciplinary/{$crossDisciplinary->id}", [
            'case_id' => $crossDisciplinary->case_id,
            'role' => $crossDisciplinary->role,
            'comments' => $crossDisciplinary->comments,
            'user_id' => $care_plan->manager_id
        ]);
        $response->assertOK();
    }

    public function test_update_notfound_cross_disciplinary() 
    {
        $crossDisciplinary = CrossDisciplinary::factory()->create();
        $response = $this->putJson("assessments-api/v1/cross-disciplinary/404", [
            'case_id' => $crossDisciplinary->case_id,
            'role' => $crossDisciplinary->role,
            'comments' => $crossDisciplinary->comments,
        ]);
        $response->assertNotFound();
    }

    public function test_delete_cross_disciplinary() 
    {
        $crossDisciplinary = CrossDisciplinary::factory()->create();
        $response = $this->deleteJson("assessments-api/v1/cross-disciplinary/{$crossDisciplinary->id}");
        $response->assertCreated();
    }
}
