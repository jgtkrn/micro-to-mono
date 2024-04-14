<?php

namespace App\Http\Controllers;

use App\Models\BznConsultationNotes;
use App\Models\BznConsultationAttachment;
use App\Models\BznCareTarget;
use App\Models\CarePlan;
use Illuminate\Http\Request;
use App\Http\Services\ConsultationFileService;
use App\Traits\RespondsWithHttpStatus;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\OMAHA\PlanExport;
use App\Exports\OMAHA\VitalSignExport;
use App\Http\Services\ExternalService;

class BznConsultationNotesController extends Controller
{
    use RespondsWithHttpStatus;

    private $fileService;
    private $externalService;

    public function __construct()
    {
        $this->fileService = new ConsultationFileService();
        $this->externalService = new ExternalService();
    }

    /**
     * @OA\Get(
     *     path="/assessments-api/v1/bzn-consultation",
     *     tags={"BznConsultationNotes"},
     *     summary="Bzn consultation notes list",
     *     operationId="bznConsultationNotesList",
     *     @OA\Parameter(
     *         name="bzn_target_id",
     *         in="query",
     *         description="Bzn target id",
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
     *                 @OA\Items(type="object",ref="#/components/schemas/BznConsultationNotes")
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
        if(
            
            $request->access_role !== 'admin'
        ){
            return response()->json([
                'data' => null,
                'message' => 'User not in BZN team access'
            ], 401);
        }
        $request->validate([
            'bzn_target_id' => 'required|integer|exists:bzn_care_targets,id,deleted_at,NULL',
        ]);

        $bzn_target_id = $request->query('bzn_target_id');

        $results = BznConsultationNotes::where('bzn_target_id', $bzn_target_id)->with(['bznConsultationAttachment', 'bznConsultationSign']);
        if(!$results){
            return response()->json(['data' => []], 404);
        }
        $data = $results->orderBy('updated_at', 'asc')->get();
        if($request->query('from') && $request->query('to')){
            $from = $request->query('from');
            $to = $request->query('to');
            $data = $results->whereBetween('assessment_date', [$from, $to])->orderBy('updated_at', 'asc')->get();
        }
        return response()->json(['data' => $data], 200);
    }
    /**
     * @OA\Post(
     *     path="/assessments-api/v1/bzn-consultation",
     *     tags={"BznConsultationNotes"},
     *     summary="Store new bzn consultation notes",
     *     operationId="bznConsultationNotesStore",
     *     @OA\Parameter(
     *         name="id",
     *         in="query",
     *         description="Id of bzn consultation notes",
     *         required=false,
     *         @OA\Schema(
     *             type="integer",
     *             example=1
     *         )
     *     ),
     *     @OA\RequestBody(
     *         description="Input bzn consultation notes information (in json)",
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"bzn_target_id"},
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="bzn_target_id", type="integer", example=1),
     *                 @OA\Property(property="assessor", type="string", example="1"),
     *                 @OA\Property(property="meeting", type="string", example="yes"),
     *                 @OA\Property(property="visit_type", type="string", example="yes"),
     *                 @OA\Property(property="assessment_date", type="string", format="date", example="2022-05-13"),
     *                 @OA\Property(property="assessment_time", type="string", example="00:00:00"),
     *                 @OA\Property(property="sbp", type="integer", example=1),
     *                 @OA\Property(property="dbp", type="integer", example=1),
     *                 @OA\Property(property="pulse", type="integer", example=1),
     *                 @OA\Property(property="pao", type="integer", example=1),
     *                 @OA\Property(property="hstix", type="integer", example=1),
     *                 @OA\Property(property="body_weight", type="integer", example=1),
     *                 @OA\Property(property="waist", type="integer", example=1),
     *                 @OA\Property(property="circumference", type="integer", example=1),
     *                 @OA\Property(property="domain", type="integer", example=1),
     *                 @OA\Property(property="urgency", type="integer", example=1),
     *                 @OA\Property(property="category", type="integer", example=1),
     *                 @OA\Property(property="intervention_remark", type="string", example="yes"),
     *                 @OA\Property(property="consultation_remark", type="string", example="yes"),
     *                 @OA\Property(property="area", type="string", example="yes"),
     *                 @OA\Property(property="priority", type="integer", example=1),
     *                 @OA\Property(property="target", type="string", example="yes"),
     *                 @OA\Property(property="modifier", type="integer", example=1),
     *                 @OA\Property(property="ssa", type="string", example="yes"),
     *                 @OA\Property(property="knowledge", type="integer", example=1),
     *                 @OA\Property(property="behaviour", type="integer", example=1),
     *                 @OA\Property(property="status", type="integer", example=1),
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
     *              )
     *          ),
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Successful",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", type="object", ref="#/components/schemas/BznConsultationNotes")
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
     *                         @OA\Property(property="field", type="string", description="Field name", example="bzn_target_id"),
     *                         @OA\Property(property="message", type="string", description="Error message of the field", example="The selected bzn care target id is invalid.")
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
        $bzn_target = BznCareTarget::where('id', $request->bzn_target_id)->first();
        if(!$bzn_target) {
            return response()->json([
                'data' => null,
                'message' => 'Unauthorized, you are not the case manager.'
            ], 401);
        }
        $care_plan = CarePlan::where('id', $bzn_target->care_plan_id)->with('caseManagers')->first();
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
            // !$request->is_bzn && 
            // $request->is_hcw
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
            'bzn_target_id' => $request->bzn_target_id,

            // Assessor Information
            'assessor' => $request->assessor,
            'meeting' => $request->meeting,
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

            // Intervention Target 1
            'domain' => $request->domain,
            'urgency' => $request->urgency,
            'category' => $request->category,
            'intervention_remark' => $request->intervention_remark,
            'consultation_remark' => $request->consultation_remark,
            'area' => $request->area,
            'priority' => $request->priority,
            'target' => $request->target,
            'modifier' => $request->modifier,
            'ssa' => $request->ssa,
            'knowledge' => $request->knowledge,
            'behaviour' => $request->behaviour,
            'status' => $request->status,
            'omaha_s' => $request->omaha_s,
            'visiting_duration' => $request->visiting_duration,

            // Case Status
            'case_status' => $request->case_status,
            'case_remark' => $request->case_remark,
        ];

        $request->validate([
            'bzn_target_id' => 'required|integer|exists:bzn_care_targets,id,deleted_at,NULL',
            'signature_file' => 'nullable|max:12288',
            'attachment_file' => 'nullable|array',
            'attachment_file.*' => 'max:12288'
        ]);

        $consultation = BznConsultationNotes::create($data);

        $files = $request->file('attachment_file');

        $status_attachment = 'failed';
        if($request->hasFile('attachment_file')){
            foreach($files as $file){
                $upload_attachment = $this->fileService->upload_bzn_attachment($file, $consultation);
                $status_attachment = $upload_attachment;
            }
        }

        $status_sign = 'failed';
        if($request->hasFile('signature_file')){
            $upload_sign = $this->fileService->upload_bzn_signature($request, $consultation);
            if($upload_sign) {
                $status_sign = $upload_sign;
            }
        }
        $results = BznConsultationNotes::where('id', $consultation->id)->with(['bznConsultationAttachment', 'bznConsultationSign'])->first();
        // if($status_attachment == 'failed' || $status_sign == 'failed'){
        //     return response()->json(['data' => $results, 'message' => 'attachment ' . "$status_attachment" . ', ' . 'signature ' . "$status_sign"], 409);
        // }
        return response()->json(['data' => $results, 'status' => 'attachment ' . "$status_attachment" . ', ' . 'signature ' . "$status_sign"], 201);

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\BznConsultationNotes  $bznConsultationNotes
     * @return \Illuminate\Http\Response
     */
    public function show(BznConsultationNotes $bznConsultationNotes)
    {
        //
    }

    /**
     * @OA\Put(
     *     path="/assessments-api/v1/bzn-consultation/{id}",
     *     tags={"BznConsultationNotes"},
     *     summary="Update bzn consultation notes",
     *     operationId="bznConsultationNotesUpdate",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Id of bzn consultation notes",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             example=1
     *         )
     *     ),
     *     @OA\RequestBody(
     *         description="Input bzn consultation notes information (in json)",
     *         required=true,
     *             @OA\JsonContent(
     *                 required={"bzn_target_id"},
     *                 @OA\Property(property="bzn_target_id", type="integer", example=1),
     *                 @OA\Property(property="assessor", type="string", example="1"),
     *                 @OA\Property(property="meeting", type="string", example="yes"),
     *                 @OA\Property(property="visit_type", type="string", example="yes"),
     *                 @OA\Property(property="assessment_date", type="string", format="date", example="2022-05-13"),
     *                 @OA\Property(property="assessment_time", type="string", example="00:00:00"),
     *                 @OA\Property(property="sbp", type="integer", example=1),
     *                 @OA\Property(property="dbp", type="integer", example=1),
     *                 @OA\Property(property="pulse", type="integer", example=1),
     *                 @OA\Property(property="pao", type="integer", example=1),
     *                 @OA\Property(property="hstix", type="integer", example=1),
     *                 @OA\Property(property="body_weight", type="integer", example=1),
     *                 @OA\Property(property="waist", type="integer", example=1),
     *                 @OA\Property(property="circumference", type="integer", example=1),
     *                 @OA\Property(property="domain", type="integer", example=1),
     *                 @OA\Property(property="urgency", type="integer", example=1),
     *                 @OA\Property(property="category", type="integer", example=1),
     *                 @OA\Property(property="intervention_remark", type="string", example="yes"),
     *                 @OA\Property(property="consultation_remark", type="string", example="yes"),
     *                 @OA\Property(property="area", type="string", example="yes"),
     *                 @OA\Property(property="priority", type="integer", example=1),
     *                 @OA\Property(property="target", type="string", example="yes"),
     *                 @OA\Property(property="modifier", type="integer", example=1),
     *                 @OA\Property(property="ssa", type="string", example="yes"),
     *                 @OA\Property(property="knowledge", type="integer", example=1),
     *                 @OA\Property(property="behaviour", type="integer", example=1),
     *                 @OA\Property(property="status", type="integer", example=1),
     *                 @OA\Property(property="case_status", type="integer", example=1),
     *                 @OA\Property(property="case_remark", type="string", example="yes"),
     *             )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", type="object", ref="#/components/schemas/BznConsultationNotes")
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
     *                         @OA\Property(property="field", type="string", description="Field name", example="bzn_target_id"),
     *                         @OA\Property(property="message", type="string", description="Error message of the field", example="The selected bzn care target id is invalid.")
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Bzn consultation notes not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="object",
     *                 @OA\Property(property="code", type="integer", description="Status code", example="404"),
     *                 @OA\Property(property="message", type="string", example="Bzn consultation notes not found"),
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
     * @param  \App\Models\BznConsultationNotes  $bznConsultationNotes
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {        
        $request->validate([
            'bzn_target_id' => 'required|integer|exists:bzn_care_targets,id,deleted_at,NULL',
        ]);

        $bzn_target = BznCareTarget::where('id', $request->bzn_target_id)->first();
        if(!$bzn_target) {
            return response()->json([
                'data' => null,
                'message' => 'Unauthorized, you are not the case manager.'
            ], 401);
        }
        $care_plan = CarePlan::where('id', $bzn_target->care_plan_id)->with('caseManagers')->first();
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
            // !$request->is_bzn && 
            // $request->is_hcw
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

        $results = BznConsultationNotes::updateOrCreate(
            [ 'id' => $id ],
            [
               'bzn_target_id' => $request->bzn_target_id,

                // Assessor Information
                'assessor' => $request->assessor,
                'meeting' => $request->meeting,
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

                // Intervention Target 1
                'domain' => $request->domain,
                'urgency' => $request->urgency,
                'category' => $request->category,
                'intervention_remark' => $request->intervention_remark,
                'consultation_remark' => $request->consultation_remark,
                'area' => $request->area,
                'priority' => $request->priority,
                'target' => $request->target,
                'modifier' => $request->modifier,
                'ssa' => $request->ssa,
                'knowledge' => $request->knowledge,
                'behaviour' => $request->behaviour,
                'status' => $request->status,
                'omaha_s' => $request->omaha_s,
                'visiting_duration' => $request->visiting_duration,

                // Case Status
                'case_status' => $request->case_status,
                'case_remark' => $request->case_remark,
            ]
        );

        if (!$results) {
            return $this->failure('Failed to update bzn consultation notes');
        }

        return response()->json(['data' => $results], 200);
    }

    /**
     * @OA\Delete(
     *     path="/assessments-api/v1/bzn-consultation/{id}",
     *     tags={"BznConsultationNotes"},
     *     summary="Delete BznConsultationNotes by id",
     *     operationId="deleteBznConsultationNotes",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Delete BznConsultationNotes by id",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             example=1
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="BznConsultationNotes deleted successfully"
     *     ),
     *     @OA\Response(
     *          response=404,
     *          description="BznConsultationNotes not found",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(
     *                  property="error",
     *                  type="object",
     *                  @OA\Property(property="code", type="number", example="404"),
     *                  @OA\Property(property="message", type="string", example="Cannot find BznConsultationNotes with id {id}")
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
        if(!$request->is_bzn && $request->access_role !== 'admin'){
            return response()->json([
                'data' => null,
                'message' => 'User not in BZN team access'
            ], 401);
        }
        $bznConsultationNotes = BznConsultationNotes::where('id', $id)->first();
        if(!$bznConsultationNotes) {
            return response()->json([
                'error' => [
                    'code' => 404,
                    'message' => "Cannot find Bzn Consultation Notes with id $id",
                    'success' => false,
                ],
            ], 404);
        }
        $bznConsultationNotes->delete();
        return response()->json([
            'data' => null,
            'message' => "Bzn Consultation Notes with id $id deleted successfully",
            'success' => true,
        ], 201);
    }

    /**
     * @OA\Get(
     *     path="/assessments-api/v1/omaha-plan-export",
     *     tags={"BznConsultationNotes"},
     *     summary="export omaha plan to csv",
     *     operationId="exportOmahaPlans",
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

    public function exportPlans(Request $request)
    {
        $uidSet = $this->externalService->getUidSet($request->bearerToken());
        $domainOptions = [
            "Environmental",
            "Psychosocial",
            "Physiological",
            "Health-related Behaviors"
        ];

        $modifierOptions = [
            "Individual",
            "Family",
            "Community"
        ];

        $urgencyOptions = [
            "Actual",
            "Health promotion",
            "Potential"
        ];

        $priorityOptions = [
            "High",
            "Low"
        ];

        $categoryOptions = [
            "Teaching, Guidance, and Counselling",
            "Treatments and Procedures",
            "Case Management",
            "Surveillance"
        ];

        $plans = BznConsultationNotes::select([
                    'assessment_date',
                    'meeting',
                    'domain',
                    'area',
                    'modifier',
                    'urgency',
                    'priority',
                    'category',
                    'target',
                    'intervention_remark',
                    'knowledge',
                    'behaviour',
                    'status',
                    'case_remark',
                    'bzn_target_id',
                    'id'
                ])
                ->with('bznCareTarget')
                ->orderBy('assessment_date', 'desc')
                ->get()
                ->toArray();
        for ($i = 0; $i < count($plans); $i++) {
            $plans[$i]['uid'] = null;
            $plans[$i]['domain'] = 
                ($plans[$i]['domain'] && $plans[$i]['domain'] != 0 && $plans[$i]['domain'] <= count($domainOptions))
                ? $domainOptions[$plans[$i]['domain'] - 1]
                : null;
            $plans[$i]['category'] = 
                ($plans[$i]['category'] && $plans[$i]['category'] != 0 && $plans[$i]['category'] <= count($categoryOptions))
                ? $categoryOptions[$plans[$i]['category'] - 1]
                : null;
            $plans[$i]['urgency'] = 
                ($plans[$i]['urgency'] && $plans[$i]['urgency'] != 0 && $plans[$i]['urgency'] <= count($urgencyOptions))
                ? $urgencyOptions[$plans[$i]['urgency'] - 1]
                : null;
            $plans[$i]['modifier'] = 
                ($plans[$i]['modifier'] && $plans[$i]['modifier'] != 0 && $plans[$i]['modifier'] <= count($modifierOptions))
                ? $modifierOptions[$plans[$i]['modifier'] - 1]
                : null;
            $plans[$i]['priority'] = 
                ($plans[$i]['priority'] && $plans[$i]['priority'] != 0 && $plans[$i]['priority'] <= count($priorityOptions))
                ? $priorityOptions[$plans[$i]['priority'] - 1]
                : null;
            if($plans[$i]['bzn_care_target']){
                if($plans[$i]['bzn_care_target']['care_plan']){
                    $case_id = $plans[$i]['bzn_care_target']['care_plan']['case_id'];
                    $plans[$i]['uid'] = $uidSet ? (
                        isset($uidSet[$case_id]) ? $uidSet[$case_id]['uid'] : null
                    ) : null;
                }
            } else {
                $plans[$i]['uid'] = null;
            }
        };
        return Excel::download(new PlanExport(collect($plans)), 'plans.csv', \Maatwebsite\Excel\Excel::CSV);
    }



    /**
     * @OA\Get(
     *     path="/assessments-api/v1/omaha-vital-export",
     *     tags={"BznConsultationNotes"},
     *     summary="export omaha vital sign to csv",
     *     operationId="exportOmahaVitalSigns",
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

    public function exportVitalSigns(Request $request)
    {
        $userSet = $this->externalService->getUsersSet($request->bearerToken());
        $uidSet = $this->externalService->getUidSet($request->bearerToken());

        $vitalSigns = BznConsultationNotes::select([
                    'assessment_date',
                    'assessment_time',
                    'assessor',
                    'meeting',
                    'sbp',
                    'dbp',
                    'pulse',
                    'pao',
                    'hstix',
                    'visiting_duration',
                    'case_remark',
                    'bzn_target_id',
                    'id',
                ])
                ->with('bznCareTarget')
                ->orderBy('assessment_date', 'desc')
                ->get()
                ->toArray();

        for($i = 0; $i < count($vitalSigns); $i++){
            $vitalSigns[$i]['uid'] = null;
            if($vitalSigns[$i]['assessor']){
                $vitalSigns[$i]['assessor'] = $userSet ? (
                    isset($userSet[$vitalSigns[$i]['assessor']]) ? $userSet[$vitalSigns[$i]['assessor']]['name'] : null
                ) : null;
            } else {
                $vitalSigns[$i]['assessor'] = null;
            }
            if($vitalSigns[$i]['bzn_care_target']){
                if($vitalSigns[$i]['bzn_care_target']['care_plan']){
                    $case_id = $vitalSigns[$i]['bzn_care_target']['care_plan']['case_id'];
                    $vitalSigns[$i]['uid'] = $uidSet ? (
                        isset($uidSet[$case_id]) ? $uidSet[$case_id]['uid'] : null
                    ) : null;
                }
            } else {
                $vitalSigns[$i]['uid'] = null;
            }
        }
        return Excel::download(new VitalSignExport(collect($vitalSigns)), 'vital-signs.csv', \Maatwebsite\Excel\Excel::CSV);
    }
}
