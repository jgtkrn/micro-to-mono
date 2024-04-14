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
use App\Models\BznCareTarget;
use App\Models\BznConsultationNotes;
use App\Models\BznConsultationSign;
use App\Models\BznConsultationAttachment;

class BznConsultationNotesTest extends TestCase
{
    use WithoutMiddleware, RefreshDatabase, WithFaker;

    // get
    public function test_get_bzn_consultation_notes_success()
    {
        $care_plan = CarePlan::factory()->create();
        $bzn_care_target = BznCareTarget::factory()
            ->for($care_plan)
            ->create();
        $bzn_consultation = BznConsultationNotes::factory()
            ->create([
                'bzn_target_id' => $bzn_care_target->id,
            ]);
        $bzn_sign = BznConsultationSign::factory()
            ->create([
                'bzn_consultation_notes_id' => $bzn_consultation->id,
            ]);
        $bzn_attachment = BznConsultationAttachment::factory()
            ->create([
                'bzn_consultation_notes_id' => $bzn_consultation->id,
            ]);                                                                   

        $response = $this->getJson("assessments-api/v1/bzn-consultation"
            . "?bzn_target_id={$bzn_care_target->id}");

        $response->assertOk();
        $response->assertJson(
            fn (AssertableJson $json) => $json
                ->where('data.0.id', $bzn_consultation->id)
                ->where('data.0.bzn_target_id', $bzn_consultation->bzn_target_id)
                
                // Assessor Information
                ->where('data.0.meeting', $bzn_consultation->meeting)
                ->where('data.0.visit_type', $bzn_consultation->visit_type)
                ->where('data.0.assessment_date', $bzn_consultation->assessment_date)
                ->where('data.0.assessment_time', $bzn_consultation->assessment_time)

                // Vital Sign
                ->where('data.0.sbp', $bzn_consultation->sbp)
                ->where('data.0.dbp', $bzn_consultation->dbp)
                ->where('data.0.pulse', $bzn_consultation->pulse)
                ->where('data.0.pao', $bzn_consultation->pao)
                ->where('data.0.hstix', $bzn_consultation->hstix)
                ->where('data.0.body_weight', $bzn_consultation->body_weight)
                ->where('data.0.waist', $bzn_consultation->waist)
                ->where('data.0.circumference', $bzn_consultation->circumference)

                // Intervention Target 1
                ->where('data.0.domain', $bzn_consultation->domain)
                ->where('data.0.urgency', $bzn_consultation->urgency)
                ->where('data.0.category', $bzn_consultation->category)
                ->where('data.0.intervention_remark', $bzn_consultation->intervention_remark)
                ->where('data.0.consultation_remark', $bzn_consultation->consultation_remark)
                ->where('data.0.area', $bzn_consultation->area)
                ->where('data.0.priority', $bzn_consultation->priority)
                ->where('data.0.target', $bzn_consultation->target)
                ->where('data.0.modifier', $bzn_consultation->modifier)
                ->where('data.0.ssa', $bzn_consultation->ssa)
                ->where('data.0.knowledge', $bzn_consultation->knowledge)
                ->where('data.0.behaviour', $bzn_consultation->behaviour)
                ->where('data.0.status', $bzn_consultation->status)

                // Case Status
                ->where('data.0.case_status', $bzn_consultation->case_status)
                ->where('data.0.case_remark', $bzn_consultation->case_remark)

                // Attachment
                ->where('data.0.bzn_consultation_attachment.0.file_name', $bzn_attachment->file_name)
                ->where('data.0.bzn_consultation_attachment.0.url', $bzn_attachment->url)

                // Signature
                ->where('data.0.bzn_consultation_sign.file_name', $bzn_sign->file_name)
                ->where('data.0.bzn_consultation_sign.url', $bzn_sign->url)

        );
    }

    public function test_get_bzn_consultation_notes_failed_no_care_plan_id()
    {
        $response = $this->getJson("assessments-api/v1/bzn-consultation"); //no care_plan_id query

        $response->assertUnprocessable();
    }

    // post
    // public function test_post_bzn_consultation_notes_success()
    // {
    //     Storage::fake('local');
    //     $file = UploadedFile::fake()->image('image.jpg');
    //     $attachment = UploadedFile::fake()->image('att.jpg');
    //     $consultation_time = new Carbon($this->faker->dateTime);
    //     $care_plan = CarePlan::factory()->create();
    //     $bzn_care_target = BznCareTarget::factory()->create([
    //         'care_plan_id' => $care_plan->id
    //     ]);

    //     $data = [
    //         'bzn_target_id' => $bzn_care_target->id,
    //         // Assessor Information
    //         'assessor' => $this->faker->word,
    //         'meeting' => $this->faker->word,
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

    //         // Intervention Target 1
    //         'domain' => $this->faker->randomNumber(1, 2),
    //         'urgency' => $this->faker->randomNumber(1, 2),
    //         'category' => $this->faker->randomNumber(1, 2),
    //         'intervention_remark' => $this->faker->word,
    //         'consultation_remark' => $this->faker->word,
    //         'area' => $this->faker->word,
    //         'priority' => $this->faker->randomNumber(1, 2),
    //         'target' => $this->faker->word,
    //         'modifier' => $this->faker->randomNumber(1, 2),
    //         'ssa' => $this->faker->word,
    //         'knowledge' => $this->faker->randomNumber(1, 2),
    //         'behaviour' => $this->faker->randomNumber(1, 2),
    //         'status' => $this->faker->randomNumber(1, 2),

    //         // Case Status
    //         'case_status' => $this->faker->randomNumber(1, 2),
    //         'case_remark' => $this->faker->word,

    //         // Attachment
    //         'attachment_file[]' => $attachment,

    //         // Signature
    //         'signature_file' => $file,
    //     ];

    //     $response = $this->postJson("assessments-api/v1/bzn-consultation", $data);

    //     $response->assertConflict();
    //     $response->assertJson(
    //         fn (AssertableJson $json) => $json
    //             ->has('data')
    //             ->has('data.id')
    //             ->where('data.bzn_target_id', $data['bzn_target_id'])
                
    //             // Assessor Information
    //             ->where('data.meeting', $data['meeting'])
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

    //             // Intervention Target 1
    //             ->where('data.domain', $data['domain'])
    //             ->where('data.urgency', $data['urgency'])
    //             ->where('data.category', $data['category'])
    //             ->where('data.intervention_remark', $data['intervention_remark'])
    //             ->where('data.consultation_remark', $data['consultation_remark'])
    //             ->where('data.area', $data['area'])
    //             ->where('data.priority', $data['priority'])
    //             ->where('data.target', $data['target'])
    //             ->where('data.modifier', $data['modifier'])
    //             ->where('data.ssa', $data['ssa'])
    //             ->where('data.knowledge', $data['knowledge'])
    //             ->where('data.behaviour', $data['behaviour'])
    //             ->where('data.status', $data['status'])

    //             // Case Status
    //             ->where('data.case_status', $data['case_status'])
    //             ->where('data.case_remark', $data['case_remark'])

    //             // Attachment
    //             ->has('data.bzn_consultation_attachment')

    //             // Signature
    //             ->has('data.bzn_consultation_sign.file_name')
    //             ->has('data.bzn_consultation_sign.url')
    //             ->has('status')
    //     );
    // }

    public function test_post_bzn_consultation_notes_failed_incomplete_data()
    {
        $data = [
            'modifier' => $this->faker->randomNumber(1, 2),
            'ssa' => $this->faker->word,
            'knowledge' => $this->faker->randomNumber(1, 2),
            'behaviour' => $this->faker->randomNumber(1, 2),
            'status' => $this->faker->randomNumber(1, 2),
            'is_bzn' => true,
        ];

        $response = $this->postJson("assessments-api/v1/bzn-consultation", $data);

        $response->assertUnprocessable();
    }

    public function test_post_bzn_consultation_notes_failed_bzn_target_id_not_exist()
    {
        $consultation_time = new Carbon($this->faker->dateTime);
        $data = [
            'is_bzn' => true,
            'bzn_target_id' => $this->faker->randomNumber(1, 9),
            // Assessor Information
            'assessor' => $this->faker->word,
            'meeting' => $this->faker->word,
            'visit_type' => $this->faker->word,
            'assessment_date' => Carbon::parse($consultation_time)->format('Y-m-d'),
            'assessment_time' => Carbon::parse($consultation_time)->format('H:I:S'),

            // Vital Sign
            'sbp' => $this->faker->randomNumber(1, 2),
            'dbp' => $this->faker->randomNumber(1, 2),
            'pulse' => $this->faker->randomNumber(1, 2),
            'pao' => $this->faker->randomNumber(1, 2),
            'hstix' => $this->faker->randomFloat(0,10,3),
            'body_weight' => $this->faker->randomNumber(1, 2),
            'waist' => $this->faker->randomNumber(1, 2),
            'circumference' => $this->faker->randomNumber(1, 2),

            // Intervention Target 1
            'domain' => $this->faker->randomNumber(1, 2),
            'urgency' => $this->faker->randomNumber(1, 2),
            'category' => $this->faker->randomNumber(1, 2),
            'intervention_remark' => $this->faker->word,
            'consultation_remark' => $this->faker->word,
            'area' => $this->faker->word,
            'priority' => $this->faker->randomNumber(1, 2),
            'target' => $this->faker->word,
            'modifier' => $this->faker->randomNumber(1, 2),
            'ssa' => $this->faker->word,
            'knowledge' => $this->faker->randomNumber(1, 2),
            'behaviour' => $this->faker->randomNumber(1, 2),
            'status' => $this->faker->randomNumber(1, 2),

            // Case Status
            'case_status' => $this->faker->randomNumber(1, 2),
            'case_remark' => $this->faker->word,
        ];

        $response = $this->postJson("assessments-api/v1/bzn-consultation", $data);

        $response->assertUnprocessable();
    }

    // update
    public function test_put_bzn_consultation_notes_success()
    {
        $care_plan = CarePlan::factory()->create();

        $bzn_care_target = BznCareTarget::factory()
            ->for($care_plan)
            ->create();
        $bzn_consultation = BznConsultationNotes::factory()
            ->create([
                'bzn_target_id' => $bzn_care_target->id,
        ]);
        $consultation_time = new Carbon($this->faker->dateTime);
        $data = [
            'is_bzn' => true,
            'bzn_target_id' => $bzn_care_target->id,
            // Assessor Information
            'assessor' => $this->faker->word,
            'meeting' => $this->faker->word,
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

            // Intervention Target 1
            'domain' => $this->faker->randomNumber(1, 2),
            'urgency' => $this->faker->randomNumber(1, 2),
            'category' => $this->faker->randomNumber(1, 2),
            'intervention_remark' => $this->faker->word,
            'consultation_remark' => $this->faker->word,
            'area' => $this->faker->word,
            'priority' => $this->faker->randomNumber(1, 2),
            'target' => $this->faker->word,
            'modifier' => $this->faker->randomNumber(1, 2),
            'ssa' => $this->faker->word,
            'knowledge' => $this->faker->randomNumber(1, 2),
            'behaviour' => $this->faker->randomNumber(1, 2),
            'status' => $this->faker->randomNumber(1, 2),

            // Case Status
            'case_status' => $this->faker->randomNumber(1, 2),
            'case_remark' => $this->faker->word,
            'user_id' => $care_plan->manager_id
        ];

        $response = $this->putJson("assessments-api/v1/bzn-consultation/{$bzn_consultation->id}", $data);

        $response->assertOk();
        $response->assertJson(
            fn (AssertableJson $json) => $json
                ->has('data') //has data
                ->where('data.id', $bzn_consultation->id)
                ->where('data.bzn_target_id', $data['bzn_target_id'])
                
                // Assessor Information
                ->where('data.meeting', $data['meeting'])
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

                // Intervention Target 1
                ->where('data.domain', $data['domain'])
                ->where('data.urgency', $data['urgency'])
                ->where('data.category', $data['category'])
                ->where('data.intervention_remark', $data['intervention_remark'])
                ->where('data.consultation_remark', $data['consultation_remark'])
                ->where('data.area', $data['area'])
                ->where('data.priority', $data['priority'])
                ->where('data.target', $data['target'])
                ->where('data.modifier', $data['modifier'])
                ->where('data.ssa', $data['ssa'])
                ->where('data.knowledge', $data['knowledge'])
                ->where('data.behaviour', $data['behaviour'])
                ->where('data.status', $data['status'])

                // Case Status
                ->where('data.case_status', $data['case_status'])
                ->where('data.case_remark', $data['case_remark'])
        );
    }

}
