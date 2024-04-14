<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;
use App\Models\CarePlan;

class CarePlanTest extends TestCase
{
    use WithoutMiddleware, RefreshDatabase, WithFaker;

    public function test_get_care_plan_by_case_id_success()
    {
        $care_plan = CarePlan::factory()->create();

        $response = $this->getJson("assessments-api/v1/care-plans?case_id={$care_plan->case_id}");

        $response->assertOk();
        $response->assertJson(
            fn (AssertableJson $json) => $json
                ->has('data') //has data
                ->where('data.id', $care_plan->id)
                ->where('data.case_id', $care_plan->case_id)
                ->where('data.case_type', $care_plan->case_type)
                ->where('data.case_manager', $care_plan->case_manager)
                ->where('data.handler', $care_plan->handler)
                ->where('data.manager_id', $care_plan->manager_id)
                ->where('data.handler_id', $care_plan->handler_id)
        );
    }

    public function test_get_care_plan_by_case_id_success_empty()
    {
        $response = $this->getJson('assessments-api/v1/care-plans?case_id=1');

        $response->assertOk();
        $response->assertJsonPath('data', null);
    }

    // public function test_get_care_plan_by_case_id_invalid_incomplete_data()
    // {
    //     $response = $this->getJson('assessments-api/v1/care-plans'); // no case id

    //     $response->assertUnprocessable();
    // }

    public function test_get_care_plan_details_success()
    {
        $care_plan = CarePlan::factory()->create();

        $response = $this->getJson("assessments-api/v1/care-plans/{$care_plan->id}");

        $response->assertOk();
        $response->assertJson(
            fn (AssertableJson $json) => $json
                ->has('data') //has data
                ->where('data.id', $care_plan->id)
                ->where('data.case_id', $care_plan->case_id)
                ->where('data.case_type', $care_plan->case_type)
                ->where('data.case_manager', $care_plan->case_manager)
                ->where('data.handler', $care_plan->handler)
                ->where('data.manager_id', $care_plan->manager_id)
                ->where('data.handler_id', $care_plan->handler_id)
        );
    }

    public function test_get_care_plan_details_failed_not_found()
    {
        $response = $this->getJson("assessments-api/v1/care-plans/100");

        $response->assertNotFound();
    }

    // public function test_post_care_plan_success()
    // {
    //     $data = [
    //         'case_id' => $this->faker->randomNumber,
    //         'case_type' => $this->faker->randomElement(['BZN', 'CGA']),
    //         'manager_id' => $this->faker->randomNumber,
    //         'handler_id' => $this->faker->randomNumber,
    //     ];

    //     $response = $this->postJson("assessments-api/v1/care-plans", $data);

    //     $response->assertCreated();
    //     $response->assertJson(
    //         fn (AssertableJson $json) => $json
    //             ->has('data') //has data
    //             ->has('data.id')
    //             ->where('data.case_id', $data['case_id'])
    //             ->where('data.case_type', $data['case_type'])
    //             ->where('data.manager_id', $data['manager_id'])
    //             ->where('data.handler_id', $data['handler_id'])
    //     );
    // }

    public function test_post_care_plan_failed_incomplete_data()
    {
        $data = [
            //'case_id' => $this->faker->randomNumber, //incomplete data
            'case_type' => $this->faker->randomElement(['BZN', 'CGA']),
            'case_manager' => $this->faker->name,
            'handler' => $this->faker->name
        ];

        $response = $this->postJson("assessments-api/v1/care-plans", $data);

        $response->assertUnprocessable();
    }

    public function test_post_care_plan_failed_case_id_already_exist()
    {
        $care_plan = CarePlan::factory()->create();

        $data = [
            'case_id' => $care_plan->case_id,
            'case_type' => $this->faker->randomElement(['BZN', 'CGA']),
            'case_manager' => $this->faker->name,
            'handler' => $this->faker->name
        ];

        $response = $this->postJson("assessments-api/v1/care-plans", $data);
        $response->assertUnprocessable();
        $response->assertJsonPath('status.message', 'Case Id already exist');
    }

    // public function test_put_care_plan_success()
    // {
    //     $care_plan = CarePlan::factory()->create();

    //     $data = [
    //         'case_id' => $care_plan->case_id,
    //         'case_type' => $care_plan->case_type,
    //         'case_manager' => $this->faker->name, //new data
    //         'handler' => $this->faker->name, //new data
    //         'manager_id' => $this->faker->randomNumber,
    //         'handler_id' => $this->faker->randomNumber,
    //     ];

    //     $response = $this->putJson("assessments-api/v1/care-plans/{$care_plan->id}", $data);

    //     $response->assertOk();
    //     $response->assertJson(
    //         fn (AssertableJson $json) => $json
    //             ->has('data') //has data
    //             ->has('data.id')
    //             ->where('data.case_id', $care_plan->case_id)
    //             ->where('data.case_type', $care_plan->case_type)
    //             ->where('data.case_manager', $data['case_manager'])
    //             ->where('data.handler', $data['handler'])
    //             ->where('data.manager_id', $data['manager_id'])
    //             ->where('data.handler_id', $data['handler_id'])
    //     );
    // }

    public function test_put_care_plan_failed_incomplete_data()
    {
        $care_plan = CarePlan::factory()->create();

        $data = [
            //'case_id' => $care_plan->case_id, //incomplete data
            'case_type' => $care_plan->case_type,
            'case_manager' => $this->faker->name, //new data
            'handler' => $this->faker->name //new data
        ];

        $response = $this->putJson("assessments-api/v1/care-plans/{$care_plan->id}", $data);

        $response->assertUnprocessable();
    }

    public function test_delete_care_plan_success()
    {
        $care_plan = CarePlan::factory()->create();
        $this->assertNull($care_plan->deleted_at);

        $response = $this->deleteJson("assessments-api/v1/care-plans/{$care_plan->id}");

        $response->assertNoContent();
        $care_plan->refresh();
        $this->assertNotNull($care_plan->deleted_at);
    }

    public function test_delete_care_plan_failed_not_found()
    {
        $response = $this->deleteJson("assessments-api/v1/care-plans/100");

        $response->assertNotFound();
    }
}
