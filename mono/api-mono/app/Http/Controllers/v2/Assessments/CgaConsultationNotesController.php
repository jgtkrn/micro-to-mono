<?php

namespace App\Http\Controllers\v2\Assessments;

use App\Exports\v2\Assessments\HealthCoachingSessionExport;
use App\Http\Requests\v2\Assessments\CgaConsultationNotesIndexRequest;
use App\Http\Requests\v2\Assessments\CgaConsultationNotesStoreRequest;
use App\Http\Services\v2\Assessments\ConsultationFileService;
use App\Http\Services\v2\Assessments\WiringServiceAssessment;
use App\Models\v2\Assessments\CarePlan;
use App\Models\v2\Assessments\CgaCareTarget;
use App\Models\v2\Assessments\CgaConsultationNotes;
use App\Traits\RespondsWithHttpStatus;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class CgaConsultationNotesController extends Controller
{
    use RespondsWithHttpStatus;

    private $fileService;
    private $wiringService;

    public function __construct()
    {
        $this->wiringService = new WiringServiceAssessment;
        $this->fileService = new ConsultationFileService;
    }

    public function index(CgaConsultationNotesIndexRequest $request)
    {
        $request->validate([
            'cga_target_id' => 'required|integer|exists:cga_care_targets,id,deleted_at,NULL',
        ]);

        $cga_target_id = $request->query('cga_target_id');

        $results = CgaConsultationNotes::where('cga_target_id', $cga_target_id)->with(['cgaConsultationAttachment', 'cgaConsultationSign']);
        if (! $results) {
            return response()->json(['data' => []], 404);
        }
        $data = $results->orderBy('updated_at', 'desc')->get();
        if ($request->query('from') && $request->query('to')) {
            $from = $request->query('from');
            $to = $request->query('to');
            $data = $results->whereBetween('assessment_date', [$from, $to])->orderBy('updated_at', 'desc')->get();
        }

        return response()->json(['data' => $data], 200);
    }

    public function store(CgaConsultationNotesStoreRequest $request)
    {
        $cga_target = CgaCareTarget::where('id', $request->cga_target_id)->first();
        if (! $cga_target) {
            return response()->json([
                'data' => null,
                'status' => [
                    'code' => 401,
                    'message' => 'Unauthorized, you are not the case manager.',
                    'errors' => [],
                ],
            ], 401);
        }
        $care_plan = CarePlan::where('id', $cga_target->care_plan_id)->first();
        if (! $care_plan) {
            return response()->json([
                'data' => null,
                'status' => [
                    'code' => 401,
                    'message' => 'Unauthorized, you are not the case manager.',
                    'errors' => [],
                ],
            ], 401);
        }
        $managers = count($care_plan->caseManagers) == 0 ? [] : $care_plan->caseManagers->pluck('manager_id')->toArray();
        if (
            $care_plan->manager_id !== $request->user_id &&
            ! in_array($request->user_id, $managers) &&
            $request->access_role !== 'admin'
            // &&
            // !$request->is_cga
        ) {
            return response()->json([
                'data' => null,
                'status' => [
                    'code' => 401,
                    'message' => 'Unauthorized, you are not the case manager.',
                    'errors' => [],
                ],
            ], 401);
        }

        if ($request->is_other && $request->access_role !== 'admin') {
            return response()->json([
                'data' => null,
                'status' => [
                    'code' => 401,
                    'message' => 'User not in any team access',
                    'errors' => [],
                ],
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
            'attachment_file.*' => 'max:12288',
        ]);

        $consultation = cgaConsultationNotes::create($data);

        $files = $request->file('attachment_file');

        $status_attachment = 'failed';
        if ($request->hasFile('attachment_file')) {
            foreach ($files as $file) {
                $upload_attachment = $this->fileService->uploadCgaAttachment($file, $consultation);
                $status_attachment = $upload_attachment;
            }
        }

        $status_sign = 'failed';
        if ($request->hasFile('signature_file')) {
            $upload_sign = $this->fileService->uploadCgaSignature($request, $consultation);
            if ($upload_sign) {
                $status_sign = $upload_sign;
            }
        }
        $results = cgaConsultationNotes::where('id', $consultation->id)->with(['cgaConsultationAttachment', 'cgaConsultationSign'])->first();

        // if($status_attachment == 'failed' || $status_sign == 'failed'){
        //     return response()->json(['data' => $results, 'message' => 'attachment ' . "$status_attachment" . ', ' . 'signature ' . "$status_sign"], 409);
        // }
        return response()->json(['data' => $results, 'message' => 'attachment ' . "{$status_attachment}" . ', ' . 'signature ' . "{$status_sign}"], 201);

    }

    public function show(CgaConsultationNotes $cgaConsultationNotes)
    {
        //
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'cga_target_id' => 'required|integer|exists:cga_care_targets,id,deleted_at,NULL',
        ]);

        $cga_target = CgaCareTarget::where('id', $request->cga_target_id)->first();
        if (! $cga_target) {
            return response()->json([
                'data' => null,
                'status' => [
                    'code' => 401,
                    'message' => 'Unauthorized, you are not the case manager.',
                    'errors' => [],
                ],
            ], 401);
        }
        $care_plan = CarePlan::where('id', $cga_target->care_plan_id)->first();
        if (! $care_plan) {
            return response()->json([
                'data' => null,
                'status' => [
                    'code' => 401,
                    'message' => 'Unauthorized, you are not the case manager.',
                    'errors' => [],
                ],
            ], 401);
        }
        $managers = count($care_plan->caseManagers) == 0 ? [] : $care_plan->caseManagers->pluck('manager_id')->toArray();
        if (
            $care_plan->manager_id !== $request->user_id &&
            ! in_array($request->user_id, $managers) &&
            $request->access_role !== 'admin'
            // &&
            // !$request->is_cga &&
            // $request->hcw
        ) {
            return response()->json([
                'data' => null,
                'status' => [
                    'code' => 401,
                    'message' => 'Unauthorized, you are not the case manager.',
                    'errors' => [],
                ],
            ], 401);
        }

        if ($request->is_other && $request->access_role !== 'admin') {
            return response()->json([
                'data' => null,
                'status' => [
                    'code' => 401,
                    'message' => 'User not in any team access',
                    'errors' => [],
                ],
            ], 401);
        }

        $results = CgaConsultationNotes::updateOrCreate(
            ['id' => $id],
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

        if (! $results) {
            return $this->failure('Failed to update cga consultation notes');
        }

        return response()->json(['data' => $results], 200);
    }

    public function destroy(Request $request, $id)
    {
        if (! $request->is_cga && $request->access_role !== 'admin') {
            return response()->json([
                'data' => null,
                'status' => [
                    'code' => 401,
                    'message' => 'User not in CGA team access',
                    'errors' => [],
                ],
            ], 401);
        }
        $cgaConsultationNotes = CgaConsultationNotes::where('id', $id)->first();
        if (! $cgaConsultationNotes) {
            return response()->json([
                'error' => [
                    'code' => 404,
                    'message' => "Cannot find Cga Consultation Notes with id {$id}",
                    'success' => false,
                ],
            ], 404);
        }
        $cgaConsultationNotes->delete();

        return response()->json([
            'data' => null,
            'message' => "Cga Consultation Notes with id {$id} deleted successfully",
            'success' => true,
        ], 201);
    }

    public function exportHCS(Request $request)
    {
        $data = CgaConsultationNotes::select(['cga_target_id', 'assessor_1',
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
            ->with('carePlan');
        if ($request->query('from') && $request->query('to')) {
            $from = Carbon::parse($request->query('from'))->startOfDay();
            $to = Carbon::parse($request->query('to'))->endOfDay();
            $data = $data->whereBetween('assessment_date', [$from, $to]);
        }
        $data = $data->get();
        $uid = $this->wiringService->getUidSetByCasesId();
        for ($i = 0; $i < count($data); $i++) {

            $caseId = strval($data[$i]->carePlan?->carePlan?->case_id);
            if ($caseId !== null) {
                $data[$i]['uid'] = isset($uid[$caseId]) ? $uid[$caseId]['uid'] : null;
            } else {
                $data[$i]['uid'] = null;
            }
        }

        return Excel::download(new HealthCoachingSessionExport($data), 'health-coaching-session.csv');
    }
}
