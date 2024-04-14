<?php

namespace App\Http\Controllers;

use App\Http\Resources\BznCareTargetResource;
use App\Models\BznCareTarget;
use App\Models\CarePlan;
use App\Traits\RespondsWithHttpStatus;
use Illuminate\Http\Request;

class BznCareTargetController extends Controller
{
    use RespondsWithHttpStatus;

    /**
     * @OA\Get(
     *     path="/assessments-api/v1/bzn-care-targets",
     *     tags={"BznCareTarget"},
     *     summary="Bzn care target list",
     *     operationId="bznCareTargetList",
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number",
     *         @OA\Schema(
     *             type="integer",
     *             format="int32"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Page size (default 10)",
     *         @OA\Schema(
     *             type="integer",
     *             format="int32"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="sort_by",
     *         in="query",
     *         description="Sort by (default created_at). Option: id, created_at, updated_at",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="sort_dir",
     *         in="query",
     *         description="Sort direction (default desc). Option: asc, desc",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
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
     *                 @OA\Items(type="object",ref="#/components/schemas/BznCareTarget")
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
     *
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // return  ["data" => $request->is_other && $request->access_role !== 'admin'];
        if($request->is_other && $request->access_role !== 'admin'){
            return response()->json([
                'data' => null,
                'message' => 'User not in any team access'
            ], 401);
        }

        $request->validate([
            'per_page' => 'nullable|integer',
            'sort_by' => 'nullable|in:id,created_at,updated_at',
            'sort_dir' => 'nullable|in:asc,desc',
            'care_plan_id' => 'nullable|integer|exists:care_plans,id,deleted_at,NULL'
        ]);

        $per_page = $request->query('per_page', 10);
        $sortBy = $request->query('sort_by', 'created_at');
        $sortDir = $request->query('sort_dir', 'asc');
        $care_plan_id = $request->query('care_plan_id') ? $request->query('care_plan_id') : null; 
        
        if(!$care_plan_id){
            return response()->json([
                'data' => null,
                'message' => 'Failed to access bzn care target, care plan not exist'
            ], 404);
        }

        $bzn_care_target = BznCareTarget::where('care_plan_id', $care_plan_id);
        return BznCareTargetResource::collection($bzn_care_target
            ->orderBy($sortBy, $sortDir)
            ->paginate($per_page)
            ->appends($request->except(['page'])));
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
     * @OA\Post(
     *     path="/assessments-api/v1/bzn-care-targets",
     *     tags={"BznCareTarget"},
     *     summary="Store new bzn care target",
     *     operationId="bznCareTargetStore",
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
     *         description="Input bzn care target information (in json)",
     *         required=true,
     *             @OA\JsonContent(
     *                  @OA\Property(property="care_plan_id", type="integer", example=1),
     *                 @OA\Property(property="intervention", type="array",collectionFormat="multi", @OA\Items( type="string",example="Text of intervention")),
     *                 @OA\Property(property="target_type", type="array",collectionFormat="multi", @OA\Items( type="integer",example=1)),
     *                 @OA\Property(property="plan", type="array",collectionFormat="multi", @OA\Items( type="string",example="Text of plan")),
     *                 @OA\Property(property="ct_area", type="array",collectionFormat="multi", @OA\Items(type="string", example=1)),
     *                 @OA\Property(property="ct_target", type="array",collectionFormat="multi", @OA\Items(type="string", example=1)),
     *                 @OA\Property(property="ct_ssa", type="array",collectionFormat="multi", @OA\Items(type="string", example=1)),
     *                 @OA\Property(property="ct_domain", type="array",collectionFormat="multi", @OA\Items(type="integer", example=1)),
     *                 @OA\Property(property="ct_urgency", type="array",collectionFormat="multi", @OA\Items(type="integer", example=1)),
     *                 @OA\Property(property="ct_category", type="array",collectionFormat="multi", @OA\Items(type="integer", example=1)),
     *                 @OA\Property(property="ct_priority", type="array",collectionFormat="multi", @OA\Items(type="integer", example=1)),
     *                 @OA\Property(property="ct_modifier", type="array",collectionFormat="multi", @OA\Items(type="integer", example=1)),
     *                 @OA\Property(property="ct_knowledge", type="array",collectionFormat="multi", @OA\Items(type="integer", example=1)),
     *                 @OA\Property(property="ct_behaviour", type="array",collectionFormat="multi", @OA\Items(type="integer", example=1)),
     *                 @OA\Property(property="ct_status", type="array",collectionFormat="multi", @OA\Items(type="integer", example=1)),
     *             )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Successful",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", type="object", ref="#/components/schemas/BznCareTarget")
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
     * 
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
                'message' => 'Failed to update bzn care target, care plan not exist'
            ], 404);
        } 
        $managers = count($care_plan_role->caseManagers) == 0 ? [] : $care_plan_role->caseManagers->pluck('manager_id')->toArray();

        if(
            $care_plan_role->manager_id !== $user && 
            !in_array($user, $managers) &&
            $request->access_role !== 'admin' 
            // && 
            // $request->hcw &&
            // !$request->is_bzn
        ){
            return response()->json([
                'data' => null,
                'message' => 'Failed to update bzn care target, you are not the author'
            ], 401);
        }

        
        if($request->is_other && $request->access_role !== 'admin'){
            return response()->json([
                'data' => null,
                'message' => 'User not in any team access'
            ], 401);
        }

        $request->validate([
            'care_plan_id' => 'integer|exists:care_plans,id,deleted_at,NULL',
            'target_type' => 'nullable|array',
            'target_type.*' => 'nullable|integer'
        ]);
        $care_plan_id = $request->query('care_plan_id') ? $request->query('care_plan_id') : $request->care_plan_id;
        $obj_length = count((array)$request->intervention);
        $bzn_plan_list = array(array());
        for ($i = 0; $i < $obj_length; $i++) {
            $bzn_plan_list[$i]['care_plan_id'] = $care_plan_id;
            $bzn_plan_list[$i]['intervention'] = $request->intervention[$i] ? $request->intervention[$i] : null;
            $bzn_plan_list[$i]['target_type'] = $request->target_type[$i] ? $request->target_type[$i] : null;
            $bzn_plan_list[$i]['plan'] = $request->plan[$i] ? $request->plan[$i] : null;
            $bzn_plan_list[$i]['ct_area'] = $request->ct_area[$i] ? $request->ct_area[$i] : null;
            $bzn_plan_list[$i]['ct_target'] = $request->ct_target[$i] ? $request->ct_target[$i] : null;
            $bzn_plan_list[$i]['ct_ssa'] = $request->ct_ssa[$i] ? $request->ct_ssa[$i] : null;
            $bzn_plan_list[$i]['ct_domain'] = $request->ct_domain[$i] ? $request->ct_domain[$i] : null;
            $bzn_plan_list[$i]['ct_urgency'] = $request->ct_urgency[$i] ? $request->ct_urgency[$i] : null;
            $bzn_plan_list[$i]['ct_category'] = $request->ct_category[$i] ? $request->ct_category[$i] : null;
            $bzn_plan_list[$i]['ct_priority'] = $request->ct_priority[$i] ? $request->ct_priority[$i] : null;
            $bzn_plan_list[$i]['ct_modifier'] = $request->ct_modifier[$i] ? $request->ct_modifier[$i] : null;
            $bzn_plan_list[$i]['ct_knowledge'] = $request->ct_knowledge[$i] ? $request->ct_knowledge[$i] : null;
            $bzn_plan_list[$i]['ct_behaviour'] = $request->ct_behaviour[$i] ? $request->ct_behaviour[$i] : null;
            $bzn_plan_list[$i]['ct_status'] = $request->ct_status[$i] ? $request->ct_status[$i] : null;
            $bzn_plan_list[$i]['omaha_s'] = $request->omaha_s[$i] ? $request->omaha_s[$i] : null;
        }
        $care_plan = CarePlan::where('id', $care_plan_id)->first();
        $bzn_target_care = $care_plan->bznCareTarget()->createMany($bzn_plan_list);
        if ($bzn_target_care) {
            $results = BznCareTarget::where('care_plan_id', $care_plan_id)->orderBy('updated_at', 'desc')->get();
        }
        return response()->json(['data' => $results], 201);
    }

    /**
     * @OA\Post(
     *     path="/assessments-api/v1/bzn-care-targets-rev",
     *     tags={"BznCareTarget"},
     *     summary="Store new bzn care target",
     *     operationId="bznCareTargetStoreV2",
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
     *         description="Input bzn care target information (in json)",
     *         required=true,
     *             @OA\JsonContent(
     *                  @OA\Property(property="care_plan_id", type="integer", example=1),
     *                  @OA\Property(
     *                              property="bzn_care_targets", 
     *                              type="array",
     *                              collectionFormat="multi",
     *                              @OA\Items(
     *                                  type="object",
     *                                  ref="#/components/schemas/BznCareTarget"
     *                              )
     *                  )
     *             )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Successful",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", type="object", ref="#/components/schemas/BznCareTarget")
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
     * 
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
                'message' => 'Failed to update bzn care target, care plan not exist'
            ], 404);
        } 
        $managers = count($care_plan_role->caseManagers) == 0 ? [] : $care_plan_role->caseManagers->pluck('manager_id')->toArray();

        if(
            $care_plan_role->manager_id !== $user && 
            !in_array($user, $managers) &&
            $request->access_role !== 'admin' 
            // && 
            // $request->hcw &&
            // !$request->is_bzn
        ){
            return response()->json([
                'data' => null,
                'message' => 'Failed to update bzn care target, you are not the author'
            ], 401);
        }
        
        if($request->is_other && $request->access_role !== 'admin'){
            return response()->json([
                'data' => null,
                'message' => 'User not in any team access'
            ], 401);
        }
        $request->validate([
            'care_plan_id' => 'integer|exists:care_plans,id,deleted_at,NULL',
            'bzn_care_targets' => 'nullable|array'
        ]);
        $obj_length = count((array)$request->bzn_care_targets);
        $bzn_plan_list = array(array());
        for ($i = 0; $i < $obj_length; $i++) {
            $bzn_plan_list[$i]['care_plan_id'] = $request->care_plan_id;
            $bzn_plan_list[$i]['intervention'] = isset($request->bzn_care_targets[$i]['intervention']) ? $request->bzn_care_targets[$i]['intervention'] : null;
            $bzn_plan_list[$i]['target_type'] = isset($request->bzn_care_targets[$i]['target_type']) ? $request->bzn_care_targets[$i]['target_type'] : null;
            $bzn_plan_list[$i]['plan'] = isset($request->bzn_care_targets[$i]['plan']) ? $request->bzn_care_targets[$i]['plan'] : null;
            $bzn_plan_list[$i]['ct_area'] = isset($request->bzn_care_targets[$i]['ct_area']) ? $request->bzn_care_targets[$i]['ct_area'] : null;
            $bzn_plan_list[$i]['ct_target'] = isset($request->bzn_care_targets[$i]['ct_target']) ? $request->bzn_care_targets[$i]['ct_target'] : null;
            $bzn_plan_list[$i]['ct_ssa'] = isset($request->bzn_care_targets[$i]['ct_ssa']) ? $request->bzn_care_targets[$i]['ct_ssa'] : null;
            $bzn_plan_list[$i]['ct_domain'] = isset($request->bzn_care_targets[$i]['ct_domain']) ? $request->bzn_care_targets[$i]['ct_domain'] : null;
            $bzn_plan_list[$i]['ct_urgency'] = isset($request->bzn_care_targets[$i]['ct_urgency']) ? $request->bzn_care_targets[$i]['ct_urgency'] : null;
            $bzn_plan_list[$i]['ct_category'] = isset($request->bzn_care_targets[$i]['ct_category']) ? $request->bzn_care_targets[$i]['ct_category'] : null;
            $bzn_plan_list[$i]['ct_priority'] = isset($request->bzn_care_targets[$i]['ct_priority']) ? $request->bzn_care_targets[$i]['ct_priority'] : null;
            $bzn_plan_list[$i]['ct_modifier'] = isset($request->bzn_care_targets[$i]['ct_modifier']) ? $request->bzn_care_targets[$i]['ct_modifier'] : null;
            $bzn_plan_list[$i]['ct_knowledge'] = isset($request->bzn_care_targets[$i]['ct_knowledge']) ? $request->bzn_care_targets[$i]['ct_knowledge'] : null;
            $bzn_plan_list[$i]['ct_behaviour'] = isset($request->bzn_care_targets[$i]['ct_behaviour']) ? $request->bzn_care_targets[$i]['ct_behaviour'] : null;
            $bzn_plan_list[$i]['ct_status'] = isset($request->bzn_care_targets[$i]['ct_status']) ? $request->bzn_care_targets[$i]['ct_status'] : null;
            $bzn_plan_list[$i]['omaha_s'] = isset($request->bzn_care_targets[$i]['omaha_s']) ? $request->bzn_care_targets[$i]['omaha_s'] : null;
        }
        $care_plan = CarePlan::where('id', $request->care_plan_id)->first();
        if(!$care_plan){
            return $this->failure("Care Plan with ID:" . " $request->care_plan_id" . "not found", 404);
        }
        $bzn_target_care = $care_plan->bznCareTarget()->createMany($bzn_plan_list);
        if ($bzn_target_care) {
            $results = BznCareTarget::where('care_plan_id', $request->care_plan_id)->orderBy('updated_at', 'desc')->get();
        }
        return response()->json(['data' => $results], 201);
    }

    /**
     * @OA\Get(
     *     path="/assessments-api/v1/bzn-care-targets/{id}",
     *     tags={"BznCareTarget"},
     *     summary="Bzn care target details",
     *     operationId="bznCareTargetDetails",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Id of bzn care target",
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
     *             @OA\Property(property="data", type="object", ref="#/components/schemas/BznCareTarget")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Bzn Care target not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="object",
     *                 @OA\Property(property="code", type="integer", description="Status code", example="404"),
     *                 @OA\Property(property="message", type="string", example="Bzn Care target not found"),
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
        
        if(
            $request->access_role !== 'admin' 
            // &&
            // !$request->is_bzn
        ){
            return response()->json([
                'data' => null,
                'message' => 'User not in BZN team access'
            ], 401);
        }
        
        if($request->is_other && $request->access_role !== 'admin'){
            return response()->json([
                'data' => null,
                'message' => 'User not in any team access'
            ], 401);
        }
        $bzn_care_target = BznCareTarget::find($id);

        if (!$bzn_care_target) {
            return $this->failure('Bzn care target not found', 404);
        }

        return new BznCareTargetResource($bzn_care_target);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * @OA\Put(
     *     path="/assessments-api/v1/bzn-care-targets/{id}",
     *     tags={"BznCareTarget"},
     *     summary="Update bzn care target",
     *     operationId="bznCareTargetUpdate",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Id of bzn care target",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         description="Input bzn care target information (in json)",
     *         required=true,
     *             @OA\JsonContent(
     *                 @OA\Property(property="intervention", type="string",  example="Text of intervention"),
     *                 @OA\Property(property="target_type", type="integer", example="1"),
     *                 @OA\Property(property="plan", type="string", example="Text of plan"),
     *                 @OA\Property(property="ct_area", type="string", example=1),
     *                 @OA\Property(property="ct_target", type="string", example=1),
     *                 @OA\Property(property="ct_ssa", type="string", example=1),
     *                 @OA\Property(property="ct_domain", type="integer", example=1),
     *                 @OA\Property(property="ct_urgency", type="integer", example=1),
     *                 @OA\Property(property="ct_category", type="integer", example=1),
     *                 @OA\Property(property="ct_priority", type="integer", example=1),
     *                 @OA\Property(property="ct_modifier", type="integer", example=1),
     *                 @OA\Property(property="ct_knowledge", type="integer", example=1),
     *                 @OA\Property(property="ct_behaviour", type="integer", example=1),
     *                 @OA\Property(property="ct_status", type="integer", example=1),
     *             )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", type="object", ref="#/components/schemas/BznCareTarget")
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
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Bzn care target not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="object",
     *                 @OA\Property(property="code", type="integer", description="Status code", example="404"),
     *                 @OA\Property(property="message", type="string", example="Bzn care target not found"),
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
        $request->validate([
            'target_type' => 'nullable|integer'
        ]);

        $bzn_care_target = BznCareTarget::find($id);

        if (!$bzn_care_target) {
            return response()->json([
                'data' => null,
                'message' => 'Failed to update, bzn care target not found'
            ], 404);
        }

        $care_plan_id = $bzn_care_target ? $bzn_care_target->care_plan_id : ($request->care_plan_id ? $request->care_plan_id : null);

        $user = $request->user_id;

        $care_plan = CarePlan::where('id', $care_plan_id)->with('caseManagers')->first();
        if(!$care_plan){
            return response()->json([
                'data' => null,
                'message' => 'Failed to update bzn care target, you are not the author'
            ], 401);
        }
        $managers = count($care_plan->caseManagers) == 0 ? [] : $care_plan->caseManagers->pluck('manager_id')->toArray();
        if(
            $care_plan->manager_id !== $user &&
            !in_array($user, $managers) &&
            $request->access_role !== 'admin' 
            // &&
            // !$request->is_bzn && 
            // $request->is_hcw
        ){
            return response()->json([
                'data' => null,
                'message' => 'Failed to update cga care target, you are not the author'
            ], 401);
        }

        if($request->is_other && $request->access_role !== 'admin'){
            return response()->json([
                'data' => null,
                'message' => 'User not in any team access'
            ], 401);
        }

        $updated = $bzn_care_target->update([
            'intervention' => $request->intervention,
            'target_type' => $request->target_type,
            'plan' => $request->plan,
            'ct_area' => $request->ct_area,
            'ct_target' => $request->ct_target,
            'ct_ssa' => $request->ct_ssa,
            'ct_domain' => $request->ct_domain,
            'ct_urgency' => $request->ct_urgency,
            'ct_category' => $request->ct_category,
            'ct_priority' => $request->ct_priority,
            'ct_modifier' => $request->ct_modifier,
            'ct_knowledge' => $request->ct_knowledge,
            'ct_behaviour' => $request->ct_behaviour,
            'ct_status' => $request->ct_status,
            'omaha_s' => $request->omaha_s
        ]);

        if (!$updated) {
            return $this->failure('Failed to update bzn care target');
        }

        return new BznCareTargetResource($bzn_care_target);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $results = BznCareTarget::where('id', $id)->delete();
        return response()->json(null);
    }
}
