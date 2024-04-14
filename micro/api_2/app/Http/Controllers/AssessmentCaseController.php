<?php

namespace App\Http\Controllers;

use App\Http\Resources\AssessmentCaseResource;
use App\Models\AssessmentCase;
use App\Traits\RespondsWithHttpStatus;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Services\ExternalService;

class AssessmentCaseController extends Controller
{
    use RespondsWithHttpStatus;

    private $externalService;

    public function __construct()
    {
        $this->externalService = new ExternalService();
    }

    /**
     * @OA\Get(
     *     path="/assessments-api/v1/assessment-cases",
     *     tags={"AssessmentCase"},
     *     summary="Get asssessment case by case id",
     *     operationId="assessmentCaseByCaseId",
     *     @OA\Parameter(
     *         name="case_id",
     *         in="query",
     *         description="Case id of assessment case",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", type="object", ref="#/components/schemas/AssessmentCase")
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
     *                         @OA\Property(property="field", type="string", description="Field name", example="case_id"),
     *                         @OA\Property(property="message", type="string", description="Error message of the field", example="The case id field is required")
     *                     ) 
     *                 )
     *             )
     *         )
     *     )
     * )
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if($request->is_other && $request->access_role !== 'admin'){
            return response()->json([
                'data' => null,
                'message' => 'User not in any team access'
            ], 401);
        }
        $request->validate([
            'case_id' => 'required'
        ]);

        $case_id = $request->query('case_id');

        $assessment_case = AssessmentCase::where('case_id', $case_id)
            ->first();

        if (!$assessment_case) {
            return $this->success(null);
        }

        $loaded_assessment_case = $this->load_form_by_case_type($assessment_case);
        return new AssessmentCaseResource($loaded_assessment_case);
    }

    /**
     * @OA\Post(
     *     path="/assessments-api/v1/assessment-cases",
     *     tags={"AssessmentCase"},
     *     summary="Store new assessment case",
     *     operationId="assessmentCaseStore",
     *     @OA\RequestBody(
     *         description="Input assessment case information (in json)",
     *         required=true,
     *             @OA\JsonContent(
     *                 required={"case_id"},
     *                 @OA\Property(property="case_id", type="integer", example="1"),
     *                 @OA\Property(property="first_assessor", type="integer", example=1),
     *                 @OA\Property(property="second_assessor", type="integer", example=2),
     *              @OA\Property(property="priority_level", type="integer", example=1),
     *                 @OA\Property(property="assessment_date", type="string", format="date", example="2022-05-13"),
     *                 @OA\Property(property="start_time", type="string", format="time", example="2022-05-13T00:00:00Z"),
     *                 @OA\Property(property="end_time", type="string", format="time", example="2022-05-13T00:00:00Z"),
     *                 @OA\Property(property="status", type="string", example="submitted")
     *             )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Successful",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", type="object", ref="#/components/schemas/AssessmentCase")
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
     *                         @OA\Property(property="field", type="string", description="Field name", example="case_id"),
     *                         @OA\Property(property="message", type="string", description="Error message of the field", example="The case id field is required")
     *                     ) 
     *                 )
     *             )
     *         )
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
        
        if($request->is_other && $request->access_role !== 'admin'){
            return response()->json([
                'data' => null,
                'message' => 'User not in any team access'
            ], 401);
        }
        $request->validate([
            'case_id' => 'required'
        ]);

        //check if assessment already exist
        $assessment_case_exist = AssessmentCase::where('case_id', $request->case_id)->first();
        if ($assessment_case_exist) {
            return $this->failure('Case Id already exist', 422);
        }

        $case_type = $this->externalService->getElderCaseId($request->case_id);
        if ($case_type == null) {
            return $this->failure('Elder case id not exist', 422);
        }

        $assessment_case = AssessmentCase::create([
            'case_id' => $request->case_id,
            'case_type' => $case_type,
            'first_assessor' => $request->first_assessor,
            'second_assessor' => $request->second_assessor,
            'assessment_date' => $request->assessment_date,
            'priority_level' => $request->priority_level,
            'start_time' => $request->start_time ? new Carbon($request->start_time) : null,
            'end_time' => $request->end_time ? new Carbon($request->end_time) : null,
            'status' => $request->status
        ]);
        return new AssessmentCaseResource($assessment_case);
    }

    /**
     * @OA\Get(
     *     path="/assessments-api/v1/assessment-cases/{id}",
     *     tags={"AssessmentCase"},
     *     summary="Get asssessment case by id",
     *     operationId="assessmentCaseById",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Id of assessment case",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", type="object", ref="#/components/schemas/AssessmentCase")
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
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {

        if($request->is_other && $request->access_role !== 'admin'){
            return response()->json([
                'data' => null,
                'message' => 'User not in any team access'
            ], 401);
        }
        $assessment_case = AssessmentCase::where('id', $id)
            ->first();

        if (!$assessment_case) {
            return $this->failure('Assessment case not found', 404);
        }

        $loaded_assessment_case = $this->load_form_by_case_type($assessment_case);
        return new AssessmentCaseResource($loaded_assessment_case);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
    }

    /**
     * @OA\Put(
     *     path="/assessments-api/v1/assessment-cases/{id}",
     *     tags={"AssessmentCase"},
     *     summary="Update assessment case",
     *     operationId="assessmentCaseUpdate",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Id of assessment case",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         description="Input assessment case information (in json)",
     *         required=true,
     *             @OA\JsonContent(
     *                 required={"case_id"},
     *                 @OA\Property(property="case_id", type="integer", example="1"),
     *                 @OA\Property(property="first_assessor", type="integer", example=1),
     *                 @OA\Property(property="second_assessor", type="integer", example=2),
     *                 @OA\Property(property="priority_level", type="integer", example=1),
     *                 @OA\Property(property="assessment_date", type="string", format="date", example="2022-05-13"),
     *                 @OA\Property(property="start_time", type="string", format="time", example="2022-05-13T00:00:00Z"),
     *                 @OA\Property(property="end_time", type="string", format="time", example="2022-05-13T00:00:00Z"),
     *                 @OA\Property(property="status", type="string", example="submitted")
     *             )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", type="object", ref="#/components/schemas/AssessmentCase")
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
     *                         @OA\Property(property="field", type="string", description="Field name", example="case_id"),
     *                         @OA\Property(property="message", type="string", description="Error message of the field", example="The case id field is required")
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
     * 
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if($request->is_other && $request->access_role !== 'admin'){
            return response()->json([
                'data' => null,
                'message' => 'User not in any team access'
            ], 401);
        }
        $is_manager = ($request->user_role == 'manager' || $request->user_role == 'admin') ? true : false;
        $weeks_ago = Carbon::now()->subDays(7);
        $two_weeks_ago = Carbon::now()->subDays(14);
        $request->validate([
            'case_id' => 'required'
        ]);

        $assessment_case = AssessmentCase::where('id', $id)->first();
        if (!$assessment_case) {
            return $this->failure('Assessment case not found', 404);
        }

        if(($assessment_case->case_type === 'BZN' && !$request->is_bzn) && !$is_manager){
            return response()->json([
                'data' => null,
                'message' => 'You are not allowed to update, because not in BZN teams.'
            ], 401);
        }

        if(
            (!$is_manager && new Carbon($assessment_case->end_time) < $weeks_ago) &&
            ($request->is_bzn && new Carbon($assessment_case->end_time) < $two_weeks_ago)
        ){
            return response()->json([
                'data' => null,
                'message' => 'You are not allowed to update.'
            ], 401);   
        }

        if(
            (!$is_manager && new Carbon($assessment_case->end_time) < $weeks_ago) &&
            (
                ($request->is_hcw || $request->is_hcsw) && 
                new Carbon($assessment_case->end_time) < $two_weeks_ago
            )
        ){
            return response()->json([
                'data' => null,
                'message' => 'You are not allowed to update.'
            ], 401);   
        }

        $updated = $assessment_case->update([
            'case_id' => $request->case_id ? $request->case_id : $assessment_case->case_id,
            'first_assessor' => $request->first_assessor,
            'second_assessor' => $request->second_assessor,
            'assessment_date' => $request->assessment_date,
            'priority_level' => $request->priority_level,
            'start_time' => $request->start_time ? new Carbon($request->start_time) : $assessment_case->start_time,
            'end_time' => $request->end_time ? new Carbon($request->end_time) : null,
            'status' => $request->status,
        ]);

        if (!$updated) {
            return $this->failure('Failed to update assessment case');
        }

        return new AssessmentCaseResource($assessment_case);
    }

    /**
     * @OA\Delete(
     *     path="/assessments-api/v1/assessment-cases/{id}",
     *     tags={"AssessmentCase"},
     *     summary="Delete assessment case by Id",
     *     operationId="assessmentCaseDelete",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Assessment case id to be deleted",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Successful"
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
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $assessment_case = AssessmentCase::where('id', $id)->first();

        if (!$assessment_case) {
            return $this->failure('Assessment case not found', 404);
        }

        $assessment_case->delete();

        return response(null, 204);
    }

    private function load_form_by_case_type($assessment_case)
    {
        if ($assessment_case->case_type == 'BZN') {
            return $assessment_case->where('id', $assessment_case->id)
                ->with('physiologicalMeasurementForm')
                ->with('physicalConditionForm')
                ->with('rePhysiologicalMeasurementForm')
                ->with('medicalConditionForm')
                ->with('lubbenSocialNetworkScaleForm')
                ->with('socialBackgroundForm')
                ->with('medicationAdherenceForm')
                ->with('functionMobilityForm')
                ->with('barthelIndexForm')
                ->with('geriatricDepressionScaleForm')
                ->with('iadlForm')
                ->with('genogramForm')
                ->with('montrealCognitiveAssessmentForm')
                ->with('assessmentCaseStatus')
                ->with('assessmentCaseAttachment')
                ->with('assessmentCaseSignature')
                ->first();
        } else {
            return $assessment_case->where('id', $assessment_case->id)
                ->with('qualtricsForm')
                ->with('montrealCognitiveAssessmentForm')
                ->with('socialWorkerForm')
                ->with('assessmentCaseStatus')
                ->with('assessmentCaseAttachment')
                ->with('assessmentCaseSignature')
                ->first();
        }
    }

    public function checkTeams(Request $request){
        $teams = $request->user_teams;
        $isCga = $request->is_cga;
        $isBzn = $request->is_bzn;
        return ['cga' => $isCga, 'isBzn' => $isBzn, 'teams' => $teams];
    } 
}
