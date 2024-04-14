<?php

namespace App\Http\Controllers;

use App\Models\CarePlan;
use Illuminate\Http\Request;
use App\Models\CgaCareTarget;
use App\Models\CgaConsultationNotes;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Services\ExternalService;
use App\Traits\RespondsWithHttpStatus;
use App\Models\CgaConsultationAttachment;
use App\Http\Services\ConsultationFileService;
use App\Exports\CGA\HealthCoachingSessionExport;

class CgaConsultationNotesController extends Controller
{
    use RespondsWithHttpStatus;

    private $fileService;
    private $externalService;
    
    public function __construct()
    {
        $this->externalService = new ExternalService();
        $this->fileService = new ConsultationFileService();
    }
    /**
     * @OA\Get(
     *     path="/assessments-api/v1/cga-consultation",
     *     tags={"CgaConsultationNotes"},
     *     summary="Cga consultation notes list",
     *     operationId="cgaConsultationNotesList",
     *     @OA\Parameter(
     *         name="cga_target_id",
     *         in="query",
     *         description="Cga target id",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="from",
     *         in="query",
     *         description="from date",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *             format="date"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="to",
     *         in="query",
     *         description="to date",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *             format="date"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(type="object",ref="#/components/schemas/CgaConsultationNotes")
     *             )
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
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $request->validate([
            'cga_target_id' => 'required|integer|exists:cga_care_targets,id,deleted_at,NULL',
        ]);

        $cga_target_id = $request->query('cga_target_id');

        $results = CgaConsultationNotes::where('cga_target_id', $cga_target_id)->with(['cgaConsultationAttachment', 'cgaConsultationSign']);
        if(!$results){
            return response()->json(['data' => []], 404);
        }
        $data = $results->orderBy('updated_at', 'desc')->get();
        if($request->query('from') && $request->query('to')){
            $from = $request->query('from');
            $to = $request->query('to');
            $data = $results->whereBetween('assessment_date', [$from, $to])->orderBy('updated_at', 'desc')->get();
        }
        return response()->json(['data' => $data], 200);
    }

    /**
     * @OA\Post(
     *     path="/assessments-api/v1/cga-consultation",
     *     tags={"CgaConsultationNotes"},
     *     summary="Store new cga consultation notes",
     *     operationId="cgaConsultationNotesStore",
     *     @OA\Parameter(
     *         name="id",
     *         in="query",
     *         description="Id of cga consultation notes",
     *         required=false,
     *         @OA\Schema(
     *             type="integer",
     *             example=1
     *         )
     *     ),
     *     @OA\RequestBody(
     *         description="Input cga consultation notes information (in json)",
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"cga_target_id"},
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="cga_target_id", type="integer", example=1),
     *                 @OA\Property(property="assessor_1", type="string", example="1"),
     *                 @OA\Property(property="assessor_2", type="string", example="2"),
     *                 @OA\Property(property="visit_type", type="string", example="yes"),
     *                 @OA\Property(property="assessment_date", type="string",
     *          format="date", example="2022-05-13"),
     *                 @OA\Property(property="assessment_time", type="string", example="00:00:00"),
     *                 @OA\Property(property="sbp", type="integer", example=1),
     *                 @OA\Property(property="dbp", type="integer", example=1),
     *                 @OA\Property(property="pulse", type="integer", example=1),
     *                 @OA\Property(property="pao", type="integer", example=1),
     *                 @OA\Property(property="hstix", type="integer", example=1),
     *                 @OA\Property(property="body_weight", type="integer", example=1),
     *                 @OA\Property(property="waist", type="integer", example=1),
     *                 @OA\Property(property="circumference", type="integer", example=1),
     *                 @OA\Property(property="purpose", type="integer", example=1),
     *                 @OA\Property(property="content", type="string", example="yes"),
     *                 @OA\Property(property="progress", type="string", example="yes"),
     *                 @OA\Property(property="case_summary", type="string", example="yes"),
     *                 @OA\Property(property="followup_options", type="integer", example=1),
     *                 @OA\Property(property="followup", type="string", example="yes"),
     *                 @OA\Property(property="personal_insight", type="string", example="yes"),
     *                 @OA\Property(property="case_status", type="integer", example=1),
     *                 @OA\Property(property="case_remark", type="string", example="yes"),
     *                 @OA\Property(
     *                     description="Single file, max file 12MB, file type allowed: jpg, jpeg, png",
     *                     property="signature_file",
     *                     type="string",
     *                     format="binary"
     *                 ),
     *                 @OA\Property(
     *                     description="Multiple file, max file 12MB, file type allowed: jpg, jpeg, png",
     *                     property="attachment_file",
     *                     type="array",
     *                     collectionFormat="multi",
     *                     @OA\Items(
     *                         type="string",
     *                         format="binary"
     *                    )
     *                 ),
     *             )
     *          ),
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Successful",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", type="object", ref="#/components/schemas/CgaConsultationNotes")
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
     *                         @OA\Property(property="field", type="string", description="Field name", example="cga_target_id"),
     *                         @OA\Property(property="message", type="string", description="Error message of the field", example="The selected cga care target id is invalid.")
     *                     )
     *                 )
     *             )
     *         )
     *     )
     * )
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $cga_target = CgaCareTarget::where('id', $request->cga_target_id)->first();
        if(!$cga_target) {
            return response()->json([
                'data' => null,
                'message' => 'Unauthorized, you are not the case manager.'
            ], 401);
        }
        $care_plan = CarePlan::where('id', $cga_target->care_plan_id)->first();
        if(!$care_plan){
            return response()->json([
                'data' => null,
                'message' => 'Unauthorized, you are not the case manager.'
            ], 401);
        }
        $managers = count($care_plan->caseManagers) == 0 ? [] : $care_plan->caseManagers->pluck('manager_id')->toArray();
        if(
            $care_plan->manager_id !== $request->user_id && 
            !in_array($request->user_id, $managers) && 
            $request->access_role !== 'admin' 
            // && 
            // !$request->is_cga
        ) {
            return response()->json([
                'data' => null,
                'message' => 'Unauthorized, you are not the case manager.'
            ], 401);
        }
        
        if($request->is_other && $request->access_role !== 'admin'){
            return response()->json([
                'data' => null,
                'message' => 'User not in any team access'
            ], 401);
        }
        $data = [
            'cga_target_id' => $request->cga_target_id,

            // Assessor Information
            'assessor_1' => $request->assessor_1,
            'assessor_2' => $request->assessor_2,
            'visit_type' => $request->visit_type,
            'assessment_date' => $request->assessment_date,
            'assessment_time' => $request->assessment_time,

            // Vital Sign
            'sbp' => $request->sbp,
            'dbp' => $request->dbp,
            'pulse' => $request->pulse,
            'pao' => $request->pao,
            'hstix' => $request->hstix,
            'body_weight' => $request->body_weight,
            'waist' => $request->waist,
            'circumference' => $request->circumference,

            // Log
            'purpose' => $request->purpose,
            'content' => $request->content,
            'progress' => $request->progress,
            'case_summary' => $request->case_summary,
            'followup_options' => $request->followup_options,
            'followup' => $request->followup,
            'personal_insight' => $request->personal_insight,
            'visiting_duration' => $request->visiting_duration,

            // Case Status
            'case_status' => $request->case_status,
            'case_remark' => $request->case_remark,
        ];
        $request->validate([
            'cga_target_id' => 'required|integer|exists:cga_care_targets,id,deleted_at,NULL',
            'signature_file' => 'nullable|max:12288',
            'attachment_file' => 'nullable|array',
            'attachment_file.*' => 'max:12288'
        ]);

        $consultation = cgaConsultationNotes::create($data);

        $files = $request->file('attachment_file');

        $status_attachment = 'failed';
        if($request->hasFile('attachment_file')){
            foreach($files as $file){
                $upload_attachment = $this->fileService->upload_cga_attachment($file, $consultation);
                $status_attachment = $upload_attachment;
            }
        }

        $status_sign = 'failed';
        if($request->hasFile('signature_file')){
            $upload_sign = $this->fileService->upload_cga_signature($request, $consultation);
            if($upload_sign) {
                $status_sign = $upload_sign;
            }
        }
        $results = cgaConsultationNotes::where('id', $consultation->id)->with(['cgaConsultationAttachment', 'cgaConsultationSign'])->first();
        // if($status_attachment == 'failed' || $status_sign == 'failed'){
        //     return response()->json(['data' => $results, 'message' => 'attachment ' . "$status_attachment" . ', ' . 'signature ' . "$status_sign"], 409);
        // }
        return response()->json(['data' => $results, 'message' => 'attachment ' . "$status_attachment" . ', ' . 'signature ' . "$status_sign"], 201);

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\CgaConsultationNotes  $cgaConsultationNotes
     * @return \Illuminate\Http\Response
     */
    public function show(CgaConsultationNotes $cgaConsultationNotes)
    {
        //
    }

    /**
     * @OA\Put(
     *     path="/assessments-api/v1/cga-consultation/{id}",
     *     tags={"CgaConsultationNotes"},
     *     summary="Update cga consultation notes",
     *     operationId="cgaConsultationNotesUpdate",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Id of cga consultation notes",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             example=1
     *         )
     *     ),
     *     @OA\RequestBody(
     *         description="Input cga consultation notes information (in json)",
     *         required=true,
     *             @OA\JsonContent(
     *                 required={"cga_target_id"},
     *                 @OA\Property(property="cga_target_id", type="integer", example=1),
     *                 @OA\Property(property="assessor_1", type="string", example="1"),
     *                 @OA\Property(property="assessor_2", type="string", example="2"),
     *                 @OA\Property(property="visit_type", type="string", example="yes"),
     *                 @OA\Property(property="assessment_date", type="string",
     *          format="date", example="2022-05-13"),
     *                 @OA\Property(property="assessment_time", type="string", example="00:00:00"),
     *                 @OA\Property(property="sbp", type="integer", example=1),
     *                 @OA\Property(property="dbp", type="integer", example=1),
     *                 @OA\Property(property="pulse", type="integer", example=1),
     *                 @OA\Property(property="pao", type="integer", example=1),
     *                 @OA\Property(property="hstix", type="integer", example=1),
     *                 @OA\Property(property="body_weight", type="integer", example=1),
     *                 @OA\Property(property="waist", type="integer", example=1),
     *                 @OA\Property(property="circumference", type="integer", example=1),
     *                 @OA\Property(property="purpose", type="integer", example=1),
     *                 @OA\Property(property="content", type="string", example="yes"),
     *                 @OA\Property(property="progress", type="string", example="yes"),
     *                 @OA\Property(property="case_summary", type="string", example="yes"),
     *                 @OA\Property(property="followup_options", type="integer", example=1),
     *                 @OA\Property(property="followup", type="string", example="yes"),
     *                 @OA\Property(property="personal_insight", type="string", example="yes"),
     *                 @OA\Property(property="case_status", type="integer", example=1),
     *                 @OA\Property(property="case_remark", type="string", example="yes"),
     *             )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", type="object", ref="#/components/schemas/CgaConsultationNotes")
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
     *                         @OA\Property(property="field", type="string", description="Field name", example="cga_target_id"),
     *                         @OA\Property(property="message", type="string", description="Error message of the field", example="The selected cga care target id is invalid.")
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="cga consultation notes not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="object",
     *                 @OA\Property(property="code", type="integer", description="Status code", example="404"),
     *                 @OA\Property(property="message", type="string", example="cga consultation notes not found"),
     *                 @OA\Property(property="errors", type="array", description="Empty array", example="[]",
     *                     @OA\Items()
     *                 )
     *             )
     *         )
     *     )
     * )
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\CgaConsultationNotes  $cgaConsultationNotes
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'cga_target_id' => 'required|integer|exists:cga_care_targets,id,deleted_at,NULL',
        ]);

        $cga_target = CgaCareTarget::where('id', $request->cga_target_id)->first();
        if(!$cga_target) {
            return response()->json([
                'data' => null,
                'message' => 'Unauthorized, you are not the case manager.'
            ], 401);
        }
        $care_plan = CarePlan::where('id', $cga_target->care_plan_id)->first();
        if(!$care_plan){
            return response()->json([
                'data' => null,
                'message' => 'Unauthorized, you are not the case manager.'
            ], 401);
        }
        $managers = count($care_plan->caseManagers) == 0 ? [] : $care_plan->caseManagers->pluck('manager_id')->toArray();
        if(
            $care_plan->manager_id !== $request->user_id && 
            !in_array($request->user_id, $managers) && 
            $request->access_role !== 'admin' 
            // && 
            // !$request->is_cga &&
            // $request->hcw
        ) {
            return response()->json([
                'data' => null,
                'message' => 'Unauthorized, you are not the case manager.'
            ], 401);
        }

        if($request->is_other && $request->access_role !== 'admin'){
            return response()->json([
                'data' => null,
                'message' => 'User not in any team access'
            ], 401);
        }

        $results = CgaConsultationNotes::updateOrCreate(
            [ 'id' => $id ],
            [
                'cga_target_id' => $request->cga_target_id,

                // Assessor Information
                'assessor_1' => $request->assessor_1,
                'assessor_2' => $request->assessor_2,
                'visit_type' => $request->visit_type,
                'assessment_date' => $request->assessment_date,
                'assessment_time' => $request->assessment_time,

                // Vital Sign
                'sbp' => $request->sbp,
                'dbp' => $request->dbp,
                'pulse' => $request->pulse,
                'pao' => $request->pao,
                'hstix' => $request->hstix,
                'body_weight' => $request->body_weight,
                'waist' => $request->waist,
                'circumference' => $request->circumference,

                // Log
                'purpose' => $request->purpose,
                'content' => $request->content,
                'progress' => $request->progress,
                'case_summary' => $request->case_summary,
                'followup_options' => $request->followup_options,
                'followup' => $request->followup,
                'personal_insight' => $request->personal_insight,
                'visiting_duration' => $request->visiting_duration,

                // Case Status
                'case_status' => $request->case_status,
                'case_remark' => $request->case_remark,
            ]
        );

        if (!$results) {
            return $this->failure('Failed to update cga consultation notes');
        }

        return response()->json(['data' => $results], 200);
    }

/**
     * @OA\Delete(
     *     path="/assessments-api/v1/cga-consultation/{id}",
     *     tags={"CgaConsultationNotes"},
     *     summary="Delete CgaConsultationNotes by id",
     *     operationId="deleteCgaConsultationNotes",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Delete CgaConsultationNotes by id",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             example=1
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="CgaConsultationNotes deleted successfully"
     *     ),
     *     @OA\Response(
     *          response=404,
     *          description="CgaConsultationNotes not found",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(
     *                  property="error",
     *                  type="object",
     *                  @OA\Property(property="code", type="number", example="404"),
     *                  @OA\Property(property="message", type="string", example="Cannot find CgaConsultationNotes with id {id}")
     *              )
     *          )
     *     )
     * )
     *
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        if(!$request->is_cga && $request->access_role !== 'admin'){
            return response()->json([
                'data' => null,
                'message' => 'User not in CGA team access'
            ], 401);
        }
        $cgaConsultationNotes = CgaConsultationNotes::where('id', $id)->first();
        if(!$cgaConsultationNotes) {
            return response()->json([
                'error' => [
                    'code' => 404,
                    'message' => "Cannot find Cga Consultation Notes with id $id",
                    'success' => false,
                ],
            ], 404);
        }
        $cgaConsultationNotes->delete();
        return response()->json([
            'data' => null,
            'message' => "Cga Consultation Notes with id $id deleted successfully",
            'success' => true,
        ], 201);
    }

    public function exportHCS(Request $request)
    {
        $data = CgaConsultationNotes::select(['cga_target_id','assessor_1', 
                                            'assessor_2',
                                            'assessment_date',
                                            'assessment_time',
                                            'visit_type',
                                            'sbp',
                                            'dbp',
                                            'pulse',
                                            'pao',
                                            'hstix',    
                                            'body_weight',
                                            'waist',
                                            'circumference',
                                            'purpose',
                                            'followup', 
                                            'progress',
                                            'personal_insight',
                                            'case_summary',
                                            'case_status',
                                            'case_remark'])
                                            ->with('carePlan')
                                            ->get();
        $uid = $this->externalService->getUidSetByCasesId($request->bearerToken());
        for($i = 0; $i < count($data); $i++){ 

            $caseId = strval($data[$i]->carePlan?->carePlan?->case_id);
            if($caseId !== null){
                $data[$i]['uid'] = isset($uid[$caseId]) ? $uid[$caseId]['uid'] : null;
            } else{
                $data[$i]['uid'] = null;
            }
        }
        return Excel::download(new HealthCoachingSessionExport($data), 'health-coaching-session.csv');
    }
}
