<?php

namespace App\Http\Services\v2\Assessments;

use App\Models\v2\Assessments\BznConsultationAttachment;
use App\Models\v2\Assessments\BznConsultationSign;
use App\Models\v2\Assessments\CgaConsultationAttachment;
use App\Models\v2\Assessments\CgaConsultationSign;
use App\Traits\RespondsWithHttpStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ConsultationFileService
{
    use RespondsWithHttpStatus;

    private $form_names = [
        'bzn_signature',
        'cga_signature',
        'bzn_attachment',
        'cga_attachment',
    ];

    public function getFormNames()
    {
        return $this->form_names;
    }

    public function download(Request $request, $id)
    {
        switch ($request->form_name) {
            case 'bzn_signature':
                return $this->downloadBznSignature($request);
            case 'cga_signature':
                return $this->downloadCgaSignature($request);
            case 'bzn_attachment':
                return $this->downloadBznAttachment($request);
            case 'cga_attachment':
                return $this->downloadCgaAttachment($request);
            default:
                return 'Invalid form name';
        }
    }

    //bzn_signature
    public function uploadBznSignature($request, $consultation)
    {
        $uploadedFile = $request->file('signature_file');
        $file_name = $uploadedFile->getClientOriginalName();
        $file_path = Storage::put(config('filestorage.source'), $uploadedFile);

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

        if ($upsert) {
            return 'created';
        } else {
            return 'failed';
        }
    }

    public function downloadBznSignature($request)
    {
        $form = BznConsultationSign::where('id', $request->query('file_id'))->first();
        if (! $form) {
            return $this->failure('Form not found', 404);
        }

        return Storage::download($form->file_path, $form->file_name);
    }

    //cga_signature
    public function uploadCgaSignature($request, $consultation)
    {
        $uploadedFile = $request->file('signature_file');
        $file_name = $uploadedFile->getClientOriginalName();
        $file_path = Storage::put(config('filestorage.source'), $uploadedFile);

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
        if ($upsert) {
            return 'created';
        } else {
            return 'failed';
        }
    }

    public function downloadCgaSignature($request)
    {
        $form = CgaConsultationSign::where('id', $request->query('file_id'))->first();
        if (! $form) {
            return $this->failure('Form not found', 404);
        }

        return Storage::download($form->file_path, $form->file_name);
    }

    //bzn_attachment
    public function uploadBznAttachment($file, $consultation)
    {
        $file_name = $file->getClientOriginalName();
        $file_path = Storage::put(config('filestorage.source'), $file);
        $url = Storage::url($file_path);

        BznConsultationAttachment::create([
            'bzn_consultation_notes_id' => $consultation->id,
            'file_name' => $file_name,
            'file_path' => $file_path,
            'url' => $url,
        ]);

        return 'created';
    }

    public function downloadBznAttachment(Request $request)
    {
        $file = BznConsultationAttachment::where('id', $request->query('file_id'))->first();
        if (! $file) {
            return $this->failure('File not found', 404);
        }

        return Storage::download($file->file_path, $file->file_name);
    }

    //cga_attachment
    public function uploadCgaAttachment($file, $consultation)
    {
        $file_name = $file->getClientOriginalName();
        $file_path = Storage::put(config('filestorage.source'), $file);
        $url = Storage::url($file_path);

        CgaConsultationAttachment::create([
            'cga_consultation_notes_id' => $consultation->id,
            'file_name' => $file_name,
            'file_path' => $file_path,
            'url' => $url,
        ]);

        return 'created';
    }

    public function downloadCgaAttachment(Request $request)
    {
        $file = CgaConsultationAttachment::where('id', $request->query('file_id'))->first();
        if (! $file) {
            return $this->failure('File not found', 404);
        }

        return Storage::download($file->file_path, $file->file_name);
    }

    public function destroyFile($id, $form)
    {
        if ($form == 'cga_signature') {
            $old_form = CgaConsultationSign::where('id', $id)->first();
            if (! $old_form) {
                return false;
            }
            if ($old_form) {
                Storage::delete($old_form->file_path);
            }
            $delete = $old_form->delete();
            if ($delete) {
                return true;
            }
        }

        if ($form == 'bzn_signature') {
            $old_form = BznConsultationSign::where('id', $id)->first();
            if (! $old_form) {
                return false;
            }
            if ($old_form) {
                Storage::delete($old_form->file_path);
            }
            $delete = $old_form->delete();
            if ($delete) {
                return true;
            }
        }

        if ($form == 'cga_attachment') {
            $old_form = CgaConsultationAttachment::where('id', $id)->first();

            if (! $old_form) {
                return false;
            }
            if ($old_form) {
                Storage::delete($old_form->file_path);
            }
            $delete = $old_form->delete();
            if ($delete) {
                return true;
            }
        }

        if ($form == 'bzn_attachment') {
            $old_form = BznConsultationAttachment::where('id', $id)->first();

            if (! $old_form) {
                return false;
            }
            if ($old_form) {
                Storage::delete($old_form->file_path);
            }
            $delete = $old_form->delete();
            if ($delete) {
                return true;
            }
        }

        return false;
    }

    public function uploadCgaAttachmentSingle($request, $consultation)
    {
        $uploadedFile = $request->file('attachment_file');
        $file_name = $uploadedFile->getClientOriginalName();
        $file_path = Storage::put(config('filestorage.source'), $uploadedFile);

        $create = CgaConsultationAttachment::create([
            'cga_consultation_notes_id' => $consultation->id,
            'file_name' => $file_name,
            'file_path' => $file_path,
            'url' => Storage::url($file_path),
        ]);
        if ($create) {
            return 'created';
        } else {
            return 'failed';
        }
    }

    public function uploadBznAttachmentSingle($request, $consultation)
    {
        $uploadedFile = $request->file('attachment_file');
        $file_name = $uploadedFile->getClientOriginalName();
        $file_path = Storage::put(config('filestorage.source'), $uploadedFile);

        $create = BznConsultationAttachment::create([
            'bzn_consultation_notes_id' => $consultation->id,
            'file_name' => $file_name,
            'file_path' => $file_path,
            'url' => Storage::url($file_path),
        ]);
        if ($create) {
            return 'created';
        } else {
            return 'failed';
        }
    }
}
