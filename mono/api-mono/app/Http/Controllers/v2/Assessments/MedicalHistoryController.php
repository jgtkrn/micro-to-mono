<?php

namespace App\Http\Controllers\v2\Assessments;

use App\Http\Requests\v2\Assessments\MedicalHistoryIndexRequest;
use App\Http\Requests\v2\Assessments\MedicalHistorySearchRequest;
use App\Http\Resources\v2\Assessments\MedicalHistoryResource;
use App\Http\Services\v2\Assessments\ValidatorService;
use App\Models\v2\Assessments\MedicalHistory;
use Illuminate\Http\Request;

class MedicalHistoryController extends Controller
{
    public function __construct()
    {
        $this->validator = new ValidatorService;
    }

    public function index(MedicalHistoryIndexRequest $request)
    {
        $this->validator->validatePaginationParams($request);

        $sortBy = $request->query('sort_by', 'created_at');
        $sortDir = $request->query('sort_dir', 'asc');
        $perPage = $request->query('per_page', 10);

        $medicalHistory = MedicalHistory::orderBy($sortBy, $sortDir)->paginate($perPage);

        return MedicalHistoryResource::collection($medicalHistory);
    }

    public function store(Request $request)
    {
        $this->validator->validateMedicalHistory($request);
        $medicalHistory = MedicalHistory::create($request->toArray());

        if (! $medicalHistory) {
            return response()->json([
                'error' => [
                    'code' => 500,
                    'message' => 'Failed to create Medical History',
                ],
            ], 500);
        }

        return response()->json([
            'data' => new MedicalHistoryResource($medicalHistory),
            'message' => 'Medical History created successfully',
            'success' => true,
        ], 200);
    }

    public function show($medicalHistoryId)
    {
        $medicalHistory = MedicalHistory::find($medicalHistoryId);

        if (! $medicalHistory) {
            return response()->json([
                'error' => [
                    'code' => 404,
                    'message' => "Cannot find Medical History with id {$medicalHistoryId}",
                ],
            ], 404);
        }

        return response()->json([
            'data' => new MedicalHistoryResource($medicalHistory),
        ]);
    }

    public function update(Request $request, $medicalHistoryId)
    {
        $this->validator->validateMedicalHistory($request);

        $medicalHistory = MedicalHistory::where('id', $medicalHistoryId)->first();

        if (! $medicalHistory) {
            return response()->json([
                'error' => [
                    'code' => 404,
                    'message' => "Cannot find Medical History with id {$medicalHistoryId}",
                    'success' => false,
                ],
            ], 404);
        }

        if (! $medicalHistory->update($request->toArray())) {
            return response()->json([
                'error' => [
                    'code' => 500,
                    'message' => 'Failed to update Medical History',
                    'success' => false,
                ],
            ], 500);
        }

        return response()->json([
            'data' => new MedicalHistoryResource($medicalHistory),
            'message' => 'Medical History updated successfully',
            'success' => true,
        ]);
    }

    public function destroy($medicalHistoryId)
    {
        $medicalHistory = MedicalHistory::find($medicalHistoryId);

        if (! $medicalHistory) {
            return response()->json([
                'error' => [
                    'code' => 404,
                    'message' => "Cannot find Medical History with id {$medicalHistoryId}",
                    'success' => false,
                ],
            ], 404);
        }

        $medicalHistory->delete();

        return response()->json([
            'data' => null,
            'message' => 'Medical History deleted successfully',
            'success' => true,
        ], 200);

    }

    public function getByCaseId($caseId)
    {
        $medicalHistoryByCaseId = MedicalHistory::join(
            'assessment_cases', 'medical_histories.case_id', '=', 'assessment_cases.case_id')
            ->select(
                'medical_histories.id as id',
                'medical_histories.medical_category_name',
                'medical_histories.medical_diagnosis_name',
                'assessment_cases.case_id as case_id',
                'assessment_cases.first_assessor as first_assessor',
                'assessment_cases.second_assessor as second_assessor',
                'assessment_cases.assessment_date as assessment_date',
                'assessment_cases.start_time as start_time',
                'assessment_cases.end_time as end_time',
                'assessment_cases.status as status',
                'assessment_cases.case_type as case_type',
            )
            ->where('medical_histories.case_id', '=', $caseId)
            ->get();

        if (! $medicalHistoryByCaseId) {
            return response()->json([
                'error' => [
                    'code' => 404,
                    'message' => "Cannot find Medical History with case_id {$caseId}",
                ],
            ], 404);
        }

        return response()->json([
            'data' => $medicalHistoryByCaseId,
            'message' => 'Data found',
            'success' => true,
        ]);
    }

    public function search(MedicalHistorySearchRequest $request)
    {
        if ($request->has('query')) {
            $result = [];
            $query = $request->get('query');
            if (! is_null($query)) {

                $result = MedicalHistory::select(
                    'medical_histories.id as medical_histories_id',
                    'medical_histories.case_id as case_id',
                    'medical_histories.medical_category_name as medical_category_name',
                    'medical_histories.medical_diagnosis_name as medical_diagnosis_name',
                )
                    ->where('medical_histories.medical_category_name', 'LIKE', '%' . $query . '%')
                    ->orWhere('medical_histories.medical_diagnosis_name', 'LIKE', '%' . $query . '%')
                    ->get();

                if (count($result)) {
                    return response()->json([
                        'data' => $result,
                        'message' => 'Data found',
                        'success' => true,
                    ]);
                } else {
                    return response()->json([
                        'error' => [
                            'code' => 404,
                            'message' => 'No Data found',
                            'success' => false,
                        ],
                    ], 404);
                }
            } else {
                return response()->json([
                    'error' => [
                        'code' => 404,
                        'message' => 'No Data found',
                        'success' => false,
                    ],
                ], 404);
            }
        } else {
            return response()->json([
                'error' => [
                    'code' => 400,
                    'message' => 'query key parameter is required',
                    'success' => false,
                ],
            ], 400);
        }
    }
}
