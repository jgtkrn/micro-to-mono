<?php

namespace App\Http\Controllers\v2\Assessments;

use App\Http\Requests\FollowUpHistoryIndexRequest;
use App\Http\Requests\v2\Assessments\FollowUpHistoryUpdateRequest;
use App\Http\Resources\v2\Assessments\FollowUpHistoryResource;
use App\Http\Services\v2\Assessments\ValidatorService;
use App\Models\v2\Assessments\FollowUpHistory;
use Illuminate\Http\Request;

class FollowUpHistoryController extends Controller
{
    public function __construct()
    {
        $this->validator = new ValidatorService;
    }

    public function index(FollowUpHistoryIndexRequest $request)
    {
        $this->validator->validatePaginationParams($request);

        $sortBy = $request->query('sort_by', 'created_at');
        $sortDir = $request->query('sort_dir', 'asc');
        $perPage = $request->query('per_page', 10);

        $followUpHistory = FollowUpHistory::join(
            'assessment_cases', 'assessment_cases.case_id', '=', 'follow_up_histories.case_id'
        )
            ->join('appointments', 'appointments.id', '=', 'follow_up_histories.appointment_id')
            ->select(
                'follow_up_histories.id as id',
                'assessment_cases.case_id as case_id',
                'follow_up_histories.date as date',
                'follow_up_histories.time as time',
                'follow_up_histories.appointment_other_text as appointment_other_text',
                'appointments.id as appointment_id',
                'appointments.cluster as cluster',
                'follow_up_histories.type as type',
                'appointments.name_en as name_en',
                'appointments.name_sc as name_sc',
                'follow_up_histories.created_at as created_at',
                'follow_up_histories.updated_at as updated_at',
                'follow_up_histories.deleted_at as deleted_at',
            )
            ->whereNotNull('follow_up_histories.case_id')
            ->whereNotNull('follow_up_histories.appointment_id')
            ->orderBy($sortBy, $sortDir)
            ->paginate($perPage);

        return new FollowUpHistoryResource($followUpHistory);
    }

    public function store(Request $request)
    {
        $this->validator->validateFollowUpHistory($request);
        $followUpHistory = FollowUpHistory::create($request->toArray());

        if (! $followUpHistory) {
            return response()->json([
                'error' => [
                    'code' => 500,
                    'message' => 'Failed to create Follow Up History',
                ],
            ], 500);
        }

        return response()->json([
            'data' => new FollowUpHistoryResource($followUpHistory),
            'message' => 'Follow Up History created successfully',
            'success' => true,
        ], 200);
    }

    public function show($followUpHistoryId)
    {
        $followUpHistory = FollowUpHistory::find($followUpHistoryId);

        if (! $followUpHistory) {
            return response()->json([
                'error' => [
                    'code' => 404,
                    'message' => "Cannot find Follow Up History with id {$followUpHistoryId}",
                ],
            ], 404);
        }

        return response()->json([
            'data' => new FollowUpHistoryResource($followUpHistory),
        ]);
    }

    public function update(FollowUpHistoryUpdateRequest $request, $followUpHistoryId)
    {
        $this->validator->validateFollowUpHistory($request);

        $followUpHistory = FollowUpHistory::where('id', $followUpHistoryId)->first();

        if (! $followUpHistory) {
            return response()->json([
                'error' => [
                    'code' => 404,
                    'message' => "Cannot find Follow Up History with id {$followUpHistoryId}",
                    'success' => false,
                ],
            ], 404);
        }

        if (! $followUpHistory->update($request->toArray())) {
            return response()->json([
                'error' => [
                    'code' => 500,
                    'message' => 'Failed to update FollowUpHistory',
                    'success' => false,
                ],
            ], 500);
        }

        return response()->json([
            'data' => new FollowUpHistoryResource($followUpHistory),
            'message' => 'Follow Up History updated successfully',
            'success' => true,
        ]);
    }

    public function destroy($followUpHistoryId)
    {
        $followUpHistory = FollowUpHistory::find($followUpHistoryId);

        if (! $followUpHistory) {
            return response()->json([
                'error' => [
                    'code' => 404,
                    'message' => "Cannot find Follow Up History with id {$followUpHistoryId}",
                    'success' => false,
                ],
            ], 404);
        }

        $followUpHistory->delete();

        return response()->json([
            'data' => null,
            'message' => 'Follow Up History deleted successfully',
            'success' => true,
        ], 201);

    }

    public function getByCaseId($caseId)
    {
        $followUpHistory = FollowUpHistory::join('appointments', 'appointments.id', '=', 'follow_up_histories.appointment_id')
            ->select(
                'follow_up_histories.id as id',
                'follow_up_histories.case_id as case_id',
                'follow_up_histories.date as date',
                'follow_up_histories.time as time',
                'follow_up_histories.appointment_other_text as appointment_other_text',
                'appointments.id as appointment_id',
                'appointments.cluster as cluster',
                'follow_up_histories.type as type',
                'appointments.name_en as name_en',
                'appointments.name_sc as name_sc',
            )
            ->where('follow_up_histories.case_id', '=', $caseId)
            ->orderBy('date', 'desc')
            ->orderBy('time', 'desc')
            ->get();

        if (! $followUpHistory) {
            return response()->json([
                'error' => [
                    'code' => 404,
                    'message' => "Cannot find Follow Up History with case_id {$caseId}",
                ],
            ], 404);
        }

        return response()->json([
            'data' => new FollowUpHistoryResource($followUpHistory),
        ]);
    }
}
