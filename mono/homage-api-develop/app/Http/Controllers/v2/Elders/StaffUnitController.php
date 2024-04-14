<?php

namespace App\Http\Controllers\v2\Elders;

use App\Http\Controllers\Controller;
use App\Http\Requests\v2\Elders\CreateStaffUnitRequest;
use App\Http\Requests\v2\Elders\StaffUnitIndexRequest;
use App\Http\Requests\v2\Elders\UpdateStaffUnitRequest;
use App\Http\Resources\v2\Elders\StaffUnitCollection;
use App\Http\Resources\v2\Elders\StaffUnitResource;
use App\Models\v2\Elders\StaffUnit;

class StaffUnitController extends Controller
{
    public function index(StaffUnitIndexRequest $request)
    {
        $perPage = $request->query('per_page', 25);
        $sortField = $request->query('sort_by');
        $sortBy = in_array($sortField, ['unit_name', 'created_at', 'updated_at', 'id']) ? $sortField : 'id';
        $sortDir = $request->query('sort_dir') == 'asc' ? 'asc' : 'desc';
        $staffUnits = StaffUnit::orderBy($sortBy, $sortDir)->paginate($perPage);

        return new StaffUnitCollection($staffUnits);
    }

    public function store(CreateStaffUnitRequest $request)
    {
        $staffUnit = StaffUnit::create($request->validated());

        return new StaffUnitResource($staffUnit);
    }

    public function show($id)
    {
        $staffUnit = StaffUnit::find($id);

        if (! $staffUnit) {
            return response()->json([
                'status' => [
                    'code' => 404,
                    'message' => "Cannot find staff unit with id {$id}",
                    'errors' => [],
                ],
            ], 404);
        }

        return new StaffUnitResource($staffUnit);
    }

    public function update(UpdateStaffUnitRequest $request, $id)
    {
        $staffUnit = StaffUnit::find($id);

        if (! $staffUnit) {
            return response()->json([
                'status' => [
                    'code' => 404,
                    'message' => "Cannot find staff unit with id {$id}",
                    'errors' => [],
                ],
            ], 404);
        }

        $staffUnit->unit_name = $request->has('unit_name') ? $request->unit_name : $staffUnit->unit_name;
        $staffUnit->save();

        return new StaffUnitResource($staffUnit);
    }

    public function destroy($id)
    {
        $staffUnit = StaffUnit::find($id);

        if (! $staffUnit) {
            return response()->json([
                'status' => [
                    'code' => 404,
                    'message' => "Cannot find staff unit with id {$id}",
                    'errors' => [],
                ],
            ], 404);
        }

        $staffUnit->delete();

        return response()->json(null, 204);
    }
}
