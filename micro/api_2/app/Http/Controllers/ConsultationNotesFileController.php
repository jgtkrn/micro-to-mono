<?php

namespace App\Http\Controllers;

use App\Http\Services\ConsultationFileService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\BznConsultationNotes;
use App\Models\CgaConsultationNotes;
use App\Models\BznConsultationSign;
use App\Models\CgaConsultationSign;
use App\Models\BznConsultationAttachment;
use App\Models\CgaConsultationAttachment;
use App\Traits\RespondsWithHttpStatus;


class ConsultationNotesFileController extends Controller
{
    use RespondsWithHttpStatus;
    private $fileService;

    public function __construct()
    {
        $this->fileService = new ConsultationFileService();
    }
    /**
     * @OA\Get(
     *     path="/assessments-api/v1/consultation-notes-files/{id}",
     *     tags={"ConsultationNotesFiles"},
     *     summary="Consultation notes file download",
     *     operationId="consultationNotesFileDownload",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Id of consultation notes file",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="form_name",
     *         in="query",
     *         description="File name. Value: bzn_signature, cga_signature, bzn_attachment, cga_attachment",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="notes_type",
     *         in="query",
     *         description="File name. Value: bzn, cga",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="file_id",
     *         in="query",
     *         description="File Id (Required for attachment)",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful",
     *         @OA\MediaType(
     *             mediaType="* / *",
     *             @OA\Schema(type="string", format="binary")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Unprocessable content",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="object",
     *                 @OA\Property(property="code", type="integer", description="Status code", example="422"),
     *                 @OA\Property(property="message", type="string", example=""),
     *                 @OA\Property(property="errors", type="array",
     *                     @OA\Items(type="object",
     *                         @OA\Property(property="field", type="string", description="Field name", example="form_name"),
     *                         @OA\Property(property="message", type="string", description="Error message of the field", example="The selected form name is invalid")
     *                     ) 
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Consultation notes file not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="object",
     *                 @OA\Property(property="code", type="integer", description="Status code", example="404"),
     *                 @OA\Property(property="message", type="string", example="Assessment case not found"),
     *                 @OA\Property(property="errors", type="array", description="Empty array", example="[]",
     *                     @OA\Items()
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function download(Request $request, $id)
    {
        // if($request->is_other && $request->access_role !== 'admin'){
        //     return response()->json([
        //         'data' => null,
        //         'message' => 'User not in any team access'
        //     ], 401);
        // }

        $request->validate([
            'form_name' => ['required', Rule::in($this->fileService->getFormNames())],
            'notes_type' => ['required', 'in:bzn,cga']
        ]);

        $notes_type = $request->query('notes_type');

        if($notes_type == 'bzn') {
            $consultation = BznConsultationNotes::where('id', $id)->first();
            if (!$consultation) {
                return $this->failure('Consultation notes file not found', 404);
            }
        } else if ($notes_type == 'cga') {
            $consultation = CgaConsultationNotes::where('id', $id)->first();
            if (!$consultation) {
                return $this->failure('Consultation notes file not found', 404);
            }
        }
        
        return $this->fileService->download($request, $id);
    }

    /**
     * @OA\Post(
     *     path="/assessments-api/v1/consultation-sign-files/{id}",
     *     tags={"ConsultationSignFiles"},
     *     summary="Consultation notes signature file update",
     *     operationId="consultationNotesFileUpsert",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Id of consultation notes bzn/cga",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="form_name",
     *         in="query",
     *         description="File name. Value: bzn, cga",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         description="Input cga consultation notes information (in json)",
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     description="Single file, max file 12MB, file type allowed: jpg, jpeg, png",
     *                     property="signature_file",
     *                     type="string",
     *                     format="binary"
     *                 ),
     *             )
     *          ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful",
     *         @OA\MediaType(
     *             mediaType="* / *",
     *             @OA\Schema(type="string", format="binary")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Unprocessable content",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="object",
     *                 @OA\Property(property="code", type="integer", description="Status code", example="422"),
     *                 @OA\Property(property="message", type="string", example=""),
     *                 @OA\Property(property="errors", type="array",
     *                     @OA\Items(type="object",
     *                         @OA\Property(property="field", type="string", description="Field name", example="form_name"),
     *                         @OA\Property(property="message", type="string", description="Error message of the field", example="The selected form name is invalid")
     *                     ) 
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Consultation notes file not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="object",
     *                 @OA\Property(property="code", type="integer", description="Status code", example="404"),
     *                 @OA\Property(property="message", type="string", example="Assessment case not found"),
     *                 @OA\Property(property="errors", type="array", description="Empty array", example="[]",
     *                     @OA\Items()
     *                 )
     *             )
     *         )
     *     )
     * )
     */

    public function upsertSign($id, Request $request)
    {

        if(!$request->form_name || !in_array($request->form_name, ['bzn','cga'])){
            return response()->json(['data' => null, 'message' => 'form name must be specified (ex: bzn, cga)'], 409);
        }
        $form_name = $request->form_name;
        $upload_sign = 'failed';
        // upload_bzn_signature($request, $notes))
        $sign = null;
        $notes = null;
        if($form_name == 'bzn'){
            $notes = BznConsultationNotes::where('id', $id)->first();
            if($notes == null) {
                return response()->json(['data' => null, 'message' => 'notes do not exist'], 404);
            }
            $upload_sign = $this->fileService->upload_bzn_signature($request, $notes);
            $sign = BznConsultationSign::where('bzn_consultation_notes_id', $id)->first();
        } else if($form_name == 'cga') {
            $notes = CgaConsultationNotes::where('id', $id)->first();
            if($notes == null) {
                return response()->json(['data' => null, 'message' => 'notes do not exist'], 404);
            }
            $upload_sign = $this->fileService->upload_cga_signature($request, $notes);
           $sign = CgaConsultationSign::where('cga_consultation_notes_id', $id)->first(); 
        }
        if($upload_sign == 'failed') {
            return response()->json(['data' => null, 'message' => 'failed update sign'], 400);
        }
        return response()->json(['data' => $sign, 'message' => 'success update sign'], 200);
    }

    /**
     * @OA\Post(
     *     path="/assessments-api/v1/consultation-attachment-files/{id}",
     *     tags={"ConsultationAttachmentFiles"},
     *     summary="Consultation notes attachment file update",
     *     operationId="consultationNotesAttachmentUpsert",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Id of consultation notes bzn/cga",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="form_name",
     *         in="query",
     *         description="File name. Value: bzn, cga",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         description="Input cga consultation notes information (in json)",
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     description="Single file, max file 12MB, file type allowed: jpg, jpeg, png",
     *                     property="attachment_file",
     *                     type="string",
     *                     format="binary"
     *                 ),
     *             )
     *          ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful",
     *         @OA\MediaType(
     *             mediaType="* / *",
     *             @OA\Schema(type="string", format="binary")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Unprocessable content",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="object",
     *                 @OA\Property(property="code", type="integer", description="Status code", example="422"),
     *                 @OA\Property(property="message", type="string", example=""),
     *                 @OA\Property(property="errors", type="array",
     *                     @OA\Items(type="object",
     *                         @OA\Property(property="field", type="string", description="Field name", example="form_name"),
     *                         @OA\Property(property="message", type="string", description="Error message of the field", example="The selected form name is invalid")
     *                     ) 
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Consultation notes file not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="object",
     *                 @OA\Property(property="code", type="integer", description="Status code", example="404"),
     *                 @OA\Property(property="message", type="string", example="Assessment case not found"),
     *                 @OA\Property(property="errors", type="array", description="Empty array", example="[]",
     *                     @OA\Items()
     *                 )
     *             )
     *         )
     *     )
     * )
     */

    public function upsertAttachment($id, Request $request)
    {
        if(!$request->form_name || !in_array($request->form_name, ['bzn','cga'])){
            return response()->json(['data' => null, 'message' => 'form name must be specified (ex: bzn, cga)'], 409);
        }
        $form_name = $request->form_name;
        $upload_attachment = 'failed';
        // upload_bzn_signature($request, $notes))
        $attachment = null;
        $notes = null;
        if($form_name == 'bzn'){
            $notes = BznConsultationNotes::where('id', $id)->first();
            if($notes == null) {
                return response()->json(['data' => null, 'message' => 'notes do not exist'], 404);
            }
            $upload_attachment = $this->fileService->upload_bzn_attachment_single($request, $notes);
            $attachment = BznConsultationAttachment::where('bzn_consultation_notes_id', $id)->get();
        } else if($form_name == 'cga') {
            $notes = CgaConsultationNotes::where('id', $id)->first();
            if($notes == null) {
                return response()->json(['data' => null, 'message' => 'notes do not exist'], 404);
            }
            $upload_attachment = $this->fileService->upload_cga_attachment_single($request, $notes);
           $attachment = CgaConsultationAttachment::where('cga_consultation_notes_id', $id)->get(); 
        }
        if($upload_attachment == 'failed') {
            return response()->json(['data' => null, 'message' => 'failed update attachment'], 400);
        }
        return response()->json(['data' => $attachment, 'message' => 'success update attachment'], 200);
    }

    /**
     * @OA\Delete(
     *     path="/assessments-api/v1/consultation-notes-files/{id}",
     *     tags={"ConsultationNotesFilesDelete"},
     *     summary="Consultation notes file delete",
     *     operationId="consultationNotesFileDelete",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Id of consultation notes file",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="form_name",
     *         in="query",
     *         description="File name. Value: bzn_signature, cga_signature, bzn_attachment, cga_attachment",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful",
     *         @OA\MediaType(
     *             mediaType="* / *",
     *             @OA\Schema(type="string", format="binary")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Unprocessable content",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="object",
     *                 @OA\Property(property="code", type="integer", description="Status code", example="422"),
     *                 @OA\Property(property="message", type="string", example=""),
     *                 @OA\Property(property="errors", type="array",
     *                     @OA\Items(type="object",
     *                         @OA\Property(property="field", type="string", description="Field name", example="form_name"),
     *                         @OA\Property(property="message", type="string", description="Error message of the field", example="The selected form name is invalid")
     *                     ) 
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Consultation notes file not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="object",
     *                 @OA\Property(property="code", type="integer", description="Status code", example="404"),
     *                 @OA\Property(property="message", type="string", example="Assessment case not found"),
     *                 @OA\Property(property="errors", type="array", description="Empty array", example="[]",
     *                     @OA\Items()
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function destroy($id, Request $request)
    {
        if(!$request->form_name || !in_array($request->form_name, ['bzn_signature', 'cga_signature', 'bzn_attachment', 'cga_attachment'])){
            return response()->json(['data' => null, 'message' => 'form name must be specified (ex: bzn_signature, cga_signature, bzn_attachment, cga_attachment)'], 409);
        }
        $delete = $this->fileService->destroy_file($id, $request->form_name);
        if(!$delete){
            return response()->json(['data' => null, 'message' => 'failed delete file'], 400);
        }
        return response()->json(['data' => null, 'message' => 'success delete file'], 200);
    }
}
