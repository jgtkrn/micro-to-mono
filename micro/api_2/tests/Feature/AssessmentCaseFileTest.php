<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Testing\Fluent\AssertableJson;
use App\Models\AssessmentCase;
use App\Models\AssessmentCaseAttachment;
use App\Models\AssessmentCaseSignature;
use App\Models\GenogramForm;

class AssessmentCaseFileTest extends TestCase
{
    use WithoutMiddleware, RefreshDatabase, WithFaker;

    //genogram
    public function test_post_upload_file_genogram_success()
    {
        $assessment_case = AssessmentCase::factory()->create();
        $file = UploadedFile::fake()->create('image.jpeg', 100);
        $response = $this->postJson('assessments-api/v1/assessment-case-files', [
            'form_name' => 'genogram',
            'id' => $assessment_case->id,
            'file' => $file
        ]);

        $response->assertStatus(200);
        $response->assertJson(
            fn (AssertableJson $json) => $json
                ->has('data')
                ->where('data.id', $assessment_case->id)
                ->where('data.file_name', $file->getClientOriginalName())
        );
    }

    public function test_get_download_file_genogram_success()
    {
        $assessment_case = AssessmentCase::factory()->create();
        GenogramForm::factory()
            ->for($assessment_case)
            ->create();

        $response = $this->getJson("assessments-api/v1/assessment-case-files/"
            . "{$assessment_case->id}?form_name=genogram");

        $response->assertStatus(200);
    }

    public function test_delete_download_file_genogram_success()
    {
        $assessment_case = AssessmentCase::factory()->create();
        $form = GenogramForm::factory()
            ->for($assessment_case)
            ->create();

        $this->assertDatabaseHas('genogram_forms', [
            'id' => $form->id
        ]);

        $response = $this->deleteJson("assessments-api/v1/assessment-case-files/"
            . "{$assessment_case->id}?form_name=genogram");

        $response->assertOk();
        $response->assertJsonPath('data', null);
        $this->assertDatabaseMissing('genogram_forms', [
            'id' => $form->id
        ]);
    }

    //attachment
    public function test_post_upload_file_attachment_success()
    {
        $assessment_case = AssessmentCase::factory()->create();
        $file = UploadedFile::fake()->create('image.jpeg', 100);
        $response = $this->postJson('assessments-api/v1/assessment-case-files', [
            'form_name' => 'attachment',
            'id' => $assessment_case->id,
            'file' => $file
        ]);

        $response->assertStatus(200);
        $response->assertJson(
            fn (AssertableJson $json) => $json
                ->has('data')
                ->where('data.id', $assessment_case->id)
                ->where('data.file_name', $file->getClientOriginalName())
        );
    }

    public function test_get_download_file_attachment_success()
    {
        $assessment_case = AssessmentCase::factory()->create();
        $form = AssessmentCaseAttachment::factory()
            ->for($assessment_case)
            ->create();

        $response = $this->getJson("assessments-api/v1/assessment-case-files/"
            . "{$assessment_case->id}?form_name=attachment&file_id={$form->id}");

        $response->assertStatus(200);
    }

    public function test_delete_download_file_attachment_success()
    {
        $assessment_case = AssessmentCase::factory()->create();
        $form = AssessmentCaseAttachment::factory()
            ->for($assessment_case)
            ->create();

        $this->assertDatabaseHas('assessment_case_attachments', [
            'id' => $form->id
        ]);

        $response = $this->deleteJson("assessments-api/v1/assessment-case-files/"
            . "{$assessment_case->id}?form_name=attachment&file_id={$form->id}");

        $response->assertOk();
        $response->assertJsonPath('data', null);
        $this->assertDatabaseMissing('assessment_case_attachments', [
            'id' => $form->id
        ]);
    }

    //signature
    public function test_post_upload_file_signature_success()
    {
        $assessment_case = AssessmentCase::factory()->create();
        $file = UploadedFile::fake()->create('image.jpeg', 100);
        $data = [
            'form_name' => 'signature',
            'id' => $assessment_case->id,
            'file' => $file,
            'name' => $this->faker->name,
            'remarks' => $this->faker->text
        ];
        $response = $this->postJson('assessments-api/v1/assessment-case-files', $data);

        $response->assertStatus(200);
        $response->assertJson(
            fn (AssertableJson $json) => $json
                ->has('data')
                ->where('data.id', $assessment_case->id)
                ->where('data.file_name', $file->getClientOriginalName())
                ->where('data.name', $data['name'])
                ->where('data.remarks', $data['remarks'])
        );
    }

    public function test_get_download_file_signature_success()
    {
        $assessment_case = AssessmentCase::factory()->create();
        AssessmentCaseSignature::factory()
            ->for($assessment_case)
            ->create();

        $response = $this->getJson("assessments-api/v1/assessment-case-files/"
            . "{$assessment_case->id}?form_name=signature");

        $response->assertStatus(200);
    }

    public function test_delete_download_file_signature_success()
    {
        $assessment_case = AssessmentCase::factory()->create();
        $form = AssessmentCaseSignature::factory()
            ->for($assessment_case)
            ->create();

        $this->assertDatabaseHas('assessment_case_signatures', [
            'id' => $form->id
        ]);

        $response = $this->deleteJson("assessments-api/v1/assessment-case-files/"
            . "{$assessment_case->id}?form_name=signature");

        $response->assertOk();
        $response->assertJsonPath('data', null);
        $this->assertDatabaseMissing('assessment_case_signatures', [
            'id' => $form->id
        ]);
    }
}
