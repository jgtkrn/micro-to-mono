<?php

namespace App\Http\Services;

use App\Models\BznConsultationNotes;
use App\Models\BznConsultationSign;
use App\Models\BznConsultationAttachment;
use App\Models\CgaConsultationNotes;
use App\Models\CgaConsultationSign;
use App\Models\CgaConsultationAttachment;
use Illuminate\Http\Request;
use App\Traits\RespondsWithHttpStatus;
use Illuminate\Support\Facades\Storage;

class ConsultationFileService
{
    use RespondsWithHttpStatus;

    private $form_names = [
        'bzn_signature',
        'cga_signature',
        'bzn_attachment',
        'cga_attachment'
    ];

    public function getFormNames()
    {
        return $this->form_names;
    }

    public function download(Request $request, $id)
    {
        switch ($request->form_name) {
            case 'bzn_signature':
                return $this->download_bzn_signature($request);
            case 'cga_signature':
                return $this->download_cga_signature($request);
            case 'bzn_attachment':
                return $this->download_bzn_attachment($request);
            case 'cga_attachment':
                return $this->download_cga_attachment($request);
            default:
                return 'Invalid form name';
        }
    }

    //bzn_signature
    public function upload_bzn_signature($request, $consultation)
    {
        $uploadedFile = $request->file('signature_file');
        $file_name = $uploadedFile->getClientOriginalName();
        $file_path = Storage::put(env('STORAGE_FOLDER', 'dev/documents'), $uploadedFile);

        //delete old file in storage
        $old_form = BznConsultationSign::where('bzn_consultation_notes_id', $consultation->id)->first();
        if ($old_form) {
            Storage::delete($old_form->file_path);
        }

        $upsert = BznConsultationSign::updateOrCreate(
            ['bzn_consultation_notes_id' => $consultation->id],
            [
                'signature_name' => $request->signature_name,
                'signature_remark' => $request->signature_remark,
                'file_name' => $file_name,
                'file_path' => $file_path,
                'url' => Storage::url($file_path),
            ]
        );

        if($upsert) {
            return "created";
        } else {
            return "failed";
        }
    }

    public function download_bzn_signature($request)
    {
        $form = BznConsultationSign::where('id', $request->query('file_id'))->first();
        if (!$form) {
            return $this->failure('Form not found', 404);
        }

        return Storage::download($form->file_path, $form->file_name);
    }

    //cga_signature
    public function upload_cga_signature($request, $consultation)
    {
        $uploadedFile = $request->file('signature_file');
        $file_name = $uploadedFile->getClientOriginalName();
        $file_path = Storage::put(env('STORAGE_FOLDER', 'dev/documents'), $uploadedFile);

        //delete old file in storage
        $old_form = CgaConsultationSign::where('cga_consultation_notes_id', $consultation->id)->first();
        if ($old_form) {
            Storage::delete($old_form->file_path);
        }

        $upsert = CgaConsultationSign::updateOrCreate(
            ['cga_consultation_notes_id' => $consultation->id],
            [
                'signature_name' => $request->signature_name,
                'signature_remark' => $request->signature_remark,
                'file_name' => $file_name,
                'file_path' => $file_path,
                'url' => Storage::url($file_path),
            ]
        );
        if($upsert) {
            return "created";
        } else {
            return "failed";
        } 
    }

    public function download_cga_signature($request)
    {
        $form = CgaConsultationSign::where('id', $request->query('file_id'))->first();
        if (!$form) {
            return $this->failure('Form not found', 404);
        }

        return Storage::download($form->file_path, $form->file_name);
    }

    //bzn_attachment
    public function upload_bzn_attachment($file, $consultation)
    {
        $file_name = $file->getClientOriginalName();
        $file_path = Storage::put(env('STORAGE_FOLDER', 'dev/documents'), $file);
        $url = Storage::url($file_path);

        BznConsultationAttachment::create([
            'bzn_consultation_notes_id' => $consultation->id,
            'file_name' => $file_name,
            'file_path' => $file_path,
            'url' => $url
        ]);

        return "created";
    }

    public function download_bzn_attachment(Request $request)
    {
        $file = BznConsultationAttachment::where('id', $request->query('file_id'))->first();
        if (!$file) {
            return $this->failure('File not found', 404);
        }

        return Storage::download($file->file_path, $file->file_name);
    }

    //cga_attachment
    public function upload_cga_attachment($file, $consultation)
    {
        $file_name = $file->getClientOriginalName();
        $file_path = Storage::put(env('STORAGE_FOLDER', 'dev/documents'), $file);
        $url = Storage::url($file_path);

        CgaConsultationAttachment::create([
            'cga_consultation_notes_id' => $consultation->id,
            'file_name' => $file_name,
            'file_path' => $file_path,
            'url' => $url
        ]);

        return "created";
    }

    public function download_cga_attachment(Request $request)
    {
        $file = CgaConsultationAttachment::where('id', $request->query('file_id'))->first();
        if (!$file) {
            return $this->failure('File not found', 404);
        }

        return Storage::download($file->file_path, $file->file_name);
    }

    public function destroy_file($id, $form){
        if($form == 'cga_signature'){
            $old_form = CgaConsultationSign::where('id', $id)->first();
            if(!$old_form) {
                return false;
            }
            if ($old_form) {
                Storage::delete($old_form->file_path);
            }
            $delete = $old_form->delete();
            if($delete){
                return true;
            }
        }

        if($form == 'bzn_signature'){
            $old_form = BznConsultationSign::where('id', $id)->first();
            if(!$old_form) {
                return false;
            }
            if ($old_form) {
                Storage::delete($old_form->file_path);
            }
            $delete = $old_form->delete();
            if($delete){
                return true;
            }
        }

        if($form == 'cga_attachment'){
            $old_form = CgaConsultationAttachment::where('id', $id)->first();

            if(!$old_form) {
                return false;
            }
            if ($old_form) {
                Storage::delete($old_form->file_path);
            }
            $delete = $old_form->delete();
            if($delete){
                return true;
            }
        }

        if($form == 'bzn_attachment'){
            $old_form = BznConsultationAttachment::where('id', $id)->first();
            
            if(!$old_form) {
                return false;
            }
            if ($old_form) {
                Storage::delete($old_form->file_path);
            }
            $delete = $old_form->delete();
            if($delete){
                return true;
            }
        }

        return false;
    }

    public function upload_cga_attachment_single($request, $consultation)
    {
        $uploadedFile = $request->file('attachment_file');
        $file_name = $uploadedFile->getClientOriginalName();
        $file_path = Storage::put(env('STORAGE_FOLDER', 'dev/documents'), $uploadedFile);

        $create = CgaConsultationAttachment::create([
            'cga_consultation_notes_id' => $consultation->id,
            'file_name' => $file_name,
            'file_path' => $file_path,
            'url' => Storage::url($file_path),
        ]);
        if($create) {
            return "created";
        } else {
            return "failed";
        } 
    }

    public function upload_bzn_attachment_single($request, $consultation)
    {
        $uploadedFile = $request->file('attachment_file');
        $file_name = $uploadedFile->getClientOriginalName();
        $file_path = Storage::put(env('STORAGE_FOLDER', 'dev/documents'), $uploadedFile);

        $create = BznConsultationAttachment::create([
            'bzn_consultation_notes_id' => $consultation->id,
            'file_name' => $file_name,
            'file_path' => $file_path,
            'url' => Storage::url($file_path),
        ]);
        if($create) {
            return "created";
        } else {
            return "failed";
        } 
    }

}
