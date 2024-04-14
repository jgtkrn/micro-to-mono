<?php

namespace App\Http\Controllers;

use App\Models\CarePlan;
use Illuminate\Http\Request;
use App\Models\CgaCareTarget;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Services\ExternalService;
use App\Traits\RespondsWithHttpStatus;
use App\Exports\CGA\HealthCoachingGoalExport;
use App\Http\Resources\CgaCareTargetResource;

class CgaCareTargetController extends Controller
{
    use RespondsWithHttpStatus;
    private $externalService;

    public function __construct()
    {
        $this->externalService = new ExternalService();
    }
    /**
     * @OA\Get(
     *     path="/assessments-api/v1/cga-care-targets",
     *     tags={"CgaCareTarget"},
     *     summary="Cga care target list",
     *     operationId="CgaCareTargetList",
     *     @OA\Parameter(
     *         name="care_plan_id",
     *         in="query",
     *         description="Care plan id",
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
     *                 property="data", 
     *                 type="array",
     *                 @OA\Items(type="object",ref="#/components/schemas/CgaCareTarget")
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
     *                         @OA\Property(property="field", type="string", description="Field name", example="name"),
     *                         @OA\Property(property="message", type="string", description="Error message of the field", example="The name field is required")
     *                     ) 
     *                 )
     *             )
     *         )
     *     )
     * )
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if($request->is_other  && $request->access_role !== 'admin'){
            return response()->json([
                'data' => null,
                'message' => 'User not in any team access'
            ], 401);
        }   
        $request->validate([
            'care_plan_id' => 'required|integer|exists:care_plans,id,deleted_at,NULL'
        ]);
        $care_plan_id = $request->query('care_plan_id');
        $results = CgaCareTarget::where('care_plan_id', $care_plan_id)->get();
        return CgaCareTargetResource::collection($results);
    }
    /**
     * @OA\Post(
     *     path="/assessments-api/v1/cga-care-targets",
     *     tags={"CgaCareTarget"},
     *     summary="Store new cga care target",
     *     operationId="cgaCareTargetStore",
     *     @OA\Parameter(
     *         name="care_plan_id",
     *         in="query",
     *         description="Id of care plan",
     *         required=false,
     *         @OA\Schema(
     *             type="integer",
     *             example=1
     *         )
     *     ),
     *     @OA\RequestBody(
     *          description="Input cga care target information (in json)",
     *             @OA\JsonContent( 
     *                  @OA\Property(
     *                      property="care_plan_id",
     *                      type="integer",
     *                      example=1
     *                  ),
     *                  @OA\Property(property="target", 
     *                              type="array",
     *                              collectionFormat="multi",
     *                              @OA\Items(
     *                                  type="string",
     *                                  example="yes"
     *                              )
     *                  ),
     *                  @OA\Property(property="health_vision", 
     *                              type="array",
     *                              collectionFormat="multi",
     *                              @OA\Items(
     *                                  type="string",
     *                                  example="yes"
     *                              )
     *                  ),
     *                  @OA\Property(property="long_term_goal", 
     *                              type="array",
     *                              collectionFormat="multi",
     *                              @OA\Items(
     *                                  type="string",
     *                                  example="yes"
     *                              )
     *                  ),
     *                  @OA\Property(property="short_term_goal", 
     *                              type="array",
     *                              collectionFormat="multi",
     *                              @OA\Items(
     *                                  type="string",
     *                                  example="yes"
     *                              )
     *                  ),
     *                  @OA\Property(property="motivation", 
     *                              type="array",
     *                              collectionFormat="multi",
     *                              @OA\Items(
     *                                  type="integer",
     *                                  example=1
     *                              )
     *                  ),
     *                  @OA\Property(property="early_change_stage", 
     *                              type="array",
     *                              collectionFormat="multi",
     *                              @OA\Items(
     *                                  type="integer",
     *                                  example=1
     *                              )
     *                  ),
     *                   @OA\Property(property="later_change_stage", 
     *                              type="array",
     *                              collectionFormat="multi",
     *                              @OA\Items(
     *                                  type="integer",
     *                                  example=1
     *                              )
     *                  ),
     *             )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Successful",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", type="object", ref="#/components/schemas/CgaCareTarget")
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
    public function store(Request $request)
    {
        $user = $request->user_id;
        $care_plan_role = CarePlan::where('id', $request->care_plan_id)->with('caseManagers')->first();
        if(!$care_plan_role){
            return response()->json([
                'data' => null,
                'message' => 'Failed to update cga care target, care plan not exist'
            ], 404);
        } 
        $managers = count($care_plan_role->caseManagers) == 0 ? [] : $care_plan_role->caseManagers->pluck('manager_id')->toArray();

        if(
            $care_plan_role->manager_id !== $user && 
            !in_array($user, $managers) &&
            $request->access_role !== 'admin' 
            // && 
            // $request->hcw &&
            // !$request->is_cga
        ){
            return response()->json([
                'data' => null,
                'message' => 'Failed to update cga care target, you are not the author'
            ], 401);
        }
        
        if($request->is_other  && $request->access_role !== 'admin'){
            return response()->json([
                'data' => null,
                'message' => 'User not in any team access'
            ], 401);
        }
        $request->validate([
            'care_plan_id' => 'integer|exists:care_plans,id,deleted_at,NULL'
        ]);
        $care_plan_id = $request->query('care_plan_id') ? $request->query('care_plan_id') : $request->care_plan_id;
        $obj_length = count((array)$request->target);
        $cga_plan_list = array(array());
        for ($i = 0; $i < $obj_length; $i++) {
            $cga_plan_list[$i]['care_plan_id'] = $care_plan_id;
            $cga_plan_list[$i]['target'] = isset($request->target[$i]) ? $request->target[$i] : null;
            $cga_plan_list[$i]['health_vision'] = isset($request->health_vision[$i]) ? $request->health_vision[$i] : null;
            $cga_plan_list[$i]['long_term_goal'] = isset($request->long_term_goal[$i]) ? $request->long_term_goal[$i] : null;
            $cga_plan_list[$i]['short_term_goal'] = isset($request->short_term_goal[$i]) ? $request->short_term_goal[$i] : null;
            $cga_plan_list[$i]['motivation'] = isset($request->motivation[$i]) ? $request->motivation[$i] : null;
            $cga_plan_list[$i]['early_change_stage'] = isset($request->early_change_stage[$i]) ? $request->early_change_stage[$i] : null;
            $cga_plan_list[$i]['later_change_stage'] = isset($request->later_change_stage[$i]) ? $request->later_change_stage[$i] : null;
        }

        $care_plan = CarePlan::where('id', $care_plan_id)->first();

        $cga_target_care = $care_plan->cgaCareTarget()->createMany($cga_plan_list);
        if ($cga_target_care) {
            $results = CgaCareTarget::where('care_plan_id', $care_plan_id)->get();
        }
        return CgaCareTargetResource::collection($results);
    }

    /**
     * @OA\Post(
     *     path="/assessments-api/v1/cga-care-targets-rev",
     *     tags={"CgaCareTarget"},
     *     summary="Store new cga care target",
     *     operationId="cgaCareTargetStoreV2",
     *     @OA\Parameter(
     *         name="care_plan_id",
     *         in="query",
     *         description="Id of care plan",
     *         required=false,
     *         @OA\Schema(
     *             type="integer",
     *             example=1
     *         )
     *     ),
     *     @OA\RequestBody(
     *          description="Input cga care target information (in json)",
     *             @OA\JsonContent(
     *                  @OA\Property(
     *                              property="cga_care_targets", 
     *                              type="array",
     *                              collectionFormat="multi",
     *                              @OA\Items(
     *                                  type="object",
     *                                  ref="#/components/schemas/CgaCareTarget"
     *                              )
     *                  ),
     *                  @OA\Property(
     *                              property="care_plan_id", 
     *                              type="integer",
     *                              example=1
     *                  ),
     *             )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Successful",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", type="object", ref="#/components/schemas/CgaCareTarget")
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
    public function storeV2(Request $request)
    {
        $user = $request->user_id;
        $care_plan_role = CarePlan::where('id', $request->care_plan_id)->with('caseManagers')->first();
        if(!$care_plan_role){
            return response()->json([
                'data' => null,
                'message' => 'Failed to update cga care target, care plan not exist'
            ], 404);
        } 
        $managers = count($care_plan_role->caseManagers) == 0 ? [] : $care_plan_role->caseManagers->pluck('manager_id')->toArray();

        if(
            $care_plan_role->manager_id !== $user && 
            !in_array($user, $managers) &&
            $request->access_role !== 'admin' 
            // && 
            // $request->hcw &&
            // !$request->is_cga
        ){
            return response()->json([
                'data' => null,
                'message' => 'Failed to update cga care target, you are not the author'
            ], 401);
        }

        if($request->is_other  && $request->access_role !== 'admin'){
            return response()->json([
                'data' => null,
                'message' => 'User not in any team access'
            ], 401);
        }
        $request->validate([ 
            'cga_care_targets' => 'array|nullable',
            'care_plan_id' => 'integer|exists:care_plans,id,deleted_at,NULL'
        ]);
        $obj_length = count((array)$request->cga_care_targets);
        $cga_plan_list = array(array());
        for ($i = 0; $i < $obj_length; $i++) {
            $cga_plan_list[$i]['care_plan_id'] = $request->care_plan_id;
            $cga_plan_list[$i]['target'] = isset($request->cga_care_targets[$i]['target']) ? $request->cga_care_targets[$i]['target'] : null;
            $cga_plan_list[$i]['health_vision'] = isset($request->cga_care_targets[$i]['health_vision']) ? $request->cga_care_targets[$i]['health_vision'] : null;
            $cga_plan_list[$i]['long_term_goal'] = isset($request->cga_care_targets[$i]['long_term_goal']) ? $request->cga_care_targets[$i]['long_term_goal'] : null;
            $cga_plan_list[$i]['short_term_goal'] = isset($request->cga_care_targets[$i]['short_term_goal']) ? $request->cga_care_targets[$i]['short_term_goal'] : null;
            $cga_plan_list[$i]['motivation'] = isset($request->cga_care_targets[$i]['motivation']) ? $request->cga_care_targets[$i]['motivation'] : null;
            $cga_plan_list[$i]['early_change_stage'] = isset($request->cga_care_targets[$i]['early_change_stage']) ? $request->cga_care_targets[$i]['early_change_stage'] : null;
            $cga_plan_list[$i]['later_change_stage'] = isset($request->cga_care_targets[$i]['later_change_stage']) ? $request->cga_care_targets[$i]['later_change_stage'] : null;
        }

        $care_plan = CarePlan::where('id', $request->care_plan_id)->first();
        if(!$care_plan){
            return $this->failure("Care Plan with ID:" . " $request->care_plan_id" . "not found", 404);
        }
        $cga_target_care = $care_plan->cgaCareTarget()->createMany($request->cga_care_targets);

        if ($cga_target_care) {
            $results = CgaCareTarget::where('care_plan_id', $request->care_plan_id)->get();
        }
        return CgaCareTargetResource::collection($results);
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
     * @OA\Put(
     *     path="/assessments-api/v1/cga-care-targets/{id}",
     *     tags={"CgaCareTarget"},
     *     summary="Update cga care target",
     *     operationId="cgaCareTargetUpdate",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="cga care target id",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         description="Input cga care target information (in json)",
     *         required=true,
     *             @OA\JsonContent(
     *                  @OA\Property(property="target", type="string", example="yes"),
     *                  @OA\Property(property="care_plan_id", type="integer", example=1),
     *                  @OA\Property(property="health_vision", type="string", example="yes"),
     *                  @OA\Property(property="long_term_goal", type="string", example="yes"),
     *                  @OA\Property(property="short_term_goal", type="string", example="yes"),
     *                  @OA\Property(property="motivation", type="integer", example=1),
     *                  @OA\Property(property="early_change_stage", type="integer", example=1),
     *                  @OA\Property(property="later_change_stage", type="integer", example=1),
     *             )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Successful",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", type="object", ref="#/components/schemas/CgaCareTarget")
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
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'care_plan_id' => 'required|integer|exists:care_plans,id,deleted_at,NULL'
        ]);

        $care_target = CgaCareTarget::where('id', $id)->first();
        $care_plan_id = $care_target ? $care_target->care_plan_id : ($request->care_plan_id ? $request->care_plan_id : null);
        $user = $request->user_id;
        $care_plan = CarePlan::where('id', $care_plan_id)->with('caseManagers')->first();
        if(!$care_plan){
            return response()->json([
                'data' => null,
                'message' => 'Failed to update cga care target, care plan not exist'
            ], 404);
        } 
        $managers = count($care_plan->caseManagers) == 0 ? [] : $care_plan->caseManagers->pluck('manager_id')->toArray();

        if(
            $care_plan->manager_id !== $user && 
            !in_array($user, $managers) &&
            $request->access_role !== 'admin' 
            // && 
            // $request->hcw &&
            // !$request->is_cga
        ){
            return response()->json([
                'data' => null,
                'message' => 'Failed to update cga care target, you are not the author'
            ], 401);
        }

        if($request->is_other  && $request->access_role !== 'admin'){
            return response()->json([
                'data' => null,
                'message' => 'User not in any team access'
            ], 401);
        }

        $results = CgaCareTarget::updateOrCreate(
            ['id' => $id],
            [
                'target' => $request->target,
                'care_plan_id' => $request->care_plan_id,
                'health_vision' => $request->health_vision,
                'long_term_goal' => $request->long_term_goal,
                'motivation' => $request->motivation,
                'short_term_goal' => $request->short_term_goal,
                'early_change_stage' => $request->early_change_stage,
                'later_change_stage' => $request->later_change_stage
            ]
        );

        if (!$results) {
            return $this->failure('Failed to update cga care target');
        }

        return new CgaCareTargetResource($results);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $results = CgaCareTarget::where('id', $id)->delete();
        return response()->json(null);
    }

    public function exportHCG(Request $request)
    {
        $data = CgaCareTarget::select(['care_plan_id', 
                                        'updated_at', 
                                        'health_vision', 
                                        'later_change_stage', 
                                        'early_change_stage', 
                                        'long_term_goal', 
                                        'short_term_goal', 
                                        'motivation'])
                                ->with('carePlan')
                                ->get();
        
        $uid = $this->externalService->getUidSetByCasesId($request->bearerToken());
        
        for($i = 0; $i < count($data); $i++){
            $caseId = strval($data[$i]->carePlan?->case_id);
            if($caseId !== null){
                $data[$i]['uid'] = isset($uid[$caseId]) ? $uid[$caseId]['uid'] : null;
            } else{
                $data[$i]['uid'] = null;
            }
        }
        return Excel::download(new HealthCoachingGoalExport($data), 'health-coaching-goal.csv');
    }
}
