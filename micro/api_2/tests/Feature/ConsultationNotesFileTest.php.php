<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Testing\Fluent\AssertableJson;
use App\Models\CarePlan;
use App\Models\BznCareTarget;
use App\Models\BznConsultationNotes;
use App\Models\CgaCareTarget;
use App\Models\CgaConsultationNotes;

class AssessmentCaseFileTest extends TestCase
{
    use WithoutMiddleware, RefreshDatabase, WithFaker;

    public function test_get_download_file_bzn_signature_success()
    {
        $care_plan = CarePlan::factory()->create();
        $bzn_target = BznCareTarget::factory()->create([
            'care_plan_id' => $care_plan->id,
        ]);
        BznConsultationNotes::factory()
            ->for($bzn_target)
            ->create();

        $response = $this->getJson("assessments-api/v1/consultation-notes-files/"
            . "{$bzn_target->id}?form_name=bzn_signature");

        $response->assertStatus(200);
    }

    public function test_get_download_file_cga_signature_success()
    {
        $care_plan = CarePlan::factory()->create();
        $cga_target = CgaCareTarget::factory()->create([
            'care_plan_id' => $care_plan->id,
        ]);
        CgaConsultationNotes::factory()
            ->for($cga_target)
            ->create();

        $response = $this->getJson("assessments-api/v1/consultation-notes-files/"
            . "{$cga_target->id}?form_name=cga_signature");

        $response->assertStatus(200);
    }

    public function test_get_download_file_bzn_attachment_success()
    {
        $care_plan = CarePlan::factory()->create();
        $bzn_target = BznCareTarget::factory()->create([
            'care_plan_id' => $care_plan->id,
        ]);
        $bzn_consultation = BznConsultationNotes::factory()
            ->for($bzn_target)
            ->create();
        $bzn_attachment = BznConsultationAttachment::factory()
            ->for($bzn_consultation)
            ->create();

        $response = $this->getJson("assessments-api/v1/assessment-case-files/"
            . "{$bzn_target->id}?form_name=bzn_attachment&file_id={$bzn_attachment->id}");

        $response->assertStatus(200);
    }

    public function test_get_download_file_cga_attachment_success()
    {
        $care_plan = CarePlan::factory()->create();
        $cga_target = CgaCareTarget::factory()->create([
            'care_plan_id' => $care_plan->id,
        ]);
        $cga_consultation = CgaConsultationNotes::factory()
            ->for($cga_target)
            ->create();
        $cga_attachment = CgaConsultationAttachment::factory()
            ->for($cga_consultation)
            ->create();

        $response = $this->getJson("assessments-api/v1/assessment-case-files/"
            . "{$cga_target->id}?form_name=cga_attachment&file_id={$cga_attachment->id}");

        $response->assertStatus(200);
    }

}
