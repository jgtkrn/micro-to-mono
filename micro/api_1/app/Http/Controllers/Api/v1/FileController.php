<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Traits\RespondsWithHttpStatus;
use App\Http\Services\FileService;
use Illuminate\Http\Request;
use App\Models\File;

class FileController extends Controller
{
    use RespondsWithHttpStatus;
    private $fileService;

    public function __construct()
    {
        $this->fileService = new FileService();
    }

    /**
     * @OA\Post(
     *     path="/appointments-api/v1/appointments/files",
     *     tags={"Attachments"},
     *     summary="File upload",
     *     operationId="uploadFile",
     *     @OA\Response(
     *         response=200,
     *         description="Successful",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", type="object", ref="#/components/schemas/File")
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
     *                         @OA\Property(property="field", type="string", description="Field name", example="title"),
     *                         @OA\Property(property="message", type="string", description="Error message of the field", example="The title field is required")
     *                     ) 
     *                 )
     *             )
     *         )
     *     ),
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
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function upload(Request $request)
    {
        $request->validate([
            'file' => ['required', 'max:12288', 'mimes:jpg,jpeg,png,pdf'] //12MB
        ]);
        $results = $this->fileService->upload($request);
        return $this->success($results);
    }

    /**
     * @OA\Get(
     *     path="/appointments-api/v1/appointments/files/{id}",
     *     tags={"Attachments"},
     *     summary="Download file by Id",
     *     operationId="downloadFile",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="File Id to be downloaded",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful",
     *         @OA\MediaType(
     *             mediaType="* / *",
     *          @OA\Schema(type="string", format="binary")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Attachment file not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="object",
     *                 @OA\Property(property="code", type="integer", description="Status code", example="404"),
     *                 @OA\Property(property="message", type="string", example="File not found"),
     *                 @OA\Property(property="errors", type="array", description="Empty array", example="[]",
     *                     @OA\Items()
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function download($id)
    {
        $file = File::find($id);
        if (!$file) {
            return $this->failure('Attachment file not found', 404);
        }
        return $this->fileService->download($file);
    }
}
