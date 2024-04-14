<?php

namespace App\Http\Controllers\v2\Elders;

use App\Http\Controllers\Controller;
use App\Http\Requests\v2\Elders\CentreResponsibleWorkerIndexRequest;
use App\Http\Requests\v2\Elders\CreateCentreResponsibleWorker;
use App\Http\Requests\v2\Elders\UpdateCentreResponsibleWorker;
use App\Http\Resources\v2\Elders\CentreResponsibleWorkerResource;
use App\Models\v2\Elders\CentreResponsibleWorker;

class CentreResponsibleWorkerController extends Controller
{
    public function index(CentreResponsibleWorkerIndexRequest $request)
    {
        $perPage = $request->query('per_page', 25);
        $sortField = $request->query('sort_by');
        $sortBy = in_array($sortField, ['name', 'code', 'created_at', 'updated_at']) ? $sortField : 'created_at';
        $sortDir = $request->query('sort_dir') == 'asc' ? 'asc' : 'desc';
        $centres = CentreResponsibleWorker::orderBy($sortBy, $sortDir)->paginate($perPage);

        return CentreResponsibleWorkerResource::collection($centres);
    }

    public function store(CreateCentreResponsibleWorker $request)
    {
        $centre = CentreResponsibleWorker::create($request->validated());

        return new CentreResponsibleWorkerResource($centre);
    }

    public function show($centreId)
    {
        $centre = CentreResponsibleWorker::find($centreId);
        if (! $centre) {
            return response()->json([
                'status' => [
                    'code' => 404,
                    'message' => "Cannot find centre with id {$centreId}",
                    'errors' => [],
                ],
            ], 404);
        }

        return new CentreResponsibleWorkerResource($centre);
    }

    public function update(UpdateCentreResponsibleWorker $request, $centreId)
    {
        $centre = CentreResponsibleWorker::find($centreId);
        if (! $centre) {
            return response()->json([
                'status' => [
                    'code' => 404,
                    'message' => "Cannot find centre with id {$centreId}",
                    'errors' => [],
                ],
            ], 404);
        }

        $centre->name = $request->has('name') ? $request->name : $centre->name;
        $centre->code = $request->has('code') ? $request->code : $centre->code;
        $centre->save();

        return new CentreResponsibleWorkerResource($centre);
    }

    public function destroy($centreId)
    {
        $centre = CentreResponsibleWorker::find($centreId);
        if (! $centre) {
            return response()->json([
                'status' => [
                    'code' => 404,
                    'message' => "Cannot find centre with id {$centreId}",
                    'errors' => [],
                ],
            ], 404);
        }
        $centre->delete();

        return response()->json(null, 204);
    }
}
