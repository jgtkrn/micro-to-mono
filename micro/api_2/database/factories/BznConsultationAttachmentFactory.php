<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BznConsultationAttachment>
 */
class BznConsultationAttachmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        Storage::fake('local');
        $file = UploadedFile::fake()->image('image.jpg');
        $file_path = Storage::put(env('STORAGE_FOLDER', 'test'), $file);
        return [
            'file_name' => $file->name,
            'file_path' => $file_path,
            'url' => $this->faker->url,
        ];
    }
}
