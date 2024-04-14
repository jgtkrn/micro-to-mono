<?php

namespace App\Http\Services\v2\Assessments;

use App\Models\v2\Assessments\AssessmentCase;
use App\Models\v2\Assessments\AssessmentCaseStatus;
use App\Models\v2\Assessments\BarthelIndexForm;
use App\Models\v2\Assessments\ChiefComplaintTable;
use App\Models\v2\Assessments\CommunityResourceTable;
use App\Models\v2\Assessments\DoReferralTables;
use App\Models\v2\Assessments\ElderLiving;
use App\Models\v2\Assessments\FinancialStateTable;
use App\Models\v2\Assessments\FollowUpHistory;
use App\Models\v2\Assessments\FunctionMobilityForm;
use App\Models\v2\Assessments\GeriatricDepressionScaleForm;
use App\Models\v2\Assessments\HomeFall;
use App\Models\v2\Assessments\HomeHygiene;
use App\Models\v2\Assessments\HomeService;
use App\Models\v2\Assessments\HospitalizationTables;
use App\Models\v2\Assessments\IadlForm;
use App\Models\v2\Assessments\LifeSupport;
use App\Models\v2\Assessments\LivingStatusTable;
use App\Models\v2\Assessments\LubbenSocialNetworkScaleForm;
use App\Models\v2\Assessments\MajorFallTable;
use App\Models\v2\Assessments\MedicalConditionForm;
use App\Models\v2\Assessments\MedicalHistoryTable;
use App\Models\v2\Assessments\MedicationAdherenceForm;
use App\Models\v2\Assessments\MedicationHistory;
use App\Models\v2\Assessments\MontrealCognitiveAssessmentForm;
use App\Models\v2\Assessments\PainSiteTable;
use App\Models\v2\Assessments\PhysicalConditionForm;
use App\Models\v2\Assessments\PhysiologicalMeasurementForm;
use App\Models\v2\Assessments\QualtricsForm;
use App\Models\v2\Assessments\RePhysiologicalMeasurementForm;
use App\Models\v2\Assessments\SocialBackgroundForm;
use App\Models\v2\Assessments\SocialWorkerForm;
use App\Models\v2\Assessments\WalkAid;
use Illuminate\Http\Request;

class FormService
{
    private $validator;
    private $wiringService;

    private $form_names = [
        'physiological_measurement',
        're_physiological_measurement',
        'medical_condition',
        'medication_adherence',
        'lubben_social_network_scale',
        'social_background',
        'function_mobility',
        'barthel_index',
        'geriatric_depression_scale',
        'iadl',
        'genogram',
        'montreal_cognitive_assessment',
        'physical_condition',
        'assessment_case_status',
        'attachment',
        'signature',
        'qualtrics',
        'social_worker',
    ];

    public function __construct()
    {
        $this->validator = new ValidatorService;
        $this->wiringService = new WiringServiceAssessment;
    }

    public function getFormNames()
    {
        return $this->form_names;
    }

    public function show($assessment_case, $form_name)
    {
        switch ($form_name) {
            case 'physiological_measurement':
                return $assessment_case->physiologicalMeasurementForm()->first();
            case 're_physiological_measurement':
                return $assessment_case->rePhysiologicalMeasurementForm()->first();
            case 'medical_condition':
                $follow_up_history = FollowUpHistory::join(
                    'assessment_cases',
                    'assessment_cases.case_id',
                    '=',
                    'follow_up_histories.case_id'
                )
                    ->join('appointments', 'appointments.id', '=', 'follow_up_histories.appointment_id')
                    ->select(
                        'follow_up_histories.id as id',
                        'assessment_cases.case_id as case_id',
                        'follow_up_histories.date as date',
                        'follow_up_histories.time as time',
                        'follow_up_histories.created_at as created_at',
                        'follow_up_histories.appointment_other_text as appointment_other_text',
                        'appointments.id as appointment_id',
                        'appointments.cluster as cluster',
                        'appointments.type as type',
                        'appointments.name_en as name_en',
                        'appointments.name_sc as name_sc',
                    )
                    ->where('follow_up_histories.case_id', '=', $assessment_case->case_id)
                    ->orderBy('created_at', 'desc')
                    ->first();

                $medication_history = MedicationHistory::where('case_id', $assessment_case->case_id)->orderBy('created_at', 'desc')->get();
                $medical_condition = $assessment_case->medicalConditionForm()->latest('updated_at')->first();
                $medication_adherence = MedicationAdherenceForm::where('assessment_case_id', $assessment_case->id)->latest('updated_at')->first();
                if ($medication_adherence) {
                    if ($medication_adherence->total_mmas_score && $medical_condition) {
                        $medical_condition->medication_adherence = $medication_adherence->total_mmas_score;
                    } else {
                        $medical_condition->medication_adherence = null;
                    }
                }

                return [
                    'medical_condition' => $medical_condition,
                    'follow_up_history' => $follow_up_history,
                    'medication_histories' => $medication_history,
                ];
            case 'medication_adherence':
                return $assessment_case->medicationAdherenceForm()->latest('updated_at')->first();
            case 'lubben_social_network_scale':
                return $assessment_case->lubbenSocialNetworkScaleForm()->latest('updated_at')->first();
            case 'social_background':
                return $assessment_case->socialBackgroundForm()->latest('updated_at')->first();
            case 'function_mobility':
                return $assessment_case->functionMobilityForm()->latest('updated_at')->first();
            case 'barthel_index':
                return $assessment_case->barthelIndexForm()->latest('updated_at')->first();
            case 'geriatric_depression_scale':
                return $assessment_case->geriatricDepressionScaleForm()->latest('updated_at')->first();
            case 'iadl':
                return $assessment_case->iadlForm()->latest('updated_at')->first();
            case 'genogram':
                return $assessment_case->genogramForm()->latest('updated_at')->first();
            case 'montreal_cognitive_assessment':
                return $assessment_case->montrealCognitiveAssessmentForm()->latest('updated_at')->first();
            case 'physical_condition':
                return $assessment_case->physicalConditionForm()->latest('updated_at')->first();
            case 'assessment_case_status':
                return $assessment_case->assessmentCaseStatus()->latest('updated_at')->first();
            case 'attachment':
                return $assessment_case->assessmentCaseAttachment()->get();
            case 'signature':
                return $assessment_case->assessmentCaseSignature()->latest('updated_at')->first();
            case 'qualtrics':
                return $assessment_case->qualtricsForm()->latest('updated_at')->first();
            case 'social_worker':
                return $assessment_case->socialWorkerForm()->latest('updated_at')->first();
            default:
                return 'Invalid form name';
        }
    }

    public function updateOrCreate(Request $request, $id)
    {
        switch ($request->form_name) {
            case 'physiological_measurement':
                return $this->updateOrCreatePhysiologicalMeasurement($request, $id);
            case 're_physiological_measurement':
                return $this->updateOrCreateRePhysiologicalMeasurement($request, $id);
            case 'medical_condition':
                return $this->updateOrCreateMedicalCondition($request, $id);
            case 'medication_adherence':
                return $this->updateOrCreateMedicationAdherence($request, $id);
            case 'lubben_social_network_scale':
                return $this->updateOrCreateLubbenSocialNetworkScale($request, $id);
            case 'social_background':
                return $this->updateOrCreateSocialBackground($request, $id);
            case 'function_mobility':
                return $this->updateOrCreateFunctionMobility($request, $id);
            case 'barthel_index':
                return $this->updateOrCreateBarthelIndex($request, $id);
            case 'geriatric_depression_scale':
                return $this->updateOrCreateGeriatricDepressionScale($request, $id);
            case 'iadl':
                return $this->updateOrCreateIadl($request, $id);
            case 'montreal_cognitive_assessment':
                return $this->updateOrCreateMontrealCognitiveAssessment($request, $id);
            case 'physical_condition':
                return $this->updateOrCreatePhysicalCondition($request, $id);
            case 'assessment_case_status':
                return $this->updateOrCreateAssessmentCaseStatus($request, $id);
            case 'qualtrics':
                return $this->updateOrCreateQualtrics($request, $id);
            case 'social_worker':
                return $this->updateOrCreateSocialWorker($request, $id);
            default:
                return 'Invalid form name';
        }
    }

    public function updateOrCreatePhysiologicalMeasurement(Request $request, $id)
    {
        $this->validator->validatePhysiologicalMeasurement($request);
        $form = PhysiologicalMeasurementForm::updateOrCreate(
            ['assessment_case_id' => $id],
            [
                'temperature' => $request->temperature,
                'sitting_sbp' => $request->sitting_sbp,
                'sitting_dbp' => $request->sitting_dbp,
                'standing_sbp' => $request->standing_sbp,
                'standing_dbp' => $request->standing_dbp,
                'blood_oxygen' => $request->blood_oxygen,
                'heart_rate' => $request->heart_rate,
                'heart_rythm' => $request->heart_rythm,
                'kardia' => $request->kardia,
                'blood_sugar' => $request->blood_sugar,
                'blood_sugar_time' => $request->blood_sugar_time,
                'waistline' => $request->waistline,
                'weight' => $request->weight,
                'height' => $request->height,
                'respiratory_rate' => $request->respiratory_rate,
                'blood_options' => $request->blood_options,
                'blood_text' => $request->blood_text,
                'meal_text' => $request->meal_text,

                // user data
                'created_by' => $request->user_id,
                'updated_by' => $request->user_id,
                'created_by_name' => $request->user_name,
                'updated_by_name' => $request->user_name,
            ]
        );

        return $form;
    }

    public function updateOrCreateRePhysiologicalMeasurement(Request $request, $id)
    {
        $this->validator->validateRePhysiologicalMeasurement($request);
        $form = RePhysiologicalMeasurementForm::updateOrCreate(
            ['assessment_case_id' => $id],
            [
                're_temperature' => $request->re_temperature,
                're_sitting_sbp' => $request->re_sitting_sbp,
                're_sitting_dbp' => $request->re_sitting_dbp,
                're_standing_sbp' => $request->re_standing_sbp,
                're_standing_dbp' => $request->re_standing_dbp,
                're_blood_oxygen' => $request->re_blood_oxygen,
                're_heart_rate' => $request->re_heart_rate,
                're_heart_rythm' => $request->re_heart_rythm,
                're_kardia' => $request->re_kardia,
                're_blood_sugar' => $request->re_blood_sugar,
                're_blood_sugar_time' => $request->re_blood_sugar_time,
                're_waistline' => $request->re_waistline,
                're_weight' => $request->re_weight,
                're_height' => $request->re_height,
                're_respiratory_rate' => $request->re_respiratory_rate,
                're_blood_options' => $request->re_blood_options,
                're_blood_text' => $request->re_blood_text,
                're_meal_text' => $request->re_meal_text,

                // user data
                'created_by' => $request->user_id,
                'updated_by' => $request->user_id,
                'created_by_name' => $request->user_name,
                'updated_by_name' => $request->user_name,
            ]
        );

        return $form;
    }

    public function updateOrCreateMedicalCondition(Request $request, $id)
    {
        $this->validator->validateMedicalCondition($request, $id);
        $form = MedicalConditionForm::updateOrCreate(
            ['assessment_case_id' => $id],
            [
                'has_medical_history' => $request->has_medical_history,
                'premorbid' => $request->premorbid,
                'premorbid_start_month' => $request->premorbid_start_month,
                'premorbid_start_year' => $request->premorbid_start_year,
                'premorbid_end_month' => $request->premorbid_end_month,
                'premorbid_end_year' => $request->premorbid_end_year,
                'premorbid_condition' => $request->premorbid_condition,
                'followup_appointment' => $request->followup_appointment,
                'has_food_allergy' => $request->has_food_allergy,
                'food_allergy_description' => $request->food_allergy_description,
                'has_drug_allergy' => $request->has_drug_allergy,
                'drug_allergy_description' => $request->drug_allergy_description,
                'has_medication' => $request->has_medication,
                'medication_description' => $request->medication_description,
                'other_complaint' => $request->other_complaint,
                'other_medical_history' => $request->other_medical_history,
                'ra_part' => $request->ra_part,
                'fracture_part' => $request->fracture_part,
                'arthritis_part' => $request->arthritis_part,

                // user data
                'created_by' => $request->user_id,
                'updated_by' => $request->user_id,
                'created_by_name' => $request->user_name,
                'updated_by_name' => $request->user_name,
            ]
        );

        $obj_length_cm = count((array) $request->medical_history);
        $medical_history_list = [[]];
        for ($i = 0; $i < $obj_length_cm; $i++) {
            $medical_history_list[$i]['medical_history'] = $request->medical_history[$i];
        }
        $medical_history = MedicalHistoryTable::where('medical_condition_form_id', $form->id);
        if ($obj_length_cm > 0 && ! $medical_history) {
            $form->medicalHistory()->createMany($medical_history_list);
        } elseif ($obj_length_cm > 0 && $medical_history) {
            $medical_history->where('medical_condition_form_id', $form->id)->delete();
            $form->medicalHistory()->createMany($medical_history_list);
        } elseif ($obj_length_cm == 0 && $medical_history) {
            $medical_history->where('medical_condition_form_id', $form->id)->delete();
        } elseif ($obj_length_cm == 0 && ! $medical_history) {
            $medical_history->where('medical_condition_form_id', $form->id)->delete();
        }

        $obj_length_cf = count((array) $request->complaint);
        $chief_complaint_list = [[]];
        for ($i = 0; $i < $obj_length_cf; $i++) {
            $chief_complaint_list[$i]['complaint'] = $request->complaint[$i];
        }

        $chief_complaint = ChiefComplaintTable::where('medical_condition_form_id', $form->id);
        if ($obj_length_cf > 0 && ! $chief_complaint) {
            $form->chiefComplaint()->createMany($chief_complaint_list);
        } elseif ($obj_length_cf > 0 && $chief_complaint) {
            $chief_complaint->where('medical_condition_form_id', $form->id)->delete();
            $form->chiefComplaint()->createMany($chief_complaint_list);
        } elseif ($obj_length_cf == 0 && $chief_complaint) {
            $chief_complaint->where('medical_condition_form_id', $form->id)->delete();
        } elseif ($obj_length_cf == 0 && ! $chief_complaint) {
            $chief_complaint->where('medical_condition_form_id', $form->id)->delete();
        }

        $assessment_case = AssessmentCase::where('id', $form->assessment_case_id)->first();

        if (! $assessment_case) {
            return response()->json([
                'error' => [
                    'code' => 404,
                    'message' => "Cannot find Assessment Case with id {$form->assessment_case_id}",
                ],
            ], 404);
        }

        if (! $request->has('date') || ! $request->date) {
        } elseif (! $request->has('time') || ! $request->time) {
        } elseif (! $request->has('appointment_id') || ! $request->appointment_id) {
        } else {
            FollowUpHistory::updateOrCreate(
                ['case_id' => $assessment_case->case_id],
                [
                    'case_id' => $assessment_case->case_id,
                    'date' => $request->date,
                    'time' => $request->time,
                    'appointment_id' => $request->appointment_id,
                ]
            );
        }

        $follow_up_history = FollowUpHistory::join(
            'assessment_cases',
            'assessment_cases.case_id',
            '=',
            'follow_up_histories.case_id'
        )
            ->join('appointments', 'appointments.id', '=', 'follow_up_histories.appointment_id')
            ->select(
                'follow_up_histories.id as id',
                'assessment_cases.case_id as case_id',
                'follow_up_histories.date as date',
                'follow_up_histories.time as time',
                'follow_up_histories.created_at as created_at',
                'follow_up_histories.appointment_other_text as appointment_other_text',
                'appointments.id as appointment_id',
                'appointments.cluster as cluster',
                'appointments.type as type',
                'appointments.name_en as name_en',
                'appointments.name_sc as name_sc',
            )
            ->where('follow_up_histories.case_id', '=', $assessment_case->case_id)
            ->orderBy('follow_up_histories.updated_at', 'desc')
            ->first();
        $medical_condition = MedicalConditionForm::where('id', $form->id)->first();
        $medication_adherence = $assessment_case->medicationAdherenceForm()->latest('updated_at')->first();
        if ($medication_adherence) {
            if ($medication_adherence->total_mmas_score) {
                $medical_condition->medication_adherence = $medication_adherence->total_mmas_score;
            }
        } else {
            $medical_condition->medication_adherence = null;
        }

        return response()->json([
            'medical_condition' => $medical_condition,
            'follow_up_history' => $follow_up_history,
        ], );
    }

    public function updateOrCreateMedicationAdherence(Request $request, $id)
    {
        $this->validator->validateMedicationAdherence($request);
        $form = MedicationAdherenceForm::updateOrCreate(
            ['assessment_case_id' => $id],
            [
                'elderly_central_ref_number' => $request->elderly_central_ref_number,
                'assessment_date' => $request->assessment_date,
                'assessor_name' => $request->assessor_name,
                'assessment_kind' => $request->assessment_kind,
                'is_forget_sometimes' => $request->is_forget_sometimes,
                'is_missed_meds' => $request->is_missed_meds,
                'is_reduce_meds' => $request->is_reduce_meds,
                'is_forget_when_travel' => $request->is_forget_when_travel,
                'is_meds_yesterday' => $request->is_meds_yesterday,
                'is_stop_when_better' => $request->is_stop_when_better,
                'is_annoyed' => $request->is_annoyed,
                'forget_sometimes' => $request->forget_sometimes,
                'missed_meds' => $request->missed_meds,
                'reduce_meds' => $request->reduce_meds,
                'forget_when_travel' => $request->forget_when_travel,
                'meds_yesterday' => $request->meds_yesterday,
                'stop_when_better' => $request->stop_when_better,
                'annoyed' => $request->annoyed,
                'forget_frequency' => $request->forget_frequency,
                'total_mmas_score' => $request->total_mmas_score,
            ]
        );

        return $form;
    }

    public function updateOrCreateLubbenSocialNetworkScale(Request $request, $id)
    {

        $this->validator->validateLubbenSocialNetworkScale($request);

        $form = LubbenSocialNetworkScaleForm::updateOrCreate(
            ['assessment_case_id' => $id],
            [
                'elderly_central_ref_number' => $request->elderly_central_ref_number,
                'assessment_date' => $request->assessment_date,
                'assessor_name' => $request->assessor_name,
                'assessment_kind' => $request->assessment_kind,
                'relatives_sum' => $request->relatives_sum,
                'relatives_to_talk' => $request->relatives_to_talk,
                'relatives_to_help' => $request->relatives_to_help,
                'friends_sum' => $request->friends_sum,
                'friends_to_talk' => $request->friends_to_talk,
                'friends_to_help' => $request->friends_to_help,
                'lubben_total_score' => $request->lubben_total_score,
            ]
        );

        return $form;
    }

    public function updateOrCreateSocialBackground(Request $request, $id)
    {
        $this->validator->validateSocialBackground($request);
        $lubben_score = LubbenSocialNetworkScaleForm::where('assessment_case_id', $id)->first();
        $score = ($lubben_score) ? $lubben_score->lubben_total_score : $request->lubben_total_score;
        $form = SocialBackgroundForm::updateOrCreate(
            ['assessment_case_id' => $id],
            [
                'marital_status' => $request->marital_status,
                'safety_alarm' => $request->safety_alarm,
                'has_carer' => $request->has_carer,
                'carer_option' => $request->carer_option,
                'carer' => $request->carer,
                'employment_status' => $request->employment_status,
                'has_community_resource' => $request->has_community_resource,
                'community_resource_other' => $request->community_resource_other,
                'education_level' => $request->education_level,
                // 'financial_state' => $request->financial_state,
                'smoking_option' => $request->smoking_option,
                'smoking' => $request->smoking,
                'drinking_option' => $request->drinking_option,
                'drinking' => $request->drinking,
                'has_religion' => $request->has_religion,
                'religion' => $request->religion,
                'has_social_activity' => $request->has_social_activity,
                'social_activity' => $request->social_activity,
                'lubben_total_score' => $score,
                'other_living_status' => $request->other_living_status,
                'relationship_other' => $request->relationship_other,
                'financial_state_other' => $request->financial_state_other,
                'religion_remark' => $request->religion_remark,
                'employment_remark' => $request->employment_remark,

                // user data
                'created_by' => $request->user_id,
                'updated_by' => $request->user_id,
                'created_by_name' => $request->user_name,
                'updated_by_name' => $request->user_name,
            ]
        );
        $obj_length = count((array) $request->ls_options);
        $living_status_list = [[]];
        for ($i = 0; $i < $obj_length; $i++) {
            $living_status_list[$i]['ls_options'] = $request->ls_options[$i];
        }

        $obj_length_cm = count((array) $request->community_resource);
        $community_resource_list = [[]];
        for ($i = 0; $i < $obj_length_cm; $i++) {
            $community_resource_list[$i]['community_resource'] = $request->community_resource[$i];
        }

        $obj_length_fs = count((array) $request->financial_state);
        $financial_state_list = [[]];
        for ($i = 0; $i < $obj_length_fs; $i++) {
            $financial_state_list[$i]['financial_state'] = $request->financial_state[$i];
        }

        $financial_state = FinancialStateTable::where('social_background_form_id', $form->id);

        $community_resource = CommunityResourceTable::where('social_background_form_id', $form->id);

        $living_status = LivingStatusTable::where('social_background_form_id', $form->id);

        if ($obj_length_cm > 0 && ! $community_resource) {
            $form->communityResourceTable()->createMany($community_resource_list);
        } elseif ($obj_length_cm > 0 && $community_resource) {
            $community_resource->where('social_background_form_id', $form->id)->delete();
            $form->communityResourceTable()->createMany($community_resource_list);
        } elseif ($obj_length_cm == 0 && $community_resource) {
            $community_resource->where('social_background_form_id', $form->id)->delete();
        } elseif ($obj_length_cm == 0 && ! $community_resource) {
            $community_resource->where('social_background_form_id', $form->id)->delete();
        }

        if ($obj_length > 0 && ! $living_status) {
            $form->livingStatusTable()->createMany($living_status_list);
        } elseif ($obj_length > 0 && $living_status) {
            $living_status->where('social_background_form_id', $form->id)->delete();
            $form->livingStatusTable()->createMany($living_status_list);
        } elseif ($obj_length == 0 && $living_status) {
            $living_status->where('social_background_form_id', $form->id)->delete();
        } elseif ($obj_length == 0 && ! $living_status) {
            $living_status->where('social_background_form_id', $form->id)->delete();
        }

        if ($obj_length_fs > 0 && ! $financial_state) {
            $form->financialStateTable()->createMany($financial_state_list);
        } elseif ($obj_length_fs > 0 && $financial_state) {
            $financial_state->where('social_background_form_id', $form->id)->delete();
            $form->financialStateTable()->createMany($financial_state_list);
        } elseif ($obj_length_fs == 0 && $financial_state) {
            $financial_state->where('social_background_form_id', $form->id)->delete();
        } elseif ($obj_length_fs == 0 && ! $financial_state) {
            $financial_state->where('social_background_form_id', $form->id)->delete();
        }

        return $form->with(['livingStatusTable', 'communityResourceTable', 'financialStateTable'])->latest('updated_at')->first();
    }

    public function updateOrCreateFunctionMobility(Request $request, $id)
    {
        $this->validator->validateFunctionMobility($request);
        $form = FunctionMobilityForm::updateOrCreate(
            ['assessment_case_id' => $id],
            [
                'iadl' => $request->iadl,
                'total_iadl_score' => $request->total_iadl_score,
                'mobility' => $request->mobility,
                'walk_with_assistance' => $request->walk_with_assistance,
                'mobility_tug' => $request->mobility_tug,
                'left_single_leg' => $request->left_single_leg,
                'right_single_leg' => $request->right_single_leg,
                'range_of_motion' => $request->range_of_motion,
                'upper_limb_left' => $request->upper_limb_left,
                'upper_limb_right' => $request->upper_limb_right,
                'lower_limb_left' => $request->lower_limb_left,
                'lower_limb_right' => $request->lower_limb_right,
                'fall_history' => $request->fall_history,
                'number_of_major_fall' => $request->number_of_major_fall,
                'mi_independent' => $request->mi_independent,
                'mi_walk_assisst' => $request->mi_walk_assisst,
                'mi_wheelchair_bound' => $request->mi_wheelchair_bound,
                'mi_bed_bound' => $request->mi_bed_bound,
                'mi_remark' => $request->mi_remark,
                'mo_independent' => $request->mo_independent,
                'mo_walk_assisst' => $request->mo_walk_assisst,
                'mo_wheelchair_bound' => $request->mo_wheelchair_bound,
                'mo_bed_bound' => $request->mo_bed_bound,
                'mo_remark' => $request->mo_remark,
                'major_fall_tables' => $request->major_fall_tables,
            ]
        );

        $obj_length = count((array) $request->major_fall_tables);
        $major_fall_list = [[]];
        for ($i = 0; $i < $obj_length; $i++) {
            $major_fall_list[$i]['location'] = $request->major_fall_tables[$i]['location'];
            $major_fall_list[$i]['injury_sustained'] = $request->major_fall_tables[$i]['injury_sustained'];
            $major_fall_list[$i]['fall_mechanism'] = $request->major_fall_tables[$i]['fall_mechanism'];
            $major_fall_list[$i]['fall_mechanism_other'] = $request->major_fall_tables[$i]['fall_mechanism_other'];
            $major_fall_list[$i]['fracture'] = $request->major_fall_tables[$i]['fracture'];
            $major_fall_list[$i]['fracture_text'] = $request->major_fall_tables[$i]['fracture_text'];
        }

        $major_fall = MajorFallTable::where('function_mobility_form_id', $form->id);

        if (! $major_fall) {
            $form->majorFallTable()->createMany($major_fall_list);

            return $form->with('majorFallTable')->latest('updated_at')->first();
        }

        $major_fall->where('function_mobility_form_id', $form->id)->delete();
        $form->majorFallTable()->createMany($major_fall_list);

        return $form->with('majorFallTable')->latest('updated_at')->first();
    }

    public function updateOrCreateBarthelIndex(Request $request, $id)
    {

        $this->validator->validateBarthelIndex($request);

        $form = BarthelIndexForm::updateOrCreate(
            ['assessment_case_id' => $id],
            [
                'bowels' => $request->bowels,
                'bladder' => $request->bladder,
                'grooming' => $request->grooming,
                'toilet_use' => $request->toilet_use,
                'feeding' => $request->feeding,
                'transfer' => $request->transfer,
                'mobility' => $request->mobility,
                'dressing' => $request->dressing,
                'stairs' => $request->stairs,
                'bathing' => $request->bathing,
                'barthel_total_score' => $request->barthel_total_score,

                // user data
                'created_by' => $request->user_id,
                'updated_by' => $request->user_id,
                'created_by_name' => $request->user_name,
                'updated_by_name' => $request->user_name,
            ]
        );

        return $form;
    }

    public function updateOrCreateGeriatricDepressionScale(Request $request, $id)
    {

        $this->validator->validateGeriatricDepressionScale($request);

        $form = GeriatricDepressionScaleForm::updateOrCreate(
            ['assessment_case_id' => $id],
            [
                'elderly_central_ref_number' => $request->elderly_central_ref_number,
                'assessment_date' => $request->assessment_date,
                'assessor_name' => $request->assessor_name,
                'assessment_kind' => $request->assessment_kind,
                'is_satisfied' => $request->is_satisfied,
                'is_given_up' => $request->is_given_up,
                'is_feel_empty' => $request->is_feel_empty,
                'is_often_bored' => $request->is_often_bored,
                'is_happy_a_lot' => $request->is_happy_a_lot,
                'is_affraid' => $request->is_affraid,
                'is_happy_all_day' => $request->is_happy_all_day,
                'is_feel_helpless' => $request->is_feel_helpless,
                'is_prefer_stay' => $request->is_prefer_stay,
                'is_memory_problem' => $request->is_memory_problem,
                'is_good_to_alive' => $request->is_good_to_alive,
                'is_feel_useless' => $request->is_feel_useless,
                'is_feel_energic' => $request->is_feel_energic,
                'is_hopeless' => $request->is_hopeless,
                'is_people_better' => $request->is_people_better,
                'gds15_score' => $request->gds15_score,
            ]
        );

        return $form;
    }

    public function updateOrCreateIadl(Request $request, $id)
    {

        $this->validator->validateIadl($request);

        $form = IadlForm::updateOrCreate(
            ['assessment_case_id' => $id],
            [
                'elderly_central_ref_number' => $request->elderly_central_ref_number,
                'assessment_date' => $request->assessment_date,
                'assessor_name' => $request->assessor_name,
                'assessment_kind' => $request->assessment_kind,
                'can_use_phone' => $request->can_use_phone,
                'text_use_phone' => $request->text_use_phone,
                'can_take_ride' => $request->can_take_ride,
                'text_take_ride' => $request->text_take_ride,
                'can_buy_food' => $request->can_buy_food,
                'text_buy_food' => $request->text_buy_food,
                'can_cook' => $request->can_cook,
                'text_cook' => $request->text_cook,
                'can_do_housework' => $request->can_do_housework,
                'text_do_housework' => $request->text_do_housework,
                'can_do_repairment' => $request->can_do_repairment,
                'text_do_repairment' => $request->text_do_repairment,
                'can_do_laundry' => $request->can_do_laundry,
                'text_do_laundry' => $request->text_do_laundry,
                'can_take_medicine' => $request->can_take_medicine,
                'text_take_medicine' => $request->text_take_medicine,
                'can_handle_finances' => $request->can_handle_finances,
                'text_handle_finances' => $request->text_handle_finances,
                'iadl_total_score' => $request->iadl_total_score,
            ]
        );

        return $form;
    }

    public function updateOrCreateMontrealCognitiveAssessment(Request $request, $id)
    {

        $this->validator->validateMontrealCognitiveAssessment($request);

        $form = MontrealCognitiveAssessmentForm::updateOrCreate(
            ['assessment_case_id' => $id],
            [
                'elderly_central_ref_number' => $request->elderly_central_ref_number,
                'assessment_date' => $request->assessment_date,
                'assessor_name' => $request->assessor_name,
                'assessment_kind' => $request->assessment_kind,
                'memory_c11' => $request->memory_c11,
                'memory_c12' => $request->memory_c12,
                'memory_c13' => $request->memory_c13,
                'memory_c14' => $request->memory_c14,
                'memory_c15' => $request->memory_c15,
                'memory_c21' => $request->memory_c21,
                'memory_c22' => $request->memory_c22,
                'memory_c23' => $request->memory_c23,
                'memory_c24' => $request->memory_c24,
                'memory_c25' => $request->memory_c25,
                'memory_score' => $request->memory_score,
                'language_fluency1' => $request->language_fluency1,
                'language_fluency2' => $request->language_fluency2,
                'language_fluency3' => $request->language_fluency3,
                'language_fluency4' => $request->language_fluency4,
                'language_fluency5' => $request->language_fluency5,
                'language_fluency6' => $request->language_fluency6,
                'language_fluency7' => $request->language_fluency7,
                'language_fluency8' => $request->language_fluency8,
                'language_fluency9' => $request->language_fluency9,
                'language_fluency10' => $request->language_fluency10,
                'language_fluency11' => $request->language_fluency11,
                'language_fluency12' => $request->language_fluency12,
                'language_fluency13' => $request->language_fluency13,
                'language_fluency14' => $request->language_fluency14,
                'language_fluency15' => $request->language_fluency15,
                'language_fluency16' => $request->language_fluency16,
                'language_fluency17' => $request->language_fluency17,
                'language_fluency18' => $request->language_fluency18,
                'language_fluency19' => $request->language_fluency19,
                'language_fluency20' => $request->language_fluency20,
                'all_words' => $request->all_words,
                'repeat_words' => $request->repeat_words,
                'non_animal_words' => $request->non_animal_words,
                'language_fluency_score' => $request->language_fluency_score,
                'orientation_day' => $request->orientation_day,
                'orientation_month' => $request->orientation_month,
                'orientation_year' => $request->orientation_year,
                'orientation_week' => $request->orientation_week,
                'orientation_place' => $request->orientation_place,
                'orientation_area' => $request->orientation_area,
                'orientation_score' => $request->orientation_score,
                'face_word' => $request->face_word,
                'velvet_word' => $request->velvet_word,
                'church_word' => $request->church_word,
                'daisy_word' => $request->daisy_word,
                'red_word' => $request->red_word,
                'delayed_memory_score' => $request->delayed_memory_score,
                'category_percentile' => $request->category_percentile,
                'total_moca_score' => $request->total_moca_score,
                'education_level' => $request->education_level,
            ]
        );

        // auto assign case type
        $qualtric = QualtricsForm::where('assessment_case_id', $id)->first();
        $social_worker = SocialWorkerForm::where('assessment_case_id', $id)->first();
        if ($request->category_percentile > 1 && $qualtric && $social_worker) {
            if ($qualtric->timedup_sec < 30 && $social_worker->diagnosed_dementia == 0) {
                //set cga_type
                $cga_type = 'CGA Health Coaching';
                //update cga_type to elderly service
                $assessment_case = AssessmentCase::where('id', $id)->first();
                $response_status = $this->wiringService->changeCgaType($assessment_case->case_id, $cga_type);
                if ($response_status != 200) {
                    error_log('Failed update cga_type with case id ' . $assessment_case->case_id);
                }
            }
        } elseif ($request->category_percentile <= 1 && $qualtric && $social_worker) {
            if ($qualtric->timedup_sec >= 30 && $social_worker->diagnosed_dementia == 1) {
                $cga_type = 'CGA Nurse Program';
                $assessment_case = AssessmentCase::where('id', $id)->first();
                $response_status = $this->wiringService->changeCgaType($assessment_case->case_id, $cga_type);
                if ($response_status != 200) {
                    error_log('Failed update cga_type with case id ' . $assessment_case->case_id);
                }
            }
        }

        return $form;
    }

    public function updateOrCreatePhysicalCondition(Request $request, $id)
    {
        $this->validator->validatePhysicalCondition($request);
        $form = PhysicalConditionForm::updateOrCreate(
            ['assessment_case_id' => $id],
            [
                // General Condition
                'general_condition' => $request->general_condition,
                'eye_opening_response' => $request->eye_opening_response,
                'verbal_response' => $request->verbal_response,
                'motor_response' => $request->motor_response,
                'glasgow_score' => $request->glasgow_score,

                // Mental State
                'mental_state' => $request->mental_state,
                'edu_percentile' => $request->edu_percentile,
                'moca_score' => $request->moca_score,

                // Emotional State
                'emotional_state' => $request->emotional_state,
                'geriatric_score' => $request->geriatric_score,

                // Sensory
                'is_good' => $request->is_good,
                'is_deaf' => $request->is_deaf,
                'dumb_left' => $request->dumb_left,
                'dumb_right' => $request->dumb_right,
                'non_verbal' => $request->non_verbal,
                'is_visual_impaired' => $request->is_visual_impaired,
                'blind_left' => $request->blind_left,
                'blind_right' => $request->blind_right,
                'no_vision' => $request->no_vision,
                'is_assistive_devices' => $request->is_assistive_devices,
                'denture' => $request->denture,
                'hearing_aid' => $request->hearing_aid,
                'glasses' => $request->glasses,

                // Nutrition
                'dat_special_diet' => $request->dat_special_diet,
                'special_diet' => $request->special_diet,
                'is_special_feeding' => $request->is_special_feeding,
                'special_feeding' => $request->special_feeding,
                'thickener_formula' => $request->thickener_formula,
                'fluid_restriction' => $request->fluid_restriction,
                'tube_next_change' => $request->tube_next_change,
                'milk_formula' => $request->milk_formula,
                'milk_regime' => $request->milk_regime,
                'feeding_person' => $request->feeding_person,
                'feeding_person_text' => $request->feeding_person_text,
                'feeding_technique' => $request->feeding_technique,
                'ng_tube' => $request->ng_tube,

                // Skin Condition
                'intact_abnormal' => $request->intact_abnormal,
                'is_napkin_associated' => $request->is_napkin_associated,
                'is_dry' => $request->is_dry,
                'is_cellulitis' => $request->is_cellulitis,
                'cellulitis_desc' => $request->cellulitis_desc,
                'is_eczema' => $request->is_eczema,
                'eczema_desc' => $request->eczema_desc,
                'is_scalp' => $request->is_scalp,
                'scalp_desc' => $request->scalp_desc,
                'is_itchy' => $request->is_itchy,
                'itchy_desc' => $request->itchy_desc,
                'is_wound' => $request->is_wound,
                'wound_desc' => $request->wound_desc,
                'wound_size' => $request->wound_size,
                'tunneling_time' => $request->tunneling_time,
                'wound_bed' => $request->wound_bed,
                'granulating_tissue' => $request->granulating_tissue,
                'necrotic_tissue' => $request->necrotic_tissue,
                'sloughy_tissue' => $request->sloughy_tissue,
                'other_tissue' => $request->other_tissue,
                'exudate_amount' => $request->exudate_amount,
                'exudate_type' => $request->exudate_type,
                'other_exudate' => $request->other_exudate,
                'surrounding_skin' => $request->surrounding_skin,
                'other_surrounding' => $request->other_surrounding,
                'odor' => $request->odor,
                'pain' => $request->pain,

                // Elimination
                'bowel_habit' => $request->bowel_habit,
                'abnormal_option' => $request->abnormal_option,
                'fi_bowel' => $request->fi_bowel,
                'urinary_habit' => $request->urinary_habit,
                'fi_urine' => $request->fi_urine,
                'urine_device' => $request->urine_device,
                'catheter_type' => $request->catheter_type,
                'catheter_next_change' => $request->catheter_next_change,
                'catheter_size_fr' => $request->catheter_size_fr,

                // Pain
                'is_pain' => $request->is_pain,
                'other_emotional_state' => $request->other_emotional_state,
                'deaf_right' => $request->deaf_right,
                'deaf_left' => $request->deaf_left,
                'visual_impaired_left' => $request->visual_impaired_left,
                'visual_impaired_right' => $request->visual_impaired_right,
                'visual_impaired_both' => $request->visual_impaired_both,
                'sensory_remark' => $request->sensory_remark,
                'other_radiation' => $request->other_radiation,
                'skin_rash' => $request->skin_rash,
                'other_skin_rash' => $request->other_skin_rash,
                'bowel_remark' => $request->bowel_remark,
                'urine_remark' => $request->urine_remark,
                'nutrition_remark' => $request->nutrition_remark,
                'napkin_associated_desc' => $request->napkin_associated_desc,

                // user data
                'created_by' => $request->user_id,
                'updated_by' => $request->user_id,
                'created_by_name' => $request->user_name,
                'updated_by_name' => $request->user_name,
            ]
        );

        $obj_length = count((array) $request->pains);
        $pain_site_list = [[]];
        for ($i = 0; $i < $obj_length; $i++) {
            // Pain
            $pain_site_list[$i]['provoking_factor'] = $request->pains[$i]['provoking_factor'] ? $request->pains[$i]['provoking_factor'] : null;
            $pain_site_list[$i]['pain_location1'] = $request->pains[$i]['pain_location1'] ?: null;
            $pain_site_list[$i]['is_dull'] = $request->pains[$i]['is_dull'] ? $request->pains[$i]['is_dull'] : null;
            $pain_site_list[$i]['is_achy'] = $request->pains[$i]['is_achy'] ? $request->pains[$i]['is_achy'] : null;
            $pain_site_list[$i]['is_sharp'] = $request->pains[$i]['is_sharp'] ? $request->pains[$i]['is_sharp'] : null;
            $pain_site_list[$i]['is_stabbing'] = $request->pains[$i]['is_stabbing'] ? $request->pains[$i]['is_stabbing'] : null;
            $pain_site_list[$i]['stabbing_option'] = $request->pains[$i]['stabbing_option'] ? $request->pains[$i]['stabbing_option'] : null;
            $pain_site_list[$i]['pain_location2'] = $request->pains[$i]['pain_location2'] ? $request->pains[$i]['pain_location2'] : null;
            $pain_site_list[$i]['is_relief'] = $request->pains[$i]['is_relief'] ? $request->pains[$i]['is_relief'] : null;
            $pain_site_list[$i]['what_relief'] = $request->pains[$i]['what_relief'] ? $request->pains[$i]['what_relief'] : null;
            $pain_site_list[$i]['have_relief_method'] = $request->pains[$i]['have_relief_method'] ? $request->pains[$i]['have_relief_method'] : null;
            $pain_site_list[$i]['relief_method'] = $request->pains[$i]['relief_method'] ? $request->pains[$i]['relief_method'] : null;
            $pain_site_list[$i]['other_relief_method'] = $request->pains[$i]['other_relief_method'] ? $request->pains[$i]['other_relief_method'] : null;
            $pain_site_list[$i]['pain_scale'] = $request->pains[$i]['pain_scale'] ? $request->pains[$i]['pain_scale'] : null;
            $pain_site_list[$i]['when_pain'] = $request->pains[$i]['when_pain'] ? $request->pains[$i]['when_pain'] : null;
            $pain_site_list[$i]['affect_adl'] = $request->pains[$i]['affect_adl'] ? $request->pains[$i]['affect_adl'] : null;
            $pain_site_list[$i]['adl_info'] = $request->pains[$i]['adl_info'] ? $request->pains[$i]['adl_info'] : null;
            $pain_site_list[$i]['pain_remark'] = $request->pains[$i]['pain_remark'] ? $request->pains[$i]['pain_remark'] : null;
            $pain_site_list[$i]['is_radiation'] = $request->pains[$i]['is_radiation'] ? $request->pains[$i]['is_radiation'] : null;
            $pain_site_list[$i]['other_radiation'] = $request->pains[$i]['other_radiation'] ? $request->pains[$i]['other_radiation'] : null;
        }

        $pain_site = PainSiteTable::where('physical_condition_form_id', $form->id);

        if (! $pain_site) {
            $form->pains()->createMany($pain_site_list);

            return $form->with('pains')->latest('updated_at')->first();
        }

        $pain_site->where('physical_condition_form_id', $form->id)->delete();
        $form->pains()->createMany($pain_site_list);

        return $form->with('pains')->latest('updated_at')->first();
    }

    public function updateOrCreateQualtrics(Request $request, $id)
    {
        $this->validator->validateQualtrics($request);
        $form = QualtricsForm::updateOrCreate(
            ['assessment_case_id' => $id],
            [
                'assessor_1' => $request->assessor_1,
                'assessor_2' => $request->assessor_2,

                // Health Professional
                // Chronic Disease History
                'no_chronic' => $request->no_chronic,
                'is_hypertension' => $request->is_hypertension,
                'is_heart_disease' => $request->is_heart_disease,
                'is_diabetes' => $request->is_diabetes,
                'is_high_cholesterol' => $request->is_high_cholesterol,
                'is_copd' => $request->is_copd,
                'is_stroke' => $request->is_stroke,
                'is_dementia' => $request->is_dementia,
                'is_cancer' => $request->is_cancer,
                'is_rheumatoid' => $request->is_rheumatoid,
                'is_osteoporosis' => $request->is_osteoporosis,
                'is_gout' => $request->is_gout,
                'is_depression' => $request->is_depression,
                'is_schizophrenia' => $request->is_schizophrenia,
                'is_enlarged_prostate' => $request->is_enlarged_prostate,
                'is_parkinson' => $request->is_parkinson,
                'is_other_disease' => $request->is_other_disease,
                'other_disease' => $request->other_disease,
                'no_followup' => $request->no_followup,
                'is_general_clinic' => $request->is_general_clinic,
                'is_internal_medicine' => $request->is_internal_medicine,
                'is_cardiology' => $request->is_cardiology,
                'is_geriatric' => $request->is_geriatric,
                'is_endocrinology' => $request->is_endocrinology,
                'is_gastroenterology' => $request->is_gastroenterology,
                'is_nephrology' => $request->is_nephrology,
                'is_dep_respiratory' => $request->is_dep_respiratory,
                'is_surgical' => $request->is_surgical,
                'is_psychiatry' => $request->is_psychiatry,
                'is_private_doctor' => $request->is_private_doctor,
                'is_oncology' => $request->is_oncology,
                'is_orthopedics' => $request->is_orthopedics,
                'is_urology' => $request->is_urology,
                'is_opthalmology' => $request->is_opthalmology,
                'is_ent' => $request->is_ent,
                'is_other_followup' => $request->is_other_followup,
                'other_followup' => $request->other_followup,
                'never_surgery' => $request->never_surgery,
                'is_aj_replace' => $request->is_aj_replace,
                'is_cataract' => $request->is_cataract,
                'is_cholecystectomy' => $request->is_cholecystectomy,
                'is_malignant' => $request->is_malignant,
                'is_colectomy' => $request->is_colectomy,
                'is_thyroidectomy' => $request->is_thyroidectomy,
                'is_hysterectomy' => $request->is_hysterectomy,
                'is_thongbo' => $request->is_thongbo,
                'is_pacemaker' => $request->is_pacemaker,
                'is_prostatectomy' => $request->is_prostatectomy,
                'is_other_surgery' => $request->is_other_surgery,
                'other_surgery' => $request->other_surgery,
                'left_ear' => $request->left_ear,
                'right_ear' => $request->right_ear,
                'left_eye' => $request->left_eye,
                'right_eye' => $request->right_eye,
                'hearing_aid' => $request->hearing_aid,
                'walk_aid' => $request->walk_aid,
                'other_walk_aid' => $request->other_walk_aid,
                'amsler_grid' => $request->amsler_grid,
                'cancer_text' => $request->cancer_text,
                'stroke_text' => $request->stroke_text,

                // Medication
                'om_regular_desc' => $request->om_regular_desc,
                'om_needed_desc' => $request->om_needed_desc,
                'tm_regular_desc' => $request->tm_regular_desc,
                'tm_needed_desc' => $request->tm_needed_desc,
                'not_prescribed_med' => $request->not_prescribed_med,
                'forget_med' => $request->forget_med,
                'missing_med' => $request->missing_med,
                'reduce_med' => $request->reduce_med,
                'left_med' => $request->left_med,
                'take_all_med' => $request->take_all_med,
                'stop_med' => $request->stop_med,
                'annoyed_by_med' => $request->annoyed_by_med,
                'diff_rem_med' => $request->diff_rem_med,

                // Pain
                'pain_semester' => $request->pain_semester,
                'other_pain_area' => $request->other_pain_area,
                'pain_level' => $request->pain_level,
                'pain_level_text' => $request->pain_level_text,

                // Fall History and Hospitalization
                'have_fallen' => $request->have_fallen,
                'adm_admitted' => $request->adm_admitted,
                'hosp_month' => $request->hosp_month,
                'hosp_year' => $request->hosp_year,
                'hosp_hosp' => $request->hosp_hosp,
                'hosp_hosp_other' => $request->hosp_hosp_other,
                'hosp_way' => $request->hosp_way,
                'hosp_home' => $request->hosp_home,
                'hosp_home_else' => $request->hosp_home_else,
                'hosp_reason' => $request->hosp_reason,
                'hosp_reason_other' => $request->hosp_reason_other,

                'abnormality' => $request->abnormality,
                'other_abnormality' => $request->other_abnormality,

                // Intervention Effectiveness Evaluation
                'ife_action' => $request->ife_action,
                'ife_self_care' => $request->ife_self_care,
                'ife_usual_act' => $request->ife_usual_act,
                'ife_discomfort' => $request->ife_discomfort,
                'ife_anxiety' => $request->ife_anxiety,
                'health_scales' => $request->health_scales,
                'health_scale_other' => $request->health_scale_other,

                // Qualtrics Form Physiological Measurement
                'rest15' => $request->rest15,
                'eathour' => $request->eathour,

                // Physiological Measurement
                'temperature' => $request->temperature,
                'sitting_sbp' => $request->sitting_sbp,
                'sitting_dbp' => $request->sitting_dbp,
                'standing_sbp' => $request->standing_sbp,
                'standing_dbp' => $request->standing_dbp,
                'blood_oxygen' => $request->blood_oxygen,
                'heart_rate' => $request->heart_rate,
                'heart_rythm' => $request->heart_rythm,
                'kardia' => $request->kardia,
                'blood_sugar' => $request->blood_sugar,
                'blood_sugar_time' => $request->blood_sugar_time,
                'waistline' => $request->waistline,
                'weight' => $request->weight,
                'height' => $request->height,
                'respiratory_rate' => $request->respiratory_rate,
                'blood_options' => $request->blood_options,
                'blood_text' => $request->blood_text,
                'meal_text' => $request->meal_text,

                // Re Physiological Measurement
                're_temperature' => $request->re_temperature,
                're_sitting_sbp' => $request->re_sitting_sbp,
                're_sitting_dbp' => $request->re_sitting_dbp,
                're_standing_sbp' => $request->re_standing_sbp,
                're_standing_dbp' => $request->re_standing_dbp,
                're_blood_oxygen' => $request->re_blood_oxygen,
                're_heart_rate' => $request->re_heart_rate,
                're_heart_rythm' => $request->re_heart_rythm,
                're_kardia' => $request->re_kardia,
                're_blood_sugar' => $request->re_blood_sugar,
                're_blood_sugar_time' => $request->re_blood_sugar_time,
                're_waistline' => $request->re_waistline,
                're_weight' => $request->re_weight,
                're_height' => $request->re_height,
                're_respiratory_rate' => $request->re_respiratory_rate,
                're_blood_options' => $request->re_blood_options,
                're_blood_text' => $request->re_blood_text,
                're_meal_text' => $request->re_meal_text,

                // Fall Risk
                'timedup_test' => $request->timedup_test,
                'timedup_test_skip' => $request->timedup_test_skip,
                'timeup_device' => $request->timeup_device,
                'timedup_other' => $request->timedup_other,
                'timedup_sec' => $request->timedup_sec,
                'timedup_sec_desc' => $request->timedup_sec_desc,
                'tr_none' => $request->tr_none,
                'tr_stopped' => $request->tr_stopped,
                'tr_impaired' => $request->tr_impaired,
                'tr_others' => $request->tr_others,
                // 'timedup_remark' => $request->timedup_remark,
                'timeup_remark_others' => $request->timeup_remark_others,
                'singlestart_sts' => $request->singlestart_sts,
                'singlestart_skip' => $request->singlestart_skip,
                'left_sts' => $request->left_sts,
                'right_sts' => $request->right_sts,

                // Qualtrics Remarks
                'qualtrics_remarks' => $request->qualtrics_remarks,

                // Free Text
                'fallrisk_fa' => $request->fallrisk_fa,
                'fallrisk_rs' => $request->fallrisk_rs,
                'hosp_fa' => $request->hosp_fa,
                'hosp_rs' => $request->hosp_rs,
                'remark_fa' => $request->remark_fa,
                'remark_rs' => $request->remark_rs,

                // user data
                'created_by' => $request->user_id,
                'updated_by' => $request->user_id,
                'created_by_name' => $request->user_name,
                'updated_by_name' => $request->user_name,
            ]
        );

        $obj_length = count((array) $request->hosp_month);
        $hospitalization_list = [[]];
        for ($i = 0; $i < $obj_length; $i++) {
            // Pain
            $hospitalization_list[$i]['hosp_month'] = isset($request->hosp_month[$i]) ? $request->hosp_month[$i] : null;
            $hospitalization_list[$i]['hosp_year'] = isset($request->hosp_year[$i]) ? $request->hosp_year[$i] : null;
            $hospitalization_list[$i]['hosp_hosp'] = isset($request->hosp_hosp[$i]) ? $request->hosp_hosp[$i] : null;
            $hospitalization_list[$i]['hosp_hosp_other'] = isset($request->hosp_hosp_other[$i]) ? $request->hosp_hosp_other[$i] : null;
            $hospitalization_list[$i]['hosp_way'] = isset($request->hosp_way[$i]) ? $request->hosp_way[$i] : null;
            $hospitalization_list[$i]['hosp_home'] = isset($request->hosp_home[$i]) ? $request->hosp_home[$i] : null;
            $hospitalization_list[$i]['hosp_home_else'] = isset($request->hosp_home_else[$i]) ? $request->hosp_home_else[$i] : null;
            $hospitalization_list[$i]['hosp_reason'] = isset($request->hosp_reason[$i]) ? $request->hosp_reason[$i] : null;
            $hospitalization_list[$i]['hosp_reason_other'] = isset($request->hosp_reason_other[$i]) ? $request->hosp_reason_other[$i] : null;
        }

        $hospitalization = HospitalizationTables::where('qualtrics_form_id', $form->id);
        $MCA = MontrealCognitiveAssessmentForm::where('assessment_case_id', $id)->first();
        $social_worker = SocialWorkerForm::where('assessment_case_id', $id)->first();

        if ($request->timedup_sec < 30 && $MCA && $social_worker) {
            if ($MCA->category_percentile > 1 && $social_worker->diagnosed_dementia == 0) {
                //set cga_type
                $cga_type = 'CGA Health Coaching';
                //update cga_type to elderly service
                $assessment_case = AssessmentCase::where('id', $id)->first();
                $response_status = $this->wiringService->changeCgaType($assessment_case->case_id, $cga_type);
                if ($response_status != 200) {
                    error_log('Failed update cga_type with case id ' . $assessment_case->case_id);
                }
            }
        } elseif ($request->timedup_sec >= 30 && $MCA && $social_worker) {
            if ($MCA->category_percentile <= 1 && $social_worker->diagnosed_dementia == 1) {
                $cga_type = 'CGA Nurse Program';
                $assessment_case = AssessmentCase::where('id', $id)->first();
                $response_status = $this->wiringService->changeCgaType($assessment_case->case_id, $cga_type);
                if ($response_status != 200) {
                    error_log('Failed update cga_type with case id ' . $assessment_case->case_id);
                }
            }
        }

        if ($obj_length > 0 && ! $hospitalization) {
            $form->hospitalizationTables()->createMany($hospitalization_list);
        } elseif ($obj_length > 0 && $hospitalization) {
            $hospitalization->where('qualtrics_form_id', $form->id)->delete();
            $form->hospitalizationTables()->createMany($hospitalization_list);
        } elseif ($obj_length == 0 && $hospitalization) {
            $hospitalization->where('qualtrics_form_id', $form->id)->delete();
        } elseif ($obj_length == 0 && ! $hospitalization) {
            $hospitalization->where('qualtrics_form_id', $form->id)->delete();
        }

        $obj_length_wa = count((array) $request->walk_aid);
        $walk_aid_list = [[]];
        for ($i = 0; $i < $obj_length_wa; $i++) {
            // Pain
            $walk_aid_list[$i]['walk_aid'] = isset($request->walk_aid[$i]) ? $request->walk_aid[$i] : null;
        }

        $walk_aid = WalkAid::where('qualtrics_form_id', $form->id);

        if ($obj_length_wa > 0 && ! $walk_aid) {
            $form->walkAids()->createMany($walk_aid_list);
        } elseif ($obj_length_wa > 0 && $walk_aid) {
            $walk_aid->where('qualtrics_form_id', $form->id)->delete();
            $form->walkAids()->createMany($walk_aid_list);
        } elseif ($obj_length_wa == 0 && $walk_aid) {
            $walk_aid->where('qualtrics_form_id', $form->id)->delete();
        } elseif ($obj_length_wa == 0 && ! $walk_aid) {
            $walk_aid->where('qualtrics_form_id', $form->id)->delete();
        }

        return $form->with('hospitalizationTables', 'walkAids')->latest('updated_at')->first();
    }

    public function updateOrCreateSocialWorker(Request $request, $id)
    {
        $this->validator->validateSocialWorker($request);
        $form = SocialWorkerForm::updateOrCreate(
            ['assessment_case_id' => $id],
            [
                'assessor_1' => $request->assessor_1,
                'assessor_2' => $request->assessor_2,

                // Social Worker
                // Elderly Information
                'elder_marital' => $request->elder_marital,
                'elder_living' => $request->elder_living,
                'elder_carer' => $request->elder_carer,
                'elder_is_carer' => $request->elder_is_carer,
                'elder_edu' => $request->elder_edu,
                'elder_religious' => $request->elder_religious,
                'elder_housetype' => $request->elder_housetype,
                'elder_bell' => $request->elder_bell,
                'elder_home_fall' => $request->elder_home_fall,
                'elder_home_hygiene' => $request->elder_home_hygiene,
                'elder_home_bug' => $request->elder_home_bug,

                // Social Service
                'elderly_center' => $request->elderly_center,
                'home_service' => $request->home_service,
                'elderly_daycare' => $request->elderly_daycare,
                'longterm_service' => $request->longterm_service,
                'life_support' => $request->life_support,
                'financial_support' => $request->financial_support,

                // Lifestyle
                'spesific_program' => $request->spesific_program,
                'high_cardio20' => $request->high_cardio20,
                'low_cardio40' => $request->low_cardio40,
                'recreation' => $request->recreation,
                'streching3w' => $request->streching3w,
                'daily_workout' => $request->daily_workout,
                'ate_fruit24' => $request->ate_fruit24,
                'ate_veggie35' => $request->ate_veggie35,
                'ate_dairy23' => $request->ate_dairy23,
                'ate_protein23' => $request->ate_protein23,
                'have_breakfast' => $request->have_breakfast,
                'smoking_behavior' => $request->smoking_behavior,
                'alcohol_frequent' => $request->alcohol_frequent,

                // Functional
                'diff_wearing' => $request->diff_wearing,
                'diff_bathing' => $request->diff_bathing,
                'diff_eating' => $request->diff_eating,
                'diff_wakeup' => $request->diff_wakeup,
                'diff_toilet' => $request->diff_toilet,
                'diff_urine' => $request->diff_urine,
                'can_use_phone' => $request->can_use_phone,
                'text_use_phone' => $request->text_use_phone,
                'can_take_ride' => $request->can_take_ride,
                'text_take_ride' => $request->text_take_ride,
                'can_buy_food' => $request->can_buy_food,
                'text_buy_food' => $request->text_buy_food,
                'can_cook' => $request->can_cook,
                'text_cook' => $request->text_cook,
                'can_do_housework' => $request->can_do_housework,
                'text_do_housework' => $request->text_do_housework,
                'can_do_repairment' => $request->can_do_repairment,
                'text_do_repairment' => $request->text_do_repairment,
                'can_do_laundry' => $request->can_do_laundry,
                'text_do_laundry' => $request->text_do_laundry,
                'can_take_medicine' => $request->can_take_medicine,
                'text_take_medicine' => $request->text_take_medicine,
                'can_handle_finances' => $request->can_handle_finances,
                'text_handle_finances' => $request->text_handle_finances,
                'iadl_total_score' => $request->iadl_total_score,

                // Cognitive
                'moca_edu' => $request->moca_edu,

                // Psycho Social
                'relatives_sum' => $request->relatives_sum,
                'relatives_to_talk' => $request->relatives_to_talk,
                'relatives_to_help' => $request->relatives_to_help,
                'friends_sum' => $request->friends_sum,
                'friends_to_talk' => $request->friends_to_talk,
                'friends_to_help' => $request->friends_to_help,
                'lubben_total_score' => $request->lubben_total_score,
                'genogram_done' => $request->genogram_done,
                'less_friend' => $request->less_friend,
                'feel_ignored' => $request->feel_ignored,
                'feel_lonely' => $request->feel_lonely,
                'most_time_good_mood' => $request->most_time_good_mood,
                'irritable_and_fidgety' => $request->irritable_and_fidgety,
                'good_to_be_alive' => $request->good_to_be_alive,
                'feeling_down' => $request->feeling_down,
                'gds4_score' => $request->gds4_score,

                // Stratification & Remark
                // 'do_referral' => $request->do_referral,
                'diagnosed_dementia' => $request->diagnosed_dementia,
                'suggest' => $request->suggest,
                'not_suitable' => $request->not_suitable,
                'sw_remark' => $request->sw_remark,

                // Free Text
                'social_fa' => $request->social_fa,
                'social_rs' => $request->social_rs,
                'stratification_fa' => $request->stratification_fa,
                'stratification_rs' => $request->stratification_rs,
                'psycho_fa' => $request->psycho_fa,
                'psycho_rs' => $request->psycho_rs,
                'cognitive_fa' => $request->cognitive_fa,
                'cognitive_rs' => $request->cognitive_rs,

                // Some Text
                'elder_edu_text' => $request->elder_edu_text,
                'elder_living_text' => $request->elder_living_text,
                'elder_religious_text' => $request->elder_religious_text,
                'elder_housetype_text' => $request->elder_housetype_text,
                'elder_home_fall_text' => $request->elder_home_fall_text,
                'elder_home_hygiene_text' => $request->elder_home_hygiene_text,
                'home_service_text' => $request->home_service_text,
                'referral_other_text' => $request->referral_other_text,

                'social_5' => $request->social_5,

                // user data
                'created_by' => $request->user_id,
                'updated_by' => $request->user_id,
                'created_by_name' => $request->user_name,
                'updated_by_name' => $request->user_name,
            ]
        );

        $MCA = MontrealCognitiveAssessmentForm::where('assessment_case_id', $id)->first();
        $qualtrics = QualtricsForm::where('assessment_case_id', $id)->first();
        if ($request->diagnosed_dementia == 0 && $MCA && $qualtrics) {
            if ($MCA->category_percentile > 1 && $qualtrics->timedup_sec < 30) {
                //set cga_type
                $cga_type = 'CGA Health Coaching';
                //update cga_type to elderly service
                $assessment_case = AssessmentCase::where('id', $id)->first();
                $response_status = $this->wiringService->changeCgaType($assessment_case->case_id, $cga_type);
                if ($response_status != 200) {
                    error_log('Failed update cga_type with case id ' . $assessment_case->case_id);
                }
            }
        } elseif ($request->diagnozed_dementia == 1 && $MCA && $qualtrics) {
            if ($MCA->category_percentile <= 1 && $qualtrics->timedup_sec >= 30) {
                $cga_type = 'CGA Nurse Program';
                $assessment_case = AssessmentCase::where('id', $id)->first();
                $response_status = $this->wiringService->changeCgaType($assessment_case->case_id, $cga_type);
                if ($response_status != 200) {
                    error_log('Failed update cga_type with case id ' . $assessment_case->case_id);
                }
            }
        }

        // Do Referral
        $obj_length = count((array) $request->do_referral);
        $do_referral_list = [[]];
        for ($i = 0; $i < $obj_length; $i++) {
            $do_referral_list[$i]['do_referral'] = $request->do_referral[$i];
        }

        $do_referral = DoReferralTables::where('social_worker_form_id', $form->id);

        if ($obj_length > 0 && ! $do_referral) {
            $form->doReferral()->createMany($do_referral_list);
        } elseif ($obj_length > 0 && $do_referral) {
            $do_referral->where('social_worker_form_id', $form->id)->delete();
            $form->doReferral()->createMany($do_referral_list);
        } elseif ($obj_length == 0 && $do_referral) {
            $do_referral->where('social_worker_form_id', $form->id)->delete();
        } elseif ($obj_length == 0 && ! $do_referral) {
            $do_referral->where('social_worker_form_id', $form->id)->delete();
        }

        // Home Fall
        $obj_length_hf = count((array) $request->elder_home_fall);
        $elder_home_fall_list = [[]];
        for ($i = 0; $i < $obj_length_hf; $i++) {
            $elder_home_fall_list[$i]['elder_home_fall'] = $request->elder_home_fall[$i];
        }

        $elder_home_fall = HomeFall::where('social_worker_form_id', $form->id);

        if ($obj_length_hf > 0 && ! $elder_home_fall) {
            $form->elderHomeFall()->createMany($elder_home_fall_list);
        } elseif ($obj_length_hf > 0 && $elder_home_fall) {
            $elder_home_fall->where('social_worker_form_id', $form->id)->delete();
            $form->elderHomeFall()->createMany($elder_home_fall_list);
        } elseif ($obj_length_hf == 0 && $elder_home_fall) {
            $elder_home_fall->where('social_worker_form_id', $form->id)->delete();
        } elseif ($obj_length_hf == 0 && ! $elder_home_fall) {
            $elder_home_fall->where('social_worker_form_id', $form->id)->delete();
        }

        // Home Hygiene
        $obj_length_hh = count((array) $request->elder_home_hygiene);
        $elder_home_hygiene_list = [[]];
        for ($i = 0; $i < $obj_length_hh; $i++) {
            $elder_home_hygiene_list[$i]['elder_home_hygiene'] = $request->elder_home_hygiene[$i];
        }

        $elder_home_hygiene = HomeHygiene::where('social_worker_form_id', $form->id);

        if ($obj_length_hh > 0 && ! $elder_home_hygiene) {
            $form->elderHomeHygiene()->createMany($elder_home_hygiene_list);
        } elseif ($obj_length_hh > 0 && $elder_home_hygiene) {
            $elder_home_hygiene->where('social_worker_form_id', $form->id)->delete();
            $form->elderHomeHygiene()->createMany($elder_home_hygiene_list);
        } elseif ($obj_length_hh == 0 && $elder_home_hygiene) {
            $elder_home_hygiene->where('social_worker_form_id', $form->id)->delete();
        } elseif ($obj_length_hh == 0 && ! $elder_home_hygiene) {
            $elder_home_hygiene->where('social_worker_form_id', $form->id)->delete();
        }

        // Home Service
        $obj_length_hs = count((array) $request->home_service);
        $home_service_list = [[]];
        for ($i = 0; $i < $obj_length_hs; $i++) {
            $home_service_list[$i]['home_service'] = $request->home_service[$i];
        }

        $home_service = HomeService::where('social_worker_form_id', $form->id);

        if ($obj_length_hs > 0 && ! $home_service) {
            $form->homeService()->createMany($home_service_list);
        } elseif ($obj_length_hs > 0 && $home_service) {
            $home_service->where('social_worker_form_id', $form->id)->delete();
            $form->homeService()->createMany($home_service_list);
        } elseif ($obj_length_hs == 0 && $home_service) {
            $home_service->where('social_worker_form_id', $form->id)->delete();
        } elseif ($obj_length_hs == 0 && ! $home_service) {
            $home_service->where('social_worker_form_id', $form->id)->delete();
        }

        // Life Support
        $obj_length_ls = count((array) $request->life_support);
        $life_support_list = [[]];
        for ($i = 0; $i < $obj_length_ls; $i++) {
            $life_support_list[$i]['life_support'] = $request->life_support[$i];
        }

        $life_support = LifeSupport::where('social_worker_form_id', $form->id);

        if ($obj_length_ls > 0 && ! $life_support) {
            $form->lifeSupport()->createMany($life_support_list);
        } elseif ($obj_length_ls > 0 && $life_support) {
            $life_support->where('social_worker_form_id', $form->id)->delete();
            $form->lifeSupport()->createMany($life_support_list);
        } elseif ($obj_length_ls == 0 && $life_support) {
            $life_support->where('social_worker_form_id', $form->id)->delete();
        } elseif ($obj_length_ls == 0 && ! $life_support) {
            $life_support->where('social_worker_form_id', $form->id)->delete();
        }

        // Elder Living
        $obj_length_el = count((array) $request->elder_living);
        $elder_living_list = [[]];
        for ($i = 0; $i < $obj_length_el; $i++) {
            $elder_living_list[$i]['elder_living'] = $request->elder_living[$i];
        }

        $elder_living = ElderLiving::where('social_worker_form_id', $form->id);

        if ($obj_length_el > 0 && ! $elder_living) {
            $form->elderLiving()->createMany($elder_living_list);
        } elseif ($obj_length_el > 0 && $elder_living) {
            $elder_living->where('social_worker_form_id', $form->id)->delete();
            $form->elderLiving()->createMany($elder_living_list);
        } elseif ($obj_length_el == 0 && $elder_living) {
            $elder_living->where('social_worker_form_id', $form->id)->delete();
        } elseif ($obj_length_el == 0 && ! $elder_living) {
            $elder_living->where('social_worker_form_id', $form->id)->delete();
        }

        return $form->with(['elderHomeFall', 'elderHomeHygiene', 'homeService', 'lifeSupport', 'elderLiving', 'doReferral'])->latest('updated_at')->first();
    }

    public function updateOrCreateAssessmentCaseStatus(Request $request, $id)
    {
        $form = AssessmentCaseStatus::updateOrCreate(
            ['assessment_case_id' => $id],
            [
                'status' => $request->status,
                'remarks' => $request->remarks,

                // user data
                'created_by' => $request->user_id,
                'updated_by' => $request->user_id,
                'created_by_name' => $request->user_name,
                'updated_by_name' => $request->user_name,
            ]
        );

        //call elder service to change cga_type in cases. no need change case status
        $case_status = null;
        $cga_type = null;

        switch ($request->status) {
            case '1':
                $case_status = '成功完成 Baseline';
                $cga_type = 'Baseline';
                break;
            case '2':
                $case_status = '未能成功完成 Baseline';
                $cga_type = 'Baseline';
                break;
            case '3':
                $case_status = '已分配HC, 待跟進';
                $cga_type = 'CGA Health Coaching';
                break;
            case '4':
                $case_status = '已分配Nurse Program, 待跟進';
                $cga_type = 'CGA Nurse Program';
                break;
            case '5':
                $case_status = '不獲分配個案';
                $cga_type = 'Baseline';
                break;
            default:
                $case_status = null;
                $cga_type = 'Baseline';
                break;
        }
        $type = $request->type;
        if ($type && ($type == 'bzn' || $type == 'BZN')) {
            $cga_type = null;
        }

        $assessment_case = AssessmentCase::where('id', $id)->first();
        $response_status = $this->wiringService->changeCgaType($assessment_case->case_id, $cga_type);
        if ($response_status != 200) {
            error_log('Failed update cga_type with case id ' . $assessment_case->case_id);
        }

        return $form;
    }
}
