<?php

namespace App\Http\Controllers;

use App\Http\Services\ExternalService;
use Illuminate\Http\Request;

class ReportsDashboardController extends Controller
{
    public function __construct()
    {
        $this->externalService = new ExternalService();
    }

    /**
     * @OA\Get(
     *     path="/assessments-api/v1/reports/staff",
     *     tags={"StaffDashboard"},
     *     summary="Staff report list",
     *     operationId="staffReportList",
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number",
     *         @OA\Schema(
     *             type="integer",
     *             format="int32"
     *         )
     *     ),
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
     *     @OA\Response(
     *         response=200,
     *         description="Successful",
     *         @OA\JsonContent(
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Unprocessable content",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="object",
     *                 @OA\Property(property="code", type="integer", description="Status code", example="422"),
     *                 @OA\Property(property="message", type="string", example=""),
     *                 @OA\Property(property="errors", type="array",
     *                     @OA\Items(type="object",
     *                         @OA\Property(property="field", type="string", description="Field name", example="name"),
     *                         @OA\Property(property="message", type="string", description="Error message of the field", example="The name field is required")
     *                     ) 
     *                 )
     *             )
     *         )
     *     )
     * )
     *
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index_staff(Request $request)
    {
        return $this->externalService->getStaffReports($request);
    }

    /**
     * @OA\Get(
     *     path="/assessments-api/v1/reports/patient",
     *     tags={"PatientDashboard"},
     *     summary="Patient report list",
     *     operationId="patientReportList",
     *     @OA\Parameter(
     *         name="from",
     *         in="query",
     *         description="start date",
     *         @OA\Schema(
     *             type="string",
     *             example="2022-12-30"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="to",
     *         in="query",
     *         description="end date",
     *         @OA\Schema(
     *             type="string",
     *             example="2022-12-31"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful",
     *         @OA\JsonContent(
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Unprocessable content",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="object",
     *                 @OA\Property(property="code", type="integer", description="Status code", example="422"),
     *                 @OA\Property(property="message", type="string", example=""),
     *                 @OA\Property(property="errors", type="array",
     *                     @OA\Items(type="object",
     *                         @OA\Property(property="field", type="string", description="Field name", example="name"),
     *                         @OA\Property(property="message", type="string", description="Error message of the field", example="The name field is required")
     *                     ) 
     *                 )
     *             )
     *         )
     *     )
     * )
     *
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index_patient(Request $request)
    {
        return $this->externalService->getPatientReports($request);
    }

    /**
     * @OA\Get(
     *     path="/assessments-api/v1/reports/insight",
     *     tags={"PatientInsight"},
     *     summary="Patient insight list",
     *     operationId="patientInsightList",
     *     @OA\Parameter(
     *         name="case_id",
     *         in="query",
     *         description="id of cases",
     *         @OA\Schema(
     *             type="integer",
     *             example=10
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful",
     *         @OA\JsonContent(
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Unprocessable content",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="object",
     *                 @OA\Property(property="code", type="integer", description="Status code", example="422"),
     *                 @OA\Property(property="message", type="string", example=""),
     *                 @OA\Property(property="errors", type="array",
     *                     @OA\Items(type="object",
     *                         @OA\Property(property="field", type="string", description="Field name", example="name"),
     *                         @OA\Property(property="message", type="string", description="Error message of the field", example="The name field is required")
     *                     ) 
     *                 )
     *             )
     *         )
     *     )
     * )
     *
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index_insight(Request $request)
    {
        return $this->externalService->getInsightReports($request);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
