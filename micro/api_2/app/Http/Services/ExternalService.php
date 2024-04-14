<?php

namespace App\Http\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\CarePlan;
use App\Models\CgaCareTarget;
use App\Models\BznCareTarget;
use App\Models\AssessmentCase;
use App\Models\SocialBackgroundForm;
use App\Models\SocialWorkerForm;
use App\Models\FunctionMobilityForm;
use App\Models\QualtricsForm;
use App\Models\MedicalConditionForm;
use Carbon\Carbon;

class ExternalService
{

    public function isElderCasesIdExists($caseId)
    {
        $response = Http::acceptJson()->get(env('ELDER_SERVICE_API_URL') . '/is-cases-id-exists?id=' . $caseId);
        $elderCasesIdExists = $response->collect('data')['status'];
        return $elderCasesIdExists;
    }

    public function getElderCaseId($caseId)
    {
        $response = Http::acceptJson()->get(env('ELDER_SERVICE_API_URL') . '/cases' . '/' . $caseId  . "?exclude=true");

        if ($response->status() == 404) {
            return null;
        }

        $case_type = $response->collect('data')['case_data']['user_type'];
        return $case_type;
    }

    public function getStaffReports($request)
    {
        // staff data
        $sort_by = '';
        $sort_dir = '';
        if ($request->query('sort_by') && $request->query('sort_dir')) {
            $sort_by = $request->query('sort_by');
            $sort_dir = strtolower($request->query('sort_dir')) == 'asc' ? 'asc' : 'desc';
        }

        $staff = Http::acceptJson()->withToken($request->bearerToken())->get(
            env('USER_SERVICE_API_URL')
                . "/users?per_page="
                . "{$request->query('per_page')}"
                . "&page="
                . "{$request->query('page')}"
                . "&sort_by="
                . "{$sort_by}"
                . "&sort_dir="
                . "{$sort_dir}"
        );

        $staff_data = $staff->collect('data');
        $user_ids = implode(',', $staff_data->pluck('id')->toArray());
        $user_names = implode(',', $staff_data->pluck('name')->toArray());

        // calls log
        $calls = Http::acceptJson()->get(
            env('ELDER_SERVICE_API_URL')
                . "/calls?by_name=$user_names"
        );
        $calls_log = $calls->collect('data');

        // appointment data
        $appointments = Http::acceptJson()->withToken($request->bearerToken())->get(
            env('APPOINTMENT_SERVICE_API_URL')
                . "/appointments" . "?user_ids=$user_ids"
        );
        $appointments_data = $appointments->collect('data');

        $results = array(array());
        for ($i = 0; $i < count($staff_data); $i++) {
            $results[$i]['staff_name'] = $staff_data[$i]['name'];
            $results[$i]['teams'] = array_column($staff_data[$i]['teams'], 'name');

            // appointment count
            $appointment_count = 0;
            $followup_count = 0;
            $meeting_count = 0;
            for ($a = 0; $a < count($appointments_data); $a++) {
                if (in_array($staff_data[$i]['id'], $appointments_data[$a]['user_ids'], true)) {
                    $appointment_count++;
                    if ($appointments_data[$a]['category_id'] == 4) {
                        $followup_count++;
                    }
                    if ($appointments_data[$a]['category_id'] == 3) {
                        $meeting_count++;
                    }
                }
            }
            $results[$i]['appointment'] = $appointment_count;
            $results[$i]['followup'] = $followup_count;
            $results[$i]['meeting'] = $meeting_count;

            // call log count
            $calls_count = 0;
            for ($j = 0; $j < count($calls_log); $j++) {
                if ($calls_log[$j]['updated_by_name'] == $staff_data[$i]['name']) {
                    $calls_count++;
                }
            }
            $results[$i]['calls_log'] = $calls_count;

            // care plan count
            $patient_care = CarePlan::where('case_manager', $staff_data[$i]['name'])->get()->count();
            $results[$i]['patient_care'] = $patient_care == 0 ? 0 : $patient_care;

            $results[$i]['admin'] = null;
        }

        if (!$staff_data || count($staff_data) == 0) {
            return response()->json($results, 404);
        }

        return response()->json($results, 200);
    }

    public function getPatientReports($request)
    {
        // appointment list
        $patient = Http::acceptJson()->withToken($request->bearerToken())->get(
            env('APPOINTMENT_SERVICE_API_URL')
                . "/appointments?"
                . "from="
                . "{$request->query('from')}"
                . "&to="
                . "{$request->query('to')}"
        );
        $patient_data = $patient->collect('data');
        $patient_uids = $patient_data->pluck('elder')->toArray();
        $uids = implode(',', collect(array_filter($patient_uids))->pluck('uid')->toArray());
        $visit = Http::acceptJson()->withToken($request->bearerToken())->get(env('APPOINTMENT_SERVICE_API_URL') . "/appointments");
        $visit_data = $visit->collect('data');
        // cases data
        $elder = Http::acceptJson()->withToken($request->bearerToken())->get(env('ELDER_SERVICE_API_URL') . "/cases" . "?exclude=true" . "elder_uids=$uids");
        $elder_data = $elder->collect('data');


        // separate appointment based on elder
        $results = array();
        $s = 0;
        for ($i = 0; $i < count($patient_data); $i++) {
            if ($patient_data[$i]['elder'] !== null) {
                $results[$s]['patient_name'] = $patient_data[$i]['elder']['name'];
                $results[$s]['uid'] = $patient_data[$i]['elder']['uid'];
                $results[$s]['last_visit'] = date("Y-m-d", strtotime($patient_data[$i]['end']));
                $results[$s]['tele_visit'] = 0;
                $results[$s]['face_visit'] = 0;
                $results[$s]['case_manager'] = null;
                $results[$s]['case_status'] = null;
                $results[$s]['patient_care_hour'] = null;
                $results[$s]['patient_bzn_notes_id'] = [];
                $results[$s]['patient_cga_notes_id'] = [];

                $hour = 0;
                for ($j = 0; $j < count($visit_data); $j++) {
                    if ($visit_data[$j]['elder'] !== null) {
                        if ($patient_data[$i]['elder']['uid'] == $visit_data[$j]['elder']['uid']) {
                            if ($patient_data[$i]['category_id'] == 2) {
                                $results[$s]['face_visit']++;
                            }
                            if ($patient_data[$i]['category_id'] == 4) {
                                $results[$s]['tele_visit']++;
                            }
                            if ($visit_data[$j]['end'] > $patient_data[$i]['end']) {
                                $results[$s]['last_visit'] = date("Y-m-d", strtotime($visit_data[$j]['end']));
                            }
                            if ($visit_data[$j]['start'] && $visit_data[$j]['end']) {
                                $start_data = $visit_data[$j]['start'];
                                $end_data = $visit_data[$j]['end'];
                                $full_time = (strtotime($end_data) - strtotime($start_data)) / 3600;
                                $hour += floor($full_time);
                                $results[$s]['patient_care_hour'] = "{$hour} Hours";
                            }
                        }
                    }
                }

                // case data
                $case_data = CarePlan::where('case_id', $patient_data[$i]['case_id'])->first();
                if ($case_data) {
                    $results[$s]['case_manager'] = $case_data->case_manager;
                    $target_care = null;
                    if ($patient_data[$i]['elder']['case_type'] == 'CGA') {
                        $cga_target_care = CgaCareTarget::where('care_plan_id', $case_data->id)->get()->toArray();
                        if ($cga_target_care) {
                            $results[$s]['patient_cga_notes_id'] = array_column($cga_target_care, 'id');
                        }
                    }
                    if ($patient_data[$i]['elder']['case_type'] == 'BZN') {
                        $bzn_target_care = BznCareTarget::where('care_plan_id', $case_data->id)->get()->toArray();
                        if ($bzn_target_care) {
                            $results[$s]['patient_bzn_notes_id'] = array_column($bzn_target_care, 'id');
                        }
                    }
                }
                if (count($elder_data) > 0) {
                    for ($k = 0; $k < count($elder_data); $k++) {
                        if ($elder_data[$k]['elder_uid'] == $patient_data[$i]['elder']['uid'] && $elder_data[$k]['id'] == $patient_data[$i]['case_id']) {
                            $results[$s]['case_status'] = $elder_data[$k]['case_status'];
                        }
                    }
                }

                $s++;
            }
        }

        return response()->json($results, 200);
    }

    public function getInsightReports($request)
    {
        $cases_id = $request->query('case_id');
        if ($cases_id) {
            $cases = Http::acceptJson()->withToken($request->bearerToken())->get(env('ELDER_SERVICE_API_URL') . "/cases" . "/{$cases_id}" . "?exclude=true");
            $cases_data = $cases->collect('data.case_data.elder');
            if (count($cases_data) > 0) {
                $birth_day = $cases_data['birth_day'];
                $birth_month = $cases_data['birth_month'];
                $birth_year = $cases_data['birth_year'];
                $data_insight = [
                    'assessment_date' => null,
                    'elder_name' => $cases_data['name'],
                    'gender' => $cases_data['gender'],
                    'living_status' => [],
                    'living_status_text' => null,
                    'marital_status' => null,
                    'uid' => $cases_data['uid'],
                    'first_assessor' => null,
                    'reffered_center' => $cases_data['source_of_referral'],
                    'date_of_birth' => "{$birth_year}-{$birth_month}-{$birth_day}",
                    'residence_status' => null,
                    'date_of_record' => null,
                    'connected_uid' => $cases_data['uid_connected_with'],
                    'relation' => $cases_data['relationship'],
                    // BZN
                    'fallrisk_fa' => null,
                    'fallrisk_rs' => null,
                    'hosp_fa' => null,
                    'hosp_rs' => null,
                    'remark_fa' => null,
                    'remark_rs' => null,
                    // CGA
                    'social_fa' => null,
                    'social_rs' => null,
                    'stratification_fa' => null,
                    'stratification_rs' => null,
                    'psycho_fa' => null,
                    'psycho_rs' => null,
                    'cognitive_fa' => null,
                    'cognitive_rs' => null,
                    'priority_level' => null,
                    'sw_remark' => null,
                    'referral_other_text' => null,
                    'refferals' => []
                    // 'bzn_qualtrics' => null,
                    // 'cga_qualtrics' => null,
                    // 'assessment_cases' => null,
                ];
                $assessment_case = AssessmentCase::where('case_id', $cases_id)->with([
                    'socialBackgroundForm',
                    'qualtricsForm',
                    'socialWorkerForm'
                ])->first();
                if ($assessment_case) {
                    // $data_insight['assessment_cases'] = $assessment_case['id'];
                    $data_insight['assessment_date'] = $assessment_case['start_time'];
                    $data_insight['first_assessor'] = $assessment_case['first_assessor'];
                    $data_insight['date_of_record'] = $assessment_case['end_date'];
                    $data_insight['priority_level'] = $assessment_case['priority_level'];
                    $assessment_id = $assessment_case['id'];

                    // find data
                    $bzn_qualtrics = QualtricsForm::where('assessment_case_id', $assessment_case->id)->first();
                    if ($bzn_qualtrics) {
                        // $data_insight['bzn_qualtrics'] = $bzn_qualtrics;
                        $data_insight['fallrisk_fa'] = $bzn_qualtrics['fallrisk_fa'];
                        $data_insight['fallrisk_rs'] = $bzn_qualtrics['fallrisk_rs'];
                        $data_insight['hosp_fa'] = $bzn_qualtrics['hosp_fa'];
                        $data_insight['hosp_rs'] = $bzn_qualtrics['hosp_rs'];
                        $data_insight['remark_fa'] = $bzn_qualtrics['remark_fa'];
                        $data_insight['remark_rs'] = $bzn_qualtrics['remark_rs'];
                    }


                    $cga_qualtrics = SocialWorkerForm::where('assessment_case_id', $assessment_case->id)->first();

                    if ($cga_qualtrics) {
                        // $data_insight['cga_qualtrics'] = $cga_qualtrics;
                        $data_insight['marital_status'] = $cga_qualtrics['elder_marital'];
                        $data_insight['living_status'] = $cga_qualtrics['elderLiving'];
                        $data_insight['living_status_text'] = $cga_qualtrics['elder_living_text'];
                        $data_insight['social_fa'] = $cga_qualtrics['social_fa'];
                        $data_insight['social_rs'] = $cga_qualtrics['social_rs'];
                        $data_insight['stratification_fa'] = $cga_qualtrics['stratification_fa'];
                        $data_insight['stratification_rs'] = $cga_qualtrics['stratification_rs'];
                        $data_insight['psycho_fa'] = $cga_qualtrics['psycho_fa'];
                        $data_insight['psycho_rs'] = $cga_qualtrics['psycho_rs'];
                        $data_insight['cognitive_fa'] = $cga_qualtrics['cognitive_fa'];
                        $data_insight['cognitive_rs'] = $cga_qualtrics['cognitive_rs'];
                        $data_insight['sw_remark'] = $cga_qualtrics['sw_remark'];
                        $data_insight['referral_other_text'] = $cga_qualtrics['referral_other_text'];
                        $data_insight['refferals'] = $cga_qualtrics['doReferral'];
                    }
                }

                return response()->json([
                    'data' => $data_insight,
                ], 200);
            }
        }

        // take assessment case id
        // get all form by assessment case id

        return response()->json([
            'data' => null,
            'message' => 'Data not Found'
        ], 404);
    }

    public function getCgaHcData($case_id)
    {
        $assessment_case = AssessmentCase::where('case_id', $case_id)->latest('updated_at')->first();
        $data = null;
        if (!$assessment_case) {
            return $data;
        }
        $social_worker = SocialWorkerForm::where('assessment_case_id', $assessment_case->id)->latest('updated_at')->first();
        $qualtrics = QualtricsForm::where('assessment_case_id', $assessment_case->id)->latest('updated_at')->first();
        $iadl_score = !$social_worker ? 0 : ($social_worker->iadl_total_score ? $social_worker->iadl_total_score : 0);
        $lubben_score = !$social_worker ? 0 : ($social_worker->lubben_total_score ? $social_worker->lubben_total_score : 0);
        $gds4_score = !$social_worker ? 0 : ($social_worker->gds4_score ? $social_worker->gds4_score : 0);
        $data = [
            'cga_date' => !$assessment_case ? null : ($assessment_case->assessment_date ? $assessment_case->assessment_date : null),
            'priority_level' => !$assessment_case ? null : ($assessment_case->priority_level ? $assessment_case->priority_level : null),
            'financial_status' => !$social_worker ? null : ($social_worker->lifeSupport ? $social_worker->lifeSupport : null),
            'living_status' => !$social_worker ? null : ($social_worker->elderLiving ? $social_worker->elderLiving : null),
            'social_service' => !$social_worker ? null : [
                'elderly_center' => !$social_worker->elderly_center ? null : $social_worker->elderly_center,
                'home_service' => !$social_worker->homeService ? null : $social_worker->homeService,
                'elderly_daycare' => !$social_worker->elderly_daycare ? null : $social_worker->elderly_daycare,
            ],
            'marital_status' => !$social_worker ? null : ($social_worker->elder_marital ? $social_worker->elder_marital : null),
            'abnormal_vital_sign' => [
                'follow_up_action' => !$social_worker ? null : ($social_worker->cognitive_fa ? $social_worker->cognitive_fa : null),
                'remarks_and_supplementary' => !$social_worker ? null : ($social_worker->cognitive_rs ? $social_worker->cognitive_rs : null)
            ],
            'medical_history' => [
                'follow_up_action' => !$qualtrics ? null : ($qualtrics->fallrisk_fa ? $qualtrics->fallrisk_fa : null),
                'remarks_and_supplementary' => !$qualtrics ? null : ($qualtrics->fallrisk_rs ? $qualtrics->fallrisk_rs : null)
            ],
            'fall_risk' => [
                'follow_up_action' => !$qualtrics ? null : ($qualtrics->remark_fa ? $qualtrics->remark_fa : null),
                'remarks_and_supplementary' => !$qualtrics ? null : ($qualtrics->remark_rs ? $qualtrics->remark_rs : null)
            ],
            'cognitive' => [
                'follow_up_action' => !$social_worker ? null : ($social_worker->psycho_fa ? $social_worker->psycho_fa : null),
                'remarks_and_supplementary' => !$social_worker ? null : ($social_worker->psycho_rs ? $social_worker->psycho_rs : null)
            ],
            'emotional_status' => [
                'follow_up_action' => !$social_worker ? null : ($social_worker->stratification_fa ? $social_worker->stratification_fa : null),
                'remarks_and_supplementary' => !$social_worker ? null : ($social_worker->stratification_rs ? $social_worker->stratification_rs : null)
            ],
            'self_care_score' => $iadl_score,
            'living_environment' => [
                'follow_up_action' => !$social_worker ? null : ($social_worker->social_fa ? $social_worker->social_fa : null),
                'remarks_and_supplementary' => !$social_worker ? null : ($social_worker->social_rs ? $social_worker->social_rs : null)
            ],
            'remarks' => [
                'health_professional_remark' => !$qualtrics ? null : ($qualtrics->qualtrics_remarks ? $qualtrics->qualtrics_remarks : null),
                'social_worker_remark' => !$social_worker ? null : ($social_worker->sw_remark ? $social_worker->sw_remark : null)
            ]
        ];
        return $data;
    }

    public function getCgaNurseData($case_id)
    {
        $assessment_case = AssessmentCase::where('case_id', $case_id)->latest('updated_at')->first();
        $data = null;
        if (!$assessment_case) {
            return $data;
        }
        $medical_condition = MedicalConditionForm::where('assessment_case_id', $assessment_case->id)->latest('updated_at')->first();
        $social_worker = SocialWorkerForm::where('assessment_case_id', $assessment_case->id)->latest('updated_at')->first();
        $qualtrics = QualtricsForm::where('assessment_case_id', $assessment_case->id)->latest('updated_at')->first();
        $iadl_score = !$social_worker ? 0 : ($social_worker->iadl_total_score ? $social_worker->iadl_total_score : 0);
        $lubben_score = !$social_worker ? 0 : ($social_worker->lubben_total_score ? $social_worker->lubben_total_score : 0);
        $gds4_score = !$social_worker ? 0 : ($social_worker->gds4_score ? $social_worker->gds4_score : 0);
        $data = [
            'cga_date' => !$assessment_case ? null : ($assessment_case->assessment_date ? $assessment_case->assessment_date : null),
            'priority_level' => !$assessment_case ? null : ($assessment_case->priority_level ? $assessment_case->priority_level : null),
            'living_status' => !$social_worker ? null : ($social_worker->elderLiving ? $social_worker->elderLiving : null),
            'marital_status' => !$social_worker ? null : ($social_worker->elder_marital ? $social_worker->elder_marital : null),
            'abnormal_vital_sign' => [
                'follow_up_action' => !$social_worker ? null : ($social_worker->cognitive_fa ? $social_worker->cognitive_fa : null),
                'remarks_and_supplementary' => !$social_worker ? null : ($social_worker->cognitive_rs ? $social_worker->cognitive_rs : null)
            ],
            'food_allergy' => !$medical_condition ? null : ($medical_condition->food_allergy_description ? $medical_condition->food_allergy_description : null),
            'drug_allergy' => !$medical_condition ? null : ($medical_condition->drug_allergy_description ? $medical_condition->drug_allergy_description : null),
            'other_medical_history' => !$medical_condition ? null : ($medical_condition->medicalHistory ? $medical_condition->medicalHistory : null),
            'medical_history' => [
                'follow_up_action' => !$qualtrics ? null : ($qualtrics->fallrisk_fa ? $qualtrics->fallrisk_fa : null),
                'remarks_and_supplementary' => !$qualtrics ? null : ($qualtrics->fallrisk_rs ? $qualtrics->fallrisk_rs : null)
            ],
            'fall_risk' => [
                'follow_up_action' => !$qualtrics ? null : ($qualtrics->remark_fa ? $qualtrics->remark_fa : null),
                'remarks_and_supplementary' => !$qualtrics ? null : ($qualtrics->remark_rs ? $qualtrics->remark_rs : null)
            ],
            'cognitive' => [
                'follow_up_action' => !$social_worker ? null : ($social_worker->psycho_fa ? $social_worker->psycho_fa : null),
                'remarks_and_supplementary' => !$social_worker ? null : ($social_worker->psycho_rs ? $social_worker->psycho_rs : null)
            ],
            'emotional_status' => [
                'follow_up_action' => !$social_worker ? null : ($social_worker->stratification_fa ? $social_worker->stratification_fa : null),
                'remarks_and_supplementary' => !$social_worker ? null : ($social_worker->stratification_rs ? $social_worker->stratification_rs : null)
            ],
            'self_care_score' => $iadl_score,
            'living_environment' => [
                'follow_up_action' => !$social_worker ? null : ($social_worker->social_fa ? $social_worker->social_fa : null),
                'remarks_and_supplementary' => !$social_worker ? null : ($social_worker->social_rs ? $social_worker->social_rs : null)
            ],
            'alert' => [
                'health_professional_remark' => !$qualtrics ? null : ($qualtrics->qualtrics_remarks ? $qualtrics->qualtrics_remarks : null),
                'social_worker_remark' => !$social_worker ? null : ($social_worker->sw_remark ? $social_worker->sw_remark : null)
            ]
        ];
        return $data;
    }

    public function getBznData($case_id)
    {
        $assessment_case = AssessmentCase::where('case_id', $case_id)->latest('updated_at')->first();
        $data = null;
        if (!$assessment_case) {
            return $data;
        }
        $medical_condition = MedicalConditionForm::where('assessment_case_id', $assessment_case->id)->latest('updated_at')->first();
        $social_worker = SocialWorkerForm::where('assessment_case_id', $assessment_case->id)->latest('updated_at')->first();
        $qualtrics = QualtricsForm::where('assessment_case_id', $assessment_case->id)->latest('updated_at')->first();
        $social_background = SocialBackgroundForm::where('assessment_case_id', $assessment_case->id)->latest('updated_at')->first();
        $function_mobility = FunctionMobilityForm::where('assessment_case_id', $assessment_case->id)->latest('updated_at')->first();
        $data = [
            'health_issue' => !$medical_condition ? null : ($medical_condition->chiefComplaint ? $medical_condition->chiefComplaint : null),
            'food_allergy' => !$medical_condition ? null : ($medical_condition->food_allergy_description ? $medical_condition->food_allergy_description : null),
            'drug_allergy' => !$medical_condition ? null : ($medical_condition->drug_allergy_description ? $medical_condition->drug_allergy_description : null),
            'alert' => [
                'health_professional_remark' => !$qualtrics ? null : ($qualtrics->qualtrics_remarks ? $qualtrics->qualtrics_remarks : null),
                'social_worker_remark' => !$social_worker ? null : ($social_worker->sw_remark ? $social_worker->sw_remark : null)
            ],
            'medical_history' => !$medical_condition ? null : ($medical_condition->medicalHistory ? $medical_condition->medicalHistory : null),
            'living_status' => !$social_background ? null : ($social_background->livingStatusTable ? $social_background->livingStatusTable : null),
            'marital_status' => !$social_background ? null : ($social_background->marital_status ? $social_background->marital_status : null),
            'mobility' => !$function_mobility ? null : ($function_mobility->iadl ? $function_mobility->iadl : null)
        ];
        return $data;
    }

    public function getCgaBaseData($case_id)
    {
        $assessment_case = AssessmentCase::where('case_id', $case_id)->latest('updated_at')->first();
        return;
    }

    public function change_cga_type($case_id, $cga_type, $token)
    {
        $response = Http::acceptJson()->withToken($token)->put(
            env('ELDER_SERVICE_API_URL') . '/cases' . '/' . $case_id,
            [
                'case_number' => $case_id, 
                'cga_type' => $cga_type
            ]
        );

        return $response->status();
    }

    public function getUsersSet($token){
        try {
            $response = Http::connectTimeout(1)->acceptJson()->withToken($token)->get(env('USER_SERVICE_API_URL') . '/users-set');
            $result = $response->collect('data');
            if(!$result) {
                return null;
            }
            return $result;
        } catch (\Exception $e) {
            return null;
        }
    }

    public function getUidSet($token){
        try {
            $response = Http::connectTimeout(1)->acceptJson()->withToken($token)->get(env('ELDER_SERVICE_API_URL') . '/cases-uid-set');
            $result = $response->collect('data');
            if(!$result) {
                return null;
            }
            return $result;
        } catch (\Exception $e) {
            return null;
        }
    }

    public function getCasesStatus($token){
        try{
            $response = Http::connectTimeout(1)->acceptJson()->withToken($token)->get(env('ELDER_SERVICE_API_URL') . '/cases-status');
            $result = $response->collect('data');
            if(!$result) {
                return null;
            }
            return $result;
        }catch(\Exception $e){
            return null;
        }
    }

    public function getCasesData($token){
        try{
            $response = Http::connectTimeout(1)->acceptJson()->withToken($token)->get(env('ELDER_SERVICE_API_URL') . '/cases');
            $result = $response->collect('data');
            if(!$result) {
                return null;
            }
            return $result;
        }catch(\Exception $e){
            return null;
        }
    }

    public function getUidSetByCasesId($token){
        try{
            $response = Http::connectTimeout(1)->acceptJson()->withToken($token)->get(env('ELDER_SERVICE_API_URL') . '/uid-set-by-cases-id');
            $result = $response->collect('data');
            if(!$result) {
                return null;
            }
            return $result;
        }catch(\Exception $e){
            return null;
        }
    }
}
