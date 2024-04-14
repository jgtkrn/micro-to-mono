<?php

namespace App\Http\Controllers\District;

use App\Exports\District\DistrictFormatExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\District\DistrictImportRequest;
use App\Http\Requests\District\DistrictRequest;
use App\Http\Resources\District\DistrictElderResource;
use App\Http\Resources\District\DistrictResource;
use App\Imports\District\DistrictImport;
use App\Models\District;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class DistrictController extends Controller
{

    /**
     * @OA\Get(
     *     path="/elderly-api/v1/districts",
     *     tags={"districts"},
     *     summary="get all district",
     *     operationId="v1GetDistricts",
     *
     *     @OA\Response(
     *         response=200,
     *         description="Successful",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                  property="data",
     *                  type="array",
     *                  @OA\Items(type="object", ref="#/components/schemas/district")
     *             ),
     *         )
     *     ),
     *      @OA\Response(
     *         response=400,
     *         description="Bad Request"
     *     ),
     * )
     */

    public function index(Request $request)
    {
        $perPage = $request->query('per_page', 15);
        $sortField = $request->query('sort_by');
        $sortBy = in_array($sortField, ['district_name', 'created-at']) ? $sortField : 'created_at';
        $sortDir = $request->query('sort_dir') == 'asc' ? 'asc' : 'desc';
        $districts = District::orderBy($sortBy, $sortDir)
            ->paginate($perPage);
        return DistrictResource::collection($districts);
    }


    /**
     * @OA\Post(
     *     path="/elderly-api/v1/districts",
     *     tags={"districts"},
     *     summary="Store new district",
     *     operationId="v1CreateDistrict",
     *     @OA\Response(
     *         response=200,
     *         description="Successful",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="District was created"),
     *             @OA\Property(property="data", type="object", ref="#/components/schemas/district")
     *         )
     *     ),
     *      @OA\Response(
     *         response=422,
     *         description="Bad Request"
     *     ),
     *     @OA\RequestBody(
     *         description="Input required call information (in string)",
     *         required=true,
     *             @OA\JsonContent(
     *                 required={"district_name,bzn_code"},
     *                 @OA\Property(property="district_name", type="string", example="Other", description="district name"),
     *                 @OA\Property(property="bzn_code", type="string", example="NAAC", description="UID code"),
     *                 @OA\Property(property="created_by", type="string", example="user abc", description="created by"),
     *                 @OA\Property(property="updated_by", type="string", example="user abc", description="updated by"),
     *             )
     *     )
     * )
     */


    public function store(DistrictRequest $request)
    {
        $call = District::create($request->toArray());
        return response()->json([
            'message' => 'District was created',
            'data' => new DistrictResource($call),
        ], 201);
    }

    /**
     * @OA\Get(
     *     path="/elderly-api/v1/districts/{id}",
     *     operationId="v1GetDistrictsDetail",
     *     summary="get district detail use ID district",
     *     tags={"districts"},
     *     @OA\Parameter(
     *          in="path",
     *          required=true,
     *          name="id",
     *          description="The id of the districts",
     *          @OA\Schema(
     *              type="integer",
     *              example="1"
     *          )
     *     ),
     *     @OA\Response(
     *          response=200,
     *          description="District detail",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="data", type="object", ref="#/components/schemas/district")
     *          )
     *     ),
     *     @OA\Response(
     *          response=404,
     *          description="User not found",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(
     *                  property="error",
     *                  type="object",
     *                  @OA\Property(property="code", type="number", example="404"),
     *                  @OA\Property(property="message", type="string", example="Cannot find district with id {id}")
     *              )
     *          )
     *     )
     * )
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */

    public function show($districtId)
    {
        $district = District::findOrFail($districtId);

        return new DistrictResource($district);
    }


    /**
     * @OA\Put(
     *     path="/elderly-api/v1/districts/{id}",
     *     tags={"districts"},
     *     summary="Update district by Id",
     *     operationId="v1UpdateDistrict",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="District Id to be updated",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful"
     *     ),
     *      @OA\Response(
     *         response=400,
     *         description="Bad Request"
     *     ),
     *     @OA\RequestBody(
     *         description="Input required call information (in string)",
     *         required=true,
     *             @OA\JsonContent(
     *                 required={"district_name,bzn_code"},
     *                 @OA\Property(property="district_name", type="string", example="Other", description="district name"),
     *                 @OA\Property(property="bzn_code", type="string", example="NAAC", description="UID code"),
     *                 @OA\Property(property="created_by", type="string", example="user abc", description="created by"),
     *                 @OA\Property(property="updated_by", type="string", example="user abc", description="updated by"),
     *             )
     *     )
     * )
     */

    public function update(DistrictRequest $request, $districtId)
    {
        $district = District::findOrFail($districtId);
        $district->update($request->validated());
        return response()->json([
            'message' => 'District was updated',
            'data' => new DistrictResource($district),
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/elderly-api/v1/districts/{id}",
     *     tags={"districts"},
     *     summary="Delete district by Id",
     *     operationId="v1DeleteDistrict",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="district Id",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Successful"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Bad Request"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function destroy($districtId)
    {
        $district = District::findOrFail($districtId);
        $district->delete();
        return response()->json(null, 204);
    }

    /**
     * @OA\Get(
     *     path="/elderly-api/v1/districts-elders",
     *     tags={"districts"},
     *     summary="get district with elder relation",
     *     operationId="v1GetDistrictElder",
     *
     *     @OA\Response(
     *         response=200,
     *         description="Successful"
     *     ),
     *      @OA\Response(
     *         response=400,
     *         description="Bad Request"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Resource not found"
     *     ),
     * )
     */

    public function districtElder()
    {
        return DistrictElderResource::collection(District::latest()->paginate(15));
    }

    /**
     * @OA\Post(
     *     path="/elderly-api/v1/districts-import",
     *     tags={"districts"},
     *     summary="File upload",
     *     operationId="uploadFile",
     *     @OA\Response(
     *         response=200,
     *         description="Successful",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", type="object", ref="#/components/schemas/district")
     *         )
     *     ),
     *      @OA\Response(
     *         response=422,
     *         description="Unprocessable content",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="errors", type="object", description="Invalid request object"),
     *             @OA\Property(property="message", type="string", description="Error message")
     *         )
     *     ),
     *     @OA\RequestBody(
     *         description="Input file",
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     description="Single file",
     *                     property="file",
     *                     type="string",
     *                     format="binary"
     *                 )
     *             )
     *         )
     *     )
     * )
     */

    public function import(DistrictImportRequest $request)
    {
        Excel::import(new DistrictImport, request()->file('file'));
        return response()->json([
            'message' => 'District Was Imported',
        ]);
    }

    /**
     * @OA\Get(
     *     path="/elderly-api/v1/districts-export-format",
     *     tags={"districts"},
     *     summary="export format districts",
     *     operationId="exportDistricts",
     *
     *     @OA\Response(
     *         response=200,
     *         description="Successful"
     *     ),
     *      @OA\Response(
     *         response=400,
     *         description="Bad Request"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Resource not found"
     *     ),
     * )
     */

    public function exportFormat()
    {

        return Excel::download(new DistrictFormatExport, 'districts_format.xlsx');
    }
}
