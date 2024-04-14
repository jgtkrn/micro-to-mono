<?php

namespace App\Http\Controllers;

use App\Http\Resources\FollowUpHistoryResource;
use App\Http\Services\ValidatorService;
use App\Models\FollowUpHistory;
use Illuminate\Http\Request;

class FollowUpHistoryController extends Controller
{
    public function __construct()
    {
        $this->validator = new ValidatorService();
    }

    /**
     * @OA\Get(
     *     path="/assessments-api/v1/follow-up-histories/case-id/{caseId}",
     *     operationId="getFollowUpHistoryByCaseId",
     *     summary="Get Follow Up History by caseId",    
     *     tags={"Follow Up History"},
     *     @OA\Parameter(
     *          in="path",
     *          required=true,
     *          name="caseId",
     *          description="The caseId of the Follow Up History",
     *          @OA\Schema(
     *              type="integer",
     *              example="1"
     *          )
     *     ),
     *     @OA\Response(
     *          response=200,
     *          description="Follow Up History by caseId",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="data", type="object", ref="#/components/schemas/FollowUpHistory")
     *          )
     *     ),
     *     @OA\Response(
     *          response=404,
     *          description="Follow Up History by caseId not found",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(
     *                  property="error",
     *                  type="object",
     *                  @OA\Property(property="code", type="number", example="404"),
     *                  @OA\Property(property="message", type="string", example="Cannot find Follow Up History with caseId {caseId}")
     *              )
     *          )
     *     )
     * )
     * 
     * Display the specified resource by case_id
     *
     * @param  int  $caseId
     * @return \Illuminate\Http\Response
     */
    public function getByCaseId($caseId)
    {
        $followUpHistory = FollowUpHistory::join('appointments', 'appointments.id', '=', 'follow_up_histories.appointment_id')
        ->select(
            'follow_up_histories.id as id',
            'follow_up_histories.case_id as case_id',
            'follow_up_histories.date as date',
            'follow_up_histories.time as time',
            'follow_up_histories.appointment_other_text as appointment_other_text',
            'appointments.id as appointment_id',
            'appointments.cluster as cluster',
            'follow_up_histories.type as type',
            'appointments.name_en as name_en',
            'appointments.name_sc as name_sc',
        )
        ->where('follow_up_histories.case_id', '=', $caseId)
        ->orderBy('date', 'desc')
        ->orderBy('time', 'desc')
        ->get();
     
        if (!$followUpHistory) {
            return response()->json([
                'error' => [
                    'code' => 404,
                    'message' => "Cannot find Follow Up History with case_id $caseId",
                ],
            ], 404);
        }

        return response()->json([
            'data' => new FollowUpHistoryResource($followUpHistory),
        ]);
    }

    /**
     * @OA\Get(
     *     path="/assessments-api/v1/follow-up-histories",
     *     tags={"Follow Up History"},
     *     summary="List of Follow Up History", 
     *     operationId="getFollowUpHistory",
     *
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
     *              @OA\Property(
     *                  property="id",
     *                  type="integer",
     *                  example="1"
     *              ),
     *              @OA\Property(
     *                  property="case_id",
     *                  type="integer",
     *                  example="1"
     *              ),
     *              @OA\Property(
     *                  property="date",
     *                  type="date",
     *                  example="2022-11-02"
     *              ),
     *              @OA\Property(
     *                  property="time",
     *                  type="datetime",
     *                  example="2022-11-02 06:59:00"
     *              ),
     *              @OA\Property(
     *                  property="appointment_id",
     *                  type="integer",
     *                  example="1"
     *              ),
     *              @OA\Property(
     *                  property="cluster",
     *                  type="string",
     *                  example="Hong Kong East Cluster (港島東聯網)"
     *              ),
     *              @OA\Property(
     *                  property="type",
     *                  type="string",
     *                  example="Hospital"
     *              ),
     *              @OA\Property(
     *                  property="name_en",
     *                  type="string",
     *                  example="Pamela Youde Nethersole Eastern Hospital (PYNEH)"
     *              ),
     *              @OA\Property(
     *                  property="name_sc",
     *                  type="string",
     *                  example="東區尤德夫人那打素醫院"
     *              ),
     *              @OA\Property(
     *                   property="created_at",
     *                   type="datetime",
     *                   example="2022-11-15T00:00:00Z"
     *              ),
     *              @OA\Property(
     *                   property="updated_at",
     *                   type="datetime",
     *                   example="2022-11-15T00:00:00Z"
     *              ),
     *              @OA\Property(
     *                   property="deleted_at",
     *                   type="datetime",
     *                   example="null"
     *              ),
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

        $followUpHistory = FollowUpHistory::join(
            'assessment_cases', 'assessment_cases.case_id', '=', 'follow_up_histories.case_id'
            )
            ->join('appointments', 'appointments.id', '=', 'follow_up_histories.appointment_id')
            ->select(
                'follow_up_histories.id as id',
                'assessment_cases.case_id as case_id',
                'follow_up_histories.date as date',
                'follow_up_histories.time as time',
                'follow_up_histories.appointment_other_text as appointment_other_text',
                'appointments.id as appointment_id',
                'appointments.cluster as cluster',
                'follow_up_histories.type as type',
                'appointments.name_en as name_en',
                'appointments.name_sc as name_sc',
                'follow_up_histories.created_at as created_at',
                'follow_up_histories.updated_at as updated_at',
                'follow_up_histories.deleted_at as deleted_at',
            )
            ->whereNotNull('follow_up_histories.case_id')
            ->whereNotNull('follow_up_histories.appointment_id')
            ->orderBy($sortBy, $sortDir)
            ->paginate($perPage);

        return new FollowUpHistoryResource($followUpHistory);
    }

    /**
     * @OA\Post(
     *     path="/assessments-api/v1/follow-up-histories",
     *     tags={"Follow Up History"},
     *     operationId="createFollowUpHistory",  
     *     summary="Create Follow Up History",
     * 
     *     @OA\RequestBody(
     *         required=true,
     *             @OA\JsonContent(
     *                 required={"case_id, date, time, appointment_id"},
     *                 @OA\Property(property="case_id", type="integer", example="1", description="AssessmentCase.case_id"),
     *                 @OA\Property(property="date", type="date", example="2022-10-30", description="Date"),
     *                 @OA\Property(property="time", type="datetime", example="2022-10-30 18:53:00", description="Time"),
     *                 @OA\Property(property="appointment_id", type="integer", example="1", description="Appointment.id"),
     *                 @OA\Property(property="type", type="string", example="yes"),
     *             )
     *     ),
     *     @OA\Response(
     *          response=200,
     *          description="Follow Up History created successfully",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="data", type="object", ref="#/components/schemas/FollowUpHistory")
     *          )
     *     ),
     *     @OA\Response(
     *          response=422,
     *          description="Follow Up History validation fail",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="name.required", type="string", example="The name field is required.")
     *          )
     *     ),
     *     @OA\Response(
     *          response=500,
     *          description="Failed to update Follow Up History",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(
     *                  property="error",
     *                  type="object",
     *                  @OA\Property(property="code", type="number", example="500"),
     *                  @OA\Property(property="message", type="string", example="Failed to update FollowUpHistory")
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
        $this->validator->validate_follow_up_history($request);
        $followUpHistory = FollowUpHistory::create($request->toArray());

        if (!$followUpHistory) {
            return response()->json([
                'error' => [
                    'code' => 500,
                    'message' => "Failed to create Follow Up History",
                ],
            ], 500);
        }

        return response()->json([
            'data' => new FollowUpHistoryResource($followUpHistory),
            'message' => 'Follow Up History created successfully',
            'success' => true,
        ], 200);
    }

    /**
     * @OA\Get(
     *     path="/assessments-api/v1/follow-up-histories/{followUpHistoryId}",
     *     operationId="getFollowUpHistoryDetail",
     *     summary="Get Follow Up History by followUpHistoryId",    
     *     tags={"Follow Up History"},
     *     @OA\Parameter(
     *          in="path",
     *          required=true,
     *          name="followUpHistoryId",
     *          description="The id of the Follow Up History",
     *          @OA\Schema(
     *              type="integer",
     *              example="1"
     *          )
     *     ),
     *     @OA\Response(
     *          response=200,
     *          description="Follow Up History detail",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="data", type="object", ref="#/components/schemas/FollowUpHistory")
     *          )
     *     ),
     *     @OA\Response(
     *          response=404,
     *          description="Follow Up History not found",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(
     *                  property="error",
     *                  type="object",
     *                  @OA\Property(property="code", type="number", example="404"),
     *                  @OA\Property(property="message", type="string", example="Cannot find Follow Up History with id {followUpHistoryId}")
     *              )
     *          )
     *     )
     * )
     * 
     * Display the specified resource.
     *
     * @param  int  $followUpHistoryId
     * @return \Illuminate\Http\Response
     */
    public function show($followUpHistoryId)
    {
        $followUpHistory = FollowUpHistory::find($followUpHistoryId);
     
        if (!$followUpHistory) {
            return response()->json([
                'error' => [
                    'code' => 404,
                    'message' => "Cannot find Follow Up History with id $followUpHistoryId",
                ],
            ], 404);
        }

        return response()->json([
            'data' => new FollowUpHistoryResource($followUpHistory),
        ]);
    }
    
    /**
     * @OA\Put(
     *     path="/assessments-api/v1/follow-up-histories/{followUpHistoryId}",
     *     tags={"Follow Up History"},
     *     summary="Update Follow Up History by followUpHistoryId",
     *     operationId="updateFollowUpHistory",
     *     @OA\Parameter(
     *         name="followUpHistoryId",
     *         in="path",
     *         description="Update Follow Up History by followUpHistoryId",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             example="1"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         description="Input required Follow Up History information (in string)",
     *         required=true,
     *             @OA\JsonContent(
     *                 required={"case_id, date, time, appointment_id"},
     *                 @OA\Property(property="case_id", type="integer", example="1", description="AssessmentCase.case_id"),
     *                 @OA\Property(property="date", type="date", example="2022-10-30", description="Date"),
     *                 @OA\Property(property="time", type="datetime", example="2022-10-30 18:53:00", description="Time"),
     *                 @OA\Property(property="appointment_id", type="integer", example="1", description="Appointment.id"),
     *                 @OA\Property(property="type", type="string", example="yes"),
     *             )
     *     ),
     *     @OA\Response(
     *          response=200,
     *          description="Follow Up History updated successfully",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="data", type="object", ref="#/components/schemas/FollowUpHistory")
     *          )
     *     ),
     *     @OA\Response(
     *          response=404,
     *          description="Follow Up History not found",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(
     *                  property="error",
     *                  type="object",
     *                  @OA\Property(property="code", type="number", example="404"),
     *                  @OA\Property(property="message", type="string", example="Cannot find Follow Up History with id {followUpHistoryId}")
     *              )
     *          )
     *     ),
     *     @OA\Response(
     *          response=422,
     *          description="Follow Up History validation fail",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="name.required", type="string", example="The name field is required.")
     *          )
     *     ),
     *     @OA\Response(
     *          response=500,
     *          description="Failed to update Follow Up History",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(
     *                  property="error",
     *                  type="object",
     *                  @OA\Property(property="code", type="number", example="500"),
     *                  @OA\Property(property="message", type="string", example="Failed to update Follow Up History")
     *              )
     *          )
     *     )
     * )
     * 
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $followUpHistoryId
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $followUpHistoryId)
    {
        $this->validator->validate_follow_up_history($request);
        
        $followUpHistory = FollowUpHistory::where('id', $followUpHistoryId)->first();

        if (!$followUpHistory) {
            return response()->json([
                'error' => [
                    'code' => 404,
                    'message' => "Cannot find Follow Up History with id $followUpHistoryId",
                    'success' => false,
                ],
            ], 404);
        }

        if (!$followUpHistory->update($request->toArray())) {
            return response()->json([
                'error' => [
                    'code' => 500,
                    'message' => "Failed to update FollowUpHistory",
                    'success' => false,
                ],
            ], 500);
        }

        return response()->json([
            'data' => new FollowUpHistoryResource($followUpHistory),
            'message' => 'Follow Up History updated successfully',
            'success' => true,
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/assessments-api/v1/follow-up-histories/{followUpHistoryId}",
     *     tags={"Follow Up History"},
     *     summary="Delete Follow Up History by followUpHistoryId",
     *     operationId="deleteFollowUpHistory",
     *     @OA\Parameter(
     *         name="followUpHistoryId",
     *         in="path",
     *         description="Delete Follow Up History by followUpHistoryId",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             example="1"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Follow Up History deleted successfully"
     *     ),
     *     @OA\Response(
     *          response=404,
     *          description="Follow Up History not found",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(
     *                  property="error",
     *                  type="object",
     *                  @OA\Property(property="code", type="number", example="404"),
     *                  @OA\Property(property="message", type="string", example="Cannot find Follow Up History with id {followUpHistoryId}")
     *              )
     *          )
     *     )
     * )
     * 
     * Remove the specified resource from storage.
     *
     * @param int $followUpHistoryId
     * @return \Illuminate\Http\Response
     */
    public function destroy($followUpHistoryId)
    {
        $followUpHistory = FollowUpHistory::find($followUpHistoryId);

        if (!$followUpHistory) { 
            return response()->json([
                'error' => [
                    'code' => 404,
                    'message' => "Cannot find Follow Up History with id $followUpHistoryId",
                    'success' => false,                
                ],
            ], 404);
        }

        $followUpHistory->delete();

        return response()->json([
            'data' => null,
            'message' => 'Follow Up History deleted successfully',
            'success' => true,
        ], 201);

    }
}
