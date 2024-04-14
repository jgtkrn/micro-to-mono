<?php

namespace App\Http\Services\v2\Appointments;

use App\Models\v2\Appointments\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FileService
{
    public function upload(Request $request)
    {
        $uploadedFile = $request->file('file');
        $file_name = $uploadedFile->getClientOriginalName();
        $disk_name = Storage::put(config('filestorage.source'), $uploadedFile);
        $user_id = $request->user_id;

        $file = new File;
        $file->file_name = $file_name;
        $file->disk_name = $disk_name;
        $file->user_id = $user_id;
        $file->save();

        $results = [];
        $results['id'] = $file->id;
        $results['file_name'] = $file->file_name;

        return $results;
    }

    public function download($file)
    {
        return Storage::download($file->disk_name, $file->file_name);
    }
}
