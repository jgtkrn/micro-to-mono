<?php

namespace App\Http\Controllers\v2\Assessments;

use App\Http\Requests\v2\Assessments\UploadFileRequest;
use App\Http\Services\v2\Assessments\AssessmentCaseFileService;
use App\Models\v2\Assessments\AssessmentCase;
use App\Traits\RespondsWithHttpStatus;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AssessmentCaseFileController extends Controller
{
    use RespondsWithHttpStatus;
    private $fileService;

    public function __construct()
    {
        $this->fileService = new AssessmentCaseFileService;
    }

    public function upload(UploadFileRequest $request)
    {
        $request->validate([
            'form_name' => ['required', Rule::in($this->fileService->getFormNames())],
            'file' => ['required', 'max:12288', 'mimes:jpg,jpeg,png,pdf'], //12MB
        ]);

        $assessment_case = AssessmentCase::where('id', $request->id)->first();
        if (! $assessment_case) {
            return $this->failure('Assessment case not found', 404);
        }

        $form = $this->fileService->upload($request, $request->id);

        return response()->json(['data' => $form], 200);
    }

    public function download(Request $request, $id)
    {
        $request->validate([
            'form_name' => ['required', Rule::in($this->fileService->getFormNames())],
        ]);

        $assessment_case = AssessmentCase::where('id', $id)->first();
        if (! $assessment_case) {
            return $this->failure('Assessment case not found', 404);
        }

        return $this->fileService->download($request, $id);
    }

    public function destroy(Request $request, $id)
    {
        $request->validate([
            'form_name' => ['required', Rule::in($this->fileService->getFormNames())],
        ]);

        $assessment_case = AssessmentCase::where('id', $id)->first();
        if (! $assessment_case) {
            return $this->failure('Assessment case not found', 404);
        }

        return $this->fileService->destroy($request, $id);
    }
}
