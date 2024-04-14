<?php

namespace App\Http\Services\v2\Assessments;

use App\Models\v2\Assessments\AssessmentCaseAttachment;
use App\Models\v2\Assessments\AssessmentCaseSignature;
use App\Models\v2\Assessments\GenogramForm;
use App\Traits\RespondsWithHttpStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AssessmentCaseFileService
{
    use RespondsWithHttpStatus;

    private $form_names = [
        'genogram',
        'attachment',
        'signature',
    ];

    public function getFormNames()
    {
        return $this->form_names;
    }

    public function upload(Request $request, $id)
    {
        switch ($request->form_name) {
            case 'genogram':
                return $this->uploadGenogram($request, $id);
            case 'attachment':
                return $this->uploadAttachment($request, $id);
            case 'signature':
                return $this->uploadSignature($request, $id);
            default:
                return 'Invalid form name';
        }
    }

    public function download(Request $request, $id)
    {
        switch ($request->form_name) {
            case 'genogram':
                return $this->downloadGenogram($id);
            case 'attachment':
                return $this->downloadAttachment($request);
            case 'signature':
                return $this->downloadSignature($id);
            default:
                return 'Invalid form name';
        }
    }

    public function destroy(Request $request, $id)
    {
        switch ($request->form_name) {
            case 'genogram':
                return $this->destroyGenogram($id);
            case 'attachment':
                return $this->destroyAttachment($request);
            case 'signature':
                return $this->destroySignature($id);
            default:
                return 'Invalid form name';
        }
    }

    //genogram
    public function uploadGenogram(Request $request, $id)
    {
        $uploadedFile = $request->file('file');
        $file_name = $uploadedFile->getClientOriginalName();
        $file_path = Storage::put(config('filestorage.source'), $uploadedFile);

        //delete old file in storage
        $old_form = GenogramForm::where('assessment_case_id', $id)->first();
        if ($old_form) {
            Storage::delete($old_form->file_path);
        }

        $form = GenogramForm::updateOrCreate(
            ['assessment_case_id' => $id],
            [
                'file_name' => $file_name,
                'file_path' => $file_path,
                'url' => Storage::url($file_path),

                // user data
                'created_by' => $request->user_id,
                'updated_by' => $request->user_id,
                'created_by_name' => $request->user_name,
                'updated_by_name' => $request->user_name,
            ]
        );

        return $form;
    }

    public function downloadGenogram($id)
    {
        $form = GenogramForm::where('assessment_case_id', $id)->first();
        if (! $form) {
            return $this->failure('Form not found', 404);
        }

        return Storage::download($form->file_path, $form->file_name);
    }

    public function destroyGenogram($id)
    {
        $form = GenogramForm::where('assessment_case_id', $id)->first();
        if (! $form) {
            return $this->failure('Form not found', 404);
        }

        Storage::delete($form->file_path);
        $form->delete();

        return $this->success(null);
    }

    //attachment
    public function uploadAttachment(Request $request, $id)
    {
        $uploadedFile = $request->file('file');
        $file_name = $uploadedFile->getClientOriginalName();
        $file_path = Storage::put(config('filestorage.source'), $uploadedFile);

        $file = AssessmentCaseAttachment::create([
            'assessment_case_id' => $id,
            'file_name' => $file_name,
            'file_path' => $file_path,

            // user data
            'created_by' => $request->user_id,
            'updated_by' => $request->user_id,
            'created_by_name' => $request->user_name,
            'updated_by_name' => $request->user_name,
        ]);

        return $file;
    }

    public function downloadAttachment(Request $request)
    {
        $file = AssessmentCaseAttachment::where('id', $request->file_id)->first();
        if (! $file) {
            return $this->failure('File not found', 404);
        }

        return Storage::download($file->file_path, $file->file_name);
    }

    public function destroyAttachment(Request $request)
    {
        $file = AssessmentCaseAttachment::where('id', $request->file_id)->first();
        if (! $file) {
            return $this->failure('File not found', 404);
        }

        Storage::delete($file->file_path);
        $file->delete();

        return $this->success(null);
    }

    //signature
    public function uploadSignature(Request $request, $id)
    {
        $uploadedFile = $request->file('file');
        $file_name = $uploadedFile->getClientOriginalName();
        $file_path = Storage::put(config('filestorage.source'), $uploadedFile);

        //delete old file in storage
        $old_form = AssessmentCaseSignature::where('assessment_case_id', $id)->first();
        if ($old_form) {
            Storage::delete($old_form->file_path);
        }

        $form = AssessmentCaseSignature::updateOrCreate(
            ['assessment_case_id' => $id],
            [
                'file_name' => $file_name,
                'file_path' => $file_path,
                'url' => Storage::url($file_path),
                'name' => $request->name,
                'remarks' => $request->remarks,

                // user data
                'created_by' => $request->user_id,
                'updated_by' => $request->user_id,
                'created_by_name' => $request->user_name,
                'updated_by_name' => $request->user_name,
            ]
        );

        return $form;
    }

    public function downloadSignature($id)
    {
        $form = AssessmentCaseSignature::where('assessment_case_id', $id)->first();
        if (! $form) {
            return $this->failure('Form not found', 404);
        }

        return Storage::download($form->file_path, $form->file_name);
    }

    public function destroySignature($id)
    {
        $form = AssessmentCaseSignature::where('assessment_case_id', $id)->first();
        if (! $form) {
            return $this->failure('Form not found', 404);
        }

        Storage::delete($form->file_path);
        $form->delete();

        return $this->success(null);
    }
}
