<?php

namespace App\Http\Controllers\v2\Elders;

use App\Exports\v2\Elders\PatientReportsExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\v2\Elders\CaseShowRequest;
use App\Http\Requests\v2\Elders\CasesIdExistsRequest;
use App\Http\Requests\v2\Elders\CasesIndexRequest;
use App\Http\Requests\v2\Elders\CasesReportsRequest;
use App\Http\Requests\v2\Elders\CasesRequest;
use App\Http\Requests\v2\Elders\UpdateCasesRequest;
use App\Http\Resources\v2\Elders\CasesResources;
use App\Http\Resources\v2\Elders\CasesSingleResource;
use App\Http\Services\v2\Elders\WiringServiceElder;
use App\Models\v2\Assessments\CarePlan;
use App\Models\v2\Assessments\CaseManager;
use App\Models\v2\Elders\Cases;
use App\Models\v2\Elders\District;
use App\Models\v2\Elders\Elder;
use App\Models\v2\Elders\ElderCalls;
use App\Models\v2\Users\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Excel as MaatExcel;
use Maatwebsite\Excel\Facades\Excel;
use stdClass;

class CasesController extends Controller
{
    private $wiringService;
    private $elder_calls;

    public function __construct()
    {
        $this->wiringService = new WiringServiceElder;
        $this->elder_calls = new CallsController;
    }

    public function index(CasesIndexRequest $request)
    {
        $perPage = $request->query('per_page', 25);
        $sortBy = $request->query('sort_by');
        $sortDir = $request->query('sort_dir');
        $elder_id = (($request->query('district') !== null) || ($request->query('search') !== null)) ? Elder::select('id') : null;
        if ($request->query('elder_uids') !== null) {
            $elder_uids = explode(',', $request->query('elder_uids'));
            $elder_id = Elder::select('id')->whereIn('uid', $elder_uids);
        }
        if ($request->query('district') !== null) {
            $districts_request = explode(',', $request->query('district'));
            $get_districts = District::select('id')->whereIn('district_name', $districts_request)->get();
            $districts_data = $get_districts->pluck('id');
            $elder_id = ($elder_id) ? $elder_id->whereIn('district_id', $districts_data) : null;
        }

        if ($request->query('search') !== null) {
            $search = $request->query('search');
            $elder_id = ($elder_id) ? $elder_id->where(function ($q) use ($search) {
                $q->where('uid', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%")
                    ->orWhere('contact_number', 'like', "%{$search}%");
            }) : null;
        }

        $elder_id = ($elder_id) ? $elder_id->get()->pluck('id') : null;

        $casesData = Cases::select('*');
        if ($elder_id !== null) {
            $casesData->whereIn('elder_id', $elder_id);
        }
        if ($request->query('user_type')) {
            if (in_array($request->query('user_type'), ['BZN', 'CGA'])) {
                $casesData->where('case_name', $request->query('user_type'));
            }
        }
        if ($request->query('case_status')) {
            $caseStatusArray = explode(',', $request->query('case_status'));
            $casesData->whereIn('case_status', $caseStatusArray);
        }
        $casesData = $casesData->with(['elder', 'elder.district', 'elder.zone']);

        // loop care plan
        $cases = $casesData->customSort($sortBy, $sortDir)
            ->paginate($perPage);
        if (! $request->query('exclude') == true || ! $request->query('exclude') == 'true') {
            $care_plans = $this->wiringService->getCarePlanData();
            $cases->getCollection()->transform(function ($value) use ($care_plans) {
                $value->case_manager = null;
                $value->first_visit = null;
                $value->last_visit = null;
                $value->total_visit = null;
                if (isset($care_plans[$value->id])) {
                    $care_plan = $care_plans[$value->id]->toArray();
                    $value->case_manager = $care_plan['case_manager'];

                    if (($value->case_name == 'BZN' || preg_match('/nurse/i', $value->case_status)) && count((array) $care_plan['bzn_notes']) > 0) {

                        $bzn_notes = $care_plan['bzn_notes'];

                        $bzn_target_ids = collect($bzn_notes)->pluck('bzn_target_id')->toArray();
                        $value->total_visit = count(array_keys($bzn_target_ids, min($bzn_target_ids))) ?? null;
                        $max_bzn_notes = count($bzn_notes);
                        $value->first_visit = $care_plan['bzn_notes'][$max_bzn_notes - 1]['assessment_date'];
                        $value->last_visit = $care_plan['bzn_notes'][0]['assessment_date'];
                    }
                    if ($value->case_name == 'CGA' && count((array) $care_plan['cga_notes']) > 0) {
                        $cga_notes = $care_plan['cga_notes'];
                        $cga_target_ids = collect($cga_notes)->pluck('cga_target_id')->toArray();
                        $value->total_visit = count(array_keys($cga_target_ids, min($cga_target_ids))) ?? null;
                        $max_cga_notes = count($cga_notes);
                        $value->first_visit = $care_plan['cga_notes'][$max_cga_notes - 1]['assessment_date'];
                        $value->last_visit = $care_plan['cga_notes'][0]['assessment_date'];
                    }
                }

                return $value;
            });
        }

        return CasesResources::collection($cases);
    }

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

    public function show(CaseShowRequest $request, $caseId)
    {
        $case = Cases::where('id', $caseId)->first();
        if (! $case) {
            return ['data' => ['case_data' => null, 'external' => null]];
        }
        $calls = ElderCalls::where('cases_id', $caseId)->orderBy('call_date', 'desc')->orderBy('id', 'desc')->get();
        if ($case && $calls) {
            $case['calls'] = $calls;
        }
        $case_data = new CasesSingleResource($case);
        $ex_data = [];
        if (! $request->query('exclude') == true || ! $request->query('exclude') == 'true') {
            $ex_data = $this->wiringService->getAssessmentData($caseId);
        }
        $result = [
            'data' => [
                'case_data' => $case_data,
                'external' => $ex_data,
            ],
        ];

        return $result;
        // return new CasesSingleResource($case);
    }

    public function update(UpdateCasesRequest $request, $caseId)
    {
        $request->merge([
            'updated_by' => $request->user_id,
            'updated_by_name' => $request->user_name,
        ]);
        if ($request->case_status) {
            $caseManager = $this->wiringService->getCaseManager($caseId);
            if (! $caseManager && $request->access_role !== 'admin') {
                return response()->json([
                    'data' => null,
                    'status' => [
                        'code' => 401,
                        'message' => 'Unauthorized, you are not the case manager.',
                        'errors' => [],
                    ],
                ], 401);
            }
            $caseManagerId = $caseManager['id'] ?? null;
            $caseManagers = $caseManager['managers'] ?? [];
            if ($caseManagerId !== $request->user_id && ! in_array($request->user_id, $caseManagers) && $request->access_role !== 'admin') {
                return response()->json([
                    'data' => null,
                    'status' => [
                        'code' => 401,
                        'message' => 'Unauthorized, you are not the case manager.',
                        'errors' => [],
                    ],
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

    public function destroy($caseId)
    {
        $case = Cases::findOrFail($caseId);
        $case->delete();

        return response()->json(null, 204);
    }

    public function isCasesIdExists(CasesIdExistsRequest $request)
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

    public function reports(CasesReportsRequest $request)
    {
        $results = [];
        $casesCount = Cases::count();
        $search = $request->query('search');
        $size = $request->query('size') > 0 ? (int) $request->query('size') : $casesCount;
        $page = $request->query('page') > 0 ? ((int) $request->query('page') - 1) * $size : 0;
        $from = $request->query('from') ? Carbon::parse($request->query('from'))->startOfDay() : null;
        $to = $request->query('to') ? Carbon::parse($request->query('to'))->startOfDay() : null;
        $elder_ids = [];
        $filter = $request->query('case_status');

        $appointments = $this->wiringService->getAppointmentsForReport($from, $to);
        $elder_calls = $this->elder_calls->elderCalls($from, $to);

        // $elder_appointments = $this->wiringService->getElderAppointments($from, $to);
        $caseContactHour = $this->wiringService->getCaseHour($from, $to);
        // return $caseContactHour;
        if ($appointments) {
            if (count($appointments['elder_ids']) > 0) {
                $elder_ids = $appointments['elder_ids'];
            }
        }

        if ($search) {
            $case_managers = User::select(['id', 'name', 'nickname'])->where('name', 'like', "%{$search}%")
                ->orWhere('nickname', 'like', "%{$search}%")
                ->get();
            if (count($case_managers) > 0) {
                $case_ids = [];
                $case_manager_ids = $case_managers->pluck('id')->toArray();
                $case_manager_case = CaseManager::select(['care_plan_id', 'manager_id'])->whereIn('manager_id', $case_manager_ids)->get();
                $care_plans_case = CarePlan::select(['case_id', 'manager_id'])->whereIn('manager_id', $case_manager_ids)->get();
                if (count($care_plans_case) > 0) {
                    $care_plan_case_ids = $care_plans_case->pluck('case_id')->toArray();
                    $case_ids = array_merge($case_ids, $care_plan_case_ids);
                }
                if (count($case_manager_ids) > 0) {
                    $care_plan_ids = $case_manager_case->pluck('care_plan_id')->toArray();
                    $case_manager_care_plans = CarePlan::select(['id', 'case_id'])->whereIn('id', $care_plan_ids)->get();
                    if (count($case_manager_care_plans) > 0) {
                        $case_manager_case_ids = $case_manager_care_plans->pluck('case_id')->toArray();
                        $case_ids = array_merge($case_ids, $case_manager_case_ids);
                    }
                }
                $cases_case_manager = Cases::select(['id', 'elder_id'])->whereIn('id', $case_ids)->get();
                if (count($cases_case_manager) > 0) {
                    $case_elder_ids = $cases_case_manager->pluck('elder_id')->toArray();
                    if ($from || $to) {
                        $elder_ids = array_intersect($elder_ids, $case_elder_ids);
                    } else {
                        $elder_ids = array_merge($elder_ids, $case_elder_ids);
                    }
                }
            }
        }

        if ($search && ($from || $to)) {
            $elders = Elder::where('name', 'like', "%{$search}%")
                ->orWhere('name_en', 'like', "%{$search}%")
                ->orWhere('uid', 'like', "%{$search}%")
                ->get();
            $elder_ids_by_name = $elders->pluck('id')->toArray();
            if (count($elder_ids_by_name)) {
                $elder_ids = array_intersect($elder_ids_by_name, $elder_ids);
            }
        } elseif ($search && ! ($from || $to)) {
            $elders = Elder::where('name', 'like', "%{$search}%")
                ->orWhere('name_en', 'like', "%{$search}%")
                ->orWhere('uid', 'like', "%{$search}%")
                ->get();
            $elder_ids = $elders->pluck('id')->toArray();
        }

        $cases = Cases::select(['id', 'elder_id', 'case_status', 'case_name'])
            ->whereIn('elder_id', $elder_ids)
            ->with('elder')
            ->skip($page)
            ->take($size)
            ->get();

        if ($filter) {
            $cases = $cases->whereIn('case_status', $filter);
            $cases = $cases->values();
        }
        if (! $cases) {
            return response()->json(['success' => false, 'data' => []], 404);
        }
        $cases_id = implode(',', $cases->pluck('id')->toArray());
        $care_plans = $this->wiringService->getCarePlanForReport($cases_id);
        $care_plans_visit = $this->wiringService->getCarePlanData();

        for ($i = 0; $i < count($cases); $i++) {
            $result = [];
            $elder_id = $cases[$i]['elder']['id'];
            $case_id = $cases[$i]['id'];
            $phone_contact = $cases[$i]['elder']['contact_number'];
            $elder_name = $cases[$i]['elder']['name'];

            if (! isset($care_plans_visit[$case_id])) {
                continue;
            }

            $care_plan = $care_plans_visit[$case_id]->toArray();

            if (count((array) $care_plan['bzn_notes']) == 0 && count((array) $care_plan['cga_notes']) == 0) {
                continue;
            }

            $result['patient_name'] = $cases[$i]['elder']['name'];
            $result['case_id'] = $cases[$i]['id'];
            $result['contact_number'] = $phone_contact;
            $result['uid'] = $cases[$i]['elder']['uid'];
            $result['case_type'] = $cases[$i]['elder']['case_type'];
            $result['case_status'] = $cases[$i]['case_status'];
            $result['case_manager'] = null;
            $result['first_visit'] = null;
            $result['last_visit'] = null;
            $result['tele_visit'] = 0;
            $result['face_visit'] = 0;
            $result['total_visit'] = 0;
            $result['calls_log'] = 0;
            $result['case_phone_contact'] = $caseContactHour->$case_id['case_phone_contact'] ?? 0;
            $result['contact_total_number'] = $caseContactHour->$case_id['contact_total_number'] ?? 0;
            $result['patient_care_hour'] = null;
            $result['patient_cga_notes_id'] = [];
            $result['patient_bzn_notes_id'] = [];

            // appointments data
            if ($appointments && isset($appointments[$elder_id])) {
                if (($cases[$i]['case_name'] == 'BZN' || preg_match('/nurse/i', $cases[$i]['case_status'])) && count((array) $care_plan['bzn_notes']) > 0) {
                    $bzn_notes = $care_plan['bzn_notes'];

                    $bzn_target_ids = collect($bzn_notes)->pluck('bzn_target_id')->toArray();
                    $result['total_visit'] = count(array_keys($bzn_target_ids, min($bzn_target_ids))) ?? null;
                    $max_bzn_notes = count($bzn_notes);
                    $result['first_visit'] = $care_plan['bzn_notes'][$max_bzn_notes - 1]['assessment_date'];
                    $result['last_visit'] = $care_plan['bzn_notes'][0]['assessment_date'];
                }
                if ($cases[$i]['case_name'] == 'CGA' && count((array) $care_plan['cga_notes']) > 0) {
                    $cga_notes = $care_plan['cga_notes'];
                    $cga_target_ids = collect($cga_notes)->pluck('cga_target_id')->toArray();
                    $result['total_visit'] = count(array_keys($cga_target_ids, min($cga_target_ids))) ?? null;
                    $max_cga_notes = count($cga_notes);
                    $result['first_visit'] = $care_plan['cga_notes'][$max_cga_notes - 1]['assessment_date'];
                    $result['last_visit'] = $care_plan['cga_notes'][0]['assessment_date'];
                }

                $result['tele_visit'] = $appointments[$elder_id]['tele_visit'];
                $result['face_visit'] = $appointments[$elder_id]['face_visit'];
                $result['patient_care_hour'] = "{$appointments[$elder_id]['patient_care_hour']} Hours";
            }

            // care plans data
            if ($care_plans && isset($care_plans[$case_id])) {
                $result['case_manager'] = $care_plans[$case_id]['case_manager'];
                if ($cases[$i]['elder']['case_type'] == 'BZN' && isset($care_plans[$case_id]['bzn_care_target'])) {
                    $result['patient_bzn_notes_id'] = array_column((array) $care_plans[$case_id]['bzn_care_target'], 'id');
                }
                if ($cases[$i]['elder']['case_type'] == 'CGA' && isset($care_plans[$case_id]['cga_care_target'])) {
                    $result['patient_cga_notes_id'] = array_column((array) $care_plans[$case_id]['cga_care_target'], 'id');
                }
            }

            if ($elder_name !== null) {
                $swipespace = preg_replace('/[^A-Za-z0-9]+/', '_', $elder_name);
                $snakeCaseElderName = strtolower($swipespace);
                $snakeCaseElderName = trim($snakeCaseElderName, '_');
                if ($elder_calls !== null && isset($elder_calls->$snakeCaseElderName)) {
                    $result['calls_log'] = $elder_calls->$snakeCaseElderName;
                }
            }

            // if ($elder_appointments && isset($elder_appointments[$case_id])) {
            //     $result['case_phone_contact'] = $elder_appointments[$case_id]['case_phone_contact'];
            //     $result['contact_total_number'] = $elder_appointments[$case_id]['contact_total_number'];
            // }
            array_push($results, $result);
        }

        return response()->json(['success' => true, 'data' => $results], 200);
    }

    public function getCasesUidSet(Request $request)
    {
        $cases = Cases::with('elder')->get();
        if (count($cases) == 0) {
            return response()->json([
                'data' => null,
            ], 404);
        }
        $result = new stdClass;
        for ($i = 0; $i < count($cases); $i++) {
            $casesId = $cases[$i]->id;
            if (! property_exists($result, $casesId)) {
                $result->$casesId = ['uid' => $cases[$i]['elder'] ? $cases[$i]['elder']['uid'] : null];
            }
        }

        return response()->json([
            'data' => $result,
        ], 200);
    }

    public function exportPatientReport(Request $request)
    {
        $result = $this->reports($request);
        $result_collection = collect($result->getData()->data);

        return Excel::download(new PatientReportsExport($result_collection), 'patient-reports.csv', MaatExcel::CSV);
    }

    public function getUidSetByCasesId()
    {
        $cases = Cases::select('id', 'elder_id')->with('elder')->get();
        if (count($cases) == 0) {
            return response()->json([
                'data' => null,
            ], 404);
        }
        $result = new stdClass;
        for ($i = 0; $i < count($cases); $i++) {
            $uid = $cases[$i]['elder'] ? $cases[$i]['elder']['uid'] : null;
            $casesId = $cases[$i]->id;
            if (! property_exists($result, $casesId && $uid !== null)) {
                $result->$casesId = ['uid' => $uid];
            }
        }

        return response()->json([
            'data' => $result,
        ], 200);
    }
}
