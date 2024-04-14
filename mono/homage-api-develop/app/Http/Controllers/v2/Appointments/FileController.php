<?php

namespace App\Http\Controllers\v2\Appointments;

use App\Http\Controllers\Controller;
use App\Http\Services\v2\Appointments\FileService;
use App\Models\v2\Appointments\File;
use App\Traits\RespondsWithHttpStatus;
use Illuminate\Http\Request;

class FileController extends Controller
{
    use RespondsWithHttpStatus;
    private $fileService;

    public function __construct()
    {
        $this->fileService = new FileService;
    }

    public function upload(Request $request)
    {
        $request->validate([
            'file' => ['required', 'max:12288', 'mimes:jpg,jpeg,png,pdf'], //12MB
        ]);
        $results = $this->fileService->upload($request);

        return $this->success($results);
    }

    public function download($id)
    {
        $file = File::find($id);
        if (! $file) {
            return $this->failure('Attachment file not found', 404);
        }

        return $this->fileService->download($file);
    }
}
