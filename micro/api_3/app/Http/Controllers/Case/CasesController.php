<?php

namespace App\Http\Controllers\Case;

use App\Models\Cases;
use App\Models\Elder;
use App\Models\District;
use App\Models\ElderCalls;
use App\Models\MeetingNotes;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Services\ExternalServices;
use App\Http\Requests\Cases\CasesRequest;
use App\Http\Resources\Cases\CasesResources;
use App\Http\Controllers\Call\CallsController;
use App\Http\Requests\Cases\UpdateCasesRequest;
use App\Http\Resources\Cases\CasesSingleResource;
use App\Exports\Reports\PatientReportsExport;
use App\Exports\Reports\PatientReportsExportExport;

class CasesController extends Controller
{
    private $externalService;
    private $elder_calls;

    public function __construct()
    {
        $this->externalService = new ExternalServices();
        $this->elder_calls = new CallsController();
    }

    /**
     * @OA\Get(
     *     path="/elderly-api/v1/cases",
     *     tags={"cases"},
     *     summary="get cases",
     *     operationId="getCases",
     *     @OA\Parameter(
     *          in="query",
     *          name="page",
     *          example="1"
     *     ),
     *     @OA\Parameter(
     *          in="query",
     *          name="per_page",
     *          example="25"
     *     ),
     *     @OA\Parameter(
     *          in="query",
     *          name="sort_by",
     *          description="uid|name|user_type|district|created_at|updated_at",
     *          example="created_at"
     *     ),
     *     @OA\Parameter(
     *          in="query",
     *          name="sort_dir",
     *          description="asc|desc",
     *          example="created_at"
     *     ),
     *     @OA\Parameter(
     *          in="query",
     *          name="search",
     *          description="search data by elder uid & name or contact_number",
     *          example="Sokka"
     *     ),
     *     @OA\Parameter(
     *          in="query",
     *          name="user_type",
     *          description="BZN|CGA",
     *          example="CGA"
     *     ),
     *     @OA\Parameter(
     *          in="query",
     *          name="case_status",
     *          description="filter by case_status, multiple calue comma separated",
     *          example="Ongoing,Completed,Follow Up"
     *     ),
     *     @OA\Parameter(
     *          in="query",
     *          name="district",
     *          description="filter by disctrict name, multiple calue comma separated",
     *          example="Kowloon City"
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Successful"
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
     */

    public function index(Request $request)
    {
        $perPage = $request->query('per_page', 25);
        $sortBy = $request->query('sort_by');
        $sortDir = $request->query('sort_dir');
        $elder_id = (($request->query('district') !== null) || ($request->query('search') !== null)) ? Elder::select('id') : null;
        if($request->query('elder_uids') !== null){
            $elder_uids = explode(',', $request->query('elder_uids'));
            $elder_id = Elder::select('id')->whereIn('uid', $elder_uids);
        }
        if($request->query('district') !== null){
            $districts_request = explode(',', $request->query('district'));
            $get_districts = District::select('id')->whereIn('district_name', $districts_request)->get();
            $districts_data = $get_districts->pluck('id');
            $elder_id = ($elder_id) ? $elder_id->whereIn('district_id', $districts_data) : null;
        }

        if($request->query('search') !== null){
            $search = $request->query('search');
            $elder_id = ($elder_id) ? $elder_id->where(function ($q) use ($search) {
                    $q->where('uid', 'like', "%$search%")
                        ->orWhere('name', 'like', "%$search%")
                        ->orWhere('contact_number', 'like', "%$search%");
                }) : null;
        }

        $elder_id = ($elder_id) ? $elder_id->get()->pluck('id') : null;

        $casesData = Cases::select('*');
        if($elder_id !== null){
            $casesData->whereIn('elder_id', $elder_id);
        }
        if($request->query('user_type')){
            if (in_array($request->query('user_type'), ['BZN', 'CGA'])) {
                $casesData->where('case_name', $request->query('user_type'));
            }
        }
        if($request->query('case_status')){
            $caseStatusArray = explode(',', $request->query('case_status'));
            $casesData->whereIn('case_status', $caseStatusArray);
        }
        $casesData = $casesData->with(['elder', 'elder.district', 'elder.zone']);

        // loop care plan
        $cases = $casesData->customSort($sortBy, $sortDir)
            ->paginate($perPage);
        if (!$request->query('exclude') == true || !$request->query('exclude') == 'true') {
            $care_plans = $this->externalService->getCarePlanData($request->bearerToken());
            $cases->getCollection()->transform(function ($value) use ($care_plans) {
                $value->case_manager = null;
                $value->first_visit = null;
                $value->last_visit = null;
                $value->total_visit = null;
                if (isset($care_plans[$value->id])) {
                    $care_plan = $care_plans[$value->id];
                    $value->case_manager = $care_plan['case_manager'];
                    if ($value->case_name == "BZN" && count($care_plan['bzn_notes']) > 0) {
                        $max_bzn_notes = array_count_values(collect($care_plan['bzn_notes'])->pluck('bzn_target_id')->toArray());
                        $bzn_notes_count = max($max_bzn_notes);
                        $value->first_visit = $care_plan['bzn_notes'][$bzn_notes_count - 1]['assessment_date'];
                        $value->last_visit = $care_plan['bzn_notes'][0]['assessment_date'];
                        $value->total_visit = $bzn_notes_count;
                    }
                    if ($value->case_name == "CGA" && count($care_plan['cga_notes']) > 0) {
                        $max_cga_notes = array_count_values(collect($care_plan['cga_notes'])->pluck('cga_target_id')->toArray());
                        $cga_notes_count = max($max_cga_notes);
                        $value->first_visit = $care_plan['cga_notes'][$cga_notes_count - 1]['assessment_date'];
                        $value->last_visit = $care_plan['cga_notes'][0]['assessment_date'];
                        $value->total_visit = $cga_notes_count;
                    }
                }
                return $value;
            });
        }

        return CasesResources::collection($cases);
    }

    /**
     * @OA\Post(
     *     path="/elderly-api/v1/cases",
     *     tags={"cases"},
     *     summary="Store new case",
     *     operationId="casesStore",
     *     @OA\Response(
     *         response=200,
     *         description="Successful"
     *     ),
     *      @OA\Response(
     *         response=400,
     *         description="Bad Request"
     *     ),
     *     @OA\RequestBody(
     *         description="Input required case information (in string)",
     *         required=true,
     *             @OA\JsonContent(
     *                 required={"elder_id,case_status,case_number"},
     *                 @OA\Property(property="case_name", type="string", example="Bz Nurse", description="case name"),
     *                 @OA\Property(property="case_status", type="string", example="Completed", description="case status"),
     *                 @OA\Property(property="case_number", type="integer", example="1", description="case number"),
     *                 @OA\Property(property="elder_id", type="integer", example="1", description="Elder id"),
     *                 @OA\Property(property="created_by", type="string", example="user abc", description="created by"),
     *                 @OA\Property(property="updated_by", type="string", example="user abc", description="updated by"),
     *             )
     *     )
     * )
     */

    public function store(CasesRequest $request)
    {
        $request->merge([
            'created_by' => $request->user_id,
            'updated_by' => $request->user_id,
            'created_by_name' => $request->user_name,
            'updated_by_name' => $request->user_name,
        ]);
        $validated = $request->toArray();
        $cases = Cases::create($validated);
        return response()->json([
            'message' => 'Case was created',
            'data' => new CasesSingleResource($cases),
        ], 201);
    }


    /**
     * @OA\Get(
     *     path="/elderly-api/v1/cases/{id}",
     *     operationId="GetCasesDetail",
     *     summary="get case detail use ID case",
     *     tags={"cases"},
     *     @OA\Parameter(
     *          in="path",
     *          required=true,
     *          name="id",
     *          description="The id of the case",
     *          @OA\Schema(
     *              type="integer",
     *              example="1"
     *          )
     *     ),
     *     @OA\Response(
     *          response=200,
     *          description="Case detail",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="data", type="object", ref="#/components/schemas/Cases")
     *          )
     *     ),
     *     @OA\Response(
     *          response=404,
     *          description="User not found",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(
     *                  property="error",
     *                  type="object",
     *                  @OA\Property(property="code", type="number", example="404"),
     *                  @OA\Property(property="message", type="string", example="Cannot find case with id {id}")
     *              )
     *          )
     *     )
     * )
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */

    public function show(Request $request, $caseId)
    {
        $case = Cases::findOrFail($caseId);
        $calls = ElderCalls::where('cases_id', $caseId)->orderBy('call_date', 'desc')->orderBy('id', 'desc')->get();
        if ($case && $calls) {
            $case['calls'] = $calls;
        }
        $case_data = new CasesSingleResource($case);
        $ex_data = array();
        if (!$request->query('exclude') == true || !$request->query('exclude') == 'true') {
            $ex_data = $this->externalService->getAssessmentData($request->bearerToken(), $caseId);
        }
        $result = [
            'data' => [
                'case_data' => $case_data,
                'external' => $ex_data
            ]
        ];
        return $result;
        // return new CasesSingleResource($case);
    }

    /**
     * @OA\Put(
     *     path="/elderly-api/v1/cases/{id}",
     *     tags={"cases"},
     *     summary="Update case by Id",
     *     operationId="casesUpdate",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Case Id to be updated",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful",
     *         @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="data", type="object", ref="#/components/schemas/Cases")
     *         )
     *     ),
     *      @OA\Response(
     *         response=400,
     *         description="Bad Request"
     *     ),
     *     @OA\RequestBody(
     *         description="Input required case information (in string)",
     *         required=true,
     *             @OA\JsonContent(
     *                 required={"elder_id,case_status,case_number"},
     *                 @OA\Property(property="case_name", type="string", example="Bz Nurse", description="case name"),
     *                 @OA\Property(property="caller_name", type="string", example="Sung Kang", description="caller name"),
     *                 @OA\Property(property="case_status", type="string", example="Completed", description="case status"),
     *                 @OA\Property(property="case_number", type="integer", example="1", description="case number"),
     *                 @OA\Property(property="elder_id", type="integer", example="1", description="Elder id"),
     *             )
     *     )
     * )
     */

    public function update(UpdateCasesRequest $request, $caseId)
    {
        if($request->access_role == 'helper'){
            return response()->json([
                'message' => 'Unauthorized'
            ], 401);
        }
        $request->merge([
            'updated_by' => $request->user_id,
            'updated_by_name' => $request->user_name,
        ]);
        if($request->case_status) {
            $caseManager = $this->externalService->getCaseManager($caseId);
            if(!$caseManager) {
                return response()->json([
                    'message' => 'Unauthorized, you are not the case manager.',
                    'data' => null
                ], 401);
            }
            if ($caseManager['id'] !== $request->user_id && !in_array($request->user_id, $caseManager['managers']) && $request->access_role !== 'admin') {
                return response()->json([
                    'message' => 'Unauthorized, you are not the case manager.',
                    'data' => null
                ], 401);
            }
        }
        $case = Cases::findOrFail($caseId);
        $case->update($request->toArray());
        return response()->json([
            'message' => 'Case was updated',
            'data' => new CasesSingleResource($case),
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/elderly-api/v1/cases/{id}",
     *     tags={"cases"},
     *     summary="Delete case by Id",
     *     operationId="caseDelete",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="case Id",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful"
     *     ),
     *      @OA\Response(
     *         response=400,
     *         description="Bad Request"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */

    public function destroy($caseId)
    {
        $case = Cases::findOrFail($caseId);
        $case->delete();
        return response()->json(null, 204);
    }

    /**
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function isCasesIdExists(Request $request)
    {
        $id = $request->query('id');
        $casesIdExists = Cases::where('id', $id)
            ->whereNull('cases.deleted_at')
            ->exists();

        return response()->json([
            'data' => [
                'status' => $casesIdExists,
            ],
        ]);
    }

    public function reports(Request $request)
    {
        $results = array();
        $search = $request->query('search');
        $size = $request->query('size') > 0 ? (int)$request->query('size') : 10;
        $page = $request->query('page') > 0 ? ((int)$request->query('page') - 1) * $size : 0;
        $from = $request->query('from');
        $to = $request->query('to');
        $elder_ids = [];
        $filter = $request->query('case_status');

        $appointments = $this->externalService->getAppointmentsForReport($from, $to);
        // return $appointments;
        $elder_calls = $this->elder_calls->elder_calls()->getData();
        // return $elder_calls;
        $elder_appointments = $this->externalService->getElderAppointments();
        if($appointments){
            if(count($appointments['elder_ids']) > 0) {
                $elder_ids = $appointments['elder_ids'];
            }
        }
        
        if($search && ($from || $to)){
            $elders = Elder::where('name', 'like', "%$search%")
                        ->orWhere('name_en', 'like', "%$search%")
                        ->orWhere('uid', 'like', "%$search%")
                        ->get();
            $elder_ids_by_name = $elders->pluck('id')->toArray();
            $elder_ids = array_intersect($elder_ids_by_name, $elder_ids);
        } else if ($search && !($from || $to)){
            $elders = Elder::where('name', 'like', "%$search%")
                        ->orWhere('name_en', 'like', "%$search%")
                        ->orWhere('uid', 'like', "%$search%")
                        ->get();
            $elder_ids = $elders->pluck('id')->toArray();
        }

        $cases = Cases::select(['id', 'elder_id', 'case_status'])
                    ->whereIn('elder_id', $elder_ids)
                    ->with('elder')
                    ->skip($page)
                    ->take($size)
                    ->get();

        if($filter){
            $cases = $cases->whereIn('case_status', $filter);
            $cases = $cases->values();
        }
        // $contact_number = $cases->elder != null ? $cases->elder->contact_number : null;
        // return $contact_number;
        // for ($i = 0; $i < count($cases); $i++){
        //     $data = $cases[$i]->elder;
        //     return response()->json([
        //         'data' => $data
        //     ], 200);
        // }
        if(!$cases){
            return response()->json(['success' => false, 'data'=> []], 404);
        }
        $cases_id = implode(',', $cases->pluck('id')->toArray());
        $care_plans = $this->externalService->getCarePlanForReport($cases_id);

        for($i = 0; $i < count($cases); $i++){
            $elder_id = $cases[$i]['elder']['id'];
            $case_id = $cases[$i]['id'];
            $phone_contact = $cases[$i]['elder']['contact_number'];
            $elder_name = $cases[$i]['elder']['name'];
            $results[$i]['patient_name'] = $cases[$i]['elder']['name'];
            $results[$i]['case_id'] = $cases[$i]['id'];
            $results[$i]['contact_number'] = $phone_contact;
            $results[$i]['uid'] = $cases[$i]['elder']['uid'];
            $results[$i]['case_type'] = $cases[$i]['elder']['case_type'];
            $results[$i]['case_status'] = $cases[$i]['case_status'];
            $results[$i]['case_manager'] = null;
            $results[$i]['first_visit'] = null;
            $results[$i]['last_visit'] = null;
            $results[$i]['tele_visit'] = 0;
            $results[$i]['face_visit'] = 0;
            $results[$i]['total_visit'] = 0;
            $results[$i]['calls_log'] = 0;
            $results[$i]['case_phone_contact']= 0;
            $results[$i]['contact_total_number'] = 0;
            $results[$i]['patient_care_hour'] = null;
            $results[$i]['patient_cga_notes_id'] = [];
            $results[$i]['patient_bzn_notes_id'] = [];

            // appointments data
            if($appointments && isset($appointments[$elder_id])){         
                $results[$i]['first_visit'] = $appointments[$elder_id]['first_visit'];   
                $results[$i]['last_visit'] = $appointments[$elder_id]['last_visit'];
                $results[$i]['tele_visit'] = $appointments[$elder_id]['tele_visit'];
                $results[$i]['face_visit'] = $appointments[$elder_id]['face_visit'];
                $results[$i]['total_visit'] = $appointments[$elder_id]['tele_visit'] + $appointments[$elder_id]['face_visit'];
                $results[$i]['patient_care_hour'] = "{$appointments[$elder_id]['patient_care_hour']} Hours";
            }

            // care plans data
            if($care_plans && isset($care_plans[$case_id])){
                    $results[$i]['case_manager'] = $care_plans[$case_id]['case_manager'];
                    if($cases[$i]['elder']['case_type'] == 'BZN' && isset($care_plans[$case_id]['bzn_care_target'])){
                        $results[$i]['patient_bzn_notes_id'] = array_column($care_plans[$case_id]['bzn_care_target'], 'id');
                    }
                    if($cases[$i]['elder']['case_type'] == 'CGA' && isset($care_plans[$case_id]['cga_care_target'])){
                        $results[$i]['patient_cga_notes_id'] = array_column($care_plans[$case_id]['cga_care_target'], 'id');
                    }
            }

            if($elder_name !== null){
                $swipespace = preg_replace('/[^A-Za-z0-9]+/', '_', $elder_name);
                $snakeCaseElderName = strtolower($swipespace);
                $snakeCaseElderName = trim($snakeCaseElderName, '_');
                if($elder_calls !== null && isset($elder_calls->data->$snakeCaseElderName)){
                    $results[$i]['calls_log'] = $elder_calls->data->$snakeCaseElderName;
                }
            }

            if($elder_appointments && isset($elder_appointments[$case_id])){
                $results[$i]['case_phone_contact'] = $elder_appointments[$case_id]['case_phone_contact'];
                $results[$i]['contact_total_number'] = $elder_appointments[$case_id]['contact_total_number'];
            }
        }
        return response()->json(['success' => true,'data' => $results], 200);
    }

    public function getCasesUidSet(Request $request){
        $cases = Cases::with('elder')->get();
        
        $result = new \stdClass();
        if(count($cases) > 0) {
            for($i = 0; $i < count($cases); $i++){
                $casesId = $cases[$i]->id;
                if(!property_exists($result, $casesId)){
                    $result->$casesId = ['uid' => $cases[$i]['elder'] ? $cases[$i]['elder']['uid'] : null];
                }
            }
        } else {
            return response()->json([
                'data' => null
            ], 404);
        }

        return response()->json([
            'data' => $result
        ], 200);

    }

    public function exportPatientReport(Request $request)
    {
        $result = $this->reports($request);
        $result_collection = collect($result->getData()->data);
        return Excel::download(new PatientReportsExport($result_collection), 'patient-reports.csv', \Maatwebsite\Excel\Excel::CSV);
    }

    public function getUidSetByCasesId()
    {
        $cases = Cases::select('id', 'elder_id')->with('elder')->get();
        $result = new \stdClass();
        if(count($cases) > 0) {
            for($i = 0; $i < count($cases); $i++){
                $uid = $cases[$i]['elder'] ? $cases[$i]['elder']['uid'] : null;
                $casesId = $cases[$i]->id;
                if(!property_exists($result, $casesId && $uid !== null)){
                    $result->$casesId = ['uid' => $uid];
                }
            }
        } else {
            return response()->json([
                'data' => null
            ], 404);
        }

        return response()->json([
            'data' => $result
        ], 200);
    }
}
