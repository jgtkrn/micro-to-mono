<?php

namespace App\Http\Controllers;

use App\Http\Requests\Referral\CreateReferralRequest;
use App\Http\Requests\Referral\UpdateReferralRequest;
use App\Http\Resources\ReferralResource;
use App\Models\Referral;
use Illuminate\Http\Request;

class ReferralController extends Controller
{
    /**
     * @OA\Get(
     *     path="/elderly-api/v1/referrals",
     *     operationId="v1GetReferrals",
     *     tags={"referrals"},
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
     *          description="available options: id, label, code, created_at, updated_at",
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
     *          description="Referral List",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(
     *                  property="data",
     *                  type="array",
     *                  @OA\Items(type="object", ref="#/components/schemas/Referral")
     *              ),
     *              @OA\Property(
     *                  property="links",
     *                  type="object",
     *                  @OA\Property(
     *                      property="first",
     *                      type="string",
     *                      example="/elderly-api/v1/referrals?page=1"
     *                  ),
     *                  @OA\Property(
     *                      property="last",
     *                      type="string",
     *                      example="/elderly-api/v1/referrals?page=10"
     *                  ),
     *                  @OA\Property(
     *                      property="prev",
     *                      type="string",
     *                      example=null
     *                  ),
     *                  @OA\Property(
     *                      property="next",
     *                      type="string",
     *                      example="/elderly-api/v1/referrals?page=2"
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
     *                      example="/elderly-api/v1/referrals"
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
        $sortBy = in_array($sortField, ['label', 'code', 'created_at', 'updated_at']) ? $sortField : 'created_at';
        $sortDir = $request->query('sort_dir') == 'asc' ? 'asc' : 'desc';
        $referrals = Referral::orderBy($sortBy, $sortDir)->paginate($perPage);

        return ReferralResource::collection($referrals);
    }

    /**
     * @OA\Post(
     *     path="/elderly-api/v1/referrals",
     *     operationId="v1CreateReferral",
     *     tags={"referrals"},
     *     @OA\RequestBody(
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="name", type="string", example="Exlusion Referral"),
     *              @OA\Property(property="code", type="string", example="exclusion_zone"),
     *          )
     *     ),
     *     @OA\Response(
     *          response=201,
     *          description="Referral created",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="data", type="object", ref="#/components/schemas/Referral")
     *          )
     *     ),
     *     @OA\Response(
     *          response=422,
     *          description="Referral data validation fail",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="name.required", type="number", example="The name field is required.")
     *          )
     *     ),
     *     @OA\Response(
     *          response=500,
     *          description="Failed to create Referral",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(
     *                  property="errors",
     *                  type="object",
     *                  @OA\Property(property="code", type="number", example="500"),
     *                  @OA\Property(property="message", type="string", example="Failed to create referral")
     *              )
     *          )
     *     )
     * )
     *
     * @param  \App\Http\Requests\Referral\CreateReferralRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CreateReferralRequest $request)
    {
        $referral = Referral::create($request->validated());

        return new ReferralResource($referral);
    }

    /**
     * @OA\Get(
     *     path="/elderly-api/v1/referrals/{referralId}",
     *     operationId="v1GetReferralDetail",
     *     tags={"referrals"},
     *     @OA\Parameter(
     *          in="path",
     *          required=true,
     *          name="referralId",
     *          description="The id of the referral",
     *          @OA\Schema(
     *              type="integer",
     *              example="1"
     *          )
     *     ),
     *     @OA\Response(
     *          response=200,
     *          description="Referral detail",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="data", type="object", ref="#/components/schemas/Referral")
     *          )
     *     ),
     *     @OA\Response(
     *          response=404,
     *          description="Referral not found",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(
     *                  property="status",
     *                  type="object",
     *                  @OA\Property(property="code", type="number", example="404"),
     *                  @OA\Property(property="message", type="string", example="Cannot find referral with id {referralId}"),
     *                  @OA\Property(property="errors", type="array", example="[]", @OA\Items(type="integer", example=null))
     *              )
     *          )
     *     )
     * )
     *
     * @param  int  $zoneId
     * @return \Illuminate\Http\Response
     */
    public function show($referralId)
    {
        $referral = Referral::find($referralId);
        if (!$referral) {
            return response()->json([
                'status' => [
                    'code' => 404,
                    'message' => "Cannot find zone with id $referralId",
                    'errors' => [],
                ]
            ], 404);
        }

        return new ReferralResource($referral);
    }

    /**
     * @OA\Put(
     *     path="/elderly-api/v1/referrals/{referralId}",
     *     operationId="v1UpdateReferral",
     *     tags={"referrals"},
     *     @OA\Parameter(
     *          in="path",
     *          required=true,
     *          name="referralId",
     *          description="The id of the referral",
     *          @OA\Schema(
     *              type="integer",
     *              example="1"
     *          )
     *     ),
     *     @OA\Response(
     *          response=200,
     *          description="Referral updated",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="data", type="object", ref="#/components/schemas/Referral")
     *          )
     *     ),
     *     @OA\Response(
     *          response=422,
     *          description="Referral data validation fail",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="name.required", type="number", example="The name field is required.")
     *          )
     *     ),
     *     @OA\Response(
     *          response=404,
     *          description="Referral not found",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(
     *                  property="status",
     *                  type="object",
     *                  @OA\Property(property="code", type="number", example="404"),
     *                  @OA\Property(property="message", type="string", example="Cannot find referral with id {referralId}"),
     *                  @OA\Property(property="errors", type="array", example="[]", @OA\Items(type="integer", example=null))
     *              )
     *          )
     *     )
     * )
     *
     * @param  \App\Http\Requests\Referral\UpdateReferralRequest  $request
     * @param  int  $zoneId
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateReferralRequest $request, $referralId)
    {
        $referral = Referral::find($referralId);
        if (!$referral) {
            return response()->json([
                'status' => [
                    'code' => 404,
                    'message' => "Cannot find zone with id $referralId",
                    'errors' => [],
                ]
            ], 404);
        }

        $referral->label = $request->has('label') ? $request->label : $referral->label;
        $referral->code = $request->has('code') ? $request->code : $referral->code;
        $referral->bzn_code = $request->has('bzn_code') ? $request->bzn_code : $referral->bzn_code;
        $referral->cga_code = $request->has('cga_code') ? $request->cga_code : $referral->cga_code;
        $referral->save();

        return new ReferralResource($referral);
    }

    /**
     * @OA\Delete(
     *      path="/elderly-api/v1/referrals/{referralId}",
     *     operationId="v1DeleteReferral",
     *     tags={"referrals"},
     *     @OA\Parameter(
     *          in="path",
     *          required=true,
     *          name="referralId",
     *          description="The id of the referral",
     *          @OA\Schema(
     *              type="integer",
     *              example="1"
     *          )
     *     ),
     *     @OA\Response(
     *          response=204,
     *          description="Referral deleted"
     *     ),
     *     @OA\Response(
     *          response=404,
     *          description="Referral not found",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(
     *                  property="status",
     *                  type="object",
     *                  @OA\Property(property="code", type="number", example="404"),
     *                  @OA\Property(property="message", type="string", example="Cannot find referral with id {referralId}"),
     *                  @OA\Property(property="errors", type="array", example="[]", @OA\Items(type="integer", example=null))
     *              )
     *          )
     *     )
     * )
     *
     * @param  int  $zoneId
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($referralId)
    {
        $referral = Referral::find($referralId);
        if (!$referral) {
            return response()->json([
                'status' => [
                    'code' => 404,
                    'message' => "Cannot find zone with id $referralId",
                    'errors' => [],
                ]
            ], 404);
        }

        $referral->delete();

        return response()->json(null, 204);
    }
}
