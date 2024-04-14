<?php

namespace App\Http\Controllers;

use App\Http\Resources\MedicationDrugResource;
use App\Http\Services\ValidatorService;
use App\Models\MedicationDrug;
use Illuminate\Http\Request;

class MedicationDrugController extends Controller
{
    public function __construct()
    {
        $this->validator = new ValidatorService();
    }
    /**
     * @OA\Post(
     *     path="/assessments-api/v1/medication-drugs/search",
     *     tags={"Medication Drug"},
     *     summary="Search Medication Drug By name",
     *     operationId="searchByName",
     *
     *     @OA\RequestBody(
     *         description="Search Medication Drug By name",
     *         required=true,
     *             @OA\JsonContent(
     *                 required={"query"},
     *                 @OA\Property(
     *                     property="query", 
     *                     type="string", 
     *                     example="GASTRO", 
     *                     description="Input Search Text"
     *                 )
     *             )
     *     ),
     * 
     *     @OA\Response(
     *         response=200,
     *         description="Successful"
     *     ),
     * 
     *      @OA\Response(
     *         response=400,
     *         description="Bad Request"
     *     ),
     * 
     *     @OA\Response(
     *         response=404,
     *         description="Resource not found"
     *     ),
     * )
     * 
     * Display a listing of the search resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function search(Request $request)
    {
        if ($request->has('query')) {
            $result = [];
            $query = $request->get('query');
            if (!is_null($query)) {
                $result = MedicationDrug::with('child')
                    ->where('medication_drugs.name', 'LIKE', '%' . $query . '%')
                    ->get();

                if (count($result)) {
                    return response()->json([
                        'data' => $result,
                        'message' => 'Data found',
                        'success' => true,
                    ]);
                } else {
                    return response()->json([
                        'error' => [
                            'code' => 404,
                            'message' => "No Data found",
                            'success' => false,
                        ],
                    ], 404);
                }
            } else {
                return response()->json([
                    'error' => [
                        'code' => 404,
                        'message' => "No Data found",
                        'success' => false,
                    ],
                ], 404);
            }
        } else {
            return response()->json([
                'error' => [
                    'code' => 400,
                    'message' => "query key parameter is required",
                    'success' => false
                ],
            ], 400);
        }
    }

    /**
     * @OA\Get(
     *     path="/assessments-api/v1/medication-drugs",
     *     tags={"Medication Drug"},
     *     summary="List of Medication Drug",
     *     operationId="getMedicationDrug",
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
     * 
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return MedicationDrugResource::collection(MedicationDrug::with('child')->where('parent_id', 0)->get());
    }

    /**
     * @OA\Post(
     *     path="/assessments-api/v1/medication-drugs",
     *     tags={"Medication Drug"},
     *     operationId="createMedicationDrug",
     *     summary="Create Medication Drug",
     *     @OA\RequestBody(
     *         required=true,
     *             @OA\JsonContent(
     *                 required={"parent_id, name"},
     *                 @OA\Property(property="parent_id", type="integer", example="86"),
     *                 @OA\Property(property="name", type="string", example="Test", description="Medication Drug Name"),
     *             )
     *     ),
     *     @OA\Response(
     *          response=200,
     *          description="Medication drug created successfully",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="data", type="object", ref="#/components/schemas/MedicationDrug")
     *          )
     *     ),
     *     @OA\Response(
     *          response=422,
     *          description="Medication Drug validation fail",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="name.required", type="number", example="The name field is required.")
     *          )
     *     ),
     *     @OA\Response(
     *          response=500,
     *          description="Failed to update Medication Drug",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(
     *                  property="error",
     *                  type="object",
     *                  @OA\Property(property="code", type="number", example="500"),
     *                  @OA\Property(property="message", type="string", example="Failed to update Medication Drug")
     *              )
     *          )
     *     )
     * )
     * 
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validator->validate_medication_drug($request);
        $medicationDrug = MedicationDrug::create($request->toArray());

        if (!$medicationDrug) {
            return response()->json([
                'error' => [
                    'code' => 500,
                    'message' => "Failed to create Medication drug",
                ],
            ], 500);
        }

        return response()->json([
            'data' => new MedicationDrugResource($medicationDrug),
            'message' => 'Medication drug created successfully',
            'success' => true,
        ], 200);
    }

    /**
     * @OA\Get(
     *     path="/assessments-api/v1/medication-drugs/{medicationDrugId}",
     *     operationId="getMedicationDrugDetail",
     *     summary="Get Medication Drug by medicationDrugId",
     *     tags={"Medication Drug"},
     *     @OA\Parameter(
     *          in="path",
     *          required=true,
     *          name="medicationDrugId",
     *          description="The id of the Medication Drug",
     *          @OA\Schema(
     *              type="integer",
     *              example="507"
     *          )
     *     ),
     *     @OA\Response(
     *          response=200,
     *          description="Medication Drug detail",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="data", type="object", ref="#/components/schemas/MedicationDrug")
     *          )
     *     ),
     *     @OA\Response(
     *          response=404,
     *          description="Medication Drug not found",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(
     *                  property="error",
     *                  type="object",
     *                  @OA\Property(property="code", type="number", example="404"),
     *                  @OA\Property(property="message", type="string", example="Cannot find Medication drug with id {medicationDrugId}")
     *              )
     *          )
     *     )
     * )
     * 
     * Display the specified resource.
     *
     * @param  int  $medicationDrugId
     * @return \Illuminate\Http\Response
     */
    public function show($medicationDrugId)
    {
        $medicationDrug = MedicationDrug::find($medicationDrugId);

        if (!$medicationDrug) {
            return response()->json([
                'error' => [
                    'code' => 404,
                    'message' => "Cannot find Medication drug with id $medicationDrugId",
                ],
            ], 404);
        }

        return response()->json([
            'data' => new MedicationDrugResource($medicationDrug),
        ]);
    }

    /**
     * @OA\Put(
     *     path="/assessments-api/v1/medication-drugs/{medicationDrugId}",
     *     tags={"Medication Drug"},
     *     summary="Update Medication Drug by medicationDrugId",
     *     operationId="updateMedicationDrug",
     *     @OA\Parameter(
     *         name="medicationDrugId",
     *         in="path",
     *         description="Update Medication Drug by medicationDrugId",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             example="507"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         description="Input required Medication Drug information (in string)",
     *         required=true,
     *             @OA\JsonContent(
     *                 required={"name"},
     *                 @OA\Property(property="name", type="string", example="Test - updated", description="Medication Drug Name"),
     *             )
     *     ),
     *     @OA\Response(
     *          response=200,
     *          description="Medication Drug updated successfully",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="data", type="object", ref="#/components/schemas/MedicationDrug")
     *          )
     *     ),
     *     @OA\Response(
     *          response=404,
     *          description="Medication Drug not found",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(
     *                  property="error",
     *                  type="object",
     *                  @OA\Property(property="code", type="number", example="404"),
     *                  @OA\Property(property="message", type="string", example="Cannot find Medication drug with id {medicationDrugId}")
     *              )
     *          )
     *     ),
     *     @OA\Response(
     *          response=422,
     *          description="Medication Drug validation fail",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="name.required", type="number", example="The name field is required.")
     *          )
     *     ),
     *     @OA\Response(
     *          response=500,
     *          description="Failed to update Medication Drug",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(
     *                  property="error",
     *                  type="object",
     *                  @OA\Property(property="code", type="number", example="500"),
     *                  @OA\Property(property="message", type="string", example="Failed to update Medication Drug")
     *              )
     *          )
     *     )
     * )
     * 
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $medicationDrugId
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $medicationDrugId)
    {
        $request->validate([
            'name' => ['required', 'string'],
        ]);

        $medicationDrug = MedicationDrug::where('id', $medicationDrugId)->first();

        if (!$medicationDrug) {
            return response()->json([
                'error' => [
                    'code' => 404,
                    'message' => "Cannot find Medication drug with id $medicationDrugId",
                    'success' => false,
                ],
            ], 404);
        }

        if (!$medicationDrug->update($request->toArray())) {
            return response()->json([
                'error' => [
                    'code' => 500,
                    'message' => "Failed to update Medication drug",
                    'success' => false,
                ],
            ], 500);
        }

        return response()->json([
            'data' => new MedicationDrugResource($medicationDrug),
            'message' => 'Medication drug updated successfully',
            'success' => true,
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/assessments-api/v1/medication-drugs/{medicationDrugId}",
     *     tags={"Medication Drug"},
     *     summary="Delete Medication Drug by medicationDrugId",
     *     operationId="deleteMedicationDrug",
     *     @OA\Parameter(
     *         name="medicationDrugId",
     *         in="path",
     *         description="Delete Medication Drug by medicationDrugId",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             example="507"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Medication Drug deleted successfully"
     *     ),
     *     @OA\Response(
     *          response=404,
     *          description="Medication Drug not found",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(
     *                  property="error",
     *                  type="object",
     *                  @OA\Property(property="code", type="number", example="404"),
     *                  @OA\Property(property="message", type="string", example="Cannot find Medication drug with id {medicationDrugId}")
     *              )
     *          )
     *     )
     * )
     * 
     * Remove the specified resource from storage.
     *
     * @param int $medicationDrugId
     * @return \Illuminate\Http\Response
     */
    public function destroy($medicationDrugId)
    {
        $medicationDrug = MedicationDrug::find($medicationDrugId);

        if (!$medicationDrug) {
            return response()->json([
                'error' => [
                    'code' => 404,
                    'message' => "Cannot find Medication drug with id $medicationDrugId",
                    'success' => false,
                ],
            ], 404);
        }

        if ($medicationDrug->child->count() > 0) {
            return response()->json([
                'error' => [
                    'code' => 403,
                    'message' => "Cannot perform delete operation, this id : $medicationDrugId has child",
                    'success' => false,
                ],
            ], 403);
        } else {
            $medicationDrug->delete();
            return response()->json([
                'data' => [],
                'message' => 'Medication drug deleted successfully',
                'success' => true,
            ]);
        }
    }
}
