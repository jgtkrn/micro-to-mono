<?php

namespace App\Http\Controllers;

use App\Http\Resources\MedicalHistoryResource;
use App\Http\Services\ValidatorService;
use App\Models\MedicalHistory;
use Illuminate\Http\Request;

class MedicalHistoryController extends Controller
{
    public function __construct()
    {
        $this->validator = new ValidatorService();
    }

    /**
     * @OA\Get(
     *     path="/assessments-api/v1/medical-histories/case-id/{caseId}",
     *     operationId="getMedicalHistoryByCaseId",
     *     summary="Get Medical History by caseId",    
     *     tags={"Medical History"},
     *     @OA\Parameter(
     *          in="path",
     *          required=true,
     *          name="caseId",
     *          description="The caseId of the Medical History",
     *          @OA\Schema(
     *              type="integer",
     *              example=1
     *          )
     *     ),
     *     @OA\Response(
     *          response=200,
     *          description="Medical History by caseId",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="data", type="object", ref="#/components/schemas/MedicalHistory")
     *          )
     *     ),
     *     @OA\Response(
     *          response=404,
     *          description="Medical History by caseId not found",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(
     *                  property="error",
     *                  type="object",
     *                  @OA\Property(property="code", type="number", example=404),
     *                  @OA\Property(property="message", type="string", example="Cannot find Medical History with caseId {caseId}")
     *              )
     *          )
     *     )
     * )
     * Display the specified resource by case_id
     *
     * @param  int  $caseId
     * @return \Illuminate\Http\Response
     */
    public function getByCaseId($caseId)
    {
        $medicalHistoryByCaseId = MedicalHistory::join(
            'assessment_cases', 'medical_histories.case_id', '=', 'assessment_cases.case_id')
        ->select(
            'medical_histories.id as id',
            'medical_histories.medical_category_name',
            'medical_histories.medical_diagnosis_name',
            'assessment_cases.case_id as case_id',
            'assessment_cases.first_assessor as first_assessor',
            'assessment_cases.second_assessor as second_assessor',
            'assessment_cases.assessment_date as assessment_date',
            'assessment_cases.start_time as start_time',
            'assessment_cases.end_time as end_time',
            'assessment_cases.status as status',
            'assessment_cases.case_type as case_type',
        )
        ->where('medical_histories.case_id', '=', $caseId)
        ->get();
     
        if (!$medicalHistoryByCaseId) {
            return response()->json([
                'error' => [
                    'code' => 404,
                    'message' => "Cannot find Medical History with case_id $caseId",
                ],
            ], 404);
        }

        return response()->json([
            'data' => $medicalHistoryByCaseId,
            'message' => 'Data found',
            'success' => true,
        ]);
    }

    /**
     * @OA\Post(
     *     path="/assessments-api/v1/medical-histories/search",
     *     tags={"Medical History"},
     *     summary="Search Medical History",
     *     operationId="searchMedicalHistory",
     *
     *     @OA\RequestBody(
     *         description="Search Medical History",
     *         required=true,
     *             @OA\JsonContent(
     *                 required={"query"},
     *                 @OA\Property(
     *                     property="query", 
     *                     type="string", 
     *                     example="Blood", 
     *                     description="Input Search Text"
     *                 )
     *             )
     *     ),
     * 
     *     @OA\Response(
     *         response=200,
     *         description="Successful"
     *     ),
     * 
     *      @OA\Response(
     *         response=400,
     *         description="Bad Request"
     *     ),
     * 
     *     @OA\Response(
     *         response=404,
     *         description="Resource not found"
     *     ),
     * )
     * 
     * Display a listing of the search resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function search(Request $request)
    {
        if ($request->has('query')) {
            $result = [];
            $query = $request->get('query');
            if (!is_null($query)) {
                
                $result = MedicalHistory::select(
                    'medical_histories.id as medical_histories_id',
                    'medical_histories.case_id as case_id',
                    'medical_histories.medical_category_name as medical_category_name',
                    'medical_histories.medical_diagnosis_name as medical_diagnosis_name',
                )
                ->where('medical_histories.medical_category_name', 'LIKE', '%'. $query. '%')
                ->orWhere('medical_histories.medical_diagnosis_name', 'LIKE', '%'. $query. '%')
                ->get();
                
                if (count($result)) {
                    return response()->json([
                        'data' => $result,
                        'message' => 'Data found',
                        'success' => true,
                    ]);
                } else {
                    return response()->json([
                        'error' => [
                            'code' => 404,
                            'message' => "No Data found",
                            'success' => false,
                        ],
                    ], 404);
                }
            } else {
                return response()->json([
                    'error' => [
                        'code' => 404,
                        'message' => "No Data found",
                        'success' => false,
                    ],
                ], 404);
            }
        } else {
            return response()->json([
                'error' => [
                    'code' => 400,
                    'message' => "query key parameter is required",
                    'success' => false
                ],
            ], 400);
        }
    }

    /**
     * @OA\Get(
     *     path="/assessments-api/v1/medical-histories",
     *     tags={"Medical History"},
     *     summary="List of Medical History", 
     *     operationId="getMedicalHistory",
     *     @OA\Parameter(
     *          in="query",
     *          name="page",
     *          description="Page number",
     *          example="1"
     *     ),
     *     @OA\Parameter(
     *          in="query",
     *          name="per_page",
     *          description="Page size (default 10)",
     *          example="10"
     *     ),
     *     @OA\Parameter(
     *          in="query",
     *          name="sort_by",
     *          description="Sort By (default: created_at), available options: id, created_at, updated_at",
     *          example="created_at"
     *     ),
     *     @OA\Parameter(
     *          in="query",
     *          name="sort_dir",
     *          description="Sort Directions (default: asc), available options: asc, desc",
     *          example="asc"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="data", 
     *                 type="array",
     *                 @OA\Items(type="object",ref="#/components/schemas/MedicalHistory")
     *             )
     *         )
     *     ),
     *      @OA\Response(
     *         response=400,
     *         description="Bad Request"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Resource not found"
     *     ),
     * )
     * 
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->validator->validate_pagination_params($request);

        $sortBy = $request->query('sort_by', 'created_at');
        $sortDir = $request->query('sort_dir', 'asc');
        $perPage = $request->query('per_page', 10);
        
        $medicalHistory = MedicalHistory::orderBy($sortBy, $sortDir)->paginate($perPage);
        
        return MedicalHistoryResource::collection($medicalHistory);
    }

    /**
     * @OA\Post(
     *     path="/assessments-api/v1/medical-histories",
     *     tags={"Medical History"},
     *     operationId="createMedicalHistory",  
     *     summary="Create Medical History",
     * 
     *     @OA\RequestBody(
     *         required=true,
     *             @OA\JsonContent(
     *                 required={"case_id, medical_category_name, medical_diagnosis_name"},
     *                 @OA\Property(property="case_id", type="integer", example="1", description="Medical History CaseID"),
     *                 @OA\Property(property="medical_category_name", type="string", example="Medical Category Name", description="Medical Category Name"),
     *                 @OA\Property(property="medical_diagnosis_name", type="string", example="Medical Diagnosis Name", description="Medical Diagnosis Name"),
     *             )
     *     ),
     *     @OA\Response(
     *          response=200,
     *          description="MedicalHistory created successfully",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="data", type="object", ref="#/components/schemas/MedicalHistory")
     *          )
     *     ),
     *     @OA\Response(
     *          response=422,
     *          description="MedicalHistory validation fail",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="name.required", type="string", example="The name field is required.")
     *          )
     *     ),
     *     @OA\Response(
     *          response=500,
     *          description="Failed to update Medical History",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(
     *                  property="error",
     *                  type="object",
     *                  @OA\Property(property="code", type="number", example="500"),
     *                  @OA\Property(property="message", type="string", example="Failed create Medical History")
     *              )
     *          )
     *     )
     * )
     * 
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validator->validate_medical_history($request);
        $medicalHistory = MedicalHistory::create($request->toArray());

        if (!$medicalHistory) {
            return response()->json([
                'error' => [
                    'code' => 500,
                    'message' => "Failed to create Medical History",
                ],
            ], 500);
        }

        return response()->json([
            'data' => new MedicalHistoryResource($medicalHistory),
            'message' => 'Medical History created successfully',
            'success' => true,
        ], 200);
    }

    /**
     * @OA\Get(
     *     path="/assessments-api/v1/medical-histories/{medicalHistoryId}",
     *     operationId="getMedicalHistoryDetail",
     *     summary="Get Medical History by medicalHistoryId",    
     *     tags={"Medical History"},
     *     @OA\Parameter(
     *          in="path",
     *          required=true,
     *          name="medicalHistoryId",
     *          description="The id of the Medical History",
     *          @OA\Schema(
     *              type="integer",
     *              example="77"
     *          )
     *     ),
     *     @OA\Response(
     *          response=200,
     *          description="MedicalHistory detail",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="data", type="object", ref="#/components/schemas/MedicalHistory")
     *          )
     *     ),
     *     @OA\Response(
     *          response=404,
     *          description="MedicalHistory not found",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(
     *                  property="error",
     *                  type="object",
     *                  @OA\Property(property="code", type="number", example="404"),
     *                  @OA\Property(property="message", type="string", example="Cannot find Medical History with id {medicalHistoryId}")
     *              )
     *          )
     *     )
     * )
     * 
     * Display the specified resource.
     *
     * @param  int  $medicalHistoryId
     * @return \Illuminate\Http\Response
     */
    public function show($medicalHistoryId)
    {
        $medicalHistory = MedicalHistory::find($medicalHistoryId);
     
        if (!$medicalHistory) {
            return response()->json([
                'error' => [
                    'code' => 404,
                    'message' => "Cannot find Medical History with id $medicalHistoryId",
                ],
            ], 404);
        }

        return response()->json([
            'data' => new MedicalHistoryResource($medicalHistory),
        ]);
    }

    /**
     * @OA\Put(
     *     path="/assessments-api/v1/medical-histories/{medicalHistoryId}",
     *     tags={"Medical History"},
     *     summary="Update Medical History by medicalHistoryId",
     *     operationId="updateMedicalHistory",
     *     @OA\Parameter(
     *         name="medicalHistoryId",
     *         in="path",
     *         description="Update Medical History by medicalHistoryId",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             example="77"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         description="Input required Medical History information (in string)",
     *         required=true,
     *             @OA\JsonContent(
     *                 required={"case_id, medical_category_name, medical_diagnosis_name"},
     *                 @OA\Property(property="case_id", type="integer", example="1", description="Medical History CaseId"),
     *                 @OA\Property(property="medical_category_name", type="string", example="Medical Category Name", description="Medical Category Name"),
     *                 @OA\Property(property="medical_diagnosis_name", type="string", example="Medical Diagnosis Name", description="Medical Diagnosis Name"),
     *             )
     *     ),
     *     @OA\Response(
     *          response=200,
     *          description="MedicalHistory updated successfully",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="data", type="object", ref="#/components/schemas/MedicalHistory")
     *          )
     *     ),
     *     @OA\Response(
     *          response=404,
     *          description="MedicalHistory not found",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(
     *                  property="error",
     *                  type="object",
     *                  @OA\Property(property="code", type="number", example="404"),
     *                  @OA\Property(property="message", type="string", example="Cannot find Medical History with id {medicalHistoryId}")
     *              )
     *          )
     *     ),
     *     @OA\Response(
     *          response=422,
     *          description="MedicalHistory validation fail",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="name.required", type="string", example="The name field is required.")
     *          )
     *     ),
     *     @OA\Response(
     *          response=500,
     *          description="Failed to update Medical History",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(
     *                  property="error",
     *                  type="object",
     *                  @OA\Property(property="code", type="number", example="500"),
     *                  @OA\Property(property="message", type="string", example="Failed to update Medical History")
     *              )
     *          )
     *     )
     * )
     * 
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $medicalHistoryId
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $medicalHistoryId)
    {
        $this->validator->validate_medical_history($request);
        
        $medicalHistory = MedicalHistory::where('id', $medicalHistoryId)->first();

        if (!$medicalHistory) {
            return response()->json([
                'error' => [
                    'code' => 404,
                    'message' => "Cannot find Medical History with id $medicalHistoryId",
                    'success' => false,
                ],
            ], 404);
        }

        if (!$medicalHistory->update($request->toArray())) {
            return response()->json([
                'error' => [
                    'code' => 500,
                    'message' => "Failed to update Medical History",
                    'success' => false,
                ],
            ], 500);
        }

        return response()->json([
            'data' => new MedicalHistoryResource($medicalHistory),
            'message' => 'Medical History updated successfully',
            'success' => true,
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/assessments-api/v1/medical-histories/{medicalHistoryId}",
     *     tags={"Medical History"},
     *     summary="Delete Medical History By medicalHistoryId",
     *     operationId="deleteMedicalHistory",
     *     @OA\Parameter(
     *         name="medicalHistoryId",
     *         in="path",
     *         description="Delete Medical History by medicalHistoryId",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             example="77"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="MedicalHistory deleted successfully"
     *     ),
     *     @OA\Response(
     *          response=404,
     *          description="MedicalHistory not found",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(
     *                  property="error",
     *                  type="object",
     *                  @OA\Property(property="code", type="number", example="404"),
     *                  @OA\Property(property="message", type="string", example="Cannot find Medical History with id {medicalHistoryId}")
     *              )
     *          )
     *     )
     * )
     * 
     * Remove the specified resource from storage.
     *
     * @param int $medicalHistoryId
     * @return \Illuminate\Http\Response
     */
    public function destroy($medicalHistoryId)
    {
        $medicalHistory = MedicalHistory::find($medicalHistoryId);

        if (!$medicalHistory) { 
            return response()->json([
                'error' => [
                    'code' => 404,
                    'message' => "Cannot find Medical History with id $medicalHistoryId",
                    'success' => false,                
                ],
            ], 404);
        }

        $medicalHistory->delete();

        return response()->json([
            'data' => null,
            'message' => 'Medical History deleted successfully',
            'success' => true,
        ], 200);

    }
}
