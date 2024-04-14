<?php

namespace App\Http\Controllers;

use App\Models\SatisfactionEvaluationForm;
use Illuminate\Http\Request;
use App\Traits\RespondsWithHttpStatus;
use Carbon\Carbon;

class SatisfactionEvaluationFormController extends Controller
{
    /**
     * @OA\Get(
     *     path="/assessments-api/v1/satisfaction-evaluation",
     *     tags={"SatisfactionEvaluationForm"},
     *     summary="Get satisfaction-evaluation-form",
     *     operationId="satisfactionEvaluationFormGet",
     *     @OA\Parameter(
     *         name="case_id",
     *         in="query",
     *         description="Case id from elder case",
     *         required=false,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     
     *     @OA\Response(
     *         response=200,
     *         description="Successful",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", type="object", ref="#/components/schemas/SatisfactionEvaluationForm")
     *         )
     *     ),
     * 
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
        $satisfactionEvaluationForm = SatisfactionEvaluationForm::orderBy('updated_at', 'desc')->get();

        if($request->query('case_id')){
            $satisfactionEvaluationForm = SatisfactionEvaluationForm::where('case_id', $request->query('case_id'))->latest('updated_at')->first();
        }

        if($satisfactionEvaluationForm){
            return response()->json([
                'data' => $satisfactionEvaluationForm,
                'message' => 'Data found.'
            ], 200);
        }

        return response()->json([
            'data' => [],
            'message' => 'Data not found'
        ], 404);
        
    }

    /**
     * @OA\Post(
     *     path="/assessments-api/v1/satisfaction-evaluation",
     *     tags={"SatisfactionEvaluationForm"},
     *     summary="Post satisfaction-evaluation-form",
     *     operationId="satisfactionEvaluationFormStore",
     *
     *     @OA\RequestBody(
     *         required=true,
     *             @OA\JsonContent(
     *                  @OA\Property( property="assessor_name", type="string", example="John Doe" ),
     *                  @OA\Property( property="elder_reference_number", type="string", example="WP20001" ),
     *                  @OA\Property( property="evaluation_date", type="date", example="2022-11-15" ),
     *                  @OA\Property( property="clear_plan", type="integer", example=1 ),
     *                  @OA\Property( property="case_id", type="integer", example=1 ),
     *                  @OA\Property( property="enough_discuss_time", type="integer", example=1 ),
     *                  @OA\Property( property="appropriate_plan", type="integer", example=1 ),
     *                  @OA\Property( property="has_discussion_team", type="integer", example=1 ),
     *                  @OA\Property( property="own_involved", type="integer", example=1 ),
     *                  @OA\Property( property="enough_opportunities", type="integer", example=1 ),
     *                  @OA\Property( property="enough_information", type="integer", example=1 ),
     *                  @OA\Property( property="selfcare_improved", type="integer", example=1 ),
     *                  @OA\Property( property="confidence_team", type="integer", example=1 ),
     *                  @OA\Property( property="feel_respected", type="integer", example=1 ),
     *                  @OA\Property( property="performance_rate", type="integer", example=1 ),
     *                  @OA\Property( property="service_scale", type="integer", example=1 ),
     *                  @OA\Property( property="recommend_service", type="integer", example=1 ),
     *             )
     *     ),
     * 
     *     @OA\Response(
     *         response=201,
     *         description="Successful",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", type="object", ref="#/components/schemas/SatisfactionEvaluationForm")
     *         )
     *     ),
     * 
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
     * Display a listing of the search resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $satisfactionEvaluationForm = SatisfactionEvaluationForm::create([
                'elder_reference_number' => $request->elder_reference_number,
                'assessor_name' => $request->assessor_name,
                'evaluation_date' => $request->evaluation_date ? new Carbon($request->evaluation_date) : null,
                'case_id' => $request->case_id,
                'clear_plan' => $request->clear_plan,
                'enough_discuss_time' => $request->enough_discuss_time,
                'appropriate_plan' => $request->appropriate_plan,
                'has_discussion_team' => $request->has_discussion_team,
                'own_involved' => $request->own_involved,
                'enough_opportunities' => $request->enough_opportunities,
                'enough_information' => $request->enough_information,
                'selfcare_improved' => $request->selfcare_improved,
                'confidence_team' => $request->confidence_team,
                'feel_respected' => $request->feel_respected,
                'performance_rate' => $request->performance_rate,
                'service_scale' => $request->service_scale,
                'recommend_service' => $request->recommend_service,
                
                // user data
                'created_by' => $request->user_id,
                'updated_by' => $request->user_id,
                'created_by_name' => $request->user_name,
                'updated_by_name' => $request->user_name,
        ]);

        if($satisfactionEvaluationForm){
            return response()->json([
                'data' => $satisfactionEvaluationForm,
                'message' => 'Data found.'
            ], 201);
        }

        return response()->json([
            'data' => [],
            'message' => 'Data not found'
        ], 404);
    }

    /**
     * @OA\Get(
     *     path="/assessments-api/v1/satisfaction-evaluation/{id}",
     *     tags={"SatisfactionEvaluationForm"},
     *     summary="Get satisfaction-evaluation-form by id",
     *     operationId="satisfactionEvaluationFormById",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Id of satisfaction evaluation",
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
     *             @OA\Property(property="data", type="object", ref="#/components/schemas/SatisfactionEvaluationForm")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Satisfaction evaluation not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="object",
     *                 @OA\Property(property="code", type="integer", description="Status code", example="404"),
     *                 @OA\Property(property="message", type="string", example="Satisfaction evaluation not found"),
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
        $satisfactionEvaluationForm = SatisfactionEvaluationForm::where('id', $id)->first();

        if($satisfactionEvaluationForm){
            return response()->json([
                'data' => $satisfactionEvaluationForm,
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
     *     path="/assessments-api/v1/satisfaction-evaluation/{id}",
     *     tags={"SatisfactionEvaluationForm"},
     *     summary="Get satisfaction-evaluation-form update",
     *     operationId="satisfactionEvaluationFormByUpdate",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Id of satisfaction evaluation",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *             @OA\JsonContent(
     *                  @OA\Property( property="assessor_name", type="string", example="John Doe" ),
     *                  @OA\Property( property="elder_reference_number", type="string", example="WP20001" ),
     *                  @OA\Property( property="evaluation_date", type="date", example="2022-11-15" ),
     *                  @OA\Property( property="clear_plan", type="integer", example=1 ),
     *                  @OA\Property( property="case_id", type="integer", example=1 ),
     *                  @OA\Property( property="enough_discuss_time", type="integer", example=1 ),
     *                  @OA\Property( property="appropriate_plan", type="integer", example=1 ),
     *                  @OA\Property( property="has_discussion_team", type="integer", example=1 ),
     *                  @OA\Property( property="own_involved", type="integer", example=1 ),
     *                  @OA\Property( property="enough_opportunities", type="integer", example=1 ),
     *                  @OA\Property( property="enough_information", type="integer", example=1 ),
     *                  @OA\Property( property="selfcare_improved", type="integer", example=1 ),
     *                  @OA\Property( property="confidence_team", type="integer", example=1 ),
     *                  @OA\Property( property="feel_respected", type="integer", example=1 ),
     *                  @OA\Property( property="performance_rate", type="integer", example=1 ),
     *                  @OA\Property( property="service_scale", type="integer", example=1 ),
     *                  @OA\Property( property="recommend_service", type="integer", example=1 ),
     *             )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", type="object", ref="#/components/schemas/SatisfactionEvaluationForm")
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
     *         description="Satisfaction evaluation not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="object",
     *                 @OA\Property(property="code", type="integer", description="Status code", example="404"),
     *                 @OA\Property(property="message", type="string", example="Satisfaction evaluation not found"),
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
    public function update(Request $request, SatisfactionEvaluationForm $satisfactionEvaluationForm)
    {
        $satisfactionEvaluationForm = SatisfactionEvaluationForm::where('id', $id)->first();

        if($satisfactionEvaluationForm){
            $updated = $satisfactionEvaluationForm->update([
                'elder_reference_number' => $request->elder_reference_number ? $request->elder_reference_number : $satisfactionEvaluationForm->elder_reference_number,
                'assessor_name' => $request->assessor_name ? $request->assessor_name : $satisfactionEvaluationForm->assessor_name,
                'evaluation_date' => $request->evaluation_date ? new Carbon($request->evaluation_date) : $satisfactionEvaluationForm->evaluation_date,
                'case_id' => $request->case_id ? $request->case_id : $satisfactionEvaluationForm->case_id,
                'clear_plan' => $request->clear_plan ? $request->clear_plan : $satisfactionEvaluationForm->clear_plan,
                'enough_discuss_time' => $request->enough_discuss_time ? $request->enough_discuss_time : $satisfactionEvaluationForm->enough_discuss_time,
                'appropriate_plan' => $request->appropriate_plan ? $request->appropriate_plan : $satisfactionEvaluationForm->appropriate_plan,
                'has_discussion_team' => $request->has_discussion_team ? $request->has_discussion_team : $satisfactionEvaluationForm->has_discussion_team,
                'own_involved' => $request->own_involved ? $request->own_involved : $satisfactionEvaluationForm->own_involved,
                'enough_opportunities' => $request->enough_opportunities ? $request->enough_opportunities : $satisfactionEvaluationForm->enough_opportunities,
                'enough_information' => $request->enough_information ? $request->enough_information : $satisfactionEvaluationForm->enough_information,
                'selfcare_improved' => $request->selfcare_improved ? $request->selfcare_improved : $satisfactionEvaluationForm->selfcare_improved,
                'confidence_team' => $request->confidence_team ? $request->confidence_team : $satisfactionEvaluationForm->confidence_team,
                'feel_respected' => $request->feel_respected ? $request->feel_respected : $satisfactionEvaluationForm->feel_respected,
                'performance_rate' => $request->performance_rate ? $request->performance_rate : $satisfactionEvaluationForm->performance_rate,
                'service_scale' => $request->service_scale ? $request->service_scale : $satisfactionEvaluationForm->service_scale,
                'recommend_service' => $request->recommend_service ? $request->recommend_service : $satisfactionEvaluationForm->recommend_service,
                
                // user data
                'updated_by' => $request->user_id,
                'updated_by_name' => $request->user_name,
            ]);
            if($updated){
                $satisfactionEvaluationForm = SatisfactionEvaluationForm::where('id', $id)->first();
                return response()->json([
                    'data' => $satisfactionEvaluationForm,
                    'message' => 'Data updated.'
                ], 202);
            }
            return response()->json([
                'data' => null,
                'message' => 'Data update failed'
            ], 409);
        }

        return response()->json([
            'data' => null,
            'message' => 'Data not found'
        ], 404);
        
    }

    /**
     * @OA\Delete(
     *     path="/assessments-api/v1/satisfaction-evaluation/{id}",
     *     tags={"SatisfactionEvaluationForm"},
     *     summary="Get satisfaction-evaluation-form delete",
     *     operationId="satisfactionEvaluationFormByDelete",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Satisfaction evaluation id to be deleted",
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
     *         description="Satisfaction evaluation not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="object",
     *                 @OA\Property(property="code", type="integer", description="Status code", example="404"),
     *                 @OA\Property(property="message", type="string", example="Satisfaction evaluation not found"),
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
        $satisfactionEvaluationForm = SatisfactionEvaluationForm::where('id', $id)->first();

        if($satisfactionEvaluationForm){
            $satisfactionEvaluationForm->delete();
            return response()->json([
                'data' => null,
                'message' => 'Data deleted.'
            ], 204);
        }

        return response()->json([
            'data' => null,
            'message' => 'Data not found'
        ], 404);
    }
}
