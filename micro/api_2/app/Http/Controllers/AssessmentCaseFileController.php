<?php

namespace App\Http\Controllers;

use App\Http\Services\AssessmentCaseFileService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\AssessmentCase;
use App\Traits\RespondsWithHttpStatus;

class AssessmentCaseFileController extends Controller
{
    use RespondsWithHttpStatus;
    private $fileService;

    public function __construct()
    {
        $this->fileService = new AssessmentCaseFileService();
    }

    /**
     * @OA\Post(
     *     path="/assessments-api/v1/assessment-case-files",
     *     tags={"AssessmentCaseFiles"},
     *     summary="Assessment case file upload",
     *     operationId="assessmentCaseFileUpload",
     *     @OA\RequestBody(
     *         description="Input file",
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     description="Single file, max file 12MB, file type allowed: jpg, jpeg, png",
     *                     property="file",
     *                     type="string",
     *                     format="binary"
     *                 ),
     *                 @OA\Property(property="id", type="integer", description="Assessment Case Id", example="1"),
     *                 @OA\Property(property="form_name", type="string", description="Form name. Value: genogram, attachment, signature", example="genogram"),
     *                 @OA\Property(property="name", type="string", description="Name (only for signature)", example="John Doe"),
     *                 @OA\Property(property="remarks", type="string", description="Remarks (only for signature)", example="Text of remarks")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful",
     *         @OA\JsonContent(
     *             @OA\Property(property="data",
     *                 oneOf={
     *                      @OA\Schema(ref="#/components/schemas/GenogramForm"),
     *                      @OA\Schema(ref="#/components/schemas/AssessmentCaseAttachment"),
     *                      @OA\Schema(ref="#/components/schemas/AssessmentCaseSignature"),
     *                 }                
     *             )
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
     *         description="Assessment case not found",
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
    public function upload(Request $request)
    {
        $request->validate([
            'form_name' => ['required', Rule::in($this->fileService->getFormNames())],
            'file' => ['required', 'max:12288', 'mimes:jpg,jpeg,png,pdf'] //12MB
        ]);

        $assessment_case = AssessmentCase::where('id', $request->id)->first();
        if (!$assessment_case) {
            return $this->failure('Assessment case not found', 404);
        }

        $form = $this->fileService->upload($request, $request->id);

        return response()->json(['data' => $form], 200);
    }

    /**
     * @OA\Get(
     *     path="/assessments-api/v1/assessment-case-files/{id}",
     *     tags={"AssessmentCaseFiles"},
     *     summary="Assessment case file download",
     *     operationId="assessmentCaseFileDownload",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Id of assessment case",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="form_name",
     *         in="query",
     *         description="Form name. Value: genogram, attachment, signature",
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
     *         description="Assessment case not found",
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
        $request->validate([
            'form_name' => ['required', Rule::in($this->fileService->getFormNames())]
        ]);

        $assessment_case = AssessmentCase::where('id', $id)->first();
        if (!$assessment_case) {
            return $this->failure('Assessment case not found', 404);
        }

        return $this->fileService->download($request, $id);
    }

    /**
     * @OA\Delete(
     *     path="/assessments-api/v1/assessment-case-files/{id}",
     *     tags={"AssessmentCaseFiles"},
     *     summary="Assessment case file delete",
     *     operationId="assessmentCaseFileDelete",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Id of assessment case",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="form_name",
     *         in="query",
     *         description="Form name. Value: genogram, attachment, signature",
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
     *         response=204,
     *         description="Successful"
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
     *         description="Assessment case not found",
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
    public function destroy(Request $request, $id)
    {
        $request->validate([
            "form_name" => ["required", Rule::in($this->fileService->getFormNames())]
        ]);

        $assessment_case = AssessmentCase::where('id', $id)->first();
        if (!$assessment_case) {
            return $this->failure('Assessment case not found', 404);
        }

        return $this->fileService->destroy($request, $id);
    }
}
