<?php

namespace App\Http\Controllers;

use App\Models\CoachingPam;
use Illuminate\Http\Request;

class CoachingPamController extends Controller
{
 /**
     * @OA\Get(
     *     path="/assessments-api/v1/coaching-pam",
     *     tags={"CoachingPam"},
     *     summary="Get coaching-pam by or not by care plan id",
     *     operationId="coachingPamByCarePlanId",
     *     @OA\Parameter(
     *         name="care_plan_id",
     *         in="query",
     *         description="id of care plan",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", type="object", ref="#/components/schemas/CoachingPam")
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
        $coachingPam = CoachingPam::orderBy('updated_at', 'desc')->get();
        if($request->query('care_plan_id')){
            $coachingPam = CoachingPam::where('care_plan_id', $request->query('care_plan_id'))->get();
        }
        if($coachingPam){
            return response()->json([
                'data' => $coachingPam,
                'message' => 'Data found.'
            ], 200);
        }

        return response()->json([
            'data' => [],
            'message' => 'Data not found'
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
     * @OA\Get(
     *     path="/assessments-api/v1/coaching-pam/{id}",
     *     tags={"CoachingPam"},
     *     summary="Get coaching pam by id",
     *     operationId="coachingPamById",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Id of coaching pam",
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
     *             @OA\Property(property="data", type="object", ref="#/components/schemas/CoachingPam")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Data not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="object",
     *                 @OA\Property(property="code", type="integer", description="Status code", example="404"),
     *                 @OA\Property(property="message", type="string", example="Care plan not found"),
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
    public function show($id)
    {
        $coachingPam = CoachingPam::where('id', $id)->first();
        if($coachingPam){
            return response()->json([
                'data' => $coachingPam,
                'message' => 'Data found.'
            ], 200);
        }

        return response()->json([
            'data' => null,
            'message' => 'Data not found'
        ], 404);   
    }

     /**
     * @OA\Put(
     *     path="/assessments-api/v1/coaching-pam",
     *     tags={"CoachingPam"},
     *     summary="Upsert Coaching Pam",
     *     operationId="coachingPamUpsert",
     *     @OA\Parameter(
     *         name="care_plan_id",
     *         in="query",
     *         description="Id of care plan",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         description="Input care plan information (in json)",
     *         required=true,
     *             @OA\JsonContent(
     *                  @OA\Property(property="section", description="PAM top section", type="integer", example=1),
     *                  @OA\Property(property="intervention_group", description="PAM top intervention group", type="integer", example=1),
     *                  @OA\Property(property="gender", description="PAM top gender", type="integer", example=1),
     *                  @OA\Property(property="health_manage", description="PAM number 1", type="integer", example=1),
     *                  @OA\Property(property="active_role", description="PAM number 2", type="integer", example=1),
     *                  @OA\Property(property="self_confidence", description="PAM number 3", type="integer", example=1),
     *                  @OA\Property(property="drug_knowledge", description="PAM number 4", type="integer", example=1),
     *                  @OA\Property(property="self_understanding", description="PAM number 5", type="integer", example=1),
     *                  @OA\Property(property="self_health", description="PAM number 6", type="integer", example=1),
     *                  @OA\Property(property="self_discipline", description="PAM number 7", type="integer", example=1),
     *                  @OA\Property(property="issue_knowledge", description="PAM number 8", type="integer", example=1),
     *                  @OA\Property(property="other_treatment", description="PAM number 9", type="integer", example=1),
     *                  @OA\Property(property="change_treatment", description="PAM number 10", type="integer", example=1),
     *                  @OA\Property(property="issue_prevention", description="PAM number 11", type="integer", example=1),
     *                  @OA\Property(property="find_solutions", description="PAM number 12", type="integer", example=1),
     *                  @OA\Property(property="able_maintain", description="PAM number 13", type="integer", example=1),
     *                  @OA\Property(property="remarks", description="PAM remarks", type="string", example="yes"),
     *             )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", type="object", ref="#/components/schemas/CoachingPam")
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
     *         description="Data not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="object",
     *                 @OA\Property(property="code", type="integer", description="Status code", example="404"),
     *                 @OA\Property(property="message", type="string", example="Care plan not found"),
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
    public function update(Request $request)
    {
        $request->validate(['care_plan_id' => 'required|exists:care_plans,id']);
        $care_plan_id = $request->query('care_plan_id') ? $request->query('care_plan_id') : $request->care_plan_id;
        if($care_plan_id){        
            CoachingPam::updateOrCreate(
                ['care_plan_id' => $care_plan_id], 
                [
                    'care_plan_id' => $care_plan_id,
                    'section' => $request->section,
                    'intervention_group' => $request->intervention_group,
                    'gender' => $request->gender,
                    'health_manage' => $request->health_manage,
                    'active_role' => $request->active_role,
                    'self_confidence' => $request->self_confidence,
                    'drug_knowledge' => $request->drug_knowledge,
                    'self_understanding' => $request->self_understanding,
                    'self_health' => $request->self_health,
                    'self_discipline' => $request->self_discipline,
                    'issue_knowledge' => $request->issue_knowledge,
                    'other_treatment' => $request->other_treatment,
                    'change_treatment' => $request->change_treatment,
                    'issue_prevention' => $request->issue_prevention,
                    'find_solutions' => $request->find_solutions,
                    'able_maintain' => $request->able_maintain,
                    'remarks' => $request->remarks   
                ]
            );
            $coachingPam = CoachingPam::where('care_plan_id', $care_plan_id)->first();
            if($coachingPam){
                return response()->json([
                    'data' => $coachingPam,
                    'message' => 'Data found.'
                ], 200);
            }
        }

        return response()->json([
            'data' => null,
            'message' => 'Data not found'
        ], 404);
    }

    /**
     * @OA\Delete(
     *     path="/assessments-api/v1/coaching-pam/{id}",
     *     tags={"CoachingPam"},
     *     summary="Delete coaching pam by Id",
     *     operationId="coachingPamDelete",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Coaching pam id to be deleted",
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
     *         description="Data not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="object",
     *                 @OA\Property(property="code", type="integer", description="Status code", example="404"),
     *                 @OA\Property(property="message", type="string", example="Care plan not found"),
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
        $coachingPam = CoachingPam::where('id', $id)->first();
        if(!$coachingPam){
            return response()->json([
                'data' => null,
                'message' => 'Data not found'
            ], 404);
        }
        CoachingPam::where('id', $id)->delete();
        return response()->json([
            'data' => null,
            'message' => 'No content'
        ], 204); 
    }
}
