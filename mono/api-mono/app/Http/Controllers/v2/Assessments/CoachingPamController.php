<?php

namespace App\Http\Controllers\v2\Assessments;

use App\Http\Requests\v2\Assessments\CoachingPamIndexRequest;
use App\Http\Requests\v2\Assessments\CoachingPamUpdateRequest;
use App\Models\v2\Assessments\CoachingPam;
use Illuminate\Http\Request;

class CoachingPamController extends Controller
{
    public function index(CoachingPamIndexRequest $request)
    {
        $coachingPam = CoachingPam::orderBy('updated_at', 'desc')->get();
        if ($request->query('care_plan_id')) {
            $coachingPam = CoachingPam::where('care_plan_id', $request->query('care_plan_id'))->get();
        }
        if ($coachingPam) {
            return response()->json([
                'data' => $coachingPam,
                'message' => 'Data found.',
            ], 200);
        }

        return response()->json([
            'data' => [],
            'message' => 'Data not found',
        ], 404);
    }

    public function store(Request $request)
    {
        //
    }

    public function show($id)
    {
        $coachingPam = CoachingPam::where('id', $id)->first();
        if ($coachingPam) {
            return response()->json([
                'data' => $coachingPam,
                'message' => 'Data found.',
            ], 200);
        }

        return response()->json([
            'data' => null,
            'message' => 'Data not found',
        ], 404);
    }

    public function update(CoachingPamUpdateRequest $request)
    {
        $request->validate(['care_plan_id' => 'required|exists:care_plans,id']);
        $care_plan_id = $request->query('care_plan_id') ? $request->query('care_plan_id') : $request->care_plan_id;
        if ($care_plan_id) {
            CoachingPam::updateOrCreate(
                ['care_plan_id' => $care_plan_id],
                [
                    'care_plan_id' => $care_plan_id,
                    'section' => $request->section,
                    'intervention_group' => $request->intervention_group,
                    'gender' => $request->gender,
                    'health_manage' => $request->health_manage,
                    'active_role' => $request->active_role,
                    'self_confidence' => $request->self_confidence,
                    'drug_knowledge' => $request->drug_knowledge,
                    'self_understanding' => $request->self_understanding,
                    'self_health' => $request->self_health,
                    'self_discipline' => $request->self_discipline,
                    'issue_knowledge' => $request->issue_knowledge,
                    'other_treatment' => $request->other_treatment,
                    'change_treatment' => $request->change_treatment,
                    'issue_prevention' => $request->issue_prevention,
                    'find_solutions' => $request->find_solutions,
                    'able_maintain' => $request->able_maintain,
                    'remarks' => $request->remarks,
                ]
            );
            $coachingPam = CoachingPam::where('care_plan_id', $care_plan_id)->first();
            if ($coachingPam) {
                return response()->json([
                    'data' => $coachingPam,
                    'message' => 'Data found.',
                ], 200);
            }
        }

        return response()->json([
            'data' => null,
            'message' => 'Data not found',
        ], 404);
    }

    public function destroy($id)
    {
        $coachingPam = CoachingPam::where('id', $id)->first();
        if (! $coachingPam) {
            return response()->json([
                'data' => null,
                'message' => 'Data not found',
            ], 404);
        }
        CoachingPam::where('id', $id)->delete();

        return response()->json([
            'data' => null,
            'message' => 'No content',
        ], 204);
    }
}
