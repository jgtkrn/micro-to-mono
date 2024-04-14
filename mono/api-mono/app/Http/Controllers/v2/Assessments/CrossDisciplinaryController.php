<?php

namespace App\Http\Controllers\v2\Assessments;

use App\Http\Requests\v2\Assessments\CrossDisciplinaryIndexRequest;
use App\Http\Requests\v2\Assessments\CrossDisciplinaryStoreRequest;
use App\Http\Resources\v2\Assessments\CrossDisciplinaryResource;
use App\Models\v2\Assessments\CarePlan;
use App\Models\v2\Assessments\CrossDisciplinary;
use App\Traits\RespondsWithHttpStatus;
use Illuminate\Http\Request;

class CrossDisciplinaryController extends Controller
{
    use RespondsWithHttpStatus;

    public function index(CrossDisciplinaryIndexRequest $request)
    {

        if (
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
        $request->validate([
            'case_id' => ['required', 'integer'],
        ]);

        $sortBy = $request->query('sort_by', 'created_at');
        $sortDir = $request->query('sort_dir', 'desc');
        $perPage = $request->query('per_page', 10);
        $crossDisciplinaries = CrossDisciplinary::where('case_id', $request->case_id)->orderBy($sortBy, $sortDir)
            ->paginate($perPage);
        if (count($crossDisciplinaries) == 0) {
            return response()->json([
                'error' => [
                    'code' => 404,
                    'message' => "Cannot find Cross Disciplinary with case_id {$request->case_id}",
                    'success' => false,
                ],
            ], 404);
        }

        return CrossDisciplinaryResource::collection($crossDisciplinaries);
    }

    public function store(CrossDisciplinaryStoreRequest $request)
    {
        if (

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

        $request->validate([
            'case_id' => ['required', 'integer'],
            'role' => ['nullable', 'string'],
            'comments' => ['nullable', 'string'],
            'name' => ['nullable', 'string'],
            'date' => ['nullable', 'date_format:Y-m-d'],
        ]);
        $crossDisciplinary = CrossDisciplinary::create([
            'case_id' => $request->case_id,
            'role' => $request->role,
            'comments' => $request->comments,
            'name' => $request->name,
            'date' => $request->date,
        ]);

        return new CrossDisciplinaryResource($crossDisciplinary);
    }

    public function show(Request $request, $id)
    {

        if (
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
        $currentCrossDisciplinary = CrossDisciplinary::where('id', $id)->first();
        if (! $currentCrossDisciplinary) {
            return response()->json([
                'error' => [
                    'code' => 404,
                    'message' => "Cannot find Cross Disciplinary with id {$id}",
                    'success' => false,
                ],
            ], 404);
        }

        return new CrossDisciplinaryResource($currentCrossDisciplinary);
    }

    public function update(Request $request, $id)
    {

        if (
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

        $user = $request->user_id;
        $currentCrossDisciplinary = CrossDisciplinary::where('id', $id)->first();
        if (! $currentCrossDisciplinary) {
            return response()->json([
                'error' => [
                    'code' => 404,
                    'message' => "Cannot find Cross Disciplinary with id {$id}",
                    'success' => false,
                ],
            ], 404);
        }
        $care_plan = CarePlan::where('case_id', $currentCrossDisciplinary->case_id)->first();
        if (! $care_plan) {
            return $this->failure('Care plan does not exists.', 404);
        }
        if (($care_plan->manager_id !== $user) && $request->access_role !== 'admin') {
            return response()->json([
                'data' => null,
                'status' => [
                    'code' => 401,
                    'message' => 'You are not the author.',
                    'errors' => [],
                ],
            ], 401);
        }
        $request->validate([
            'case_id' => ['nullable', 'integer'],
            'role' => ['nullable', 'string'],
            'comments' => ['nullable', 'string'],
            'name' => ['nullable', 'string'],
            'date' => ['nullable', 'date_format:Y-m-d'],
        ]);

        $crossDisciplinary = CrossDisciplinary::where('id', $id)->update([
            'case_id' => $request->case_id ? $request->case_id : $currentCrossDisciplinary->case_id,
            'role' => $request->role ? $request->role : $currentCrossDisciplinary->role,
            'comments' => $request->comments ? $request->comments : $currentCrossDisciplinary->comments,
            'name' => $request->name ? $request->name : $currentCrossDisciplinary->name,
            'date' => $request->date ? $request->date : $currentCrossDisciplinary->date,
        ]);
        $currentCrossDisciplinary = CrossDisciplinary::where('id', $id)->first();

        return new CrossDisciplinaryResource($currentCrossDisciplinary);
    }

    public function destroy(Request $request, $id)
    {

        if (

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

        $currentCrossDisciplinary = CrossDisciplinary::where('id', $id)->first();
        if (! $currentCrossDisciplinary) {
            return response()->json([
                'error' => [
                    'code' => 404,
                    'message' => "Cannot find Cross Disciplinary with id {$id}",
                    'success' => false,
                ],
            ], 404);
        }
        $currentCrossDisciplinary->delete();

        return response()->json([
            'data' => null,
            'message' => "Cross Disciplinary with id {$id} deleted successfully",
            'success' => true,
        ], 201);
    }
}
