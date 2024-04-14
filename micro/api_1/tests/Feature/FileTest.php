<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;
use App\Models\File;

class FileTest extends TestCase
{
    use WithoutMiddleware, RefreshDatabase, WithFaker;

    public function test_upload_file_success()
    {
        $file = UploadedFile::fake()->create('image.jpeg', 100);
        $response = $this->postJson('appointments-api/v1/appointments/files', [
            'file' => $file
        ]);

        $response->assertStatus(200);
        $response->assertJson(fn (AssertableJson $json) =>
        $json->has('data.id'));
        $response->assertJsonPath('data.file_name', $file->name);
        $this->assertDatabaseHas('files', [
            'file_name' => $file->name
        ]);

        //clean storage/app/test manually if needed (0 bytes files)
    }

    public function test_upload_file_exceeded_size()
    {
        $file = UploadedFile::fake()->create('image.jpeg', 13000);
        $response = $this->postJson('appointments-api/v1/appointments/files', [
            'file' => $file
        ]);

        $response->assertStatus(422);
    }

    public function test_upload_file_invalid_type()
    {
        $file = UploadedFile::fake()->create('document.doc', 100);
        $response = $this->postJson('appointments-api/v1/appointments/files', [
            'file' => $file
        ]);

        $response->assertStatus(422);
    }

    public function test_upload_file_empty_file()
    {
        $response = $this->postJson('appointments-api/v1/appointments/files', []);

        $response->assertStatus(422);
    }

    public function test_download_file_success()
    {
        $file = File::factory()->create();
        $response = $this->get("appointments-api/v1/appointments/files/{$file->id}");

        $response->assertOk();
    }

    public function test_download_file_not_found()
    {
        $response = $this->get("appointments-api/v1/appointments/files/100");

        $response->assertNotFound();
    }
}
