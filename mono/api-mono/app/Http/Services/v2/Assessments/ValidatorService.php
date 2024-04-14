<?php

namespace App\Http\Services\v2\Assessments;

use App\Rules\MedicationAdherenceRule;

class ValidatorService
{
    // Physiological measurement
    public function validatePhysiologicalMeasurement($request)
    {
        return $request->validate([
            'temperature' => ['nullable', 'numeric', 'min:25.00', 'max:48.00'],
            'sitting_sbp' => ['nullable', 'integer', 'min:10', 'max:300'],
            'sitting_dbp' => ['nullable', 'integer', 'min:10', 'max:300'],
            'standing_sbp' => ['nullable', 'integer', 'min:10', 'max:300'],
            'standing_dbp' => ['nullable', 'integer', 'min:10', 'max:300'],
            'blood_oxygen' => ['nullable', 'integer', 'min:0', 'max:100'],
            'heart_rate' => ['nullable', 'integer', 'min:0', 'max:300'],
            'heart_rythm' => ['nullable', 'integer', 'min:1', 'max:2'],
            'kardia' => ['nullable', 'integer', 'min:1', 'max:4'],
            'blood_sugar' => ['nullable', 'numeric', 'min:0', 'max:35'],
            'blood_sugar_time' => ['nullable', 'integer', 'min:1', 'max:4'],
            'waistline' => ['nullable', 'numeric', 'min:20.00', 'max:250.00'],
            'weight' => ['nullable', 'numeric', 'min:10.00', 'max:200.00'],
            'height' => ['nullable', 'numeric', 'min:1.00', 'max:3.00'],
            'respiratory_rate' => ['nullable', 'integer', 'min:0', 'max:50'],
            'blood_options' => ['nullable', 'integer'],
            'blood_text' => ['nullable', 'string'],
            'meal_text' => ['nullable', 'string'],
        ]);
    }

    // Re Physiological measurement
    public function validateRePhysiologicalMeasurement($request)
    {
        return $request->validate([
            're_temperature' => ['nullable', 'numeric', 'min:25.00', 'max:48.00'],
            're_sitting_sbp' => ['nullable', 'integer', 'min:10', 'max:300'],
            're_sitting_dbp' => ['nullable', 'integer', 'min:10', 'max:300'],
            're_standing_sbp' => ['nullable', 'integer', 'min:10', 'max:300'],
            're_standing_dbp' => ['nullable', 'integer', 'min:10', 'max:300'],
            're_blood_oxygen' => ['nullable', 'integer', 'min:0', 'max:100'],
            're_heart_rate' => ['nullable', 'integer', 'min:0', 'max:300'],
            're_heart_rythm' => ['nullable', 'integer', 'min:1', 'max:2'],
            're_kardia' => ['nullable', 'integer', 'min:1', 'max:4'],
            're_blood_sugar' => ['nullable', 'numeric', 'min:0', 'max:35'],
            're_blood_sugar_time' => ['nullable', 'integer', 'min:1', 'max:4'],
            're_waistline' => ['nullable', 'numeric', 'min:20.00', 'max:250.00'],
            're_weight' => ['nullable', 'numeric', 'min:10.00', 'max:200.00'],
            're_height' => ['nullable', 'numeric', 'min:1', 'max:3'],
            're_respiratory_rate' => ['nullable', 'integer', 'min:0', 'max:50'],
            're_blood_options' => ['nullable', 'integer'],
            're_blood_text' => ['nullable', 'string'],
            're_meal_text' => ['nullable', 'string'],
        ]);
    }

    // Medical condition
    public function validateMedicalCondition($request, $id)
    {
        return $request->validate([
            'complaint' => ['nullable', 'array'],
            'complaint.*' => ['nullable', 'string'],
            'has_medical_history' => ['nullable', 'boolean'],
            'medical_history' => ['nullable', 'array'],
            'medical_history.*' => ['nullable', 'string'],
            'premorbid' => ['nullable', 'integer'],
            'premorbid_start_month' => ['nullable', 'integer', 'min:1', 'max:12'],
            'premorbid_start_year' => ['nullable', 'integer', 'min:1900', 'max:' . date('Y') + 1],
            'premorbid_end_month' => ['nullable', 'integer', 'min:1', 'max:12'],
            'premorbid_end_year' => ['nullable', 'integer', 'min:1900', 'max:' . date('Y') + 1],
            'followup_appointment' => ['nullable', 'string'],
            'has_food_allergy' => ['nullable', 'boolean'],
            'food_allergy_description' => ['nullable', 'string'],
            'has_drug_allergy' => ['nullable', 'boolean'],
            'drug_allergy_description' => ['nullable', 'string'],
            'has_medication' => ['nullable', 'boolean', new MedicationAdherenceRule($id)],
            'medication_description' => ['nullable', 'string'],
            'other_complaint' => ['nullable', 'string'],
            'other_medical_history' => ['nullable', 'string'],
            'premorbid_condition' => ['nullable', 'string'],
        ]);
    }

    // Medication adherence
    public function validateMedicationAdherence($request)
    {
        return $request->validate([
            'is_forget_sometimes' => ['nullable', 'boolean'],
            'is_missed_meds' => ['nullable', 'boolean'],
            'is_reduce_meds' => ['nullable', 'boolean'],
            'is_forget_when_travel' => ['nullable', 'boolean'],
            'is_meds_yesterday' => ['nullable', 'boolean'],
            'is_stop_when_better' => ['nullable', 'boolean'],
            'is_annoyed' => ['nullable', 'boolean'],
            'forget_sometimes' => ['nullable', 'string'],
            'missed_meds' => ['nullable', 'string'],
            'reduce_meds' => ['nullable', 'string'],
            'forget_when_travel' => ['nullable', 'string'],
            'meds_yesterday' => ['nullable', 'string'],
            'stop_when_better' => ['nullable', 'string'],
            'annoyed' => ['nullable', 'string'],
            'forget_frequency' => ['nullable', 'integer', 'min:1', 'max:5'],
            'total_mmas_score' => ['nullable', 'numeric', 'min:0'],
        ]);
    }

    // Geriatric Depression Scale Form Validator
    public function validateGeriatricDepressionScale($request)
    {
        return $request->validate([
            'assessment_case_id' => ['nullable', 'integer', 'exists:assessment_cases,id'],
            'elderly_central_ref_number' => ['nullable', 'string'],
            'assessment_date' => ['nullable', 'date_format:Y-m-d'],
            'assessor_name' => ['nullable', 'string'],
            'assessment_kind' => ['nullable', 'integer'],
            'is_satisfied' => ['nullable', 'integer'],
            'is_given_up' => ['nullable', 'integer'],
            'is_feel_empty' => ['nullable', 'integer'],
            'is_often_bored' => ['nullable', 'integer'],
            'is_happy_a_lot' => ['nullable', 'integer'],
            'is_affraid' => ['nullable', 'integer'],
            'is_happy_all_day' => ['nullable', 'integer'],
            'is_feel_helpless' => ['nullable', 'integer'],
            'is_prefer_stay' => ['nullable', 'integer'],
            'is_memory_problem' => ['nullable', 'integer'],
            'is_good_to_alive' => ['nullable', 'integer'],
            'is_feel_useless' => ['nullable', 'integer'],
            'is_feel_energic' => ['nullable', 'integer'],
            'is_hopeless' => ['nullable', 'integer'],
            'is_people_better' => ['nullable', 'integer'],
            'gds15_score' => ['nullable', 'integer', 'min:0', 'max:15'],
        ]);
    }

    // Barthel Index Form Validator
    public function validateBarthelIndex($request)
    {
        return $request->validate([
            'assessment_case_id' => ['nullable', 'integer', 'exists:assessment_cases,id'],
            'bowels' => ['nullable', 'integer', 'min:0', 'max:2'],
            'bladder' => ['nullable', 'integer', 'min:0', 'max:2'],
            'grooming' => ['nullable', 'integer', 'min:0', 'max:1'],
            'toilet_use' => ['nullable', 'integer', 'min:0', 'max:2'],
            'feeding' => ['nullable', 'integer', 'min:0', 'max:2'],
            'transfer' => ['nullable', 'integer', 'min:0', 'max:3'],
            'mobility' => ['nullable', 'integer', 'min:0', 'max:3'],
            'dressing' => ['nullable', 'integer', 'min:0', 'max:2'],
            'stairs' => ['nullable', 'integer', 'min:0', 'max:2'],
            'bathing' => ['nullable', 'integer', 'min:0', 'max:1'],
            'barthel_total_score' => ['nullable', 'integer', 'min:0', 'max:20'],
        ]);
    }

    // Lubben Social Network Scale Form Validator
    public function validateLubbenSocialNetworkScale($request)
    {
        return $request->validate([
            'assessment_case_id' => ['nullable', 'integer', 'exists:assessment_cases,id'],
            'elderly_central_ref_number' => ['nullable', 'string'],
            'assessment_date' => ['nullable', 'date_format:Y-m-d'],
            'assessor_name' => ['nullable', 'string'],
            'assessment_kind' => ['nullable', 'integer', 'min:0', 'max:2'],
            'relatives_sum' => ['nullable', 'integer', 'min:0', 'max:5'],
            'relatives_to_talk' => ['nullable', 'integer', 'min:0', 'max:5'],
            'relatives_to_help' => ['nullable', 'integer', 'min:0', 'max:5'],
            'friends_sum' => ['nullable', 'integer', 'min:0', 'max:5'],
            'friends_to_talk' => ['nullable', 'integer', 'min:0', 'max:5'],
            'friends_to_help' => ['nullable', 'integer', 'min:0', 'max:5'],
            'lubben_total_score' => ['nullable', 'integer', 'min:0', 'max:30'],
        ]);
    }

    // Iadl Form Validator
    public function validateIadl($request)
    {
        return $request->validate([
            'assessment_case_id' => ['nullable', 'integer', 'exists:assessment_cases,id'],
            'elderly_central_ref_number' => ['nullable', 'string'],
            'assessment_date' => ['nullable', 'date_format:Y-m-d'],
            'assessor_name' => ['nullable', 'string'],
            'assessment_kind' => ['nullable', 'integer', 'min:0', 'max:2'],
            'can_use_phone' => ['nullable', 'integer', 'min:0', 'max:2'],
            'text_use_phone' => ['nullable', 'string'],
            'can_take_ride' => ['nullable', 'integer', 'min:0', 'max:2'],
            'text_take_ride' => ['nullable', 'string'],
            'can_buy_food' => ['nullable', 'integer', 'min:0', 'max:2'],
            'text_buy_food' => ['nullable', 'string'],
            'can_cook' => ['nullable', 'integer', 'min:0', 'max:2'],
            'text_cook' => ['nullable', 'string'],
            'can_do_housework' => ['nullable', 'integer', 'min:0', 'max:2'],
            'text_do_housework' => ['nullable', 'string'],
            'can_do_repairment' => ['nullable', 'integer', 'min:0', 'max:2'],
            'text_do_repairment' => ['nullable', 'string'],
            'can_do_laundry' => ['nullable', 'integer', 'min:0', 'max:2'],
            'text_do_laundry' => ['nullable', 'string'],
            'can_take_medicine' => ['nullable', 'integer', 'min:0', 'max:2'],
            'text_take_medicine' => ['nullable', 'string'],
            'can_handle_finances' => ['nullable', 'integer', 'min:0', 'max:2'],
            'text_handle_finances' => ['nullable', 'string'],
            'iadl_total_score' => ['nullable', 'integer', 'min:0', 'max:18'],
        ]);
    }

    // Social Background Validator
    public function validateSocialBackground($request)
    {
        return $request->validate([
            'assessment_case_id' => ['nullable', 'integer', 'exists:assessment_cases,id'],
            'marital_status' => ['nullable', 'in: 1, 2, 3, 4'],
            'safety_alarm' => ['nullable', 'boolean'],
            'has_carer' => ['nullable', 'boolean'],
            'carer_option' => ['nullable', 'integer'],
            'carer' => ['nullable', 'string'],
            'employment_status' => ['nullable', 'in: 1, 2, 3, 4'],
            'has_community_resource' => ['nullable', 'boolean'],
            'education_level' => ['nullable', 'in: 1, 2, 3, 4, 5'],
            'financial_state' => ['array', 'nullable'],
            'financial_state.*' => ['nullable', 'integer'],
            'smoking_option' => ['nullable', 'in: 1, 2, 3'],
            'smoking' => ['nullable', 'integer'],
            'drinking_option' => ['nullable', 'in: 1, 2, 3'],
            'drinking' => ['nullable', 'integer'],
            'has_religion' => ['nullable', 'boolean'],
            'religion' => ['nullable', 'string'],
            'has_social_activity' => ['nullable', 'boolean'],
            'social_activity' => ['nullable', 'string'],
            'lubben_total_score' => ['nullable', 'integer', 'min: 0', 'max: 30'],
        ]);
    }

    // Function and Mobility Validator
    public function validateFunctionMobility($request)
    {
        return $request->validate([
            'iadl' => ['nullable', 'in: 1, 2, 3'],
            'total_iadl_score' => ['nullable', 'min: 0', 'max:18'],
            'mobility' => ['nullable', 'in: 1, 2, 3, 4'],
            'walk_with_assistance' => ['nullable', 'required_if: mobility, 2', 'min: 1', 'max: 7'],
            'mobility_tug' => ['nullable', 'required_if: mobility, 2'],
            'left_single_leg' => ['nullable', 'required_if: mobility, 2'],
            'rightt_single_leg' => ['nullable', 'required_if: mobility, 2'],
            'range_of_motion' => ['nullable', 'in: 1, 2'],
            'upper_limb_left' => ['nullable', 'min: 0', 'max: 5'],
            'upper_limb_right' => ['nullable', 'min: 0', 'max: 5'],
            'lower_limb_left' => ['nullable', 'min: 0', 'max: 5'],
            'lower_limb_right' => ['nullable', 'min: 0', 'max: 5'],
            'fall_history' => ['nullable', 'boolean'],
            'number_of_major_fall' => ['nullable', 'integer', 'required_if: fall_history, true'],
            'mi_independent' => ['nullable', 'boolean'],
            'mi_walk_assisst' => ['nullable', 'boolean'],
            'mi_wheelchair_bound' => ['nullable', 'boolean'],
            'mi_bed_bound' => ['nullable', 'boolean'],
            'mo_independent' => ['nullable', 'boolean'],
            'mo_walk_assisst' => ['nullable', 'boolean'],
            'mo_wheelchair_bound' => ['nullable', 'boolean'],
            'mo_bed_bound' => ['nullable', 'boolean'],
            'major_fall_tables' => ['nullable', 'array'],

        ]);
    }

    // MoCA Form Validator
    public function validateMontrealCognitiveAssessment($request)
    {
        return $request->validate([
            'assessment_case_id' => ['nullable', 'integer', 'exists:assessment_cases,id'],
            'orientation_day' => ['nullable', 'integer', 'min:1', 'max:31'],
            'orientation_month' => ['nullable', 'integer', 'min:1', 'max:12'],
            'orientation_year' => ['nullable', 'digits:4', 'integer', 'min:1900', 'max:' . date('Y') + 1],
            'orientation_week' => ['nullable', 'integer', 'min:1', 'max:4'],
            'memory_score' => ['nullable', 'numeric', 'max:5.00'],
            'all_words' => ['nullable', 'numeric', 'max:50.00'],
            'repeat_words' => ['nullable'],
            'non_animal_words' => ['nullable'],
            'language_fluency_score' => ['nullable', 'numeric', 'max:9.00'],
            'orientation_score' => ['nullable', 'numeric', 'max:6.00'],
            'face_word' => ['nullable', 'in: 1, 2, 3'],
            'velvet_word' => ['nullable', 'in: 1, 2, 3'],
            'church_word' => ['nullable', 'in: 1, 2, 3'],
            'daisy_word' => ['nullable', 'in: 1, 2, 3'],
            'red_word' => ['nullable', 'in: 1, 2, 3'],
            'delayed_memory_score' => ['nullable', 'numeric', 'max:10.00'],
            'category_percentile' => ['nullable', 'in: 1, 2, 3, 4'],
            'total_moca_score' => ['nullable', 'numeric', 'max:30.00'],
            'education_level' => ['nullable', 'string'],
        ]);
    }

    // Physical Condition Validator
    public function validatePhysicalCondition($request)
    {
        return $request->validate([
            // General Condition
            'general_condition' => ['nullable', 'min: 1', 'max: 4'],
            'eye_opening_response' => ['nullable', 'min: 1', 'max: 4'],
            'verbal_response' => ['nullable', 'min: 1', 'max: 5'],
            'motor_response' => ['nullable', 'min: 1', 'max: 6'],
            'glasgow_score' => ['nullable', 'min: 0', 'max: 15'],

            // Mental State
            'mental_state' => ['nullable', 'min: 1', 'max: 4'],
            'edu_percentile' => ['nullable', 'min: 1', 'max: 4'],
            'moca_score' => ['nullable', 'min: 0', 'max: 30'],

            // Emotional State
            'emotional_state' => ['nullable', 'min: 1', 'max: 3'],
            'geriatric_score' => ['nullable', 'min: 0', 'max: 30'],

            // Sensory
            'is_good' => ['nullable', 'boolean'],
            'is_deaf' => ['nullable', 'boolean', 'required_if: is_good, false'],
            'dumb_left' => ['nullable', 'boolean', 'required_if: is_deaf, true'],
            'dumb_right' => ['nullable', 'boolean', 'required_if: is_deaf, true'],
            'non_verbal' => ['nullable', 'boolean'],
            'is_visual_impaired' => ['nullable', 'boolean', 'required_if: is_good, false'],
            'blind_left' => ['nullable', 'boolean', 'required_if: is_visual_impaired, true'],
            'blind_right' => ['nullable', 'boolean', 'required_if: is_visual_impaired, true'],
            'no_vision' => ['nullable', 'boolean'],
            'is_assistive_devices' => ['nullable', 'boolean', 'required_if: is_good, false'],
            'denture' => ['nullable', 'boolean', 'required_if: is_assistive_devices, true'],
            'hearing_aid' => ['nullable', 'boolean', 'required_if: is_assistive_devices, true'],
            'glasses' => ['nullable', 'boolean', 'required_if: is_assistive_devices, true'],

            // Pain
            'is_pain' => ['nullable', 'integer'],

            // Nutrition
            'dat_special_diet' => ['nullable', 'integer'],
            'special_diet' => ['nullable', 'string'],
            'is_special_feeding' => ['nullable', 'integer'],
            'special_feeding' => ['nullable', 'required_if: is_special_feeding, true', 'min: 1', 'max: 2'],
            'thickener_formula' => ['nullable', 'string'],
            'fluid_restriction' => ['nullable', 'string'],
            'tube_next_change' => ['nullable', 'string'],
            'ng_tube' => ['nullable', 'string'],
            'milk_formula' => ['nullable', 'string'],
            'milk_regime' => ['nullable', 'string'],
            'feeding_person' => ['nullable', 'integer'],
            'feeding_person_text' => ['nullable', 'string'],
            'feeding_technique' => ['nullable', 'min: 1', 'max: 4'],

            // Skin Condition
            'intact_abnormal' => ['nullable', 'integer'],
            'is_napkin_associated' => ['nullable', 'boolean'],
            'is_dry' => ['nullable', 'boolean'],
            'is_cellulitis' => ['nullable', 'boolean'],
            'cellulitis_desc' => ['nullable', 'string', 'required_if: is_cellulitis, true'],
            'is_eczema' => ['nullable', 'boolean'],
            'eczema_desc' => ['nullable', 'string', 'required_if: is_eczema, true'],
            'is_scalp' => ['nullable', 'boolean'],
            'scalp_desc' => ['nullable', 'string', 'required_if: is_scalp, true'],
            'is_itchy' => ['nullable', 'boolean'],
            'itchy_desc' => ['nullable', 'string', 'required_if: is_itchy, true'],
            'is_wound' => ['nullable', 'boolean'],
            'wound_desc' => ['nullable', 'string', 'required_if: is_wound, true'],
            'wound_size' => ['nullable', 'numeric', 'required_if: is_wound, true', 'max: 100.00'],
            'tunneling_time' => ['nullable', 'string', 'required_if: is_wound, true'],
            'wound_bed' => ['nullable', 'numeric', 'required_if: is_wound, true', 'max: 100.00'],
            'granulating_tissue' => ['nullable', 'numeric', 'required_if: is_wound, true', 'max: 100.00'],
            'necrotic_tissue' => ['nullable', 'numeric', 'required_if: is_wound, true', 'max: 100.00'],
            'sloughy_tissue' => ['nullable', 'numeric', 'required_if: is_wound, true', 'max: 100.00'],
            'other_tissue' => ['nullable', 'numeric', 'required_if: is_wound, true', 'max: 100.00'],
            'exudate_amount' => ['nullable', 'integer', 'min: 1', 'max: 3'],
            'exudate_type' => ['nullable', 'integer', 'min: 1', 'max: 4'],
            'other_exudate' => ['nullable', 'string'],
            'surrounding_skin' => ['nullable', 'min: 1', 'max: 6'],
            'other_surrounding' => ['nullable', 'string'],
            'odor' => ['nullable', 'integer'],
            'pain' => ['nullable', 'integer'],

            // Elimination
            'bowel_habit' => ['nullable', 'integer'],
            'abnormal_option' => ['nullable', 'integer'],
            'fi_bowel' => ['nullable', 'required_if: abnormal_option, 3', 'min: 1', 'max: 2'],
            'urinary_habit' => ['nullable', 'integer'],
            'fi_urine' => ['nullable', 'integer'],
            'urine_device' => ['nullable', 'integer'],
            'catheter_type' => ['nullable', 'required_if: fi_urine, 2', 'in: 1, 2, 3'],
            'catheter_next_change' => ['nullable', 'string'],
            'catheter_size_fr' => ['nullable', 'integer'],

        ]);
    }

    // Qualtrics Form Validator
    public function validateQualtrics($request)
    {
        return $request->validate([
            'assessment_case_id' => ['nullable', 'integer', 'exists:assessment_cases,id'],

            'assessor_1' => ['nullable', 'string'],
            'assessor_2' => ['nullable', 'string'],

            // Health Professional
            // Chronic Disease History
            'no_chronic' => ['nullable', 'boolean'],
            'is_hypertension' => ['nullable', 'boolean'],
            'is_heart_disease' => ['nullable', 'boolean'],
            'is_diabetes' => ['nullable', 'boolean'],
            'is_high_cholesterol' => ['nullable', 'boolean'],
            'is_copd' => ['nullable', 'boolean'],
            'is_stroke' => ['nullable', 'boolean'],
            'is_dementia' => ['nullable', 'boolean'],
            'is_cancer' => ['nullable', 'boolean'],
            'is_rheumatoid' => ['nullable', 'boolean'],
            'is_osteoporosis' => ['nullable', 'boolean'],
            'is_gout' => ['nullable', 'boolean'],
            'is_depression' => ['nullable', 'boolean'],
            'is_schizophrenia' => ['nullable', 'boolean'],
            'is_enlarged_prostate' => ['nullable', 'boolean'],
            'is_parkinson' => ['nullable', 'boolean'],
            'is_other_disease' => ['nullable', 'boolean'],
            'other_disease' => ['nullable', 'string', 'required_if:is_other_disease,true'],
            'no_followup' => ['nullable', 'boolean'],
            'is_general_clinic' => ['nullable', 'boolean'],
            'is_internal_medicine' => ['nullable', 'boolean'],
            'is_cardiology' => ['nullable', 'boolean'],
            'is_geriatric' => ['nullable', 'boolean'],
            'is_endocrinology' => ['nullable', 'boolean'],
            'is_gastroenterology' => ['nullable', 'boolean'],
            'is_nephrology' => ['nullable', 'boolean'],
            'is_dep_respiratory' => ['nullable', 'boolean'],
            'is_surgical' => ['nullable', 'boolean'],
            'is_psychiatry' => ['nullable', 'boolean'],
            'is_private_doctor' => ['nullable', 'boolean'],
            'is_oncology' => ['nullable', 'boolean'],
            'is_orthopedics' => ['nullable', 'boolean'],
            'is_urology' => ['nullable', 'boolean'],
            'is_opthalmology' => ['nullable', 'boolean'],
            'is_ent' => ['nullable', 'boolean'],
            'is_other_followup' => ['nullable', 'boolean'],
            'other_followup' => ['nullable', 'string', 'required_if:is_other_followup,true'],
            'never_surgery' => ['nullable', 'boolean'],
            'is_aj_replace' => ['nullable', 'boolean'],
            'is_cataract' => ['nullable', 'boolean'],
            'is_cholecystectomy' => ['nullable', 'boolean'],
            'is_malignant' => ['nullable', 'boolean'],
            'is_colectomy' => ['nullable', 'boolean'],
            'is_thyroidectomy' => ['nullable', 'boolean'],
            'is_hysterectomy' => ['nullable', 'boolean'],
            'is_thongbo' => ['nullable', 'boolean'],
            'is_pacemaker' => ['nullable', 'boolean'],
            'is_prostatectomy' => ['nullable', 'boolean'],
            'is_other_surgery' => ['nullable', 'boolean'],
            'other_surgery' => ['nullable', 'string', 'required_if:is_other_surgery,true'],
            'left_ear' => ['nullable', 'in: 0, 1, 2'],
            'right_ear' => ['nullable', 'in: 0, 1, 2'],
            'left_eye' => ['nullable', 'in: 0,1, 2'],
            'right_eye' => ['nullable', 'in: 0, 1, 2'],
            'hearing_aid' => ['nullable', 'in: 0, 1'],
            'walk_aid' => ['nullable', 'array'],
            'walk_aid.*' => ['nullable', 'integer'],
            'other_walk_aid' => ['nullable', 'string'],
            'amsler_grid' => ['nullable', 'in: 1, 2, 3, 4, 5'],
            'abnormality' => ['nullable', 'integer'],
            'other_abnormality' => ['nullable', 'string'],
            'cancer_text' => ['nullable', 'string'],
            'stroke_text' => ['nullable', 'string'],

            // Medication
            // 'om_regular' => ['nullable', 'boolean'],
            'om_regular_desc' => ['nullable', 'string'],
            // 'om_needed' => ['nullable', 'boolean'],
            'om_needed_desc' => ['nullable', 'string'],
            // 'tm_regular' => ['nullable', 'boolean'],
            'tm_regular_desc' => ['nullable', 'string'],
            // 'tm_needed' => ['nullable', 'boolean'],
            'tm_needed_desc' => ['nullable', 'string'],
            'not_prescribed_med' => ['nullable', 'string'],
            'forget_med' => ['nullable', 'in: 0, 1, 2'],
            'missing_med' => ['nullable', 'in: 0, 1, 2'],
            'reduce_med' => ['nullable', 'in: 0, 1, 2'],
            'left_med' => ['nullable', 'in: 0, 1, 2'],
            'take_all_med' => ['nullable', 'in: 0, 1, 2'],
            'stop_med' => ['nullable', 'in: 0, 1, 2'],
            'annoyed_by_med' => ['nullable', 'in: 0, 1, 2'],
            'diff_rem_med' => ['nullable', 'string'],

            // Pain
            'pain_semester' => ['nullable', 'integer'],
            'other_pain_area' => ['nullable', 'string'],
            'pain_level_text' => ['nullable', 'string'],

            // Fall History and Hospitalization
            'have_fallen' => ['nullable', 'string'],
            'adm_admitted' => ['nullable', 'string'],

            // Intervention Effectiveness Evaluation
            'ife_action' => ['nullable', 'in: 1, 2, 3, 4, 5, 6'],
            'ife_self_care' => ['nullable', 'in: 1, 2, 3, 4, 5, 6'],
            'ife_usual_act' => ['nullable', 'in: 1, 2, 3, 4, 5, 6'],
            'ife_discomfort' => ['nullable', 'in: 1, 2, 3, 4, 5, 6'],
            'ife_anxiety' => ['nullable', 'in: 1, 2, 3, 4, 5, 6'],
            'health_scales' => ['nullable', 'string'],
            'health_scale_other' => ['nullable', 'string', 'required_if: health_scales, other'],

            // Qualtrics Form Physiological Measurement
            'rest15' => ['nullable', 'in: 0, 1'],
            'eathour' => ['nullable', 'in: 1, 2, 3'],

            // Physiological Measurement
            'temperature' => ['nullable', 'numeric', 'min:25', 'max:48'],
            'sitting_sbp' => ['nullable', 'integer', 'min:10', 'max:300'],
            'sitting_dbp' => ['nullable', 'integer', 'min:10', 'max:300'],
            'standing_sbp' => ['nullable', 'integer', 'min:10', 'max:300'],
            'standing_dbp' => ['nullable', 'integer', 'min:10', 'max:300'],
            'blood_oxygen' => ['nullable', 'integer', 'min:0', 'max:100'],
            'blood_sugar' => ['nullable', 'numeric', 'min:0', 'max:35'],
            'blood_sugar_time' => ['nullable', 'integer', 'min:1', 'max:4'],
            'waistline' => ['nullable', 'numeric', 'min:20', 'max:250'],
            'weight' => ['nullable', 'numeric', 'min:10', 'max:200'],
            'height' => ['nullable', 'numeric', 'min:1', 'max:3'],
            'respiratory_rate' => ['nullable', 'integer', 'min:0', 'max:50'],
            'blood_options' => ['nullable', 'integer'],
            'blood_text' => ['nullable', 'string'],
            'meal_text' => ['nullable', 'string'],

            // Re Physiological Measurement
            're_temperature' => ['nullable', 'numeric', 'min:25', 'max:48'],
            're_sitting_sbp' => ['nullable', 'integer', 'min:10', 'max:300'],
            're_sitting_dbp' => ['nullable', 'integer', 'min:10', 'max:300'],
            're_standing_sbp' => ['nullable', 'integer', 'min:10', 'max:300'],
            're_standing_dbp' => ['nullable', 'integer', 'min:10', 'max:300'],
            're_blood_oxygen' => ['nullable', 'integer', 'min:0', 'max:100'],
            're_heart_rate' => ['nullable', 'integer', 'min:0', 'max:300'],
            're_heart_rythm' => ['nullable', 'integer', 'min:1', 'max:2'],
            're_kardia' => ['nullable', 'integer', 'min:1', 'max:4'],
            're_blood_sugar' => ['nullable', 'numeric', 'min:0', 'max:35'],
            're_blood_sugar_time' => ['nullable', 'integer', 'min:1', 'max:4'],
            're_waistline' => ['nullable', 'numeric', 'min:20', 'max:250'],
            're_weight' => ['nullable', 'numeric', 'min:10', 'max:200'],
            're_height' => ['nullable', 'numeric', 'min:1', 'max:3'],
            're_respiratory_rate' => ['nullable', 'integer', 'min:0', 'max:50'],
            're_blood_options' => ['nullable', 'integer'],
            're_blood_text' => ['nullable', 'string'],
            're_meal_text' => ['nullable', 'string'],

            // Fall Risk
            'timedup_test' => ['nullable', 'in: 1, 2'],
            'timedup_test_skip' => ['nullable', 'string', 'required_if: timedup_test, 2'],
            'timeup_device' => ['nullable', 'in: 1, 2, 3, 4'],
            'timedup_other' => ['nullable', 'string', 'required_if: timeup_device, 4'],
            'timedup_sec' => ['nullable', 'integer'],
            'timedup_sec_desc' => ['nullable', 'string'],
            'tr_none' => ['nullable', 'boolean'],
            'tr_stopped' => ['nullable', 'boolean'],
            'tr_impaired' => ['nullable', 'boolean'],
            'tr_others' => ['nullable', 'boolean'],
            // 'timedup_remark' => ['nullable', 'in: 1, 2, 3, 4'],
            'timeup_remark_others' => ['nullable', 'string', 'required_if:tr_others,true'],
            'singlestart_sts' => ['nullable', 'in: 1, 2'],
            'singlestart_skip' => ['nullable', 'string', 'required_if: singlestart_sts, 2'],
            'left_sts' => ['nullable', 'in: 1, 2, 3'],
            'right_sts' => ['nullable', 'in: 1, 2, 3'],

            // Qualtrics Remarks
            'qualtrics_remarks' => ['nullable', 'string'],
        ]);
    }

    // Social Worker
    public function validateSocialWorker($request)
    {
        return $request->validate([
            'assessment_case_id' => ['nullable', 'integer', 'exists:assessment_cases,id'],

            'assessor_1' => ['nullable', 'string'],
            'assessor_2' => ['nullable', 'string'],

            // Social Worker
            // Elderly Information
            'elder_marital' => ['nullable', 'in:1,2,3,4,5'],
            'elder_living' => ['nullable', 'array'],
            'elder_living.*' => ['nullable', 'integer', 'in:1,2,3,4,5,6'],
            'elder_carer' => ['nullable', 'in:1,2,3'],
            'elder_is_carer' => ['nullable', 'in:1,2,3'],
            'elder_edu' => ['nullable', 'in:1,2,3,4,5'],
            'elder_religious' => ['nullable', 'in:1,2,3,4,5,6,7,8,9'],
            'elder_housetype' => ['nullable', 'in:1,2,3,4,5,6,7,8'],
            'elder_bell' => ['nullable', 'in:1,2,3'],
            'elder_home_fall' => ['nullable', 'array'],
            'elder_home_fall.*' => ['nullable', 'integer', 'in:1,2,3,4,5,6'],
            'elder_home_hygiene' => ['nullable', 'array'],
            'elder_home_hygiene.*' => ['nullable', 'integer', 'in:1,2,3,4,5,6,7'],
            'elder_home_bug' => ['nullable', 'in:1,2'],

            // Social Service
            'elderly_center' => ['nullable', 'in:1,2'],
            'home_service' => ['nullable', 'array'],
            'home_service.*' => ['nullable', 'integer', 'in:1,2,3,4,5,6,7'],
            'elderly_daycare' => ['nullable', 'in:1,2'],
            'longterm_service' => ['nullable', 'in:1,2'],
            'life_support' => ['nullable', 'array'],
            'life_support.*' => ['nullable', 'integer', 'in:1,2,3,4,5,6,7'],
            'financial_support' => ['nullable', 'in:1,2'],

            // Lifestyle
            'spesific_program' => ['nullable', 'in:1,2,3,4,5'],
            'high_cardio20' => ['nullable', 'in:1,2,3,4,5'],
            'low_cardio40' => ['nullable', 'in:1,2,3,4,5'],
            'recreation' => ['nullable', 'in:1,2,3,4,5'],
            'streching3w' => ['nullable', 'in:1,2,3,4,5'],
            'daily_workout' => ['nullable', 'in:1,2,3,4,5'],
            'ate_fruit24' => ['nullable', 'in:1,2,3,4,5'],
            'ate_veggie35' => ['nullable', 'in:1,2,3,4,5'],
            'ate_dairy23' => ['nullable', 'in:1,2,3,4,5'],
            'ate_protein23' => ['nullable', 'in:1,2,3,4,5'],
            'have_breakfast' => ['nullable', 'in:1,2,3,4,5'],
            'smoking_behavior' => ['nullable', 'in:1,2,3'],
            'alcohol_frequent' => ['nullable', 'in:1,2,3,4,5'],

            // Functional
            'diff_wearing' => ['nullable', 'in:1,2,3,4,5'],
            'diff_bathing' => ['nullable', 'in:1,2,3,4,5'],
            'diff_eating' => ['nullable', 'in:1,2,3,4,5'],
            'diff_wakeup' => ['nullable', 'in:1,2,3,4,5'],
            'diff_toilet' => ['nullable', 'in:1,2,3,4,5'],
            'diff_urine' => ['nullable', 'in:1,2,3,4,5'],
            'can_use_phone' => ['nullable', 'integer', 'min:0', 'max:3'],
            'text_use_phone' => ['nullable', 'string'],
            'can_take_ride' => ['nullable', 'integer', 'min:0', 'max:3'],
            'text_take_ride' => ['nullable', 'string'],
            'can_buy_food' => ['nullable', 'integer', 'min:0', 'max:3'],
            'text_buy_food' => ['nullable', 'string'],
            'can_cook' => ['nullable', 'integer', 'min:0', 'max:3'],
            'text_cook' => ['nullable', 'string'],
            'can_do_housework' => ['nullable', 'integer', 'min:0', 'max:3'],
            'text_do_housework' => ['nullable', 'string'],
            'can_do_repairment' => ['nullable', 'integer', 'min:0', 'max:3'],
            'text_do_repairment' => ['nullable', 'string'],
            'can_do_laundry' => ['nullable', 'integer', 'min:0', 'max:3'],
            'text_do_laundry' => ['nullable', 'string'],
            'can_take_medicine' => ['nullable', 'integer', 'min:0', 'max:3'],
            'text_take_medicine' => ['nullable', 'string'],
            'can_handle_finances' => ['nullable', 'integer', 'min:0', 'max:3'],
            'text_handle_finances' => ['nullable', 'string'],
            'iadl_total_score' => ['nullable', 'integer', 'min:0', 'max:18'],

            // Cognitive
            'moca_edu' => ['nullable', 'in:1,2,3,4,5,6'],

            // Psycho Social
            'relatives_sum' => ['nullable', 'integer', 'min:0', 'max:5'],
            'relatives_to_talk' => ['nullable', 'integer', 'min:0', 'max:5'],
            'relatives_to_help' => ['nullable', 'integer', 'min:0', 'max:5'],
            'friends_sum' => ['nullable', 'integer', 'min:0', 'max:5'],
            'friends_to_talk' => ['nullable', 'integer', 'min:0', 'max:5'],
            'friends_to_help' => ['nullable', 'integer', 'min:0', 'max:5'],
            'lubben_total_score' => ['nullable', 'integer', 'min:0', 'max:30'],
            'genogram_done' => ['nullable', 'boolean'],
            'less_friend' => ['nullable', 'in:1,2,3,4,5'],
            'feel_ignored' => ['nullable', 'in:1,2,3,4,5'],
            'feel_lonely' => ['nullable', 'in:1,2,3,4,5'],
            'most_time_good_mood' => ['nullable', 'integer', 'min:0', 'max:2'],
            'irritable_and_fidgety' => ['nullable', 'integer', 'min:0', 'max:2'],
            'good_to_be_alive' => ['nullable', 'integer', 'min:0', 'max:2'],
            'feeling_down' => ['nullable', 'integer', 'min:0', 'max:2'],
            'social_5' => ['nullable', 'integer'],
            'gds4_score' => ['nullable', 'integer', 'min:0', 'max:4'],
            'do_referral' => ['nullable', 'array'],
            'do_referral.*' => ['nullable', 'in:1,2,3,4,5,6'],

            // Stratification & Remark
            'diagnosed_dementia' => ['nullable', 'in:0,1'],
            'suggest' => ['nullable', 'in:1,2,3'],
            'not_suitable' => ['nullable', 'in:1,2,3,4,5,6,7'],
            'sw_remark' => ['nullable', 'string'],
            'elder_edu_text' => ['nullable', 'string'],
            'elder_living_text' => ['nullable', 'string'],
            'elder_religious_text' => ['nullable', 'string'],
            'elder_housetype_text' => ['nullable', 'string'],
            'elder_home_fall_text' => ['nullable', 'string'],
            'elder_home_hygiene_text' => ['nullable', 'string'],
            'home_service_text' => ['nullable', 'string'],
        ]);
    }

    // Medication Histories
    public function validateMedicationHistories($request)
    {
        return $request->validate([
            'case_id' => ['required', 'integer'],
            'medication_category' => ['required', 'string'],
            'medication_name' => ['required', 'string'],
            'dosage' => ['nullable', 'string'],
            'number_of_intake' => ['nullable', 'string'],
            'frequency' => ['required', 'array'],
            'route' => ['required', 'string'],
            'remarks' => ['nullable', 'string'],
            'gp' => ['nullable', 'boolean'],
            'epr' => ['nullable', 'boolean'],
        ]);
    }

    // Appointment / Clinic
    public function validateAppointment($request)
    {
        return $request->validate([
            'cluster' => ['required', 'string'],
            'type' => ['required', 'string'],
            'name_en' => ['required', 'string'],
            'name_sc' => ['required', 'string'],
        ]);
    }

    // Medication Drug
    public function validateMedicationDrug($request)
    {
        return $request->validate([
            'parent_id' => ['required', 'integer'],
            'name' => ['required', 'string'],
        ]);
    }

    // Follow Up History
    public function validateFollowUpHistory($request)
    {
        return $request->validate([
            'case_id' => ['required', 'integer'],
            'date' => ['required', 'date_format:Y-m-d'],
            'time' => ['nullable', 'date_format:Y-m-d H:i:s'],
            'appointment_id' => ['required', 'integer', 'exists:appointments,id,deleted_at,NULL'],
            'appointment_other_text' => ['nullable', 'string'],
        ]);
    }

    // Medical History
    public function validateMedicalHistory($request)
    {
        return $request->validate([
            'case_id' => ['required', 'integer'],
            'medical_category_name' => ['required', 'string'],
            'medical_diagnosis_name' => ['required', 'string'],
        ]);
    }

    public function validatePaginationParams($request)
    {
        return $request->validate([
            'page' => ['nullable', 'integer'],
            'per_page' => ['nullable', 'integer'],
            'sort_by' => ['nullable', 'in:id,created_at,updated_at'],
            'sort_dir' => ['nullable', 'in:asc,desc'],
        ]);
    }
}
