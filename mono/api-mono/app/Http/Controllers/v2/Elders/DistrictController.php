<?php

namespace App\Http\Controllers\v2\Elders;

use App\Exports\v2\Elders\DistrictFormatExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\v2\Elders\DistrictImportRequest;
use App\Http\Requests\v2\Elders\DistrictIndexRequest;
use App\Http\Requests\v2\Elders\DistrictRequest;
use App\Http\Resources\v2\Elders\DistrictElderResource;
use App\Http\Resources\v2\Elders\DistrictResource;
use App\Imports\v2\Elders\DistrictImport;
use App\Models\v2\Elders\District;
use Maatwebsite\Excel\Facades\Excel;

class DistrictController extends Controller
{
    public function index(DistrictIndexRequest $request)
    {
        $perPage = $request->query('per_page', 15);
        $sortField = $request->query('sort_by');
        $sortBy = in_array($sortField, ['district_name', 'created-at']) ? $sortField : 'created_at';
        $sortDir = $request->query('sort_dir') == 'asc' ? 'asc' : 'desc';
        $districts = District::orderBy($sortBy, $sortDir)
            ->paginate($perPage);

        return DistrictResource::collection($districts);
    }

    public function store(DistrictRequest $request)
    {
        $call = District::create($request->toArray());

        return response()->json([
            'message' => 'District was created',
            'data' => new DistrictResource($call),
        ], 201);
    }

    public function show($districtId)
    {
        $district = District::findOrFail($districtId);

        return new DistrictResource($district);
    }

    public function update(DistrictRequest $request, $districtId)
    {
        $district = District::findOrFail($districtId);
        $district->update($request->validated());

        return response()->json([
            'message' => 'District was updated',
            'data' => new DistrictResource($district),
        ]);
    }

    public function destroy($districtId)
    {
        $district = District::findOrFail($districtId);
        $district->delete();

        return response()->json(null, 204);
    }

    public function districtElder()
    {
        return DistrictElderResource::collection(District::latest()->paginate(15));
    }

    public function import(DistrictImportRequest $request)
    {
        Excel::import(new DistrictImport, request()->file('file'));

        return response()->json([
            'message' => 'District Was Imported',
        ]);
    }

    public function exportFormat()
    {

        return Excel::download(new DistrictFormatExport, 'districts_format.xlsx');
    }
}
