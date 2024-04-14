<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;
use App\Models\CarePlan;
use App\Models\BznCareTarget;

class BznCareTargetTest extends TestCase
{
    use WithoutMiddleware, RefreshDatabase, WithFaker;

    public function test_get_bzn_care_targets_success()
    {
        $care_plan = CarePlan::factory()->create();
        $bzn_care_target = BznCareTarget::factory()
            ->for($care_plan)
            ->create();

        $response = $this->getJson("assessments-api/v1/bzn-care-targets"
            . "?care_plan_id={$care_plan->id}", []);

        $response->assertOk();
        $response->assertJson(
            fn (AssertableJson $json) => $json
                ->has('data', 1) //has 1 data
                ->has('meta') //has meta
                ->has('links') //has links
                ->where('data.0.id', $bzn_care_target->id)
                ->where('data.0.intervention', $bzn_care_target->intervention)
                ->where('data.0.target_type', $bzn_care_target->target_type)
                ->where('data.0.plan', $bzn_care_target->plan)
                ->where('meta.current_page', 1)
                ->where('meta.last_page', 1)
                ->where('meta.per_page', 10)
                ->where('meta.total', 1)
        );
    }

    public function test_get_bzn_care_targets_failed_no_care_plan_id()
    {
        $response = $this->getJson("assessments-api/v1/bzn-care-targets"); //no care_plan_id query

        $response->assertUnprocessable();
    }

    public function test_post_bzn_care_target_success()
    {
        $care_plan = CarePlan::factory()->create();

        $data = [
            'intervention' => ["yes", "yes"],
            'target_type' => [1,2],
            'plan' => ["yes", "yes"],
            'ct_area' => ["yes", "yes"],
            'ct_target' => ["yes", "yes"],
            'ct_ssa' => ["yes", "yes"],
            'ct_domain' => [1,2],
            'ct_urgency' => [1,2],
            'ct_category' => [1,2],
            'ct_priority' => [1,2],
            'ct_modifier' => [1,2],
            'ct_knowledge' => [1,2],
            'ct_behaviour' => [1,2],
            'ct_status' => [1,2],
            'omaha_s' => ["yes", "yes"],
            'is_bzn' => true,
        ];

        $response = $this->postJson("assessments-api/v1/bzn-care-targets?care_plan_id=1", $data);

        $response->assertCreated();
        $response->assertJson(
            fn (AssertableJson $json) => $json
                ->has('data') //has data
                ->has('data.0.id')
                ->where('data.0.intervention', $data['intervention'][0])
                ->where('data.0.target_type', $data['target_type'][0])
                ->where('data.0.plan', $data['plan'][0])
        );
    }

    public function test_post_bzn_care_target_failed_incomplete_data()
    {
        $data = [
            //'care_plan_id' => $this->faker->randomNumber(), //incomplete data
            'intervention' => $this->faker->text,
            'target_type' => $this->faker->randomNumber(),
            'plan' => $this->faker->text,
            'is_bzn' => true,
        ];

        $response = $this->postJson("assessments-api/v1/bzn-care-targets", $data);

        $response->assertUnprocessable();
    }

    public function test_post_bzn_care_target_failed_care_plan_id_not_exist()
    {
        $data = [
            'care_plan_id' => $this->faker->randomNumber(), //care_plan_id not created yet / not exist
            'intervention' => $this->faker->text,
            'target_type' => $this->faker->randomNumber(),
            'plan' => $this->faker->text,
            'is_bzn' => true,
        ];

        $response = $this->postJson("assessments-api/v1/bzn-care-targets", $data);

        $response->assertUnprocessable();
    }

    public function test_get_bzn_care_target_details_success()
    {
        $care_plan = CarePlan::factory()->create();
        $bzn_care_target = BznCareTarget::factory()
            ->for($care_plan)
            ->create();

        $response = $this->getJson("assessments-api/v1/bzn-care-targets/{$bzn_care_target->id}", ['is_bzn' => true]);

        $response->assertOk();
        $response->assertJson(
            fn (AssertableJson $json) => $json
                ->where('data.id', $bzn_care_target->id)
                ->where('data.intervention', $bzn_care_target->intervention)
                ->where('data.target_type', $bzn_care_target->target_type)
                ->where('data.plan', $bzn_care_target->plan)
        );
    }

    public function test_get_bzn_care_target_details_failed_not_found()
    {
        $response = $this->getJson("assessments-api/v1/bzn-care-targets/1000",['is_bzn' => true]
        );

        $response->assertNotFound();
    }

    // public function test_put_bzn_care_target_success()
    // {
    //     $care_plan = CarePlan::factory()->create();

    //     $bzn_care_target = BznCareTarget::factory()
    //         ->for($care_plan)
    //         ->create();

    //     $data = [
    //         'intervention' => $this->faker->text,
    //         'target_type' => $this->faker->randomNumber(),
    //         'plan' => $this->faker->text,
    //         'user_id' => $care_plan->manager_id,
    //         'is_bzn' => true,
    //     ];

    //     $response = $this->putJson("assessments-api/v1/bzn-care-targets/{$bzn_care_target->id}", $data);

    //     $response->assertOk();
    //     $response->assertJson(
    //         fn (AssertableJson $json) => $json
    //             ->has('data') //has data
    //             ->where('data.id', $bzn_care_target->id)
    //             ->where('data.intervention', $data['intervention'])
    //             ->where('data.target_type', $data['target_type'])
    //             ->where('data.plan', $data['plan'])
    //     );
    // }

    // public function test_put_bzn_care_target_failed_not_found()
    // {
    //     $data = [
    //         'intervention' => $this->faker->text,
    //         'target_type' => $this->faker->randomNumber(),
    //         'plan' => $this->faker->text,
    //         'is_bzn' => true,
    //     ];

    //     $response = $this->putJson("assessments-api/v1/bzn-care-targets/100", $data);

    //     $response->assertNotFound();
    // }
}
