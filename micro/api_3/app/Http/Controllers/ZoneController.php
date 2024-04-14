<?php

namespace App\Http\Controllers;

use App\Http\Requests\Zone\CreateZoneRequest;
use App\Http\Requests\Zone\UpdateZoneRequest;
use App\Http\Resources\ZoneCollection;
use App\Http\Resources\ZoneResource;
use App\Models\Zone;
use http\Env\Response;
use Illuminate\Http\Request;

class ZoneController extends Controller
{
    /**
     * @OA\Get(
     *     path="/elderly-api/v1/zones",
     *     operationId="v1GetZones",
     *     tags={"zones"},
     *     @OA\Parameter(
     *          in="query",
     *          name="page",
     *          example="1"
     *     ),
     *     @OA\Parameter(
     *          in="query",
     *          name="per_page",
     *          example="10"
     *     ),
     *     @OA\Parameter(
     *          in="query",
     *          name="sort_by",
     *          description="available options: id, name, code, created_at, updated_at",
     *          example="created_at"
     *     ),
     *     @OA\Parameter(
     *          in="query",
     *          name="sort_dir",
     *          description="available options: asc, desc",
     *          example="desc"
     *     ),
     *     @OA\Response(
     *          response=200,
     *          description="Zone List",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(
     *                  property="data",
     *                  type="array",
     *                  @OA\Items(type="object", ref="#/components/schemas/Zone")
     *              ),
     *              @OA\Property(
     *                  property="links",
     *                  type="object",
     *                  @OA\Property(
     *                      property="first",
     *                      type="string",
     *                      example="/elderly-api/v1/zones?page=1"
     *                  ),
     *                  @OA\Property(
     *                      property="last",
     *                      type="string",
     *                      example="/elderly-api/v1/zones?page=10"
     *                  ),
     *                  @OA\Property(
     *                      property="prev",
     *                      type="string",
     *                      example=null
     *                  ),
     *                  @OA\Property(
     *                      property="next",
     *                      type="string",
     *                      example="/elderly-api/v1/zones?page=2"
     *                  ),
     *              ),
     *              @OA\Property(
     *                  property="meta",
     *                  type="object",
     *                  @OA\Property(
     *                      property="current_page",
     *                      type="integer",
     *                      example="1"
     *                  ),
     *                  @OA\Property(
     *                      property="from",
     *                      type="integer",
     *                      example="1"
     *                  ),
     *                  @OA\Property(
     *                      property="last_page",
     *                      type="integer",
     *                      example="10"
     *                  ),
     *                  @OA\Property(
     *                      property="links",
     *                      type="array",
     *                      @OA\Items(
     *                          type="object",
     *                          @OA\Property(
     *                              property="url",
     *                              type="string",
     *                              nullable=true,
     *                              example=null
     *                          ),
     *                          @OA\Property(
     *                              property="label",
     *                              type="string",
     *                              example="&laqou; Previous"
     *                          ),
     *                          @OA\Property(
     *                              property="active",
     *                              type="boolean",
     *                              example="false"
     *                          )
     *                      )
     *                  ),
     *                  @OA\Property(
     *                      property="path",
     *                      type="string",
     *                      example="/elderly-api/v1/zones"
     *                  ),
     *                  @OA\Property(
     *                      property="per_page",
     *                      type="integer",
     *                      example="10"
     *                  ),
     *                  @OA\Property(
     *                      property="to",
     *                      type="integer",
     *                      example="10"
     *                  ),
     *                  @OA\Property(
     *                      property="total",
     *                      type="integer",
     *                      example="100"
     *                  )
     *              )
     *          )
     *     )
     * )
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $perPage = $request->query('per_page', 25);
        $sortField = $request->query('sort_by');
        $sortBy = in_array($sortField, ['name', 'code', 'created_at', 'updated_at', 'id']) ? $sortField : 'id';
        $sortDir = $request->query('sort_dir') == 'asc' ? 'asc' : 'desc';
        $zones = Zone::orderBy($sortBy, $sortDir)->paginate($perPage);

        return new ZoneCollection($zones);
    }

    /**
     * @OA\Post(
     *     path="/elderly-api/v1/zones",
     *     operationId="v1CreateZone",
     *     tags={"zones"},
     *     @OA\RequestBody(
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="name", type="string", example="Exlusion Zone"),
     *              @OA\Property(property="code", type="string", example="exclusion_zone"),
     *          )
     *     ),
     *     @OA\Response(
     *          response=201,
     *          description="Zone created",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="data", type="object", ref="#/components/schemas/Zone")
     *          )
     *     ),
     *     @OA\Response(
     *          response=422,
     *          description="Zone data validation fail",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="name.required", type="number", example="The name field is required.")
     *          )
     *     ),
     *     @OA\Response(
     *          response=500,
     *          description="Failed to create Zone",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(
     *                  property="errors",
     *                  type="object",
     *                  @OA\Property(property="code", type="number", example="500"),
     *                  @OA\Property(property="message", type="string", example="Failed to create zone")
     *              )
     *          )
     *     )
     * )
     *
     * @param  \App\Http\Requests\Zone\CreateZoneRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CreateZoneRequest $request)
    {
        $zone = Zone::create($request->validated());

        return new ZoneResource($zone);
    }

    /**
     * @OA\Get(
     *     path="/elderly-api/v1/zones/{zoneId}",
     *     operationId="v1GetZoneDetail",
     *     tags={"zones"},
     *     @OA\Parameter(
     *          in="path",
     *          required=true,
     *          name="zoneId",
     *          description="The id of the zone",
     *          @OA\Schema(
     *              type="integer",
     *              example="1"
     *          )
     *     ),
     *     @OA\Response(
     *          response=200,
     *          description="Zone detail",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="data", type="object", ref="#/components/schemas/Zone")
     *          )
     *     ),
     *     @OA\Response(
     *          response=404,
     *          description="Zone not found",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(
     *                  property="status",
     *                  type="object",
     *                  @OA\Property(property="code", type="number", example="404"),
     *                  @OA\Property(property="message", type="string", example="Cannot find zone with id {zoneId}"),
     *                  @OA\Property(property="errors", type="array", example="[]", @OA\Items(type="integer", example=null))
     *              )
     *          )
     *     )
     * )
     *
     * @param  int  $zoneId
     * @return \Illuminate\Http\Response
     */
    public function show($zoneId)
    {
        $zone = Zone::find($zoneId);

        if (!$zone) {
            return response()->json([
                'status' => [
                    'code' => 404,
                    'message' => "Cannot find zone with id $zoneId",
                    'errors' => [],
                ]
            ], 404);
        }

        return new ZoneResource($zone);
    }

    /**
     * @OA\Put(
     *     path="/elderly-api/v1/zones/{zoneId}",
     *     operationId="v1UpdateZone",
     *     tags={"zones"},
     *     @OA\Parameter(
     *          in="path",
     *          required=true,
     *          name="zoneId",
     *          description="The id of the zone",
     *          @OA\Schema(
     *              type="integer",
     *              example="1"
     *          )
     *     ),
     *     @OA\Response(
     *          response=200,
     *          description="Zone updated",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="data", type="object", ref="#/components/schemas/Zone")
     *          )
     *     ),
     *     @OA\Response(
     *          response=422,
     *          description="Zone data validation fail",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="name.required", type="number", example="The name field is required.")
     *          )
     *     ),
     *     @OA\Response(
     *          response=404,
     *          description="Zone not found",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(
     *                  property="status",
     *                  type="object",
     *                  @OA\Property(property="code", type="number", example="404"),
     *                  @OA\Property(property="message", type="string", example="Cannot find zone with id {zoneId}"),
     *                  @OA\Property(property="errors", type="array", example="[]", @OA\Items(type="integer", example=null))
     *              )
     *          )
     *     )
     * )
     *
     * @param  \App\Http\Requests\Zone\UpdateZoneRequest  $request
     * @param  int  $zoneId
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateZoneRequest $request, $zoneId)
    {
        $zone = Zone::find($zoneId);

        if (!$zone) {
            return response()->json([
                'status' => [
                    'code' => 404,
                    'message' => "Cannot find zone with id $zoneId",
                    'errors' => [],
                ]
            ], 404);
        }

        $zone->name = $request->has('name') ? $request->name : $zone->name;
        $zone->code = $request->has('code') ? $request->code : $zone->code;
        $zone->save();

        return new ZoneResource($zone);
    }

    /**
     * @OA\Delete(
     *      path="/elderly-api/v1/zones/{zoneId}",
     *     operationId="v1DeleteZone",
     *     tags={"zones"},
     *     @OA\Parameter(
     *          in="path",
     *          required=true,
     *          name="zoneId",
     *          description="The id of the zone",
     *          @OA\Schema(
     *              type="integer",
     *              example="1"
     *          )
     *     ),
     *     @OA\Response(
     *          response=204,
     *          description="Zone deleted"
     *     ),
     *     @OA\Response(
     *          response=404,
     *          description="Zone not found",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(
     *                  property="status",
     *                  type="object",
     *                  @OA\Property(property="code", type="number", example="404"),
     *                  @OA\Property(property="message", type="string", example="Cannot find zone with id {zoneId}"),
     *                  @OA\Property(property="errors", type="array", example="[]", @OA\Items(type="integer", example=null))
     *              )
     *          )
     *     )
     * )
     *
     * @param  int  $zoneId
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($zoneId)
    {
        $zone = Zone::find($zoneId);

        if (!$zone) {
            return response()->json([
                'status' => [
                    'code' => 404,
                    'message' => "Cannot find zone with id $zoneId",
                    'errors' => [],
                ]
            ], 404);
        }

        $zone->delete();

        return response()->json(null, 204);
    }
}
