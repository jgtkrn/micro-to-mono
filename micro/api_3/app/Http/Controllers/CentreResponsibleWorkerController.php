<?php

namespace App\Http\Controllers;

use App\Http\Requests\Centre\CreateCentreResponsibleWorker;
use App\Http\Requests\Centre\UpdateCentreResponsibleWorker;
use App\Http\Resources\CentreResponsibleWorkerResource;
use App\Models\CentreResponsibleWorker;
use Illuminate\Http\Request;

class CentreResponsibleWorkerController extends Controller
{
    /**
     * @OA\Get(
     *     path="/elderly-api/v1/centres",
     *     operationId="v1GetCentre",
     *     tags={"centres"},
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
     *          description="Centre List",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(
     *                  property="data",
     *                  type="array",
     *                  @OA\Items(type="object", ref="#/components/schemas/Centre")
     *              ),
     *              @OA\Property(
     *                  property="links",
     *                  type="object",
     *                  @OA\Property(
     *                      property="first",
     *                      type="string",
     *                      example="/elderly-api/v1/centres?page=1"
     *                  ),
     *                  @OA\Property(
     *                      property="last",
     *                      type="string",
     *                      example="/elderly-api/v1/centres?page=10"
     *                  ),
     *                  @OA\Property(
     *                      property="prev",
     *                      type="string",
     *                      example=null
     *                  ),
     *                  @OA\Property(
     *                      property="next",
     *                      type="string",
     *                      example="/elderly-api/v1/centres?page=2"
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
     *                      example="/elderly-api/v1/centres"
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
        $sortBy = in_array($sortField, ['name', 'code', 'created_at', 'updated_at']) ? $sortField : 'created_at';
        $sortDir = $request->query('sort_dir') == 'asc' ? 'asc' : 'desc';
        $centres = CentreResponsibleWorker::orderBy($sortBy, $sortDir)->paginate($perPage);

        return CentreResponsibleWorkerResource::collection($centres);
    }

    /**
     * @OA\Post(
     *     path="/elderly-api/v1/centres",
     *     operationId="v1CreateCentre",
     *     tags={"centres"},
     *     @OA\RequestBody(
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="name", type="string", example="Baizhu Clinic"),
     *              @OA\Property(property="code", type="string", example="baizhu_clinic"),
     *          )
     *     ),
     *     @OA\Response(
     *          response=201,
     *          description="Centre created",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="data", type="object", ref="#/components/schemas/Centre")
     *          )
     *     ),
     *     @OA\Response(
     *          response=422,
     *          description="Centre data validation fail",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="name.required", type="number", example="The name field is required.")
     *          )
     *     ),
     *     @OA\Response(
     *          response=500,
     *          description="Failed to create Centre",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(
     *                  property="errors",
     *                  type="object",
     *                  @OA\Property(property="code", type="number", example="500"),
     *                  @OA\Property(property="message", type="string", example="Failed to create centre")
     *              )
     *          )
     *     )
     * )
     *
     * @param  \App\Http\Requests\Centre\CreateCentreResponsibleWorker  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateCentreResponsibleWorker $request)
    {
        $centre = CentreResponsibleWorker::create($request->validated());

        return new CentreResponsibleWorkerResource($centre);
    }

    /**
     * @OA\Get(
     *     path="/elderly-api/v1/centres/{centreId}",
     *     operationId="v1GetCentreDetail",
     *     tags={"centres"},
     *     @OA\Parameter(
     *          in="path",
     *          required=true,
     *          name="centreId",
     *          description="The id of the centre",
     *          @OA\Schema(
     *              type="integer",
     *              example="1"
     *          )
     *     ),
     *     @OA\Response(
     *          response=200,
     *          description="Centre detail",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="data", type="object", ref="#/components/schemas/Centre")
     *          )
     *     ),
     *     @OA\Response(
     *          response=404,
     *          description="Centre not found",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(
     *                  property="status",
     *                  type="object",
     *                  @OA\Property(property="code", type="number", example="404"),
     *                  @OA\Property(property="message", type="string", example="Cannot find centre with id {centreId}"),
     *                  @OA\Property(property="errors", type="array", example="[]", @OA\Items(type="integer", example=null))
     *              )
     *          )
     *     )
     * )
     *
     * @param  int  $centreId
     * @return \Illuminate\Http\Response
     */
    public function show($centreId)
    {
        $centre = CentreResponsibleWorker::find($centreId);
        if (!$centre) {
            return response()->json([
                'status' => [
                    'code' => 404,
                    'message' => "Cannot find centre with id $centreId",
                    'errors' => [],
                ]
            ], 404);
        }

        return new CentreResponsibleWorkerResource($centre);
    }

    /**
     * @OA\Put(
     *     path="/elderly-api/v1/centres/{centreId}",
     *     operationId="v1UpdateCentre",
     *     tags={"centres"},
     *     @OA\Parameter(
     *          in="path",
     *          required=true,
     *          name="centreId",
     *          description="The id of the centre",
     *          @OA\Schema(
     *              type="integer",
     *              example="1"
     *          )
     *     ),
     *     @OA\Response(
     *          response=200,
     *          description="Centre updated",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="data", type="object", ref="#/components/schemas/Centre")
     *          )
     *     ),
     *     @OA\Response(
     *          response=422,
     *          description="Centre data validation fail",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="name.required", type="number", example="The name field is required.")
     *          )
     *     ),
     *     @OA\Response(
     *          response=404,
     *          description="Centre not found",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(
     *                  property="status",
     *                  type="object",
     *                  @OA\Property(property="code", type="number", example="404"),
     *                  @OA\Property(property="message", type="string", example="Cannot find centre with id {centreId}"),
     *                  @OA\Property(property="errors", type="array", example="[]", @OA\Items(type="integer", example=null))
     *              )
     *          )
     *     )
     * )
     *
     * @param  \App\Http\Requests\Centre\UpdateCentreResponsibleWorker  $request
     * @param  int  $centreId
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateCentreResponsibleWorker $request, $centreId)
    {
        $centre = CentreResponsibleWorker::find($centreId);
        if (!$centre) {
            return response()->json([
                'status' => [
                    'code' => 404,
                    'message' => "Cannot find centre with id $centreId",
                    'errors' => [],
                ]
            ], 404);
        }

        $centre->name = $request->has('name') ? $request->name : $centre->name;
        $centre->code = $request->has('code') ? $request->code : $centre->code;
        $centre->save();

        return new CentreResponsibleWorkerResource($centre);
    }

    /**
     * @OA\Delete(
     *      path="/elderly-api/v1/centres/{centreId}",
     *     operationId="v1DeleteCentre",
     *     tags={"centres"},
     *     @OA\Parameter(
     *          in="path",
     *          required=true,
     *          name="centreId",
     *          description="The id of the centre",
     *          @OA\Schema(
     *              type="integer",
     *              example="1"
     *          )
     *     ),
     *     @OA\Response(
     *          response=204,
     *          description="Centre deleted"
     *     ),
     *     @OA\Response(
     *          response=404,
     *          description="Centre not found",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(
     *                  property="status",
     *                  type="object",
     *                  @OA\Property(property="code", type="number", example="404"),
     *                  @OA\Property(property="message", type="string", example="Cannot find centre with id {centreId}"),
     *                  @OA\Property(property="errors", type="array", example="[]", @OA\Items(type="integer", example=null))
     *              )
     *          )
     *     )
     * )
     *
     * @param  int  $centreId
     * @return \Illuminate\Http\Response
     */
    public function destroy($centreId)
    {
        $centre = CentreResponsibleWorker::find($centreId);
        if (!$centre) {
            return response()->json([
                'status' => [
                    'code' => 404,
                    'message' => "Cannot find centre with id $centreId",
                    'errors' => [],
                ]
            ], 404);
        }
        $centre->delete();

        return response()->json(null, 204);
    }
}
