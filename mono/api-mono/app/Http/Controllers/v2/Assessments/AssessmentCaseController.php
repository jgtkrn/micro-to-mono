<?php

namespace App\Http\Controllers\v2\Assessments;

use App\Http\Requests\v2\Assessments\CheckTeamsRequest;
use App\Http\Requests\v2\Assessments\IndexAssessmentRequest;
use App\Http\Requests\v2\Assessments\StoreAssessmentsRequest;
use App\Http\Resources\v2\Assessments\AssessmentCaseResource;
use App\Http\Services\v2\Assessments\WiringServiceAssessment;
use App\Models\v2\Assessments\AssessmentCase;
use App\Traits\RespondsWithHttpStatus;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AssessmentCaseController extends Controller
{
    use RespondsWithHttpStatus;

    private $wiringService;

    public function __construct()
    {
        $this->wiringService = new WiringServiceAssessment;
    }

    public function index(IndexAssessmentRequest $request)
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
            'case_id' => 'required',
        ]);

        $case_id = $request->query('case_id');

        $assessment_case = AssessmentCase::where('case_id', $case_id)
            ->first();

        if (! $assessment_case) {
            return $this->success(null);
        }

        $loaded_assessment_case = $this->loadFormByCaseType($assessment_case);

        return new AssessmentCaseResource($loaded_assessment_case);
    }

    public function store(StoreAssessmentsRequest $request)
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
            'case_id' => 'required',
        ]);

        //check if assessment already exist
        $assessment_case_exist = AssessmentCase::where('case_id', $request->case_id)->first();
        if ($assessment_case_exist) {
            return $this->failure('Case Id already exist', 422);
        }

        $case_type = $this->wiringService->getElderCaseId($request->case_id);
        if ($case_type == null) {
            return $this->failure('Elder case id not exist', 422);
        }

        $assessment_case = AssessmentCase::create([
            'case_id' => $request->case_id,
            'case_type' => $case_type,
            'first_assessor' => $request->first_assessor,
            'second_assessor' => $request->second_assessor,
            'assessment_date' => $request->assessment_date,
            'priority_level' => $request->priority_level,
            'start_time' => $request->start_time ? new Carbon($request->start_time) : null,
            'end_time' => $request->end_time ? new Carbon($request->end_time) : null,
            'status' => $request->status,
        ]);

        return new AssessmentCaseResource($assessment_case);
    }

    public function show(Request $request, $id)
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
        $assessment_case = AssessmentCase::where('id', $id)
            ->first();

        if (! $assessment_case) {
            return $this->failure('Assessment case not found', 404);
        }

        $loaded_assessment_case = $this->loadFormByCaseType($assessment_case);

        return new AssessmentCaseResource($loaded_assessment_case);
    }

    public function edit($id)
    {
    }

    public function update(Request $request, $id)
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
        $is_manager = ($request->user_role == 'manager' || $request->user_role == 'admin') ? true : false;
        $weeks_ago = Carbon::now()->subDays(7);
        $two_weeks_ago = Carbon::now()->subDays(14);
        $request->validate([
            'case_id' => 'required',
        ]);

        $assessment_case = AssessmentCase::where('id', $id)->first();
        if (! $assessment_case) {
            return $this->failure('Assessment case not found', 404);
        }

        if (($assessment_case->case_type === 'BZN' && ! $request->is_bzn) && ! $is_manager) {
            return response()->json([
                'data' => null,
                'status' => [
                    'code' => 401,
                    'message' => 'You are not allowed to update, because not in BZN teams.',
                    'errors' => [],
                ],
            ], 401);
        }

        if (
            (! $is_manager && new Carbon($assessment_case->end_time) < $weeks_ago) &&
            ($request->is_bzn && new Carbon($assessment_case->end_time) < $two_weeks_ago)
        ) {
            return response()->json([
                'data' => null,
                'status' => [
                    'code' => 401,
                    'message' => 'You are not allowed to update.',
                    'errors' => [],
                ],
            ], 401);
        }

        if (
            (! $is_manager && new Carbon($assessment_case->end_time) < $weeks_ago) &&
            (
                ($request->is_hcw || $request->is_hcsw) &&
                new Carbon($assessment_case->end_time) < $two_weeks_ago
            )
        ) {
            return response()->json([
                'data' => null,
                'status' => [
                    'code' => 401,
                    'message' => 'You are not allowed to update.',
                    'errors' => [],
                ],
            ], 401);
        }

        $updated = $assessment_case->update([
            'case_id' => $request->case_id ? $request->case_id : $assessment_case->case_id,
            'first_assessor' => $request->first_assessor,
            'second_assessor' => $request->second_assessor,
            'assessment_date' => $request->assessment_date,
            'priority_level' => $request->priority_level,
            'start_time' => $request->start_time ? new Carbon($request->start_time) : $assessment_case->start_time,
            'end_time' => $request->end_time ? new Carbon($request->end_time) : null,
            'status' => $request->status,
        ]);

        if (! $updated) {
            return $this->failure('Failed to update assessment case');
        }

        return new AssessmentCaseResource($assessment_case);
    }

    public function destroy($id)
    {
        $assessment_case = AssessmentCase::where('id', $id)->first();

        if (! $assessment_case) {
            return $this->failure('Assessment case not found', 404);
        }

        $assessment_case->delete();

        return response(null, 204);
    }

    public function checkTeams(CheckTeamsRequest $request)
    {
        $teams = $request->user_teams;
        $isCga = $request->is_cga;
        $isBzn = $request->is_bzn;

        return ['cga' => $isCga, 'isBzn' => $isBzn, 'teams' => $teams];
    }

    private function loadFormByCaseType($assessment_case)
    {
        if ($assessment_case->case_type == 'BZN') {
            return $assessment_case->where('id', $assessment_case->id)
                ->with('physiologicalMeasurementForm')
                ->with('physicalConditionForm')
                ->with('rePhysiologicalMeasurementForm')
                ->with('medicalConditionForm')
                ->with('lubbenSocialNetworkScaleForm')
                ->with('socialBackgroundForm')
                ->with('medicationAdherenceForm')
                ->with('functionMobilityForm')
                ->with('barthelIndexForm')
                ->with('geriatricDepressionScaleForm')
                ->with('iadlForm')
                ->with('genogramForm')
                ->with('montrealCognitiveAssessmentForm')
                ->with('assessmentCaseStatus')
                ->with('assessmentCaseAttachment')
                ->with('assessmentCaseSignature')
                ->first();
        } else {
            return $assessment_case->where('id', $assessment_case->id)
                ->with('qualtricsForm')
                ->with('montrealCognitiveAssessmentForm')
                ->with('socialWorkerForm')
                ->with('assessmentCaseStatus')
                ->with('assessmentCaseAttachment')
                ->with('assessmentCaseSignature')
                ->first();
        }
    }
}
