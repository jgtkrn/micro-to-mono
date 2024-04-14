<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AssessmentCase;
use App\Http\Services\ExternalService;

class ElderProfile extends Controller
{
    private $externalService;
    public function __construct()
    {
        $this->externalService = new ExternalService();
    }
    /**
     * @OA\Get(
     *     path="/assessments-api/v1/elder-profile",
     *     tags={"ElderProfile"},
     *     summary="Elder profile list",
     *     operationId="elderProfileList",
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
     *             @OA\Property(
     *             )
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
    public function index(Request $request)
    {
        $case_id = $request->query('case_id');
        $case_type = $request->query('case_type');
        $data = null;
        if($case_id && $case_type){
            $regex_base = "/baseline/i";
            $regex_hc = "/hc/i";
            $regex_bzn = "/bzn/i";
            $regex_nurse = "/nurse/i";
            if(preg_match($regex_hc, $case_type) > 0){
                $data = $this->externalService->getCgaHcData($case_id);
            }
            if(preg_match($regex_bzn, $case_type) > 0){
                $data = $this->externalService->getBznData($case_id);
            }
            if(preg_match($regex_nurse, $case_type) > 0){
                $data = $this->externalService->getCgaNurseData($case_id);
            }
            if(preg_match($regex_base, $case_type) > 0){
                $data = $this->externalService->getCgaBaseData($case_id);
            }
        }
        if($data != null){
            return response()->json([
                'data' => $data,
            ], 200);
        }
        return response()->json([
            'data' => null,
            'message' => 'Data not Found'
        ], 404);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
