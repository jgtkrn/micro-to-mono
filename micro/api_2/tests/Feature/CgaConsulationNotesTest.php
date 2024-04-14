<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use App\Models\CarePlan;
use App\Models\CgaCareTarget;
use App\Models\CgaConsultationNotes;
use App\Models\CgaConsultationSign;
use App\Models\CgaConsultationAttachment;

class CgaConsulationNotesTest extends TestCase
{
    use WithoutMiddleware, RefreshDatabase, WithFaker;

     // get
    public function test_get_cga_consultation_notes_success()
    {
        $care_plan = CarePlan::factory()->create();
        $cga_care_target = CgaCareTarget::factory()
            ->for($care_plan)
            ->create();
        $cga_consultation = CgaConsultationNotes::factory()
            ->create([
                'cga_target_id' => $cga_care_target->id,
            ]);
        $cga_sign = CgaConsultationSign::factory()
            ->create([
                'cga_consultation_notes_id' => $cga_consultation->id,
            ]);
        $cga_attachment = CgaConsultationAttachment::factory()
            ->create([
                'cga_consultation_notes_id' => $cga_consultation->id,
            ]);                                                                   

        $response = $this->getJson("assessments-api/v1/cga-consultation"
            . "?cga_target_id={$cga_care_target->id}");

        $response->assertOk();
        $response->assertJson(
            fn (AssertableJson $json) => $json
                ->where('data.0.id', $cga_consultation->id)
                ->where('data.0.cga_target_id', $cga_consultation->cga_target_id)
                
                // Assessor Information
                ->where('data.0.visit_type', $cga_consultation->visit_type)
                ->where('data.0.assessment_date', $cga_consultation->assessment_date)
                ->where('data.0.assessment_time', $cga_consultation->assessment_time)

                // Vital Sign
                ->where('data.0.sbp', $cga_consultation->sbp)
                ->where('data.0.dbp', $cga_consultation->dbp)
                ->where('data.0.pulse', $cga_consultation->pulse)
                ->where('data.0.pao', $cga_consultation->pao)
                ->where('data.0.hstix', $cga_consultation->hstix)
                ->where('data.0.body_weight', $cga_consultation->body_weight)
                ->where('data.0.waist', $cga_consultation->waist)
                ->where('data.0.circumference', $cga_consultation->circumference)

                // Log
                ->where('data.0.purpose', $cga_consultation->purpose)
                ->where('data.0.content', $cga_consultation->content)
                ->where('data.0.progress', $cga_consultation->progress)
                ->where('data.0.case_summary', $cga_consultation->case_summary)
                ->where('data.0.followup_options', $cga_consultation->followup_options)
                ->where('data.0.followup', $cga_consultation->followup)
                ->where('data.0.personal_insight', $cga_consultation->personal_insight)

                // Case Status
                ->where('data.0.case_status', $cga_consultation->case_status)
                ->where('data.0.case_remark', $cga_consultation->case_remark)

                // Attachment
                ->where('data.0.cga_consultation_attachment.0.file_name', $cga_attachment->file_name)
                ->where('data.0.cga_consultation_attachment.0.url', $cga_attachment->url)

                // Signature
                ->where('data.0.cga_consultation_sign.file_name', $cga_sign->file_name)
                ->where('data.0.cga_consultation_sign.url', $cga_sign->url)
        );
    }

    public function test_get_cga_consultation_notes_failed_no_care_plan_id()
    {
        $response = $this->getJson("assessments-api/v1/cga-consultation"); //no care_plan_id query

        $response->assertUnprocessable();
    }

    // post
    // public function test_post_cga_consultation_notes_success()
    // {
    //     Storage::fake('local');
    //     $file = UploadedFile::fake()->image('image.jpg');
    //     $attachment = UploadedFile::fake()->image('att.jpg');
    //     $consultation_time = new Carbon($this->faker->dateTime);
    //     $care_plan = CarePlan::factory()->create();
    //     $cga_care_target = CgaCareTarget::factory()->create([
    //         'care_plan_id' => $care_plan->id
    //     ]);

    //     $data = [
    //         'cga_target_id' => $cga_care_target->id,
    //         // Assessor Information
    //         'assessor_1' => $this->faker->word,
    //         'assessor_2' => $this->faker->word,
    //         'visit_type' => $this->faker->word,
    //         'assessment_date' => Carbon::parse($consultation_time)->format('Y-m-d'),
    //         'assessment_time' => Carbon::parse($consultation_time)->format('H:I:S'),

    //         // Vital Sign
    //         'sbp' => $this->faker->randomNumber(1, 2),
    //         'dbp' => $this->faker->randomNumber(1, 2),
    //         'pulse' => $this->faker->randomNumber(1, 2),
    //         'pao' => $this->faker->randomNumber(1, 2),
    //         'hstix' => $this->faker->randomNumber(1, 2),
    //         'body_weight' => $this->faker->randomNumber(1, 2),
    //         'waist' => $this->faker->randomNumber(1, 2),
    //         'circumference' => $this->faker->randomNumber(1, 2),

    //         // Log
    //         'purpose' => $this->faker->randomNumber(1, 2),
    //         'content' => $this->faker->word,
    //         'progress' => $this->faker->word,
    //         'case_summary' => $this->faker->word,
    //         'followup_options' => $this->faker->randomNumber(1, 2),
    //         'followup' => $this->faker->word,
    //         'personal_insight' => $this->faker->word,

    //         // Case Status
    //         'case_status' => $this->faker->randomNumber(1, 2),
    //         'case_remark' => $this->faker->word,

    //         // Attachment
    //         'attachment_file[]' => $attachment,

    //         // Signature
    //         'signature_file' => $file,
    //     ];

    //     $response = $this->postJson("assessments-api/v1/cga-consultation", $data);

    //     $response->assertConflict();
    //     $response->assertJson(
    //         fn (AssertableJson $json) => $json
    //             ->has('data')
    //             ->has('data.id')
    //             ->where('data.cga_target_id', $data['cga_target_id'])
                
    //             // Assessor Information
    //             ->where('data.visit_type', $data['visit_type'])
    //             ->where('data.assessment_date', $data['assessment_date'])
    //             ->where('data.assessment_time', $data['assessment_time'])

    //             // Vital Sign
    //             ->where('data.sbp', $data['sbp'])
    //             ->where('data.dbp', $data['dbp'])
    //             ->where('data.pulse', $data['pulse'])
    //             ->where('data.pao', $data['pao'])
    //             ->where('data.hstix', $data['hstix'])
    //             ->where('data.body_weight', $data['body_weight'])
    //             ->where('data.waist', $data['waist'])
    //             ->where('data.circumference', $data['circumference'])

    //             // Log
    //             ->where('data.purpose', $data['purpose'])
    //             ->where('data.content', $data['content'])
    //             ->where('data.progress', $data['progress'])
    //             ->where('data.case_summary', $data['case_summary'])
    //             ->where('data.followup_options', $data['followup_options'])
    //             ->where('data.followup', $data['followup'])
    //             ->where('data.personal_insight', $data['personal_insight'])

    //             // Case Status
    //             ->where('data.case_status', $data['case_status'])
    //             ->where('data.case_remark', $data['case_remark'])

    //             // Attachment
    //             ->has('data.cga_consultation_attachment')

    //             // Signature
    //             ->has('data.cga_consultation_sign.file_name')
    //             ->has('data.cga_consultation_sign.url')
    //             ->has('status')
    //     );
    // }

    // update
    public function test_put_cga_consultation_notes_success()
    {
        $care_plan = CarePlan::factory()->create();

        $cga_care_target = CgaCareTarget::factory()
            ->for($care_plan)
            ->create();
        $cga_consultation = CgaConsultationNotes::factory()
            ->create([
                'cga_target_id' => $cga_care_target->id,
        ]);
        $consultation_time = new Carbon($this->faker->dateTime);
        $data = [
            'is_cga' => true,
            'cga_target_id' => $cga_care_target->id,
            // Assessor Information
            'assessor_1' => $this->faker->word,
            'assessor_2' => $this->faker->word,
            'visit_type' => $this->faker->word,
            'assessment_date' => Carbon::parse($consultation_time)->format('Y-m-d'),
            'assessment_time' => Carbon::parse($consultation_time)->format('H:I:S'),

            // Vital Sign
            'sbp' => $this->faker->randomNumber(1, 2),
            'dbp' => $this->faker->randomNumber(1, 2),
            'pulse' => $this->faker->randomNumber(1, 2),
            'pao' => $this->faker->randomNumber(1, 2),
            'hstix' => null,
            'body_weight' => $this->faker->randomNumber(1, 2),
            'waist' => $this->faker->randomNumber(1, 2),
            'circumference' => $this->faker->randomNumber(1, 2),

            // Log
            'purpose' => $this->faker->word,
            'content' => $this->faker->word,
            'progress' => $this->faker->word,
            'case_summary' => $this->faker->word,
            'followup_options' => $this->faker->randomNumber(1, 2),
            'followup' => $this->faker->word,
            'personal_insight' => $this->faker->word,

            // Case Status
            'case_status' => $this->faker->randomNumber(1, 2),
            'case_remark' => $this->faker->word,
            'user_id' => $care_plan->manager_id
        ];

        $response = $this->putJson("assessments-api/v1/cga-consultation/{$cga_consultation->id}", $data);

        $response->assertOk();
        $response->assertJson(
            fn (AssertableJson $json) => $json
                ->has('data')
                ->has('data.id')
                ->where('data.cga_target_id', $data['cga_target_id'])
                
                // Assessor Information
                ->where('data.visit_type', $data['visit_type'])
                ->where('data.assessment_date', $data['assessment_date'])
                ->where('data.assessment_time', $data['assessment_time'])

                // Vital Sign
                ->where('data.sbp', $data['sbp'])
                ->where('data.dbp', $data['dbp'])
                ->where('data.pulse', $data['pulse'])
                ->where('data.pao', $data['pao'])
                ->where('data.hstix', $data['hstix'])
                ->where('data.body_weight', $data['body_weight'])
                ->where('data.waist', $data['waist'])
                ->where('data.circumference', $data['circumference'])

                // Log
                ->where('data.purpose', $data['purpose'])
                ->where('data.content', $data['content'])
                ->where('data.progress', $data['progress'])
                ->where('data.case_summary', $data['case_summary'])
                ->where('data.followup_options', $data['followup_options'])
                ->where('data.followup', $data['followup'])
                ->where('data.personal_insight', $data['personal_insight'])

                // Case Status
                ->where('data.case_status', $data['case_status'])
                ->where('data.case_remark', $data['case_remark'])
        );
    }
}
