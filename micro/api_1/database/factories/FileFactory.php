<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\File>
 */
class FileFactory extends Factory
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
        $disk_name = Storage::put(env('STORAGE_FOLDER', 'test'), $file);
        return [
            'file_name' => $file->name,
            'disk_name' => $disk_name
        ];
    }
}
