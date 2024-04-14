<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Resources\MedicationHistoryResource;
use App\Http\Services\ExternalService;
use App\Http\Services\ValidatorService;
use App\Models\MedicationHistory;
use Illuminate\Http\Request;

class MedicationHistoryController extends Controller
{
    private $validator;
    private $externalService;

    public function __construct()
    {
        $this->validator = new ValidatorService();
        $this->externalService = new ExternalService();
    }

    /**
     * @OA\Get(
     *     path="/assessments-api/v1/medication-histories/case-id/{caseId}",
     *     operationId="getMedicationHistoryByCaseId",
     *     summary="Get Medication History by caseId",    
     *     tags={"Medication History"},
     *     @OA\Parameter(
     *          in="path",
     *          required=true,
     *          name="caseId",
     *          description="The caseId of the Medication History",
     *          @OA\Schema(
     *              type="integer",
     *              example=1
     *          )
     *     ),
     *     @OA\Response(
     *          response=200,
     *          description="Medication History by caseId",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="data", type="object", ref="#/components/schemas/MedicationHistory")
     *          )
     *     ),
     *     @OA\Response(
     *          response=404,
     *          description="Medication History by caseId not found",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(
     *                  property="error",
     *                  type="object",
     *                  @OA\Property(property="code", type="number", example=404),
     *                  @OA\Property(property="message", type="string", example="Cannot find Medication History with caseId {caseId}")
     *              )
     *          )
     *     )
     * )
     * 
     * Display the specified resource by case_id
     *
     * @param  int  $caseId
     * @return \Illuminate\Http\Response
     */
    public function getByCaseId($caseId)
    {
        $elderCasesIdExists = $this->externalService->isElderCasesIdExists($caseId);
        $medicationHistoriesByCaseId = MedicationHistory::where('case_id', $caseId)
            ->orderBy('updated_at', 'desc')
            ->get();

        if ($elderCasesIdExists && count($medicationHistoriesByCaseId) > 0) {
            return response()->json([
                'data' => MedicationHistoryResource::collection($medicationHistoriesByCaseId),
                'message' => 'Data found',
                'success' => true,
            ]);
        } else {
            return response()->json([
                'error' => [
                    'code' => 404,
                    'message' => "Cannot find Medication History with case_id $caseId",
                ],
            ], 404);
        }
    }

    /**
     * @OA\Get(
     *     path="/assessments-api/v1/medication-histories",
     *     tags={"Medication History"},
     *     summary="List of Medication History",
     *     operationId="getMedicationHistories",
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
        return MedicationHistoryResource::collection(MedicationHistory::latest()->paginate(10));
    }

    /**
     * @OA\Post(
     *     path="/assessments-api/v1/medication-histories",
     *     tags={"Medication History"},
     *     operationId="createMedicationHistory",
     *     summary="Create Medication History",
     *     @OA\RequestBody(
     *         required=true,
     *             @OA\JsonContent(
     *                 required={"case_id, medication_category, medication_name, dosage, number_of_intake, frequency, route"},
     *                 @OA\Property(property="case_id", type="integer", example=1, description="Medication History CaseID"),
     *                 @OA\Property(property="medication_category", type="string", example="hyspepsia and gastro-oesophaeal reflux disease", description="Medication Category"),
     *                 @OA\Property(property="medication_name", type="string", example="Mylanta", description="Medication Name"),
     *                 @OA\Property(property="dosage", type="string", example="400mg", description="Medication Dosage"),
     *                 @OA\Property(property="number_of_intake", type="string", example="1 tab", description="Number of Intake"),
     *                 @OA\Property(
     *                      property="frequency",
     *                      type="array",
     *                      @OA\Items(
     *                          type="string"
     *                      ),
     *                      example="[""Daily"", ""BD"", ""TDS"", ""QID"", ""Q_H"", ""Nocte"", ""prn"", ""Others""]"
     *                 ),
     *                 @OA\Property(property="route", type="string", example="PO/SL/LA/PUFF/SC/PR/Other", description="Route"),
     *                 @OA\Property(property="remarks", type="string", example="remarks example", description="Remarks"),
     *                 @OA\Property(property="gp", type="boolean", example="false", description="GP checkbox"),
     *                 @OA\Property(property="epr", type="boolean", example="false", description="ePR checkbox"),
     *                 @OA\Property(property="sign_off", type="boolean", example="false", description="sign_off data")
     *             )
     *     ),
     *     @OA\Response(
     *          response=200,
     *          description="Medication history created successfully",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="data", type="object", ref="#/components/schemas/MedicationHistory")
     *          )
     *     ),
     *     @OA\Response(
     *          response=422,
     *          description="Medication History validation fail",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="name.required", type="number", example="The name field is required.")
     *          )
     *     ),
     *     @OA\Response(
     *          response=500,
     *          description="Failed to update Medication History",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(
     *                  property="error",
     *                  type="object",
     *                  @OA\Property(property="code", type="number", example="500"),
     *                  @OA\Property(property="message", type="string", example="Failed to update Medication History")
     *              )
     *          )
     *     ),
     *     @OA\Response(
     *          response=404,
     *          description="Elder cases id join table is not found",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(
     *                  property="error",
     *                  type="object",
     *                  @OA\Property(property="code", type="number", example=404),
     *                  @OA\Property(property="message", type="string", example="Cannot find Elder cases record table with id {caseId}")
     *              )
     *          )
     *     )
     * )
     * 
     * Store a newly created resource in storage.
     *
     * @param  App\Http\Requests\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validator->validate_medication_histories($request);
        $request->merge([
            'created_by' => $request->user_id,
            'updated_by' => $request->user_id,
            'created_by_name' => $request->user_name,
            'updated_by_name' => $request->user_name,
        ]);
        $caseId = $request->case_id;
        $elderCasesIdExists = $this->externalService->isElderCasesIdExists($caseId);
        if ($elderCasesIdExists) {
            $medicationHistories = MedicationHistory::create($request->toArray());

            if (!$medicationHistories) {
                return response()->json([
                    'error' => [
                        'code' => 500,
                        'message' => "Failed to create Medication History",
                    ],
                ], 500);
            }

            return response()->json([
                'data' => new MedicationHistoryResource($medicationHistories),
                'message' => 'Medication history created successfully',
                'success' => true,
            ], 200);
        } else {
            return response()->json([
                'error' => [
                    'code' => 404,
                    'message' => "Cannot find Elder cases record with id $caseId",
                ],
            ], 404);
        }
    }

    /**
     * @OA\Get(
     *     path="/assessments-api/v1/medication-histories/{medicationHistoryId}",
     *     operationId="getMedicationHistoryDetail",
     *     summary="Get Medication History by medicationHistoryId",
     *     tags={"Medication History"},
     *     @OA\Parameter(
     *          in="path",
     *          required=true,
     *          name="medicationHistoryId",
     *          description="The id of the Medication History",
     *          @OA\Schema(
     *              type="integer",
     *              example="1"
     *          )
     *     ),
     *     @OA\Response(
     *          response=200,
     *          description="Medication History detail",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="data", type="object", ref="#/components/schemas/MedicationHistory")
     *          )
     *     ),
     *     @OA\Response(
     *          response=404,
     *          description="Medication History not found",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(
     *                  property="error",
     *                  type="object",
     *                  @OA\Property(property="code", type="number", example="404"),
     *                  @OA\Property(property="message", type="string", example="Cannot find Medication history with id {medicationHistoryId}")
     *              )
     *          )
     *     )
     * )
     * 
     * Display the specified resource.
     *
     * @param  int  $medicationHistoryId
     * @return \Illuminate\Http\Response
     */
    public function show($medicationHistoryId)
    {
        $medicationHistories = MedicationHistory::find($medicationHistoryId);

        if (!$medicationHistories) {
            return response()->json([
                'error' => [
                    'code' => 404,
                    'message' => "Cannot find Medication history with id $medicationHistoryId",
                ],
            ], 404);
        }

        return response()->json([
            'data' => new MedicationHistoryResource($medicationHistories),
        ]);
    }

    /**
     * @OA\Put(
     *     path="/assessments-api/v1/medication-histories/{medicationHistoryId}",
     *     tags={"Medication History"},
     *     summary="Update Medication History by medicationHistoryId",
     *     operationId="updateMedicationHistory",
     *     @OA\Parameter(
     *         name="medicationHistoryId",
     *         in="path",
     *         description="Medication History Id to be updated",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             example="1"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         description="Input required Medication Histories information (in string)",
     *         required=true,
     *             @OA\JsonContent(
     *                 required={"case_id, medication_category, medication_name, dosage, number_of_intake, frequency, route"},
     *                 @OA\Property(property="case_id", type="integer", example=1, description="Medication History CaseID"),
     *                 @OA\Property(property="medication_category", type="string", example="hyspepsia and gastro-oesophaeal reflux disease", description="Medication Category"),
     *                 @OA\Property(property="medication_name", type="string", example="Mylanta", description="Medication Name"),
     *                 @OA\Property(property="dosage", type="string", example="400mg", description="Medication Dosage"),
     *                 @OA\Property(property="number_of_intake", type="string", example="1 tab", description="Number of Intake"),
     *                 @OA\Property(
     *                      property="frequency",
     *                      type="array",
     *                      @OA\Items(
     *                          type="string"
     *                      ),
     *                      example="[""Daily"", ""BD"", ""TDS"", ""QID"", ""Q_H"", ""Nocte"", ""prn"", ""Others""]"
     *                 ),
     *                 @OA\Property(property="route", type="string", example="PO/SL/LA/PUFF/SC/PR/Other", description="Route"),
     *                 @OA\Property(property="remarks", type="string", example="remarks example", description="Remarks"),
     *                 @OA\Property(property="gp", type="boolean", example="false", description="GP checkbox"),
     *                 @OA\Property(property="epr", type="boolean", example="false", description="ePR checkbox"),
     *                 @OA\Property(property="sign_off", type="boolean", example="false", description="sign_off data")
     *             )
     *     ),
     *     @OA\Response(
     *          response=200,
     *          description="Medication History updated successfully",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="data", type="object", ref="#/components/schemas/MedicationHistory")
     *          )
     *     ),
     *     @OA\Response(
     *          response=404,
     *          description="Medication History not found",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(
     *                  property="error",
     *                  type="object",
     *                  @OA\Property(property="code", type="number", example="404"),
     *                  @OA\Property(property="message", type="string", example="Cannot find Medication history with id {medicationHistoryId}")
     *              )
     *          )
     *     ),
     *     @OA\Response(
     *          response=422,
     *          description="Medication History validation fail",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="name.required", type="number", example="The name field is required.")
     *          )
     *     ),
     *     @OA\Response(
     *          response=500,
     *          description="Failed to update Medication History",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(
     *                  property="error",
     *                  type="object",
     *                  @OA\Property(property="code", type="number", example="500"),
     *                  @OA\Property(property="message", type="string", example="Failed to update Medication History")
     *              )
     *          )
     *     )
     * )
     * 
     * Update the specified resource in storage.
     *
     * @param  int  $medicationHistoryId
     * @param  App\Http\Requests\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $medicationHistoryId)
    {
        $this->validator->validate_medication_histories($request);
        $request->merge([
            'created_by' => $request->user_id,
            'updated_by' => $request->user_id,
            'created_by_name' => $request->user_name,
            'updated_by_name' => $request->user_name,
        ]);

        $medicationHistory = MedicationHistory::where('id', $medicationHistoryId)->first();

        if (!$medicationHistory) {
            return response()->json([
                'error' => [
                    'code' => 404,
                    'message' => "Cannot find Medication history with id $medicationHistoryId",
                    'success' => false,
                ],
            ], 404);
        }

        if (!$medicationHistory->update($request->toArray())) {
            return response()->json([
                'error' => [
                    'code' => 500,
                    'message' => "Failed to update Medication History",
                    'success' => false,
                ],
            ], 500);
        }

        return response()->json([
            'data' => new MedicationHistoryResource($medicationHistory),
            'message' => 'Medication history updated successfully',
            'success' => true,
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/assessments-api/v1/medication-histories/{medicationHistoryId}",
     *     tags={"Medication History"},
     *     summary="Delete Medication History by medicationHistoryId",
     *     operationId="deleteMedicationHistory",
     *     @OA\Parameter(
     *         name="medicationHistoryId",
     *         in="path",
     *         description="Medication History Id",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             example="1"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Medication History deleted successfully"
     *     ),
     *     @OA\Response(
     *          response=404,
     *          description="Medication History not found",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(
     *                  property="error",
     *                  type="object",
     *                  @OA\Property(property="code", type="number", example="404"),
     *                  @OA\Property(property="message", type="string", example="Cannot find Medication history with id {medicationHistoryId}")
     *              )
     *          )
     *     )
     * )
     * 
     * Remove the specified resource from storage.
     *
     * @param int $medicationHistoryId
     * @return \Illuminate\Http\Response
     */
    public function destroy($medicationHistoryId)
    {
        $medicationHistory = MedicationHistory::find($medicationHistoryId);

        if (!$medicationHistory) {
            return response()->json([
                'error' => [
                    'code' => 404,
                    'message' => "Cannot find Medication history with id $medicationHistoryId",
                    'success' => false,
                ],
            ], 404);
        }

        $medicationHistory->delete();

        return response()->json([
            'data' => null,
            'message' => 'Medication history deleted successfully',
            'success' => true,
        ], 200);
    }
}
