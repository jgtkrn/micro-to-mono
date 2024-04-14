<?php

namespace App\Http\Controllers;

use App\Http\Resources\AppointmentResource;
use App\Http\Services\ValidatorService;
use App\Models\Appointment;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    public function __construct()
    {
        $this->validator = new ValidatorService();
    }

    /**
     * @OA\Post(
     *     path="/assessments-api/v1/appointments/search",
     *     tags={"Appointment / Clinic"},
     *     summary="Search Appointment",
     *     operationId="searchAppointment",
     *
     *     @OA\RequestBody(
     *         description="Search Appointment",
     *         required=true,
     *             @OA\JsonContent(
     *                 required={"query"},
     *                 @OA\Property(
     *                     property="query", 
     *                     type="string", 
     *                     example="cluster", 
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
                
                $result = Appointment::select(
                    'appointments.cluster as cluster',
                    'appointments.type as type',
                    'appointments.name_en as name_en',
                    'appointments.name_sc as name_sc',
                )
                ->where('appointments.cluster', 'LIKE', '%'. $query. '%')
                ->orWhere('appointments.type', 'LIKE', '%'. $query. '%')
                ->orWhere('appointments.name_en', 'LIKE', '%'. $query. '%')
                ->orWhere('appointments.name_sc', 'LIKE', '%'. $query. '%')
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
     *     path="/assessments-api/v1/appointments",
     *     tags={"Appointment / Clinic"},
     *     summary="List of Appointment", 
     *     operationId="getAppointment",
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Page size (default 10)",
     *         @OA\Schema(
     *             type="integer",
     *             format="int32"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="sort_by",
     *         in="query",
     *         description="Sort by (default created_at). Option: id, created_at, updated_at",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="sort_dir",
     *         in="query",
     *         description="Sort direction (default desc). Option: asc, desc",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
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
    public function index(Request $request)
    {    
        $request->validate([
            'per_page' => 'nullable|integer',
            'sort_by' => 'nullable|in:id,created_at,updated_at',
            'sort_dir' => 'nullable|in:asc,desc'
        ]);

        // take request
        
        $per_page = $request->query('per_page', 10);
        $sortBy = $request->query('sort_by', 'created_at');
        $sortDir = $request->query('sort_dir', 'asc');
        $appointment = Appointment::query();
        
        return AppointmentResource::collection($appointment
            ->orderBy($sortBy, $sortDir)
            ->paginate($per_page)
            ->appends($request->except(['page']))
        );
    }

    /**
     * @OA\Post(
     *     path="/assessments-api/v1/appointments",
     *     tags={"Appointment / Clinic"},
     *     operationId="createAppointment",  
     *     summary="Create Appointment",
     * 
     *     @OA\RequestBody(
     *         required=true,
     *             @OA\JsonContent(
     *                 required={"cluster, type, name_en, name_sc"},
     *                 @OA\Property(property="cluster", type="string", example="cluster", description="Cluster"),
     *                 @OA\Property(property="type", type="string", example="type", description="Type"),
     *                 @OA\Property(property="name_en", type="string", example="name_en", description="Name in English"),
     *                 @OA\Property(property="name_sc", type="string", example="name_sc", description="Namie in Simplified Chinese"),
     *             )
     *     ),
     *     @OA\Response(
     *          response=200,
     *          description="Appointment created successfully",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="data", type="object", ref="#/components/schemas/Appointment")
     *          )
     *     ),
     *     @OA\Response(
     *          response=422,
     *          description="Appointment validation fail",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="name.required", type="string", example="The name field is required.")
     *          )
     *     ),
     *     @OA\Response(
     *          response=500,
     *          description="Failed to update Appointment",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(
     *                  property="error",
     *                  type="object",
     *                  @OA\Property(property="code", type="number", example="500"),
     *                  @OA\Property(property="message", type="string", example="Failed to update Appointment")
     *              )
     *          )
     *     )
     * )
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validator->validate_appointment($request);
        $appointment = Appointment::create($request->toArray());

        if (!$appointment) {
            return response()->json([
                'error' => [
                    'code' => 500,
                    'message' => "Failed to create Appointment",
                ],
            ], 500);
        }

        return response()->json([
            'data' => new AppointmentResource($appointment),
            'message' => 'Appointment created successfully',
            'success' => true,
        ], 200);
    }

    /**
     * @OA\Get(
     *     path="/assessments-api/v1/appointments/{appointmentId}",
     *     operationId="getAppointmentDetail",
     *     summary="Get Appointment by appointmentId",    
     *     tags={"Appointment / Clinic"},
     *     @OA\Parameter(
     *          in="path",
     *          required=true,
     *          name="appointmentId",
     *          description="The id of the Appointment",
     *          @OA\Schema(
     *              type="integer",
     *              example="98"
     *          )
     *     ),
     *     @OA\Response(
     *          response=200,
     *          description="Appointment detail",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="data", type="object", ref="#/components/schemas/Appointment")
     *          )
     *     ),
     *     @OA\Response(
     *          response=404,
     *          description="Appointment not found",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(
     *                  property="error",
     *                  type="object",
     *                  @OA\Property(property="code", type="number", example="404"),
     *                  @OA\Property(property="message", type="string", example="Cannot find Appointment with id {appointmentId}")
     *              )
     *          )
     *     )
     * )
     * Display the specified resource.
     *
     * @param  int  $appointmentId
     * @return \Illuminate\Http\Response
     */
    public function show($appointmentId)
    {
        $appointment = Appointment::find($appointmentId);
     
        if (!$appointment) {
            return response()->json([
                'error' => [
                    'code' => 404,
                    'message' => "Cannot find Appointment with id $appointmentId",
                ],
            ], 404);
        }

        return response()->json([
            'data' => new AppointmentResource($appointment),
        ]);
    }

    /**
     * @OA\Put(
     *     path="/assessments-api/v1/appointments/{appointmentId}",
     *     tags={"Appointment / Clinic"},
     *     summary="Update Appointment by appointmentId",
     *     operationId="updateAppointment",
     *     @OA\Parameter(
     *         name="appointmentId",
     *         in="path",
     *         description="Update Appointment by appointmentId",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             example="98"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         description="Input required Appointment information (in string)",
     *         required=true,
     *             @OA\JsonContent(
     *                 required={"cluster, type, name_en, name_sc"},
     *                 @OA\Property(property="cluster", type="string", example="cluster", description="Cluster"),
     *                 @OA\Property(property="type", type="string", example="type", description="Type"),
     *                 @OA\Property(property="name_en", type="string", example="name_en", description="Name in English"),
     *                 @OA\Property(property="name_sc", type="string", example="name_sc", description="Namie in Simplified Chinese"),
     *             )
     *     ),
     *     @OA\Response(
     *          response=200,
     *          description="Appointment updated successfully",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="data", type="object", ref="#/components/schemas/Appointment")
     *          )
     *     ),
     *     @OA\Response(
     *          response=404,
     *          description="Appointment not found",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(
     *                  property="error",
     *                  type="object",
     *                  @OA\Property(property="code", type="number", example="404"),
     *                  @OA\Property(property="message", type="string", example="Cannot find Appointment with id {appointmentId}")
     *              )
     *          )
     *     ),
     *     @OA\Response(
     *          response=422,
     *          description="Appointment validation fail",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="name.required", type="string", example="The name field is required.")
     *          )
     *     ),
     *     @OA\Response(
     *          response=500,
     *          description="Failed to update Appointment",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(
     *                  property="error",
     *                  type="object",
     *                  @OA\Property(property="code", type="number", example="500"),
     *                  @OA\Property(property="message", type="string", example="Failed to update Appointment")
     *              )
     *          )
     *     )
     * )
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $appointmentId
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $appointmentId)
    {
        $this->validator->validate_appointment($request);
        
        $appointment = Appointment::where('id', $appointmentId)->first();

        if (!$appointment) {
            return response()->json([
                'error' => [
                    'code' => 404,
                    'message' => "Cannot find Appointment with id $appointmentId",
                    'success' => false,
                ],
            ], 404);
        }

        if (!$appointment->update($request->toArray())) {
            return response()->json([
                'error' => [
                    'code' => 500,
                    'message' => "Failed to update Appointment",
                    'success' => false,
                ],
            ], 500);
        }

        return response()->json([
            'data' => new AppointmentResource($appointment),
            'message' => 'Appointment updated successfully',
            'success' => true,
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/assessments-api/v1/appointments/{appointmentId}",
     *     tags={"Appointment / Clinic"},
     *     summary="Delete Appointment By appointmentId",
     *     operationId="deleteAppointment",
     *     @OA\Parameter(
     *         name="appointmentId",
     *         in="path",
     *         description="Delete Appointment by appointmentId",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             example="98"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Appointment deleted successfully"
     *     ),
     *     @OA\Response(
     *          response=404,
     *          description="Appointment not found",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(
     *                  property="error",
     *                  type="object",
     *                  @OA\Property(property="code", type="number", example="404"),
     *                  @OA\Property(property="message", type="string", example="Cannot find Appointment with id {appointmentId}")
     *              )
     *          )
     *     )
     * )
     * Remove the specified resource from storage.
     *
     * @param int $appointmentId
     * @return \Illuminate\Http\Response
     */
    public function destroy($appointmentId)
    {
        $appointment = Appointment::find($appointmentId);

        if (!$appointment) { 
            return response()->json([
                'error' => [
                    'code' => 404,
                    'message' => "Cannot find Appointment with id $appointmentId",
                    'success' => false,                
                ],
            ], 404);
        }

        $appointment->delete();

        return response()->json([
            'data' => [],
            'message' => 'Appointment deleted successfully',
            'success' => true,
        ]);

    }
}
