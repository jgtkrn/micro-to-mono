<?php

namespace App\Http\Controllers\v2\Assessments;

use App\Exports\v2\Assessments\HealthCoachingGoalExport;
use App\Http\Requests\v2\Assessments\CgaCareTargetStoreRequest;
use App\Http\Requests\v2\Assessments\CgaCareTargetStoreV2Request;
use App\Http\Requests\v2\Assessments\IndexCgaCareTargetRequest;
use App\Http\Resources\v2\Assessments\CgaCareTargetResource;
use App\Http\Services\v2\Assessments\WiringServiceAssessment;
use App\Models\v2\Assessments\CarePlan;
use App\Models\v2\Assessments\CgaCareTarget;
use App\Models\v2\Assessments\CgaConsultationNotes;
use App\Traits\RespondsWithHttpStatus;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class CgaCareTargetController extends Controller
{
    use RespondsWithHttpStatus;
    private $wiringService;

    public function __construct()
    {
        $this->wiringService = new WiringServiceAssessment;
    }

    public function index(IndexCgaCareTargetRequest $request)
    {
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
        $request->validate([
            'care_plan_id' => 'required|integer|exists:care_plans,id,deleted_at,NULL',
        ]);
        $care_plan_id = $request->query('care_plan_id');
        $results = CgaCareTarget::where('care_plan_id', $care_plan_id)->get();

        return CgaCareTargetResource::collection($results);
    }

    public function store(CgaCareTargetStoreRequest $request)
    {
        $user = $request->user_id;
        $care_plan_role = CarePlan::where('id', $request->care_plan_id)->with('caseManagers')->first();
        if (! $care_plan_role) {
            return response()->json([
                'data' => null,
                'message' => 'Failed to update cga care target, care plan not exist',
            ], 404);
        }
        $managers = count($care_plan_role->caseManagers) == 0 ? [] : $care_plan_role->caseManagers->pluck('manager_id')->toArray();

        if (
            $care_plan_role->manager_id !== $user &&
            ! in_array($user, $managers) &&
            $request->access_role !== 'admin'
            // &&
            // $request->hcw &&
            // !$request->is_cga
        ) {
            return response()->json([
                'data' => null,
                'status' => [
                    'code' => 401,
                    'message' => 'Failed to update cga care target, you are not the author',
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
        $request->validate([
            'care_plan_id' => 'integer|exists:care_plans,id,deleted_at,NULL',
        ]);
        $care_plan_id = $request->query('care_plan_id') ? $request->query('care_plan_id') : $request->care_plan_id;
        $obj_length = count((array) $request->target);
        $cga_plan_list = [[]];
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

    public function show($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'care_plan_id' => 'required|integer|exists:care_plans,id,deleted_at,NULL',
        ]);

        $care_target = CgaCareTarget::where('id', $id)->first();
        $care_plan_id = $care_target ? $care_target->care_plan_id : ($request->care_plan_id ? $request->care_plan_id : null);
        $user = $request->user_id;
        $care_plan = CarePlan::where('id', $care_plan_id)->with('caseManagers')->first();
        if (! $care_plan) {
            return response()->json([
                'data' => null,
                'message' => 'Failed to update cga care target, care plan not exist',
            ], 404);
        }
        $managers = count($care_plan->caseManagers) == 0 ? [] : $care_plan->caseManagers->pluck('manager_id')->toArray();

        if (
            $care_plan->manager_id !== $user &&
            ! in_array($user, $managers) &&
            $request->access_role !== 'admin'
            // &&
            // $request->hcw &&
            // !$request->is_cga
        ) {
            return response()->json([
                'data' => null,
                'status' => [
                    'code' => 401,
                    'message' => 'Failed to update cga care target, you are not the author',
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
                'later_change_stage' => $request->later_change_stage,
            ]
        );

        if (! $results) {
            return $this->failure('Failed to update cga care target');
        }

        return new CgaCareTargetResource($results);
    }

    public function destroy($id)
    {
        $notes = CgaConsultationNotes::where('cga_target_id', $id)->get();
        if (count($notes) > 0) {
            return response()->json([
                'data' => null,
                'message' => 'This cga care target has consultation notes',
            ], 409);
        }
        $target = CgaCareTarget::where('id', $id)->first();
        if (! $target) {
            return response()->json([
                'data' => null,
                'message' => 'This cga care target does not exist',
            ], 404);
        }
        CgaCareTarget::where('id', $id)->delete();

        return response()->json([
            'data' => null,
            'message' => 'Success delete cga care target',
        ]);
    }

    public function storeV2(CgaCareTargetStoreV2Request $request)
    {
        $user = $request->user_id;
        $care_plan_role = CarePlan::where('id', $request->care_plan_id)->with('caseManagers')->first();
        if (! $care_plan_role) {
            return response()->json([
                'data' => null,
                'message' => 'Failed to update cga care target, care plan not exist',
            ], 404);
        }
        $managers = count($care_plan_role->caseManagers) == 0 ? [] : $care_plan_role->caseManagers->pluck('manager_id')->toArray();

        if (
            $care_plan_role->manager_id !== $user &&
            ! in_array($user, $managers) &&
            $request->access_role !== 'admin'
            // &&
            // $request->hcw &&
            // !$request->is_cga
        ) {
            return response()->json([
                'data' => null,
                'status' => [
                    'code' => 401,
                    'message' => 'Failed to update cga care target, you are not the author',
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
        $request->validate([
            'cga_care_targets' => 'array|nullable',
            'care_plan_id' => 'integer|exists:care_plans,id,deleted_at,NULL',
        ]);
        $obj_length = count((array) $request->cga_care_targets);
        $cga_plan_list = [[]];
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
        if (! $care_plan) {
            return $this->failure('Care Plan with ID:' . " {$request->care_plan_id}" . 'not found', 404);
        }
        $cga_target_care = $care_plan->cgaCareTarget()->createMany($request->cga_care_targets);

        if ($cga_target_care) {
            $results = CgaCareTarget::where('care_plan_id', $request->care_plan_id)->get();
        }

        return CgaCareTargetResource::collection($results);
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
            ->with('carePlan');

        if ($request->query('from') && $request->query('to')) {
            $from = Carbon::parse($request->query('from'))->startOfDay();
            $to = Carbon::parse($request->query('to'))->endOfDay();
            $data = $data->whereBetween('updated_at', [$from, $to]);
        }

        $data = $data->get();

        $uid = $this->wiringService->getUidSetByCasesId();

        for ($i = 0; $i < count($data); $i++) {
            $caseId = strval($data[$i]->carePlan?->case_id);
            if ($caseId !== null) {
                $data[$i]['uid'] = isset($uid[$caseId]) ? $uid[$caseId]['uid'] : null;
            } else {
                $data[$i]['uid'] = null;
            }
        }

        return Excel::download(new HealthCoachingGoalExport($data), 'health-coaching-goal.csv');
    }
}
