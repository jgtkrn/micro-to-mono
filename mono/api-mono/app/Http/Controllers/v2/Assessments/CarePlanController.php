<?php

namespace App\Http\Controllers\v2\Assessments;

use App\Http\Requests\v2\Assessments\CarePlanIndexRequest;
use App\Http\Requests\v2\Assessments\CarePlanStoreRequest;
use App\Http\Requests\v2\Assessments\CheckCarePlanCaseManagerRequest;
use App\Http\Requests\v2\Assessments\ReportResourceSetRequest;
use App\Http\Requests\v2\Assessments\ReportsResourceStaffSetRequest;
use App\Http\Resources\v2\Assessments\CarePlanResource;
use App\Http\Services\v2\Assessments\WiringServiceAssessment;
use App\Models\v2\Assessments\CarePlan;
use App\Models\v2\Assessments\CoachingPam;
use App\Models\v2\Assessments\PreCoachingPam;
use App\Traits\RespondsWithHttpStatus;
use Illuminate\Http\Request;
use stdClass;

class CarePlanController extends Controller
{
    use RespondsWithHttpStatus;
    private $wiringService;

    public function __construct()
    {
        $this->wiringService = new WiringServiceAssessment;
    }

    public function index(CarePlanIndexRequest $request)
    {
        $case_id = $request->query('case_id');
        if ($case_id) {
            $care_plan = CarePlan::where('case_id', $case_id)->first();
            if (! $care_plan) {
                return $this->success(null);
            }
            $coachingPam = CoachingPam::where('care_plan_id', $care_plan->id)->first();
            if (! $coachingPam) {
                CoachingPam::updateOrCreate(['care_plan_id' => $care_plan->id], []);
            }
            $preCoachingPam = PreCoachingPam::where('care_plan_id', $care_plan->id)->first();
            if (! $preCoachingPam) {
                PreCoachingPam::updateOrCreate(['care_plan_id' => $care_plan->id], []);
            }

            return new CarePlanResource($care_plan);
        }
        if (! $case_id) {
            $care_plan = CarePlan::orderBy('created_at', 'desc')->get();
            if (! $care_plan) {
                return $this->success(null);
            }

            return CarePlanResource::collection($care_plan);
        }
    }

    public function create()
    {
    }

    public function store(CarePlanStoreRequest $request)
    {
        if (
            $request->access_role !== 'admin'
        ) {
            return response()->json([
                'data' => null,
                'status' => [
                    'code' => 401,
                    'message' => 'Unauthorized',
                    'errors' => [],
                ],
            ], 401);
        }

        if ($request->is_other && $request->access_role !== 'admin') {
            return response()->json([
                'data' => null,
                'status' => [
                    'code' => 401,
                    'message' => 'User not in any team access',
                    'errors' => [],
                ],
            ], 401);
        }

        $manager_name = null;
        $handler_name = null;
        $manager_id = null;
        $handler_id = null;
        if ($request->manager_id) {
            $manager = $this->wiringService->getUserById($request->manager_id);
            if ($manager) {
                $manager_name = $manager['nickname'];
                $manager_id = $request->manager_id;
            }
        }
        if ($request->handler_id) {
            $handler = $this->wiringService->getUserById($request->handler_id);
            if ($handler) {
                $handler_name = $handler['nickname'];
                $handler_id = $request->handler_id;
            }
        }

        $request->merge([
            'case_manager' => $manager_name,
            'handler' => $handler_name,
            'manager_id' => $manager_id,
            'handler_id' => $handler_id,
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
            'handler_id' => $request->handler_id,
        ]);
        $coachingPam = CoachingPam::where('care_plan_id', $care_plan->id)->first();
        if (! $coachingPam) {
            CoachingPam::updateOrCreate(['care_plan_id' => $care_plan->id], []);
        }
        $preCoachingPam = PreCoachingPam::where('care_plan_id', $care_plan->id)->first();
        if (! $preCoachingPam) {
            PreCoachingPam::updateOrCreate(['care_plan_id' => $care_plan->id], []);
        }

        return new CarePlanResource($care_plan);
    }

    public function show(Request $request, $id)
    {

        if (
            $request->access_role !== 'admin' &&
            $request->access_role !== 'manager' &&
            $request->access_role !== 'user'
        ) {
            return response()->json([
                'data' => null,
                'status' => [
                    'code' => 401,
                    'message' => 'Unauthorized',
                    'errors' => [],
                ],
            ], 401);
        }

        if ($request->is_other && $request->access_role !== 'admin') {
            return response()->json([
                'data' => null,
                'status' => [
                    'code' => 401,
                    'message' => 'User not in any team access',
                    'errors' => [],
                ],
            ], 401);
        }

        $care_plan = CarePlan::find($id);

        if (! $care_plan) {
            return $this->failure('Care plan not found', 404);
        }

        $coachingPam = CoachingPam::where('care_plan_id', $care_plan->id)->first();
        if (! $coachingPam) {
            CoachingPam::updateOrCreate(['care_plan_id' => $care_plan->id], []);
        }

        $preCoachingPam = PreCoachingPam::where('care_plan_id', $care_plan->id)->first();
        if (! $preCoachingPam) {
            PreCoachingPam::updateOrCreate(['care_plan_id' => $care_plan->id], []);
        }

        return new CarePlanResource($care_plan);
    }

    public function update(Request $request, $id)
    {
        $user = $request->user_id;
        $care_plan_role = CarePlan::where('id', $id)->with('caseManagers')->first();
        if (! $care_plan_role) {
            return response()->json([
                'data' => null,
                'message' => 'Care Plan not Found',
            ], 404);
        }
        $managers = count($care_plan_role->caseManagers) == 0 ? [] : $care_plan_role->caseManagers->pluck('manager_id')->toArray();

        if (
            $care_plan_role->manager_id !== $user &&
            ! in_array($user, $managers) &&
            $request->access_role !== 'admin'
        ) {
            return response()->json([
                'data' => null,
                'status' => [
                    'code' => 401,
                    'message' => 'Failed to update care plan, you are not the author',
                    'errors' => [],
                ],
            ], 401);
        }

        if ($request->is_other && $request->access_role !== 'admin') {

            return response()->json([
                'data' => null,
                'status' => [
                    'code' => 401,
                    'message' => 'User not in any team access',
                    'errors' => [],
                ],
            ], 401);
        }

        $manager_name = null;
        $handler_name = null;
        $manager_id = null;
        $handler_id = null;
        if ($request->manager_id) {
            $manager = $this->wiringService->getUserById($request->manager_id);
            if ($manager) {
                $manager_name = $manager['nickname'];
                $manager_id = $request->manager_id;
            }
        }
        if ($request->handler_id) {
            $handler = $this->wiringService->getUserById($request->handler_id);
            if ($handler) {
                $handler_name = $handler['nickname'];
                $handler_id = $request->handler_id;
            }
        }

        $request->merge([
            'case_manager' => $manager_name,
            'handler' => $handler_name,
            'manager_id' => $manager_id,
            'handler_id' => $handler_id,
        ]);
        $request->validate([
            'case_id' => 'required',
            'case_type' => 'required|in:CGA,BZN',
        ]);

        $care_plan = CarePlan::find($id);

        if (! $care_plan) {
            return $this->failure('Care plan not found', 404);
        }

        $updated = $care_plan->update([
            'case_id' => $request->case_id,
            'case_type' => $request->case_type,
            'case_manager' => $request->case_manager,
            'handler' => $request->handler,
            'manager_id' => $request->manager_id,
            'handler_id' => $request->handler_id,
        ]);

        if (! $updated) {
            return $this->failure('Failed to update care plan');
        }

        return new CarePlanResource($care_plan);
    }

    public function destroy(Request $request, $id)
    {

        if (
            $request->is_hcsw &&
            $request->is_hcw &&
            $request->access_role !== 'admin') {
            return response()->json([
                'data' => null,
                'status' => [
                    'code' => 401,
                    'message' => 'Unauthorized',
                    'errors' => [],
                ],
            ], 401);
        }

        if ($request->is_other && $request->access_role !== 'admin') {
            return response()->json([
                'data' => null,
                'status' => [
                    'code' => 401,
                    'message' => 'User not in any team access',
                    'errors' => [],
                ],
            ], 401);
        }

        $care_plan = CarePlan::find($id);

        if (! $care_plan) {
            return $this->failure('Care plan not found', 404);
        }

        $care_plan->delete();

        return response(null, 204);
    }

    public function reportsResourceSet(ReportResourceSetRequest $request)
    {
        $care_plans = CarePlan::select(['id', 'case_id', 'case_manager', 'manager_id']);
        if ($request->query('cases_id')) {
            $cases_id = explode(',', $request->query('cases_id'));
            $care_plans = $care_plans->whereIn('case_id', $cases_id);
        }
        $care_plans = $care_plans->without(['coachingPam', 'bznNotes', 'cgaNotes'])
            ->with([
                'bznCareTarget' => function ($query) {
                    $query->select(['care_plan_id', 'id'])->without('bznConsultationNotes')->get();
                },
                'cgaCareTarget' => function ($query) {
                    $query->select(['care_plan_id', 'id'])->without('cgaConsultationNotes')->get();
                },
            ])
            ->get();
        if (count($care_plans) == 0) {
            return response()->json(['data' => null], 404);
        }
        $results = new stdClass;
        for ($i = 0; $i < count($care_plans); $i++) {
            $case_id = $care_plans[$i]['case_id'];
            if ($case_id !== null && ! property_exists($results, $case_id)) {
                $results->$case_id['care_plan_id'] = $care_plans[$i]['id'];
                $results->$case_id['case_id'] = $care_plans[$i]['case_id'];
                $results->$case_id['case_manager'] = $care_plans[$i]['case_manager'];
                $results->$case_id['bzn_care_target'] = $care_plans[$i]['bznCareTarget'];
                $results->$case_id['cga_care_target'] = $care_plans[$i]['cgaCareTarget'];
            }
        }

        return response()->json(['data' => $results], 200);
    }

    public function reportsResourceStaffSet(ReportsResourceStaffSetRequest $request)
    {
        $care_plans = CarePlan::select(['id', 'case_manager']);
        if ($request->query('staffNames')) {
            $staffNames = explode(',', $request->query('staffNames'));
            $care_plans = $care_plans->whereIn('case_manager', $staffNames);
        }
        $care_plans = $care_plans->without(['coachingPam', 'bznNotes', 'cgaNotes'])
            ->get();
        if (count($care_plans) == 0) {
            return response()->json(['data' => null], 404);
        }
        $results = new stdClass;
        for ($i = 0; $i < count($care_plans); $i++) {
            $staffName = $care_plans[$i]['case_manager'];
            if ($staffName !== null) {
                $swipespace = preg_replace('/[^A-Za-z0-9]+/', '_', $staffName);
                $snakeCaseStaffName = strtolower($swipespace);
                $snakeCaseStaffName = trim($snakeCaseStaffName, '_');
                if (! property_exists($results, $staffName)) {
                    $results->$snakeCaseStaffName = 1;
                } elseif (property_exists($results, $staffName)) {
                    $results->$snakeCaseStaffName += 1;
                }
            }
        }

        return response()->json(['data' => $results], 200);
    }

    public function checkCarePlanCaseManager(CheckCarePlanCaseManagerRequest $request)
    {
        $caseId = $request->query('case_id');
        if (! $caseId) {
            return response()->json(['data' => null], 404);
        }

        $care_plan = CarePlan::select(['id', 'case_id', 'case_manager', 'manager_id'])->without(['coachingPam', 'bznNotes', 'cgaNotes'])->where('case_id', $caseId)->with('caseManagers')->first();
        if (! $care_plan) {
            return response()->json(['data' => null], 404);
        } elseif ($care_plan->case_manager === null) {
            return response()->json(['data' => null], 404);
        }

        return response()->json(['data' => $care_plan], 200);
    }

    public function caseManagerByCasesSet(Request $request)
    {
        $care_plans = CarePlan::select(['id', 'case_id', 'case_manager'])->get();
        if (! $care_plans) {
            return response()->json(['data' => null], 404);
        }
        $result = new stdClass;
        for ($i = 0; $i < count($care_plans); $i++) {
            $caseId = $care_plans[$i]->case_id;
            if (
                ! isset($result->$caseId) &&
                $caseId !== null &&
                $care_plans[$i]->case_manager !== null
            ) {
                $result->$caseId['case_manager'] = $care_plans[$i]->case_manager;
            }
        }

        return response()->json(['data' => $result], 200);
    }

    public function getCaseStatus(Request $request)
    {
        $case_status = $this->wiringService->getCasesStatus();
        // return $case_status["204"];
        if (! $case_status) {
            return response()->json(['data' => null], 404);
        }
        $care_plans = CarePlan::select(['id', 'case_id', 'manager_id', 'case_type'])->with(['bznNotes', 'cgaNotes'])->get();
        // return $care_plans;
        $result = new stdClass;
        $case_status = $case_status->toArray();

        for ($i = 0; $i < count($care_plans); $i++) {
            $caseId = $care_plans[$i]->case_id;
            $caseIdString = "{$caseId}";
            $managerId = $care_plans[$i]->manager_id;
            $managerIdString = "{$managerId}";
            $cgaNotes = $care_plans[$i]->cgaNotes ? $care_plans[$i]->cgaNotes->toArray() : [];
            $bznNotes = $care_plans[$i]->bznNotes ? $care_plans[$i]->bznNotes->toArray() : [];

            if ($managerId) {
                if (! isset($result->$managerIdString)) {
                    if (isset($case_status[$caseIdString])) {
                        $result->$managerIdString = $case_status[$caseIdString];
                    }
                }
                if (isset($case_status[$caseIdString])) {
                    $result->$managerIdString['on_going'] = $case_status[$caseIdString]['on_going'] + $result->$managerIdString['on_going'];
                    $result->$managerIdString['pending'] = $case_status[$caseIdString]['pending'] + $result->$managerIdString['pending'];
                    $result->$managerIdString['finished'] = $case_status[$caseIdString]['finished'] + $result->$managerIdString['finished'];
                    $result->$managerIdString['total_visit'] = 0;
                    if ($care_plans[$i]->case_type === 'CGA' && count($cgaNotes) > 0) {
                        $result->$managerIdString['total_visit'] += count($cgaNotes);
                    }
                    if ($care_plans[$i]->case_type === 'BZN' && count($bznNotes) > 0) {
                        $result->$managerIdString['total_visit'] += count($bznNotes);
                    }
                }

            }
        }

        return response()->json(['data' => $result], 200);
    }
}
