<?php

namespace App\Http\Controllers\v2\Assessments;

use App\Http\Requests\v2\Assessments\CaseManagerIndexRequest;
use App\Http\Resources\v2\Assessments\CaseManagerResource;
use App\Models\v2\Assessments\CarePlan;
use App\Models\v2\Assessments\CaseManager;
use App\Traits\RespondsWithHttpStatus;
use Illuminate\Http\Request;

class CaseManagerController extends Controller
{
    use RespondsWithHttpStatus;

    public function index(CaseManagerIndexRequest $request)
    {
        $request->validate([
            'care_plan_id' => 'required|integer|exists:care_plans,id,deleted_at,NULL',
        ]);
        $care_plan_id = $request->query('care_plan_id');
        $case_managers = CaseManager::where('care_plan_id', $care_plan_id)->get();

        return CaseManagerResource::collection($case_managers);
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        //
    }

    public function show(CaseManager $caseManager)
    {
        //
    }

    public function edit(CaseManager $caseManager)
    {
        //
    }

    public function update(Request $request)
    {
        if ($request->is_other && $request->access_role !== 'admin') {
            return response()->json([
                'status' => [
                    'code' => 401,
                    'message' => '',
                    'errors' => [
                        [
                            'message' => 'User not in any team access',
                        ],
                    ],
                ],
            ], 401);
        }

        $request->validate([
            'care_plan_id' => 'required|integer|exists:care_plans,id,deleted_at,NULL',
            'case_managers' => 'nullable|array',
            'case_managers.*.manager_id' => 'required|integer',
            'case_managers.*.manager_name' => 'required|string',
        ]);

        $obj_length = count((array) $request->case_managers);
        $case_manager_list = [[]];

        for ($i = 0; $i < $obj_length; $i++) {
            $case_manager_list[$i]['care_plan_id'] = $request->care_plan_id;
            $case_manager_list[$i]['manager_id'] = $request->case_managers[$i]['manager_id'];
            $case_manager_list[$i]['manager_name'] = $request->case_managers[$i]['manager_name'];
        }

        $care_plan = CarePlan::where('id', $request->care_plan_id)->first();

        if ($obj_length > 0 && ! $care_plan) {
            $care_plan->caseManagers()->createMany($case_manager_list);
        } elseif ($obj_length > 0 && $care_plan) {
            $care_plan->caseManagers()->delete();
            $care_plan->caseManagers()->createMany($case_manager_list);
        } elseif ($obj_length == 0 && $care_plan) {
            $care_plan->caseManagers()->delete();
        } elseif ($obj_length == 0 && ! $care_plan) {
            $care_plan->caseManagers()->delete();
        }

        return CaseManagerResource::collection($care_plan->caseManagers()->get());
    }

    public function destroy($id)
    {
        $case_manager = CaseManager::where('id', $id)->first();
        if (! $case_manager) {
            return $this->failure('Case Manager not found', 404);
        }
        $case_manager->delete();

        return response()->json([
            'data' => null,
            'message' => 'success delete case manager access',
        ], 200);
    }
}
