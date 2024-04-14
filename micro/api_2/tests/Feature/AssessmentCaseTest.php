<?php

namespace Tests\Feature;

use App\Models\AssessmentCase;
use App\Models\AssessmentCaseAttachment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Testing\Fluent\AssertableJson;
use Carbon\Carbon;
use Tests\TestCase;

class AssessmentCaseTest extends TestCase
{
    use WithoutMiddleware, RefreshDatabase, WithFaker;

    public function test_get_assessment_cases_success()
    {
        $assessment_case = AssessmentCase::factory()->create();

        $response = $this->getJson("assessments-api/v1/assessment-cases?case_id={$assessment_case->case_id}");

        $response->assertOk();
        $response->assertJson(
            fn (AssertableJson $json) => $json
                ->has('data') //has data
                ->where('data.id', $assessment_case->id)
                ->where('data.case_id', $assessment_case->case_id)
                ->where('data.case_type', $assessment_case->case_type)
                ->where('data.first_assessor', $assessment_case->first_assessor)
                ->where('data.second_assessor', $assessment_case->second_assessor)
                ->where('data.assessment_date', $assessment_case->assessment_date)
                ->where('data.start_time', Carbon::parse($assessment_case->start_time)->format('Y-m-d H:i:s'))
                ->where('data.end_time', Carbon::parse($assessment_case->end_time)->format('Y-m-d H:i:s'))
                ->has('data.forms_submitted.0.name')
                ->has('data.forms_submitted.0.submit')
        );
    }

    public function test_get_assessment_cases_success_empty()
    {
        $response = $this->getJson('assessments-api/v1/assessment-cases?case_id=1');

        $response->assertOk();
        $response->assertJsonPath('data', null);
    }

    public function test_get_assessment_cases_invalid_incomplete_data()
    {
        $response = $this->getJson('assessments-api/v1/assessment-cases'); //no case id query param

        $response->assertUnprocessable();
    }

    public function test_get_assessment_case_detail_success()
    {
        $assessment_case = AssessmentCase::factory()->create();

        $response = $this->getJson("assessments-api/v1/assessment-cases/{$assessment_case->id}");

        $response->assertOk();
        $response->assertJson(
            fn (AssertableJson $json) => $json
                ->has('data') //has data
                ->where('data.id', $assessment_case->id)
                ->where('data.case_id', $assessment_case->case_id)
                ->where('data.case_type', $assessment_case->case_type)
                ->where('data.first_assessor', $assessment_case->first_assessor)
                ->where('data.second_assessor', $assessment_case->second_assessor)
                ->where('data.assessment_date', $assessment_case->assessment_date)
                ->where('data.start_time', Carbon::parse($assessment_case->start_time)->format('Y-m-d H:i:s'))
                ->where('data.end_time', Carbon::parse($assessment_case->end_time)->format('Y-m-d H:i:s'))
                ->has('data.forms_submitted.0.name')
                ->has('data.forms_submitted.0.submit')
        );
    }

    public function test_get_assessment_case_detail_with_attachment()
    {
        //check form status for attachment: true if have attachment file
        $assessment_case = AssessmentCase::factory()->create();
        AssessmentCaseAttachment::factory()
            ->for($assessment_case)
            ->create();

        $response = $this->getJson("assessments-api/v1/assessment-cases/{$assessment_case->id}");

        $forms_submitted = $response->decodeResponseJson()['data']['forms_submitted'];
        $attachment_status_form = collect($forms_submitted)->where('name', '=', 'attachment')->first();
        $this->assertTrue($attachment_status_form['submit']);
    }

    public function test_get_assessment_case_detail_without_attachment()
    {
        //check form status for attachment: false if doesn't have attachment file
        $assessment_case = AssessmentCase::factory()->create();

        $response = $this->getJson("assessments-api/v1/assessment-cases/{$assessment_case->id}");

        $forms_submitted = $response->decodeResponseJson()['data']['forms_submitted'];
        $attachment_status_form = collect($forms_submitted)->where('name', '=', 'attachment')->first();
        $this->assertFalse($attachment_status_form['submit']);
    }

    public function test_get_assessment_case_detail_failed_not_found()
    {
        $response = $this->getJson("assessments-api/v1/assessment-cases/100");

        $response->assertNotFound();
    }

    public function test_post_assessment_case_failed_incomplete()
    {
        $start_time = new Carbon($this->faker->dateTime);
        $end_time = clone $start_time;
        $assessment_date = clone $start_time;

        $data = [
            //'case_id' => $this->faker->randomNumber, //incomplete data
            'first_assessor' => $this->faker->name,
            'second_assessor' => $this->faker->name,
            'assessment_date' => Carbon::parse($assessment_date)->format('Y-m-d'),
            'start_time' => $start_time,
            'end_time' => $end_time->addHours(1)
        ];

        $response = $this->postJson("assessments-api/v1/assessment-cases", $data);

        $response->assertUnprocessable();
    }

    public function test_post_assessment_case_failed_case_id_already_exist()
    {
        $assessment_case = AssessmentCase::factory()->create();

        $start_time = new Carbon($this->faker->dateTime);
        $end_time = clone $start_time;
        $assessment_date = clone $start_time;

        $data = [
            'case_id' => $assessment_case->case_id, //already exist
            'first_assessor' => $this->faker->name,
            'second_assessor' => $this->faker->name,
            'assessment_date' => Carbon::parse($assessment_date)->format('Y-m-d'),
            'start_time' => $start_time,
            'end_time' => $end_time->addHours(1)
        ];

        $response = $this->postJson("assessments-api/v1/assessment-cases", $data);

        $response->assertUnprocessable();
        $response->assertJsonPath('status.message', 'Case Id already exist');
    }

    public function test_put_assessment_case_success()
    {
        $assessment_case = AssessmentCase::factory()->create();

        $data = [
            'user_role' => 'manager',
            'case_id' => $assessment_case->case_id,
            'first_assessor' => $this->faker->name, //new data
            'second_assessor' => $this->faker->name, //new data
            'assessment_date' => $assessment_case->assessment_date,
            'start_time' => $assessment_case->start_time,
            'end_time' => $assessment_case->end_time,
            'user_role' => 'manager'
        ];

        $response = $this->putJson("assessments-api/v1/assessment-cases/{$assessment_case->id}", $data);

        $response->assertOk();
        $response->assertJson(
            fn (AssertableJson $json) => $json
                ->has('data')
                ->where('data.id', $assessment_case->id)
                ->where('data.case_id', $assessment_case->case_id)
                ->where('data.first_assessor', $data['first_assessor']) //new data
                ->where('data.second_assessor', $data['second_assessor']) //new data
                ->where('data.assessment_date', $assessment_case->assessment_date)
                ->where('data.start_time', $data['start_time']->toISOString())
                ->where('data.end_time', $data['end_time']->toISOString())
        );
    }

    public function test_put_assessment_case_failed_incomplete()
    {
        $assessment_case = AssessmentCase::factory()->create();

        $data = [
            //'case_id' => $assessment_case->case_id, //incomplete data
            'user_role' => 'manager',
            'first_assessor' => $this->faker->name, //new data
            'second_assessor' => $this->faker->name, //new data
            'assessment_date' => $assessment_case->assessment_date,
            'start_time' => $assessment_case->start_time,
            'end_time' => $assessment_case->end_time
        ];

        $response = $this->putJson("assessments-api/v1/assessment-cases/{$assessment_case->id}", $data);

        $response->assertUnprocessable();
    }

    public function test_delete_assessment_case_success()
    {
        $assessment_case = AssessmentCase::factory()->create();
        $this->assertNull($assessment_case->deleted_at);

        $response = $this->deleteJson("assessments-api/v1/assessment-cases/{$assessment_case->id}");

        $response->assertNoContent();
        $assessment_case->refresh();
        $this->assertNotNull($assessment_case->deleted_at);
    }

    public function test_delete_assessment_case_failed_not_found()
    {
        $response = $this->deleteJson("assessments-api/v1/assessment-cases/100");

        $response->assertNotFound();
    }
}
