<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;
use App\Models\CarePlan;
use App\Models\CgaCareTarget;

class CgaCareTargetTest extends TestCase
{
    use WithoutMiddleware, RefreshDatabase, WithFaker;

    public function test_get_cga_care_targets_success()
    {
        $care_plan = CarePlan::factory()->create();
        $cga_care_target = cgaCareTarget::factory()
            ->for($care_plan)
            ->create();

        $response = $this->getJson("assessments-api/v1/cga-care-targets"
            . "?care_plan_id={$care_plan->id}");

        $response->assertOk();
    }

    public function test_get_cga_care_targets_failed_no_care_plan_id()
    {
        $response = $this->getJson("assessments-api/v1/cga-care-targets"); //no care_plan_id query

        $response->assertUnprocessable();
    }
}
