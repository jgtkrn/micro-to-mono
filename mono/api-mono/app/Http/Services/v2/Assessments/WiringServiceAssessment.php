<?php

namespace App\Http\Services\v2\Assessments;

use App\Models\v2\Assessments\AssessmentCase;
use App\Models\v2\Assessments\FunctionMobilityForm;
use App\Models\v2\Assessments\MedicalConditionForm;
use App\Models\v2\Assessments\QualtricsForm;
use App\Models\v2\Assessments\SocialBackgroundForm;
use App\Models\v2\Assessments\SocialWorkerForm;
use App\Models\v2\Elders\Cases;
use App\Models\v2\Users\User;
use Exception;
use stdClass;

class WiringServiceAssessment
{
    public function getElderCaseId($caseId)
    {
        $response = Cases::where('id', $caseId)->with('elder')->first();
        if (! $response) {
            return null;
        }
        $case_type = $response->case_name;

        return $case_type;
    }

    public function getElderFromCase($caseId)
    {
        $response = Cases::where('id', $caseId)->with('elder')->first();
        if (! $response) {
            return null;
        }

        return $response;
    }

    public function getInsightReports($request)
    {
        $cases_id = $request->query('case_id');
        if ($cases_id) {
            $cases = $this->getElderFromCase($cases_id);
            $cases_data = $cases?->elder;
            if ($cases_data && count((array) $cases_data) > 0) {
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
                    'refferals' => [],
                    // 'bzn_qualtrics' => null,
                    // 'cga_qualtrics' => null,
                    // 'assessment_cases' => null,
                ];
                $assessment_case = AssessmentCase::where('case_id', $cases_id)->with([
                    'socialBackgroundForm',
                    'qualtricsForm',
                    'socialWorkerForm',
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
            'message' => 'Data not Found',
        ], 404);
    }

    public function getCgaHcData($case_id)
    {
        $assessment_case = AssessmentCase::where('case_id', $case_id)->latest('updated_at')->first();
        $data = null;
        if (! $assessment_case) {
            return $data;
        }
        $social_worker = SocialWorkerForm::where('assessment_case_id', $assessment_case->id)->latest('updated_at')->first();
        $qualtrics = QualtricsForm::where('assessment_case_id', $assessment_case->id)->latest('updated_at')->first();
        $iadl_score = ! $social_worker ? 0 : ($social_worker->iadl_total_score ? $social_worker->iadl_total_score : 0);
        $lubben_score = ! $social_worker ? 0 : ($social_worker->lubben_total_score ? $social_worker->lubben_total_score : 0);
        $gds4_score = ! $social_worker ? 0 : ($social_worker->gds4_score ? $social_worker->gds4_score : 0);
        $data = [
            'cga_date' => ! $assessment_case ? null : ($assessment_case->assessment_date ? $assessment_case->assessment_date : null),
            'priority_level' => ! $assessment_case ? null : ($assessment_case->priority_level ? $assessment_case->priority_level : null),
            'financial_status' => ! $social_worker ? null : ($social_worker->lifeSupport ? $social_worker->lifeSupport : null),
            'living_status' => ! $social_worker ? null : ($social_worker->elderLiving ? $social_worker->elderLiving : null),
            'social_service' => ! $social_worker ? null : [
                'elderly_center' => ! $social_worker->elderly_center ? null : $social_worker->elderly_center,
                'home_service' => ! $social_worker->homeService ? null : $social_worker->homeService,
                'elderly_daycare' => ! $social_worker->elderly_daycare ? null : $social_worker->elderly_daycare,
            ],
            'marital_status' => ! $social_worker ? null : ($social_worker->elder_marital ? $social_worker->elder_marital : null),
            'abnormal_vital_sign' => [
                'follow_up_action' => ! $social_worker ? null : ($social_worker->cognitive_fa ? $social_worker->cognitive_fa : null),
                'remarks_and_supplementary' => ! $social_worker ? null : ($social_worker->cognitive_rs ? $social_worker->cognitive_rs : null),
            ],
            'medical_history' => [
                'follow_up_action' => ! $qualtrics ? null : ($qualtrics->fallrisk_fa ? $qualtrics->fallrisk_fa : null),
                'remarks_and_supplementary' => ! $qualtrics ? null : ($qualtrics->fallrisk_rs ? $qualtrics->fallrisk_rs : null),
            ],
            'fall_risk' => [
                'follow_up_action' => ! $qualtrics ? null : ($qualtrics->remark_fa ? $qualtrics->remark_fa : null),
                'remarks_and_supplementary' => ! $qualtrics ? null : ($qualtrics->remark_rs ? $qualtrics->remark_rs : null),
            ],
            'cognitive' => [
                'follow_up_action' => ! $social_worker ? null : ($social_worker->psycho_fa ? $social_worker->psycho_fa : null),
                'remarks_and_supplementary' => ! $social_worker ? null : ($social_worker->psycho_rs ? $social_worker->psycho_rs : null),
            ],
            'emotional_status' => [
                'follow_up_action' => ! $social_worker ? null : ($social_worker->stratification_fa ? $social_worker->stratification_fa : null),
                'remarks_and_supplementary' => ! $social_worker ? null : ($social_worker->stratification_rs ? $social_worker->stratification_rs : null),
            ],
            'self_care_score' => $iadl_score,
            'living_environment' => [
                'follow_up_action' => ! $social_worker ? null : ($social_worker->social_fa ? $social_worker->social_fa : null),
                'remarks_and_supplementary' => ! $social_worker ? null : ($social_worker->social_rs ? $social_worker->social_rs : null),
            ],
            'remarks' => [
                'health_professional_remark' => ! $qualtrics ? null : ($qualtrics->qualtrics_remarks ? $qualtrics->qualtrics_remarks : null),
                'social_worker_remark' => ! $social_worker ? null : ($social_worker->sw_remark ? $social_worker->sw_remark : null),
            ],
        ];

        return $data;
    }

    public function getCgaNurseData($case_id)
    {
        $assessment_case = AssessmentCase::where('case_id', $case_id)->latest('updated_at')->first();
        $data = null;
        if (! $assessment_case) {
            return $data;
        }
        $medical_condition = MedicalConditionForm::where('assessment_case_id', $assessment_case->id)->latest('updated_at')->first();
        $social_worker = SocialWorkerForm::where('assessment_case_id', $assessment_case->id)->latest('updated_at')->first();
        $qualtrics = QualtricsForm::where('assessment_case_id', $assessment_case->id)->latest('updated_at')->first();
        $iadl_score = ! $social_worker ? 0 : ($social_worker->iadl_total_score ? $social_worker->iadl_total_score : 0);
        $lubben_score = ! $social_worker ? 0 : ($social_worker->lubben_total_score ? $social_worker->lubben_total_score : 0);
        $gds4_score = ! $social_worker ? 0 : ($social_worker->gds4_score ? $social_worker->gds4_score : 0);
        $data = [
            'cga_date' => ! $assessment_case ? null : ($assessment_case->assessment_date ? $assessment_case->assessment_date : null),
            'priority_level' => ! $assessment_case ? null : ($assessment_case->priority_level ? $assessment_case->priority_level : null),
            'living_status' => ! $social_worker ? null : ($social_worker->elderLiving ? $social_worker->elderLiving : null),
            'marital_status' => ! $social_worker ? null : ($social_worker->elder_marital ? $social_worker->elder_marital : null),
            'abnormal_vital_sign' => [
                'follow_up_action' => ! $social_worker ? null : ($social_worker->cognitive_fa ? $social_worker->cognitive_fa : null),
                'remarks_and_supplementary' => ! $social_worker ? null : ($social_worker->cognitive_rs ? $social_worker->cognitive_rs : null),
            ],
            'food_allergy' => ! $medical_condition ? null : ($medical_condition->food_allergy_description ? $medical_condition->food_allergy_description : null),
            'drug_allergy' => ! $medical_condition ? null : ($medical_condition->drug_allergy_description ? $medical_condition->drug_allergy_description : null),
            'other_medical_history' => ! $medical_condition ? null : ($medical_condition->medicalHistory ? $medical_condition->medicalHistory : null),
            'medical_history' => [
                'follow_up_action' => ! $qualtrics ? null : ($qualtrics->fallrisk_fa ? $qualtrics->fallrisk_fa : null),
                'remarks_and_supplementary' => ! $qualtrics ? null : ($qualtrics->fallrisk_rs ? $qualtrics->fallrisk_rs : null),
            ],
            'fall_risk' => [
                'follow_up_action' => ! $qualtrics ? null : ($qualtrics->remark_fa ? $qualtrics->remark_fa : null),
                'remarks_and_supplementary' => ! $qualtrics ? null : ($qualtrics->remark_rs ? $qualtrics->remark_rs : null),
            ],
            'cognitive' => [
                'follow_up_action' => ! $social_worker ? null : ($social_worker->psycho_fa ? $social_worker->psycho_fa : null),
                'remarks_and_supplementary' => ! $social_worker ? null : ($social_worker->psycho_rs ? $social_worker->psycho_rs : null),
            ],
            'emotional_status' => [
                'follow_up_action' => ! $social_worker ? null : ($social_worker->stratification_fa ? $social_worker->stratification_fa : null),
                'remarks_and_supplementary' => ! $social_worker ? null : ($social_worker->stratification_rs ? $social_worker->stratification_rs : null),
            ],
            'self_care_score' => $iadl_score,
            'living_environment' => [
                'follow_up_action' => ! $social_worker ? null : ($social_worker->social_fa ? $social_worker->social_fa : null),
                'remarks_and_supplementary' => ! $social_worker ? null : ($social_worker->social_rs ? $social_worker->social_rs : null),
            ],
            'alert' => [
                'health_professional_remark' => ! $qualtrics ? null : ($qualtrics->qualtrics_remarks ? $qualtrics->qualtrics_remarks : null),
                'social_worker_remark' => ! $social_worker ? null : ($social_worker->sw_remark ? $social_worker->sw_remark : null),
            ],
        ];

        return $data;
    }

    public function getBznData($case_id)
    {
        $assessment_case = AssessmentCase::where('case_id', $case_id)->latest('updated_at')->first();
        $data = null;
        if (! $assessment_case) {
            return $data;
        }
        $medical_condition = MedicalConditionForm::where('assessment_case_id', $assessment_case->id)->latest('updated_at')->first();
        $social_worker = SocialWorkerForm::where('assessment_case_id', $assessment_case->id)->latest('updated_at')->first();
        $qualtrics = QualtricsForm::where('assessment_case_id', $assessment_case->id)->latest('updated_at')->first();
        $social_background = SocialBackgroundForm::where('assessment_case_id', $assessment_case->id)->latest('updated_at')->first();
        $function_mobility = FunctionMobilityForm::where('assessment_case_id', $assessment_case->id)->latest('updated_at')->first();
        $data = [
            'health_issue' => ! $medical_condition ? null : ($medical_condition->chiefComplaint ? $medical_condition->chiefComplaint : null),
            'food_allergy' => ! $medical_condition ? null : ($medical_condition->food_allergy_description ? $medical_condition->food_allergy_description : null),
            'drug_allergy' => ! $medical_condition ? null : ($medical_condition->drug_allergy_description ? $medical_condition->drug_allergy_description : null),
            'alert' => [
                'health_professional_remark' => ! $qualtrics ? null : ($qualtrics->qualtrics_remarks ? $qualtrics->qualtrics_remarks : null),
                'social_worker_remark' => ! $social_worker ? null : ($social_worker->sw_remark ? $social_worker->sw_remark : null),
            ],
            'medical_history' => ! $medical_condition ? null : ($medical_condition->medicalHistory ? $medical_condition->medicalHistory : null),
            'living_status' => ! $social_background ? null : ($social_background->livingStatusTable ? $social_background->livingStatusTable : null),
            'marital_status' => ! $social_background ? null : ($social_background->marital_status ? $social_background->marital_status : null),
            'mobility' => ! $function_mobility ? null : ($function_mobility->iadl ? $function_mobility->iadl : null),
        ];

        return $data;
    }

    public function getCgaBaseData($case_id)
    {
        $assessment_case = AssessmentCase::where('case_id', $case_id)->latest('updated_at')->first();

    }

    public function changeCgaType($case_id, $cga_type)
    {
        $case = Cases::where('id', $case_id)->update(['cga_type' => $cga_type]);
        if (! $case) {
            return false;
        }

        return true;
    }

    public function getUsersSet()
    {
        try {
            $users = User::select(['id', 'name'])->get();
            if (count($users) == 0) {
                return null;
            }
            $result = new stdClass;
            for ($i = 0; $i < count($users); $i++) {
                $userId = $users[$i]->id;
                if (! property_exists($result, $userId)) {
                    $result->$userId = $users[$i];
                }
            }

            return (array) $result;
        } catch (Exception $e) {
            return null;
        }
    }

    public function getUidSet()
    {
        try {
            $cases = Cases::with('elder')->get();
            if (count($cases) == 0) {
                return null;
            }
            $result = new stdClass;
            for ($i = 0; $i < count($cases); $i++) {
                $casesId = $cases[$i]->id;
                if (! property_exists($result, $casesId)) {
                    $result->$casesId = ['uid' => $cases[$i]['elder'] ? $cases[$i]['elder']['uid'] : null];
                }
            }

            return (array) $result;
        } catch (Exception $e) {
            return null;
        }
    }

    public function getCasesStatus()
    {
        try {
            $cases = Cases::select('id', 'case_status')->get();
            if (count($cases) == 0) {
                return null;
            }

            $on_going_keys = ['On going', 'Baseline Completed', 'Enrolled - BZN', 'Enrolled - CGA', 'Enrolled-BZN', 'Enrolled-CGA'];
            $pending_keys = ['Pending', 'Pending for waiting 1st visit'];
            $finished_keys = ['Reject', 'Dropout', 'Completed'];
            $result = new stdClass;
            for ($i = 0; $i < count($cases); $i++) {
                $case_id = $cases[$i]['id'];
                if (! property_exists($result, $cases[$i]['id'])) {
                    $result->$case_id['on_going'] = 0;
                    $result->$case_id['pending'] = 0;
                    $result->$case_id['finished'] = 0;
                    if (in_array($cases[$i]['case_status'], $on_going_keys)) {
                        $result->$case_id['on_going'] = 1;
                    } elseif (in_array($cases[$i]['case_status'], $pending_keys)) {
                        $result->$case_id['pending'] = 1;
                    } elseif (in_array($cases[$i]['case_status'], $finished_keys)) {
                        $result->$case_id['finished'] = 1;
                    }

                } elseif (property_exists($result, $cases[$i]['id'])) {
                    if (in_array($cases[$i]['case_status'], $on_going_keys)) {
                        $result->$case_id['on_going'] = +1;
                    } elseif (in_array($cases[$i]['case_status'], $pending_keys)) {
                        $result->$case_id['pending'] = +1;
                    } elseif (in_array($cases[$i]['case_status'], $finished_keys)) {
                        $result->$case_id['finished'] = +1;
                    }
                }
            }

            return (array) $result;
        } catch (Exception $e) {
            return null;
        }
    }

    public function getUidSetByCasesId()
    {
        try {
            $cases = Cases::select('id', 'elder_id')->with('elder')->get();
            if (count($cases) == 0) {
                return null;
            }
            $result = new stdClass;
            for ($i = 0; $i < count($cases); $i++) {
                $uid = $cases[$i]['elder'] ? $cases[$i]['elder']['uid'] : null;
                $casesId = $cases[$i]->id;
                if (! property_exists($result, $casesId && $uid !== null)) {
                    $result->$casesId = ['uid' => $uid];
                }
            }

            return (array) $result;
        } catch (Exception $e) {
            return null;
        }
    }

    public function getUserById($id)
    {
        $user = User::where('id', $id)->first();
        if (! $user) {
            return null;
        }

        return $user->toArray();
    }
}
