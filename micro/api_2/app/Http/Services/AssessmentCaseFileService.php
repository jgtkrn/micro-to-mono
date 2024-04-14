<?php

namespace App\Http\Services;

use App\Models\AssessmentCaseAttachment;
use App\Models\AssessmentCaseSignature;
use Illuminate\Http\Request;
use App\Models\GenogramForm;
use App\Traits\RespondsWithHttpStatus;
use Illuminate\Support\Facades\Storage;

class AssessmentCaseFileService
{
    use RespondsWithHttpStatus;

    private $form_names = [
        'genogram',
        'attachment',
        'signature'
    ];

    public function getFormNames()
    {
        return $this->form_names;
    }

    public function upload(Request $request, $id)
    {
        switch ($request->form_name) {
            case 'genogram':
                return $this->upload_genogram($request, $id);
            case 'attachment':
                return $this->upload_attachment($request, $id);
            case 'signature':
                return $this->upload_signature($request, $id);
            default:
                return 'Invalid form name';
        }
    }

    public function download(Request $request, $id)
    {
        switch ($request->form_name) {
            case 'genogram':
                return $this->download_genogram($id);
            case 'attachment':
                return $this->download_attachment($request);
            case 'signature':
                return $this->download_signature($id);
            default:
                return 'Invalid form name';
        }
    }

    public function destroy(Request $request, $id)
    {
        switch ($request->form_name) {
            case 'genogram':
                return $this->destroy_genogram($id);
            case 'attachment':
                return $this->destroy_attachment($request);
            case 'signature':
                return $this->destroy_signature($id);
            default:
                return 'Invalid form name';
        }
    }

    //genogram
    public function upload_genogram(Request $request, $id)
    {
        $uploadedFile = $request->file('file');
        $file_name = $uploadedFile->getClientOriginalName();
        $file_path = Storage::put(env('STORAGE_FOLDER', 'dev/documents'), $uploadedFile);

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

    public function download_genogram($id)
    {
        $form = GenogramForm::where('assessment_case_id', $id)->first();
        if (!$form) {
            return $this->failure('Form not found', 404);
        }

        return Storage::download($form->file_path, $form->file_name);
    }

    public function destroy_genogram($id)
    {
        $form = GenogramForm::where('assessment_case_id', $id)->first();
        if (!$form) {
            return $this->failure('Form not found', 404);
        }

        Storage::delete($form->file_path);
        $form->delete();
        return $this->success(null);
    }

    //attachment
    public function upload_attachment(Request $request, $id)
    {
        $uploadedFile = $request->file('file');
        $file_name = $uploadedFile->getClientOriginalName();
        $file_path = Storage::put(env('STORAGE_FOLDER', 'dev/documents'), $uploadedFile);

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

    public function download_attachment(Request $request)
    {
        $file = AssessmentCaseAttachment::where('id', $request->file_id)->first();
        if (!$file) {
            return $this->failure('File not found', 404);
        }

        return Storage::download($file->file_path, $file->file_name);
    }

    public function destroy_attachment(Request $request)
    {
        $file = AssessmentCaseAttachment::where('id', $request->file_id)->first();
        if (!$file) {
            return $this->failure('File not found', 404);
        }

        Storage::delete($file->file_path);
        $file->delete();
        return $this->success(null);
    }

    //signature
    public function upload_signature(Request $request, $id)
    {
        $uploadedFile = $request->file('file');
        $file_name = $uploadedFile->getClientOriginalName();
        $file_path = Storage::put(env('STORAGE_FOLDER', 'dev/documents'), $uploadedFile);

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

    public function download_signature($id)
    {
        $form = AssessmentCaseSignature::where('assessment_case_id', $id)->first();
        if (!$form) {
            return $this->failure('Form not found', 404);
        }

        return Storage::download($form->file_path, $form->file_name);
    }

    public function destroy_signature($id)
    {
        $form = AssessmentCaseSignature::where('assessment_case_id', $id)->first();
        if (!$form) {
            return $this->failure('Form not found', 404);
        }

        Storage::delete($form->file_path);
        $form->delete();
        return $this->success(null);
    }
}
