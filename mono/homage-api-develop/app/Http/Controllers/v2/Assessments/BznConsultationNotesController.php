<?php

namespace App\Http\Controllers\v2\Assessments;

use App\Exports\v2\Assessments\PlanExport;
use App\Exports\v2\Assessments\VitalSignExport;
use App\Http\Requests\v2\Assessments\BznConsultationNotesIndexRequest;
use App\Http\Requests\v2\Assessments\BznConsultationNotesStoreRequest;
use App\Http\Services\v2\Assessments\ConsultationFileService;
use App\Http\Services\v2\Assessments\WiringServiceAssessment;
use App\Models\v2\Assessments\BznCareTarget;
use App\Models\v2\Assessments\BznConsultationNotes;
use App\Models\v2\Assessments\CarePlan;
use App\Traits\RespondsWithHttpStatus;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Excel as MaatExcel;
use Maatwebsite\Excel\Facades\Excel;

class BznConsultationNotesController extends Controller
{
    use RespondsWithHttpStatus;

    private $fileService;
    private $wiringService;

    public function __construct()
    {
        $this->fileService = new ConsultationFileService;
        $this->wiringService = new WiringServiceAssessment;
    }

    public function index(BznConsultationNotesIndexRequest $request)
    {
        if (
            $request->access_role !== 'admin'
        ) {
            return response()->json([
                'data' => null,
                'status' => [
                    'code' => 401,
                    'message' => 'User not in BZN team access',
                    'errors' => [],
                ],
            ], 401);
        }
        $request->validate([
            'bzn_target_id' => 'required|integer|exists:bzn_care_targets,id,deleted_at,NULL',
        ]);

        $bzn_target_id = $request->query('bzn_target_id');

        $results = BznConsultationNotes::where('bzn_target_id', $bzn_target_id)->with(['bznConsultationAttachment', 'bznConsultationSign']);
        if (! $results) {
            return response()->json(['data' => []], 404);
        }
        $data = $results->orderBy('updated_at', 'asc')->get();
        if ($request->query('from') && $request->query('to')) {
            $from = $request->query('from');
            $to = $request->query('to');
            $data = $results->whereBetween('assessment_date', [$from, $to])->orderBy('updated_at', 'asc')->get();
        }

        return response()->json(['data' => $data], 200);
    }

    public function store(BznConsultationNotesStoreRequest $request)
    {
        $bzn_target = BznCareTarget::where('id', $request->bzn_target_id)->first();
        if (! $bzn_target) {
            return response()->json([
                'data' => null,
                'status' => [
                    'code' => 401,
                    'message' => 'Unauthorized, you are not the case manager.',
                    'errors' => [],
                ],
            ], 401);
        }
        $care_plan = CarePlan::where('id', $bzn_target->care_plan_id)->with('caseManagers')->first();
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
            // !$request->is_bzn &&
            // $request->is_hcw
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
            'attachment_file.*' => 'max:12288',
        ]);

        $consultation = BznConsultationNotes::create($data);

        $files = $request->file('attachment_file');

        $status_attachment = 'failed';
        if ($request->hasFile('attachment_file')) {
            foreach ($files as $file) {
                $upload_attachment = $this->fileService->uploadBznAttachment($file, $consultation);
                $status_attachment = $upload_attachment;
            }
        }

        $status_sign = 'failed';
        if ($request->hasFile('signature_file')) {
            $upload_sign = $this->fileService->uploadBznSignature($request, $consultation);
            if ($upload_sign) {
                $status_sign = $upload_sign;
            }
        }
        $results = BznConsultationNotes::where('id', $consultation->id)->with(['bznConsultationAttachment', 'bznConsultationSign'])->first();

        // if($status_attachment == 'failed' || $status_sign == 'failed'){
        //     return response()->json(['data' => $results, 'message' => 'attachment ' . "$status_attachment" . ', ' . 'signature ' . "$status_sign"], 409);
        // }
        return response()->json(['data' => $results, 'status' => 'attachment ' . "{$status_attachment}" . ', ' . 'signature ' . "{$status_sign}"], 201);

    }

    public function show(BznConsultationNotes $bznConsultationNotes)
    {
        //
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'bzn_target_id' => 'required|integer|exists:bzn_care_targets,id,deleted_at,NULL',
        ]);

        $bzn_target = BznCareTarget::where('id', $request->bzn_target_id)->first();
        if (! $bzn_target) {
            return response()->json([
                'data' => null,
                'status' => [
                    'code' => 401,
                    'message' => 'Unauthorized, you are not the case manager.',
                    'errors' => [],
                ],
            ], 401);
        }
        $care_plan = CarePlan::where('id', $bzn_target->care_plan_id)->with('caseManagers')->first();
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
            // !$request->is_bzn &&
            // $request->is_hcw
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

        $results = BznConsultationNotes::updateOrCreate(
            ['id' => $id],
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

        if (! $results) {
            return $this->failure('Failed to update bzn consultation notes');
        }

        return response()->json(['data' => $results], 200);
    }

    public function destroy(Request $request, $id)
    {
        if (! $request->is_bzn && $request->access_role !== 'admin') {
            return response()->json([
                'data' => null,
                'status' => [
                    'code' => 401,
                    'message' => 'User not in BZN team access',
                    'errors' => [],
                ],
            ], 401);
        }
        $bznConsultationNotes = BznConsultationNotes::where('id', $id)->first();
        if (! $bznConsultationNotes) {
            return response()->json([
                'error' => [
                    'code' => 404,
                    'message' => "Cannot find Bzn Consultation Notes with id {$id}",
                    'success' => false,
                ],
            ], 404);
        }
        $bznConsultationNotes->delete();

        return response()->json([
            'data' => null,
            'message' => "Bzn Consultation Notes with id {$id} deleted successfully",
            'success' => true,
        ], 201);
    }

    public function exportPlans(Request $request)
    {
        $uidSet = $this->wiringService->getUidSet($request->bearerToken());
        $areaOptions = [
            'income' => 1,
            'sanitation' => 2,
            'residence' => 3,
            'neighborhood/workplace safety' => 4,
            'communication with community resources' => 5,
            'social contact' => 6,
            'role change' => 7,
            'interpersonal relationship' => 8,
            'spirituality' => 9,
            'grief' => 10,
            'mental health' => 11,
            'sexuality' => 12,
            'communication with community resources' => 13,
            'social contact' => 14,
            'role change' => 15,
            'interpersonal relationship' => 16,
            'spirituality' => 17,
            'grief' => 18,
            'mental health' => 19,
            'sexuality' => 20,
            'hearing' => 21,
            'vision' => 22,
            'speech and language' => 23,
            'oral health' => 24,
            'cognition' => 25,
            'pain' => 26,
            'consciousness' => 27,
            'skin' => 28,
            'neuro-musculo-skeletal function' => 29,
            'respiration' => 30,
            'circulation' => 31,
            'digestion-hydration' => 32,
            'bowel function' => 33,
            'urinary function' => 34,
            'reproductive function' => 35,
            'pregnancy' => 36,
            'postpartum' => 37,
            'communicable/infectious condition' => 38,
            'nutrition' => 39,
            'sleep and rest patterns' => 40,
            'physical activity' => 41,
            'personal care' => 42,
            'substance use' => 43,
            'family planning' => 44,
            'health care supervision' => 45,
            'medication regimen' => 46,
            'other' => 47,
        ];

        $targetOptions = [
            'anatomy / physiology' => 1,
            'anger management' => 2,
            'behavior modification' => 3,
            'bladder care' => 4,
            'bonding / attachment' => 5,
            'bowel care' => 6,
            'cardiac care' => 7,
            'caretaking / parenting skills' => 8,
            'cast care' => 9,
            'communication' => 10,
            'community outreach worker services' => 11,
            'continuity of care' => 12,
            'coping skills' => 13,
            'day care/respite' => 14,
            'dietary management' => 15,
            'discipline' => 16,
            'dressing change / wound care' => 17,
            'durable medical equipment' => 18,
            'education' => 19,
            'employment' => 20,
            'end-of-life care' => 21,
            'environment' => 22,
            'exercises' => 23,
            'family planning care' => 24,
            'feeding procedures' => 25,
            'finances' => 26,
            'gait training' => 27,
            'genetics' => 28,
            'growth / development care' => 29,
            'home' => 30,
            'homemaking / housekeeping' => 31,
            'infection precautions' => 32,
            'interaction' => 33,
            'interpreter / translator services' => 34,
            'laboratory findings' => 35,
            'legal system' => 36,
            'medical / dental care' => 37,
            'medication action / side effects' => 38,
            'medication administration' => 39,
            'medication coordination / ordering' => 40,
            'medication prescription' => 41,
            'medication set-up' => 42,
            'mobility / transfers' => 43,
            'nursing care' => 44,
            'nutritionist care' => 45,
            'occupational therapy care' => 46,
            'ostomy care' => 47,
            'other community resources' => 48,
            'paraprofessional/aide care' => 49,
            'personal hygiene' => 50,
            'physical therapy care' => 51,
            'positioning' => 52,
            'recreational therapy care' => 53,
            'relaxation / breathing techniques' => 54,
            'respiratory care' => 55,
            'respiratory therapy care' => 56,
            'rest / sleep' => 57,
            'safety' => 58,
            'screening procedures' => 59,
            'sickness / injury care' => 60,
            'signs / symptoms-mental / emotional' => 61,
            'signs / symptoms-physical' => 62,
            'skin care' => 63,
            'social work / counseling care' => 64,
            'specimen collection' => 65,
            'speech and language pathology care' => 66,
            'spiritual care' => 67,
            'stimulation / nurturance' => 68,
            'stress management' => 69,
            'substance use cessation' => 70,
            'supplies' => 71,
            'support group' => 72,
            'support system' => 73,
            'transportation' => 74,
            'wellness' => 75,
            'other' => 76,
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
            'id',
        ])
            ->with('bznCareTarget');

        if ($request->query('from') && $request->query('to')) {
            $from = Carbon::parse($request->query('from'))->startOfDay();
            $to = Carbon::parse($request->query('to'))->endOfDay();
            $plans = $plans->whereBetween('assessment_date', [$from, $to]);
        }

        $plans = $plans->orderBy('assessment_date', 'desc')
            ->get()
            ->toArray();

        for ($i = 0; $i < count($plans); $i++) {
            $plans[$i]['uid'] = null;
            $area = strtolower($plans[$i]['area']);
            $target = strtolower($plans[$i]['target']);
            $plans[$i]['area'] = $areaOptions[$area] ?? $areaOptions['other'];
            $plans[$i]['target'] = $targetOptions[$target] ?? $targetOptions['other'];
            if ($plans[$i]['bzn_care_target']) {
                if ($plans[$i]['bzn_care_target']['care_plan']) {
                    $case_id = $plans[$i]['bzn_care_target']['care_plan']['case_id'];
                    $plans[$i]['uid'] = $uidSet ? (
                        isset($uidSet[$case_id]) ? $uidSet[$case_id]['uid'] : null
                    ) : null;
                }
            } else {
                $plans[$i]['uid'] = null;
            }
        }

        return Excel::download(new PlanExport(collect($plans)), 'plans.csv', MaatExcel::CSV);
    }

    public function exportVitalSigns(Request $request)
    {
        $userSet = $this->wiringService->getUsersSet();
        $uidSet = $this->wiringService->getUidSet();

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
            ->with('bznCareTarget');

        if ($request->query('from') && $request->query('to')) {
            $from = Carbon::parse($request->query('from'))->startOfDay();
            $to = Carbon::parse($request->query('to'))->endOfDay();
            $vitalSigns = $vitalSigns->whereBetween('assessment_date', [$from, $to]);
        }

        $vitalSigns = $vitalSigns->orderBy('assessment_date', 'desc')
            ->get()
            ->toArray();

        for ($i = 0; $i < count($vitalSigns); $i++) {
            $vitalSigns[$i]['uid'] = null;
            if ($vitalSigns[$i]['assessor']) {
                $vitalSigns[$i]['assessor'] = $userSet ? (
                    isset($userSet[$vitalSigns[$i]['assessor']]) ? $userSet[$vitalSigns[$i]['assessor']]['name'] : null
                ) : null;
            } else {
                $vitalSigns[$i]['assessor'] = null;
            }
            if ($vitalSigns[$i]['bzn_care_target']) {
                if ($vitalSigns[$i]['bzn_care_target']['care_plan']) {
                    $case_id = $vitalSigns[$i]['bzn_care_target']['care_plan']['case_id'];
                    $vitalSigns[$i]['uid'] = $uidSet ? (
                        isset($uidSet[$case_id]) ? $uidSet[$case_id]['uid'] : null
                    ) : null;
                }
            } else {
                $vitalSigns[$i]['uid'] = null;
            }
        }

        return Excel::download(new VitalSignExport(collect($vitalSigns)), 'vital-signs.csv', MaatExcel::CSV);
    }
}
