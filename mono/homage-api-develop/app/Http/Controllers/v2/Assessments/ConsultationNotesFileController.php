<?php

namespace App\Http\Controllers\v2\Assessments;

use App\Http\Services\v2\Assessments\ConsultationFileService;
use App\Models\v2\Assessments\BznConsultationAttachment;
use App\Models\v2\Assessments\BznConsultationNotes;
use App\Models\v2\Assessments\BznConsultationSign;
use App\Models\v2\Assessments\CgaConsultationAttachment;
use App\Models\v2\Assessments\CgaConsultationNotes;
use App\Models\v2\Assessments\CgaConsultationSign;
use App\Traits\RespondsWithHttpStatus;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ConsultationNotesFileController extends Controller
{
    use RespondsWithHttpStatus;
    private $fileService;

    public function __construct()
    {
        $this->fileService = new ConsultationFileService;
    }

    public function destroy($id, Request $request)
    {
        if (! $request->form_name || ! in_array($request->form_name, ['bzn_signature', 'cga_signature', 'bzn_attachment', 'cga_attachment'])) {
            return response()->json(['data' => null, 'message' => 'form name must be specified (ex: bzn_signature, cga_signature, bzn_attachment, cga_attachment)'], 409);
        }
        $delete = $this->fileService->destroyFile($id, $request->form_name);
        if (! $delete) {
            return response()->json(['data' => null, 'message' => 'failed delete file'], 400);
        }

        return response()->json(['data' => null, 'message' => 'success delete file'], 200);
    }

    public function download(Request $request, $id)
    {
        $request->validate([
            'form_name' => ['required', Rule::in($this->fileService->getFormNames())],
            'notes_type' => ['required', 'in:bzn,cga'],
        ]);

        $notes_type = $request->query('notes_type');

        if ($notes_type == 'bzn') {
            $consultation = BznConsultationNotes::where('id', $id)->first();
            if (! $consultation) {
                return $this->failure('Consultation notes file not found', 404);
            }
        } elseif ($notes_type == 'cga') {
            $consultation = CgaConsultationNotes::where('id', $id)->first();
            if (! $consultation) {
                return $this->failure('Consultation notes file not found', 404);
            }
        }

        return $this->fileService->download($request, $id);
    }

    public function upsertSign($id, Request $request)
    {

        if (! $request->form_name || ! in_array($request->form_name, ['bzn', 'cga'])) {
            return response()->json(['data' => null, 'message' => 'form name must be specified (ex: bzn, cga)'], 409);
        }
        $form_name = $request->form_name;
        $upload_sign = 'failed';
        // uploadBznSignature($request, $notes))
        $sign = null;
        $notes = null;
        if ($form_name == 'bzn') {
            $notes = BznConsultationNotes::where('id', $id)->first();
            if ($notes == null) {
                return response()->json(['data' => null, 'message' => 'notes do not exist'], 404);
            }
            $upload_sign = $this->fileService->uploadBznSignature($request, $notes);
            $sign = BznConsultationSign::where('bzn_consultation_notes_id', $id)->first();
        } elseif ($form_name == 'cga') {
            $notes = CgaConsultationNotes::where('id', $id)->first();
            if ($notes == null) {
                return response()->json(['data' => null, 'message' => 'notes do not exist'], 404);
            }
            $upload_sign = $this->fileService->uploadCgaSignature($request, $notes);
            $sign = CgaConsultationSign::where('cga_consultation_notes_id', $id)->first();
        }
        if ($upload_sign == 'failed') {
            return response()->json(['data' => null, 'message' => 'failed update sign'], 400);
        }

        return response()->json(['data' => $sign, 'message' => 'success update sign'], 200);
    }

    public function upsertAttachment($id, Request $request)
    {
        if (! $request->form_name || ! in_array($request->form_name, ['bzn', 'cga'])) {
            return response()->json(['data' => null, 'message' => 'form name must be specified (ex: bzn, cga)'], 409);
        }
        $form_name = $request->form_name;
        $upload_attachment = 'failed';
        // uploadBznSignature($request, $notes))
        $attachment = null;
        $notes = null;
        if ($form_name == 'bzn') {
            $notes = BznConsultationNotes::where('id', $id)->first();
            if ($notes == null) {
                return response()->json(['data' => null, 'message' => 'notes do not exist'], 404);
            }
            $upload_attachment = $this->fileService->uploadBznAttachmentSingle($request, $notes);
            $attachment = BznConsultationAttachment::where('bzn_consultation_notes_id', $id)->get();
        } elseif ($form_name == 'cga') {
            $notes = CgaConsultationNotes::where('id', $id)->first();
            if ($notes == null) {
                return response()->json(['data' => null, 'message' => 'notes do not exist'], 404);
            }
            $upload_attachment = $this->fileService->uploadCgaAttachmentSingle($request, $notes);
            $attachment = CgaConsultationAttachment::where('cga_consultation_notes_id', $id)->get();
        }
        if ($upload_attachment == 'failed') {
            return response()->json(['data' => null, 'message' => 'failed update attachment'], 400);
        }

        return response()->json(['data' => $attachment, 'message' => 'success update attachment'], 200);
    }
}
