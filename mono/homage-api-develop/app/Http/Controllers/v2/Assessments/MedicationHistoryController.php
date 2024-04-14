<?php

namespace App\Http\Controllers\v2\Assessments;

use App\Http\Requests\v2\Assessments\MedicationHistoryStoreRequest;
use App\Http\Requests\v2\Assessments\UpdateMedicationHistoryRequest;
use App\Http\Resources\v2\Assessments\MedicationHistoryResource;
use App\Http\Services\v2\Assessments\ValidatorService;
use App\Models\v2\Assessments\MedicationHistory;
use App\Models\v2\Elders\Cases;

class MedicationHistoryController extends Controller
{
    private $validator;

    public function __construct()
    {
        $this->validator = new ValidatorService;
    }

    public function index()
    {
        return MedicationHistoryResource::collection(MedicationHistory::latest()->paginate(10));
    }

    public function store(MedicationHistoryStoreRequest $request)
    {
        $this->validator->validateMedicationHistories($request);
        $request->merge([
            'created_by' => $request->user_id,
            'updated_by' => $request->user_id,
            'created_by_name' => $request->user_name,
            'updated_by_name' => $request->user_name,
        ]);
        $caseId = $request->case_id;
        $elderCasesIdExists = Cases::where('id', $caseId)->first();
        if ($elderCasesIdExists) {
            $medicationHistories = MedicationHistory::create($request->toArray());

            if (! $medicationHistories) {
                return response()->json([
                    'error' => [
                        'code' => 500,
                        'message' => 'Failed to create Medication History',
                    ],
                ], 500);
            }

            return response()->json([
                'data' => new MedicationHistoryResource($medicationHistories),
                'message' => 'Medication history created successfully',
                'success' => true,
            ], 200);
        } else {
            return response()->json([
                'error' => [
                    'code' => 404,
                    'message' => "Cannot find Elder cases record with id {$caseId}",
                ],
            ], 404);
        }
    }

    public function show($medicationHistoryId)
    {
        $medicationHistories = MedicationHistory::find($medicationHistoryId);

        if (! $medicationHistories) {
            return response()->json([
                'error' => [
                    'code' => 404,
                    'message' => "Cannot find Medication history with id {$medicationHistoryId}",
                ],
            ], 404);
        }

        return response()->json([
            'data' => new MedicationHistoryResource($medicationHistories),
        ]);
    }

    public function update(UpdateMedicationHistoryRequest $request, $medicationHistoryId)
    {
        $this->validator->validateMedicationHistories($request);
        $request->merge([
            'created_by' => $request->user_id,
            'updated_by' => $request->user_id,
            'created_by_name' => $request->user_name,
            'updated_by_name' => $request->user_name,
        ]);

        $medicationHistory = MedicationHistory::where('id', $medicationHistoryId)->first();

        if (! $medicationHistory) {
            return response()->json([
                'error' => [
                    'code' => 404,
                    'message' => "Cannot find Medication history with id {$medicationHistoryId}",
                    'success' => false,
                ],
            ], 404);
        }

        if (! $medicationHistory->update($request->toArray())) {
            return response()->json([
                'error' => [
                    'code' => 500,
                    'message' => 'Failed to update Medication History',
                    'success' => false,
                ],
            ], 500);
        }

        return response()->json([
            'data' => new MedicationHistoryResource($medicationHistory),
            'message' => 'Medication history updated successfully',
            'success' => true,
        ]);
    }

    public function destroy($medicationHistoryId)
    {
        $medicationHistory = MedicationHistory::find($medicationHistoryId);

        if (! $medicationHistory) {
            return response()->json([
                'error' => [
                    'code' => 404,
                    'message' => "Cannot find Medication history with id {$medicationHistoryId}",
                    'success' => false,
                ],
            ], 404);
        }

        $medicationHistory->delete();

        return response()->json([
            'data' => null,
            'message' => 'Medication history deleted successfully',
            'success' => true,
        ], 200);
    }

    public function getByCaseId($caseId)
    {
        $elderCasesIdExists = Cases::where('id', $caseId)->first();
        $medicationHistoriesByCaseId = MedicationHistory::where('case_id', $caseId)
            ->orderBy('updated_at', 'desc')
            ->get();

        if ($elderCasesIdExists && count($medicationHistoriesByCaseId) > 0) {
            return response()->json([
                'data' => MedicationHistoryResource::collection($medicationHistoriesByCaseId),
                'message' => 'Data found',
                'success' => true,
            ]);
        } else {
            return response()->json([
                'error' => [
                    'code' => 404,
                    'message' => "Cannot find Medication History with case_id {$caseId}",
                ],
            ], 404);
        }
    }
}
