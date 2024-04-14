<?php

namespace App\Http\Controllers;

use App\Models\CaseManager;
use App\Models\CarePlan;
use Illuminate\Http\Request;
use App\Http\Resources\CaseManagerResource;
use App\Traits\RespondsWithHttpStatus;

class CaseManagerController extends Controller
{
    use RespondsWithHttpStatus;
    /**
     * @OA\Get(
     *     path="/assessments-api/v1/assigned-case-managers",
     *     tags={"CaseManager"},
     *     summary="Get case manager by care plan id",
     *     operationId="caseManagerByCarePlanId",
     *     @OA\Parameter(
     *         name="care_plan_id",
     *         in="query",
     *         description="Id of care plan",
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
     *             @OA\Property(
     *                  property="data",
     *                  type="array",
     *                  collectionFormat="multi",
     *                  @OA\Items(
     *                     type="object",
     *                     ref="#/components/schemas/CaseManager"
     *                  )
     *              )
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
     *                         @OA\Property(property="field", type="string", description="Field name", example="care_plan_id"),
     *                         @OA\Property(property="message", type="string", description="Error message of the field", example="The care plan id field is required")
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
        $request->validate([
            'care_plan_id' => 'required|integer|exists:care_plans,id,deleted_at,NULL'
        ]);
        $care_plan_id = $request->query('care_plan_id');
        $case_managers = CaseManager::where('care_plan_id', $care_plan_id)->get();
        return CaseManagerResource::collection($case_managers);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
     * @param  \App\Models\CaseManager  $caseManager
     * @return \Illuminate\Http\Response
     */
    public function show(CaseManager $caseManager)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\CaseManager  $caseManager
     * @return \Illuminate\Http\Response
     */
    public function edit(CaseManager $caseManager)
    {
        //
    }

    /**
     * @OA\Put(
     *     path="/assessments-api/v1/assigned-case-managers",
     *     tags={"CaseManager"},
     *     summary="Upsert case managers",
     *     operationId="caseManagersUpsert",
     *     @OA\RequestBody(
     *          description="Input case managers array (in json)",
     *             @OA\JsonContent(
     *                  @OA\Property(
     *                          property="case_managers", 
     *                              type="array",
     *                              collectionFormat="multi",
     *                              @OA\Items(
     *                                  type="object",
     *                                  ref="#/components/schemas/CaseManager"
     *                              )
     *                  ),
     *                  @OA\Property(
     *                              property="care_plan_id",
     *                              type="integer",
     *                              example=1,
     *                  )
     *             )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                  property="data",
     *                  type="array",
     *                  collectionFormat="multi",
     *                  @OA\Items(
     *                     type="object",
     *                     ref="#/components/schemas/CaseManager"
     *                  )
     *              )
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
     *                         @OA\Property(property="field", type="string", description="Field name", example="care_plan_id"),
     *                         @OA\Property(property="message", type="string", description="Error message of the field", example="The selected care plan id is invalid.")
     *                     ) 
     *                 )
     *             )
     *         )
     *     )
     * )
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        // ================ old ==================
        // if(
        //     // $request->is_hcsw &&
        //     // $request->is_hcw && 
        //     $request->access_role !== 'admin' ||
        //     $request->access_role !== 'manager'
        // ){
        //     return response()->json([
        //         "status" => [
        //             "code" => 401,
        //             "message" => "",
        //             "errors" => [
        //                 [
        //                     "message" => "Unauthorized"
        //                 ]
        //             ]
        //         ]
        //     ], 401);
        // }

        if($request->is_other && $request->access_role !== 'admin'){
            return response()->json([
                "status" => [
                    "code" => 401,
                    "message" => "",
                    "errors" => [
                        [
                            'message' => 'User not in any team access'
                        ]
                    ]
                ]
            ], 401);
        }

        $request->validate([
            'care_plan_id' => 'required|integer|exists:care_plans,id,deleted_at,NULL',
            'case_managers' => 'present|array',
            'case_managers.*.manager_id' => 'required|integer',
            'case_managers.*.manager_name' => 'required|string'
        ]);

        $obj_length = count((array)$request->case_managers);
        $case_manager_list = array(array());
        
        for ($i = 0; $i < $obj_length; $i++) {
            $case_manager_list[$i]['care_plan_id'] = $request->care_plan_id;
            $case_manager_list[$i]['manager_id'] = $request->case_managers[$i]['manager_id'];
            $case_manager_list[$i]['manager_name'] = $request->case_managers[$i]['manager_name'];
        }
        
        $care_plan = CarePlan::where('id', $request->care_plan_id)->first();

        if ($obj_length > 0 && !$care_plan) {
            $care_plan->caseManagers()->createMany($case_manager_list);
        } else if ($obj_length > 0 && $care_plan) {
            $care_plan->caseManagers()->delete();
            $care_plan->caseManagers()->createMany($case_manager_list);
        } else if ($obj_length == 0 && $care_plan) {
            $care_plan->caseManagers()->delete();
        } else if ($obj_length == 0 && !$care_plan) {
            $care_plan->caseManagers()->delete();
        }
        return CaseManagerResource::collection($care_plan->caseManagers()->get());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\CaseManager  $caseManager
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $case_manager = CaseManager::where('id', $id)->first();
        if(!$case_manager){
            return $this->failure('Case Manager not found', 404);
        }
        $case_manager->delete();
        return response()->json([
            'data' => null,
            'message' => 'success delete case manager access'
        ], 200);
    }
}
