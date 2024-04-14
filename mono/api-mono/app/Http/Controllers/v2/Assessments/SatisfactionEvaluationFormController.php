<?php

namespace App\Http\Controllers\v2\Assessments;

use App\Http\Requests\v2\Assessments\SatisfactionEvaluationFormIndexRequest;
use App\Http\Requests\v2\Assessments\SatisfactionEvaluationFormStoreRequest;
use App\Http\Requests\v2\Assessments\SatisfactionEvaluationFormUpdateRequest;
use App\Models\v2\Assessments\SatisfactionEvaluationForm;
use Carbon\Carbon;

class SatisfactionEvaluationFormController extends Controller
{
    public function index(SatisfactionEvaluationFormIndexRequest $request)
    {
        $satisfactionEvaluationForm = SatisfactionEvaluationForm::orderBy('updated_at', 'desc')->get();

        if ($request->query('case_id')) {
            $satisfactionEvaluationForm = SatisfactionEvaluationForm::where('case_id', $request->query('case_id'))->latest('updated_at')->first();
        }

        if ($satisfactionEvaluationForm) {
            return response()->json([
                'data' => $satisfactionEvaluationForm,
                'message' => 'Data found.',
            ], 200);
        }

        return response()->json([
            'data' => [],
            'message' => 'Data not found',
        ], 404);

    }

    public function store(SatisfactionEvaluationFormStoreRequest $request)
    {
        $satisfactionEvaluationForm = SatisfactionEvaluationForm::create([
            'elder_reference_number' => $request->elder_reference_number,
            'assessor_name' => $request->assessor_name,
            'evaluation_date' => $request->evaluation_date ? new Carbon($request->evaluation_date) : null,
            'case_id' => $request->case_id,
            'clear_plan' => $request->clear_plan,
            'enough_discuss_time' => $request->enough_discuss_time,
            'appropriate_plan' => $request->appropriate_plan,
            'has_discussion_team' => $request->has_discussion_team,
            'own_involved' => $request->own_involved,
            'enough_opportunities' => $request->enough_opportunities,
            'enough_information' => $request->enough_information,
            'selfcare_improved' => $request->selfcare_improved,
            'confidence_team' => $request->confidence_team,
            'feel_respected' => $request->feel_respected,
            'performance_rate' => $request->performance_rate,
            'service_scale' => $request->service_scale,
            'recommend_service' => $request->recommend_service,

            // user data
            'created_by' => $request->user_id,
            'updated_by' => $request->user_id,
            'created_by_name' => $request->user_name,
            'updated_by_name' => $request->user_name,
        ]);

        if ($satisfactionEvaluationForm) {
            return response()->json([
                'data' => $satisfactionEvaluationForm,
                'message' => 'Data found.',
            ], 201);
        }

        return response()->json([
            'data' => [],
            'message' => 'Data not found',
        ], 404);
    }

    public function show($id)
    {
        $satisfactionEvaluationForm = SatisfactionEvaluationForm::where('id', $id)->first();

        if ($satisfactionEvaluationForm) {
            return response()->json([
                'data' => $satisfactionEvaluationForm,
                'message' => 'Data found.',
            ], 200);
        }

        return response()->json([
            'data' => null,
            'message' => 'Data not found',
        ], 404);
    }

    public function update(SatisfactionEvaluationFormUpdateRequest $request, $id)
    {
        $satisfactionEvaluationForm = SatisfactionEvaluationForm::where('id', $id)->first();

        if ($satisfactionEvaluationForm) {
            $updated = $satisfactionEvaluationForm->update([
                'elder_reference_number' => $request->elder_reference_number ? $request->elder_reference_number : $satisfactionEvaluationForm->elder_reference_number,
                'assessor_name' => $request->assessor_name ? $request->assessor_name : $satisfactionEvaluationForm->assessor_name,
                'evaluation_date' => $request->evaluation_date ? new Carbon($request->evaluation_date) : $satisfactionEvaluationForm->evaluation_date,
                'case_id' => $request->case_id ? $request->case_id : $satisfactionEvaluationForm->case_id,
                'clear_plan' => $request->clear_plan ? $request->clear_plan : $satisfactionEvaluationForm->clear_plan,
                'enough_discuss_time' => $request->enough_discuss_time ? $request->enough_discuss_time : $satisfactionEvaluationForm->enough_discuss_time,
                'appropriate_plan' => $request->appropriate_plan ? $request->appropriate_plan : $satisfactionEvaluationForm->appropriate_plan,
                'has_discussion_team' => $request->has_discussion_team ? $request->has_discussion_team : $satisfactionEvaluationForm->has_discussion_team,
                'own_involved' => $request->own_involved ? $request->own_involved : $satisfactionEvaluationForm->own_involved,
                'enough_opportunities' => $request->enough_opportunities ? $request->enough_opportunities : $satisfactionEvaluationForm->enough_opportunities,
                'enough_information' => $request->enough_information ? $request->enough_information : $satisfactionEvaluationForm->enough_information,
                'selfcare_improved' => $request->selfcare_improved ? $request->selfcare_improved : $satisfactionEvaluationForm->selfcare_improved,
                'confidence_team' => $request->confidence_team ? $request->confidence_team : $satisfactionEvaluationForm->confidence_team,
                'feel_respected' => $request->feel_respected ? $request->feel_respected : $satisfactionEvaluationForm->feel_respected,
                'performance_rate' => $request->performance_rate ? $request->performance_rate : $satisfactionEvaluationForm->performance_rate,
                'service_scale' => $request->service_scale ? $request->service_scale : $satisfactionEvaluationForm->service_scale,
                'recommend_service' => $request->recommend_service ? $request->recommend_service : $satisfactionEvaluationForm->recommend_service,

                // user data
                'updated_by' => $request->user_id,
                'updated_by_name' => $request->user_name,
            ]);
            if ($updated) {
                $satisfactionEvaluationForm = SatisfactionEvaluationForm::where('id', $id)->first();

                return response()->json([
                    'data' => $satisfactionEvaluationForm,
                    'message' => 'Data updated.',
                ], 202);
            }

            return response()->json([
                'data' => null,
                'message' => 'Data update failed',
            ], 409);
        }

        return response()->json([
            'data' => null,
            'message' => 'Data not found',
        ], 404);

    }

    public function destroy($id)
    {
        $satisfactionEvaluationForm = SatisfactionEvaluationForm::where('id', $id)->first();

        if ($satisfactionEvaluationForm) {
            $satisfactionEvaluationForm->delete();

            return response()->json([
                'data' => null,
                'message' => 'Data deleted.',
            ], 204);
        }

        return response()->json([
            'data' => null,
            'message' => 'Data not found',
        ], 404);
    }
}
