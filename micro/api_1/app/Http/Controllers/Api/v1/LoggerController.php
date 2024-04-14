<?php

namespace App\Http\Controllers\Api\v1;

use Illuminate\Http\Request;
use App\Models\RouteLogger;
use App\Traits\RespondsWithHttpStatus;
use App\Http\Controllers\Controller;

class LoggerController extends Controller
{
    use RespondsWithHttpStatus;
    /**
     * @OA\Get(
     *     path="/appoinments-api/v1/load-logger",
     *     tags={"LoadLogger"},
     *     summary="Load logger list",
     *     operationId="loadLoggerList",
     *     @OA\Parameter(
     *         name="case_id",
     *         in="query",
     *         description="id of eder case",
     *         @OA\Schema(
     *             type="integer",
     *             format="int32"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="case_type",
     *         in="query",
     *         description="elder case type",
     *         @OA\Schema(
     *             type="integer",
     *             format="int32"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful",
     *         @OA\JsonContent(
     *             type="object",
     *              @OA\Property(property="data", type="object", ref="#/components/schemas/RouteLogger")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Data not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="object",
     *                 @OA\Property(property="code", type="integer", description="Status code", example="422"),
     *                 @OA\Property(property="message", type="string", example=""),
     *                 @OA\Property(property="errors", type="array",
     *                     @OA\Items(type="object",
     *                         @OA\Property(property="field", type="string", description="Field name", example="case_id"),
     *                         @OA\Property(property="message", type="string", description="Error message of the field", example="The case_id field is required")
     *                     ) 
     *                 )
     *             )
     *         )
     *     )
     * )
     *
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = RouteLogger::orderBy('created_at', 'desc')->paginate(10);
        return $data;   
    }
}
