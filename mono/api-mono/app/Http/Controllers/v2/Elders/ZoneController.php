<?php

namespace App\Http\Controllers\v2\Elders;

use App\Http\Controllers\Controller;
use App\Http\Requests\v2\Elders\CreateZoneRequest;
use App\Http\Requests\v2\Elders\UpdateZoneRequest;
use App\Http\Requests\v2\Elders\ZoneIndexRequest;
use App\Http\Resources\v2\Elders\ZoneCollection;
use App\Http\Resources\v2\Elders\ZoneResource;
use App\Models\v2\Elders\Zone;

class ZoneController extends Controller
{
    public function index(ZoneIndexRequest $request)
    {
        $perPage = $request->query('per_page', 25);
        $sortField = $request->query('sort_by');
        $sortBy = in_array($sortField, ['name', 'code', 'created_at', 'updated_at', 'id']) ? $sortField : 'id';
        $sortDir = $request->query('sort_dir') == 'asc' ? 'asc' : 'desc';
        $zones = Zone::orderBy($sortBy, $sortDir)->paginate($perPage);

        return new ZoneCollection($zones);
    }

    public function store(CreateZoneRequest $request)
    {
        $zone = Zone::create($request->validated());

        return new ZoneResource($zone);
    }

    public function show($zoneId)
    {
        $zone = Zone::find($zoneId);

        if (! $zone) {
            return response()->json([
                'status' => [
                    'code' => 404,
                    'message' => "Cannot find zone with id {$zoneId}",
                    'errors' => [],
                ],
            ], 404);
        }

        return new ZoneResource($zone);
    }

    public function update(UpdateZoneRequest $request, $zoneId)
    {
        $zone = Zone::find($zoneId);

        if (! $zone) {
            return response()->json([
                'status' => [
                    'code' => 404,
                    'message' => "Cannot find zone with id {$zoneId}",
                    'errors' => [],
                ],
            ], 404);
        }

        $zone->name = $request->has('name') ? $request->name : $zone->name;
        $zone->code = $request->has('code') ? $request->code : $zone->code;
        $zone->save();

        return new ZoneResource($zone);
    }

    public function destroy($zoneId)
    {
        $zone = Zone::find($zoneId);

        if (! $zone) {
            return response()->json([
                'status' => [
                    'code' => 404,
                    'message' => "Cannot find zone with id {$zoneId}",
                    'errors' => [],
                ],
            ], 404);
        }

        $zone->delete();

        return response()->json(null, 204);
    }
}
