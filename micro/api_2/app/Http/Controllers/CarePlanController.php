<?php

namespace App\Http\Controllers;

use App\Models\CarePlan;
use App\Models\CoachingPam;
use Illuminate\Http\Request;
use App\Models\PreCoachingPam;
use App\Models\BznConsultationNotes;
use Illuminate\Support\Facades\Http;
use App\Http\Services\ExternalService;
use App\Traits\RespondsWithHttpStatus;
use App\Http\Resources\CarePlanResource;
use App\Models\BznCareTarget;

class CarePlanController extends Controller
{
    use RespondsWithHttpStatus;
    private $externalService;
    public function __construct()
    {
        $this->externalService = new ExternalService();
    }

    /**
     * @OA\Get(
     *     path="/assessments-api/v1/care-plans",
     *     tags={"CarePlan"},
     *     summary="Get care plan by case id",
     *     operationId="carePlanByCaseId",
     *     @OA\Parameter(
     *         name="case_id",
     *         in="query",
     *         description="Case id of care plan",
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
     *             @OA\Property(property="data", type="object", ref="#/components/schemas/CarePlan")
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
        // $request->validate([
        //     'case_id' => 'required'
        // ]);
        if(
            // $request->is_hcsw &&
            // $request->is_hcw && 
            $request->access_role !== 'admin' &&
            $request->access_role !== 'manager' &&
            $request->access_role !== 'user'
        ){
            return response()->json([
                'data' => null,
                'message' => 'Unauthorized'
            ], 401);
        }
        if($request->is_other && $request->access_role !== 'admin'){
            return response()->json([
                'data' => null,
                'message' => 'User not in any team access'
            ], 401);
        }
        $case_id = $request->query('case_id');
        if($case_id){
            $care_plan = CarePlan::where('case_id', $case_id)->first();
            if (!$care_plan) {
                return $this->success(null);
            }
            $coachingPam = CoachingPam::where('care_plan_id', $care_plan->id)->first();
            if(!$coachingPam){
                CoachingPam::updateOrCreate(['care_plan_id' => $care_plan->id], []);
            }
            $preCoachingPam = PreCoachingPam::where('care_plan_id', $care_plan->id)->first();
            if(!$preCoachingPam){
                PreCoachingPam::updateOrCreate(['care_plan_id' => $care_plan->id], []);
            }
            return new CarePlanResource($care_plan);
        }
        if(!$case_id){
            $care_plan = CarePlan::orderBy('created_at', 'desc')->get();
            if (!$care_plan) {
                return $this->success(null);
            }
            return CarePlanResource::collection($care_plan);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
    }

    /**
     * @OA\Post(
     *     path="/assessments-api/v1/care-plans",
     *     tags={"CarePlan"},
     *     summary="Store new care plan",
     *     operationId="carePlanStore",
     *     @OA\RequestBody(
     *         description="Input care plan information (in json)",
     *         required=true,
     *             @OA\JsonContent(
     *                 required={"case_id, case_type"},
     *                 @OA\Property(property="case_id", type="integer", example="1"),
     *                 @OA\Property(property="case_type", type="enum", format="BZN, CGA", example="BZN"),
     *                 @OA\Property(property="manager_id", type="integer", example=1),
     *                 @OA\Property(property="handler_id", type="integer", example=2),
     *             )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Successful",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", type="object", ref="#/components/schemas/CarePlan")
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
        if(
            // $request->is_hcsw &&
            // $request->is_hcw && 
            $request->access_role !== 'admin' &&
            $request->access_role !== 'manager' 
        ){
            return response()->json([
                'data' => null,
                'message' => 'Unauthorized'
            ], 401);
        }

        if($request->is_other && $request->access_role !== 'admin'){
            return response()->json([
                'data' => null,
                'message' => 'User not in any team access'
            ], 401);
        }

        $manager_name = null;
        $handler_name = null;
        $manager_id = null;
        $handler_id = null;
        if($request->manager_id){
            $manager = Http::acceptJson()->withToken($request->bearerToken())->get(env('USER_SERVICE_API_URL') 
                . "/users/{$request->manager_id}"
            );
            $manager_data = $manager->collect('data');
            if($manager_data){
                $manager_name = $manager_data['nickname'];
                $manager_id = $request->manager_id;
            }
        }
        if($request->handler_id){
            $handler = Http::acceptJson()->withToken($request->bearerToken())->get(env('USER_SERVICE_API_URL') 
                . "/users/{$request->handler_id}"
            );
            $handler_data = $handler->collect('data');
            if($handler_data){
                $handler_name = $handler_data['nickname'];
                $handler_id = $request->handler_id;
            }
        }

        $request->merge([
            'case_manager' => $manager_name,
            'handler' => $handler_name,
            'manager_id' => $manager_id,
            'handler_id' => $handler_id
        ]);
        
        $request->validate([
            'case_id' => 'required',
            'case_type' => 'required|in:CGA,BZN',
        ]);

        $care_plan_exist = CarePlan::where('case_id', $request->case_id)->first();
        if ($care_plan_exist) {
            return $this->failure('Case Id already exist', 422);
        }

        $care_plan = CarePlan::create([
            'case_id' => $request->case_id,
            'case_type' => $request->case_type,
            'case_manager' => $request->case_manager,
            'handler' => $request->handler,
            'manager_id' => $request->manager_id,
            'handler_id' => $request->handler_id
        ]);
        $coachingPam = CoachingPam::where('care_plan_id', $care_plan->id)->first();
        if(!$coachingPam){
            CoachingPam::updateOrCreate(['care_plan_id' => $care_plan->id], []);
        }
        $preCoachingPam = PreCoachingPam::where('care_plan_id', $care_plan->id)->first();
        if(!$preCoachingPam){
            PreCoachingPam::updateOrCreate(['care_plan_id' => $care_plan->id], []);
        }
        return new CarePlanResource($care_plan);
    }

    /**
     * @OA\Get(
     *     path="/assessments-api/v1/care-plans/{id}",
     *     tags={"CarePlan"},
     *     summary="Get care plan by id",
     *     operationId="carePlanById",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
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
     *             @OA\Property(property="data", type="object", ref="#/components/schemas/CarePlan")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Care plan not found",
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
    public function show(Request $request, $id)
    {

        if(
            // $request->is_hcsw &&
            // $request->is_hcw && 
            $request->access_role !== 'admin' &&
            $request->access_role !== 'manager' &&
            $request->access_role !== 'user'
        ){
            return response()->json([
                'data' => null,
                'message' => 'Unauthorized'
            ], 401);
        }

        if($request->is_other && $request->access_role !== 'admin'){
            return response()->json([
                'data' => null,
                'message' => 'User not in any team access'
            ], 401);
        }

        $care_plan = CarePlan::find($id);

        if (!$care_plan) {
            return $this->failure('Care plan not found', 404);
        }

        $coachingPam = CoachingPam::where('care_plan_id', $care_plan->id)->first();
        if(!$coachingPam){
            CoachingPam::updateOrCreate(['care_plan_id' => $care_plan->id], []);
        }

        $preCoachingPam = PreCoachingPam::where('care_plan_id', $care_plan->id)->first();
        if(!$preCoachingPam){
            PreCoachingPam::updateOrCreate(['care_plan_id' => $care_plan->id], []);
        }

        return new CarePlanResource($care_plan);
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
     *     path="/assessments-api/v1/care-plans/{id}",
     *     tags={"CarePlan"},
     *     summary="Update care plan",
     *     operationId="carePlanUpdate",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
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
     *                 required={"case_id, case_type"},
     *                 @OA\Property(property="case_id", type="integer", example="1"),
     *                 @OA\Property(property="case_type", type="enum", format="BZN, CGA", example="BZN"),
     *                 @OA\Property(property="manager_id", type="integer", example=1),
     *                 @OA\Property(property="handler_id", type="integer", example=1),
     *             )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", type="object", ref="#/components/schemas/CarePlan")
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
     *         description="Care plan not found",
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
    public function update(Request $request, $id)
    {
        $user = $request->user_id;
        $care_plan_role = CarePlan::where('id', $id)->with('caseManagers')->first();
        if(!$care_plan_role){
            return response()->json([
                'data' => null,
                'message' => 'Care Plan not Found' 
            ], 404);
        } 
        $managers = count($care_plan_role->caseManagers) == 0 ? [] : $care_plan_role->caseManagers->pluck('manager_id')->toArray();

        if(
            $care_plan_role->manager_id !== $user && 
            !in_array($user, $managers) &&
            $request->access_role !== 'admin' &&
            $request->access_role !== 'manager'  
            // && 
            // $request->hcw &&
            // $request->hcsw
        ){
            return response()->json([
                'data' => null,
                'message' => 'Failed to update care plan, you are not the author'
            ], 401);
        }

        if($request->is_other && $request->access_role !== 'admin'){

            return response()->json([
                'data' => null,
                'message' => 'User not in any team access'
            ], 401);
        }

        $manager_name = null;
        $handler_name = null;
        $manager_id = null;
        $handler_id = null;
        if($request->manager_id){
            $manager = Http::acceptJson()->withToken($request->bearerToken())->get(env('USER_SERVICE_API_URL') 
                . "/users/{$request->manager_id}"
            );
            $manager_data = $manager->collect('data');
            if($manager_data){
                $manager_name = $manager_data['nickname'];
                $manager_id = $request->manager_id;
            }
        }
        if($request->handler_id){
            $handler = Http::acceptJson()->withToken($request->bearerToken())->get(env('USER_SERVICE_API_URL') 
                . "/users/{$request->handler_id}"
            );
            $handler_data = $handler->collect('data');
            if($handler_data){
                $handler_name = $handler_data['nickname'];
                $handler_id = $request->handler_id;
            }
        }

        $request->merge([
            'case_manager' => $manager_name,
            'handler' => $handler_name,
            'manager_id' => $manager_id,
            'handler_id' => $handler_id
        ]);
        $request->validate([
            'case_id' => 'required',
            'case_type' => 'required|in:CGA,BZN',
        ]);

        $care_plan = CarePlan::find($id);

        if (!$care_plan) {
            return $this->failure('Care plan not found', 404);
        }

        $updated = $care_plan->update([
            'case_id' => $request->case_id,
            'case_type' => $request->case_type,
            'case_manager' => $request->case_manager,
            'handler' => $request->handler,
            'manager_id' => $request->manager_id,
            'handler_id' => $request->handler_id
        ]);

        if (!$updated) {
            return $this->failure('Failed to update care plan');
        }

        return new CarePlanResource($care_plan);
    }

    /**
     * @OA\Delete(
     *     path="/assessments-api/v1/care-plans/{id}",
     *     tags={"CarePlan"},
     *     summary="Delete care plan by Id",
     *     operationId="carePlanDelete",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Care plan id to be deleted",
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
     *         description="Care plan not found",
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
    public function destroy(Request $request, $id)
    {

        if(
            $request->is_hcsw &&
            $request->is_hcw && 
            $request->access_role !== 'admin'){
            return response()->json([
                'data' => null,
                'message' => 'Unauthorized'
            ], 401);
        }

        if($request->is_other && $request->access_role !== 'admin'){
            return response()->json([
                'data' => null,
                'message' => 'User not in any team access'
            ], 401);
        }
        
        $care_plan = CarePlan::find($id);

        if (!$care_plan) {
            return $this->failure('Care plan not found', 404);
        }

        $care_plan->delete();

        return response(null, 204);
    }

    public function reportsResourceSet(Request $request){
        $care_plans = CarePlan::select(['id', 'case_id', 'case_manager', 'manager_id']);
        if($request->query('cases_id')){
            $cases_id = explode(',', $request->query('cases_id'));
            $care_plans = $care_plans->whereIn('case_id', $cases_id);
        }
        $care_plans = $care_plans->without(['coachingPam', 'bznNotes', 'cgaNotes'])
        ->with([
            'bznCareTarget' => function($query){
                $query->select(['care_plan_id', 'id'])->without('bznConsultationNotes')->get();
            }, 
            'cgaCareTarget' => function($query){
                $query->select(['care_plan_id', 'id'])->without('cgaConsultationNotes')->get();
            }
        ])
        ->get();
        if(count($care_plans) == 0){
            return response()->json(['data' => null], 404);
        }
        $results = new \stdClass();
        for($i = 0; $i < count($care_plans); $i++){
            $case_id = $care_plans[$i]['case_id'];
            if($case_id !== null && !property_exists($results, $case_id)){
                $results->$case_id['care_plan_id'] = $care_plans[$i]['id'];
                $results->$case_id['case_id'] = $care_plans[$i]['case_id'];
                $results->$case_id['case_manager'] = $care_plans[$i]['case_manager'];
                $results->$case_id['bzn_care_target'] = $care_plans[$i]['bznCareTarget'];
                $results->$case_id['cga_care_target'] = $care_plans[$i]['cgaCareTarget'];
            }
        }
        return response()->json(['data' => $results], 200);
    }

    public function reportsResourceStaffSet(Request $request){
        $care_plans = CarePlan::select(['id', 'case_manager']);
        if($request->query('staffNames')){
            $staffNames = explode(',', $request->query('staffNames'));
            $care_plans = $care_plans->whereIn('case_manager', $staffNames);
        }
        $care_plans = $care_plans->without(['coachingPam', 'bznNotes', 'cgaNotes'])
        ->get();
        if(count($care_plans) == 0){
            return response()->json(['data' => null], 404);
        }
        $results = new \stdClass();
        for($i = 0; $i < count($care_plans); $i++){
            $staffName = $care_plans[$i]['case_manager'];
            if($staffName !== null){
                $swipespace = preg_replace('/[^A-Za-z0-9]+/', '_', $staffName);
                $snakeCaseStaffName = strtolower($swipespace);
                $snakeCaseStaffName = trim($snakeCaseStaffName, '_');            
                if(!property_exists($results, $staffName)){
                    $results->$snakeCaseStaffName = 1;
                } else if (property_exists($results, $staffName)) {
                    $results->$snakeCaseStaffName += 1;
                }
            }
        }
        return response()->json(['data' => $results], 200);
    }

    public function checkCarePlanCaseManager(Request $request) {
        $caseId = $request->query('case_id');
        if(!$caseId){
            return response()->json(['data' => null], 404);
        }

        $care_plan = CarePlan::select(['id', 'case_id', 'case_manager', 'manager_id'])->without(['coachingPam', 'bznNotes', 'cgaNotes'])->where('case_id', $caseId)->with('caseManagers')->first();
        if(!$care_plan){
            return response()->json(['data' => null], 404);
        } else if ($care_plan->case_manager === null) {
            return response()->json(['data' => null], 404);
        }
        return response()->json(['data' => $care_plan], 200);
    }

    public function caseManagerByCasesSet(Request $request){
        $care_plans = CarePlan::select(['id', 'case_id', 'case_manager'])->get();
        if(!$care_plans){
            return response()->json(['data' => null], 404);
        }
        $result = new \stdClass();
        for($i = 0; $i< count($care_plans); $i++){
            $caseId = $care_plans[$i]->case_id;
            if(
                !isset($result->$caseId) && 
                $caseId !== null && 
                $care_plans[$i]->case_manager !== null
            ){
                $result->$caseId['case_manager'] = $care_plans[$i]->case_manager;
            }
        }
        return response()->json(['data' => $result], 200);
    }

    public function getCaseStatus(Request $request){
        $case_status = $this->externalService->getCasesStatus($request->bearerToken());
        // return $case_status["204"];
        if(!$case_status){
            return response()->json(['data' => null], 404);
        }
        $care_plans = CarePlan::select(['id','case_id','manager_id', 'case_type'])->with(['bznNotes', 'cgaNotes'])->get();
        // return $care_plans;
        $result = new \stdClass();
        $case_status = $case_status->toArray();

        for($i = 0; $i<count($care_plans); $i++){
            $caseId = $care_plans[$i]->case_id;
            $caseIdString = "$caseId";
            $managerId = $care_plans[$i]->manager_id;
            $managerIdString = "$managerId";
            $cgaNotes = $care_plans[$i]->cgaNotes  ? $care_plans[$i]->cgaNotes->toArray() : [];
            $bznNotes = $care_plans[$i]->bznNotes  ? $care_plans[$i]->bznNotes->toArray() : [];
            
            if($managerId){
                // $result->$managerIdString['total_visit'] = 0;
                if(!isset($result->$managerIdString)){
                    if(isset($case_status[$caseIdString])){
                        $result->$managerIdString = $case_status[$caseIdString];
                    }
                }
                if(isset($case_status[$caseIdString])){
                    $result->$managerIdString['on_going'] = $case_status[$caseIdString]['on_going'] + $result->$managerIdString['on_going'];
                    $result->$managerIdString['pending'] = $case_status[$caseIdString]['pending'] + $result->$managerIdString['pending'];
                    $result->$managerIdString['finished'] = $case_status[$caseIdString]['finished'] + $result->$managerIdString['finished'];
                    $result->$managerIdString['total_visit'] = 0;
                    if($care_plans[$i]->case_type === 'CGA' && count($cgaNotes) > 0){
                        $result->$managerIdString['total_visit'] += count($cgaNotes);
                    }
                    if($care_plans[$i]->case_type === 'BZN' && count($bznNotes) > 0){
                        $result->$managerIdString['total_visit'] += count($bznNotes);
                    }
                }

            }
        }
        return response()->json(['data' => $result], 200);
    }
}
