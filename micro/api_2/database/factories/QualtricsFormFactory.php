<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\QualtricsForm>
 */
class QualtricsFormFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'assessor_1' => $this->faker->word,
            'assessor_2' => $this->faker->word,

            // Health Professional
            // Chronic Disease History
            'no_chronic' => $this->faker->boolean,
            'is_hypertension' => $this->faker->boolean,
            'is_heart_disease' => $this->faker->boolean,
            'is_diabetes' => $this->faker->boolean,
            'is_high_cholesterol' => $this->faker->boolean,
            'is_copd' => $this->faker->boolean,
            'is_stroke' => $this->faker->boolean,
            'is_dementia' => $this->faker->boolean,
            'is_cancer' => $this->faker->boolean,
            'is_rheumatoid' => $this->faker->boolean,
            'is_osteoporosis' => $this->faker->boolean,
            'is_gout' => $this->faker->boolean,
            'is_depression' => $this->faker->boolean,
            'is_schizophrenia' => $this->faker->boolean,
            'is_enlarged_prostate' => $this->faker->boolean,
            'is_parkinson' => $this->faker->boolean,
            'is_other_disease' => $this->faker->boolean,
            'other_disease' => $this->faker->word,
            'no_followup' => $this->faker->boolean,
            'is_general_clinic' => $this->faker->boolean,
            'is_internal_medicine' => $this->faker->boolean,
            'is_cardiology' => $this->faker->boolean,
            'is_geriatric' => $this->faker->boolean,
            'is_endocrinology' => $this->faker->boolean,
            'is_gastroenterology' => $this->faker->boolean,
            'is_nephrology' => $this->faker->boolean,
            'is_dep_respiratory' => $this->faker->boolean,
            'is_surgical' => $this->faker->boolean,
            'is_psychiatry' => $this->faker->boolean,
            'is_private_doctor' => $this->faker->boolean,
            'is_oncology' => $this->faker->boolean,
            'is_orthopedics' => $this->faker->boolean,
            'is_urology' => $this->faker->boolean,
            'is_opthalmology' => $this->faker->boolean,
            'is_ent' => $this->faker->boolean,
            'is_other_followup' => $this->faker->boolean,
            'other_followup' => $this->faker->word,
            'never_surgery' => $this->faker->boolean,
            'is_aj_replace' => $this->faker->boolean,
            'is_cataract' => $this->faker->boolean,
            'is_cholecystectomy' => $this->faker->boolean,
            'is_malignant' => $this->faker->boolean,
            'is_colectomy' => $this->faker->boolean,
            'is_thyroidectomy' => $this->faker->boolean,
            'is_hysterectomy' => $this->faker->boolean,
            'is_thongbo' => $this->faker->boolean,
            'is_pacemaker' => $this->faker->boolean,
            'is_prostatectomy' => $this->faker->boolean,
            'is_other_surgery' => $this->faker->boolean,
            'other_surgery' => $this->faker->word,
            'left_ear' => $this->faker->randomElement([0, 1, 2]),
            'right_ear' => $this->faker->randomElement([0, 1, 2]),
            'left_eye' => $this->faker->randomElement([0, 1, 2]),
            'right_eye' => $this->faker->randomElement([0, 1, 2]),
            'hearing_aid' => $this->faker->randomElement([0, 1]),
            // 'walk_aid' => $this->faker->randomElement([1, 2, 3, 4, 5, 6]),
            'other_walk_aid' => $this->faker->word,
            'amsler_grid' => $this->faker->randomElement([1, 2, 3, 4, 5]),
            'cancer_text' => $this->faker->word,
            'stroke_text' => $this->faker->word,

            // Medication
            // 'om_regular' => $this->faker->boolean,
            'om_regular_desc' => $this->faker->word,
            // 'om_needed' => $this->faker->boolean,
            'om_needed_desc' => $this->faker->word,
            // 'tm_regular' => $this->faker->boolean,
            'tm_regular_desc' => $this->faker->word,
            // 'tm_needed' => $this->faker->boolean,
            'tm_needed_desc' => $this->faker->word,
            'not_prescribed_med' => $this->faker->word,
            'forget_med' => $this->faker->randomElement([0, 1, 2]),
            'missing_med' => $this->faker->randomElement([0, 1, 2]),
            'reduce_med' => $this->faker->randomElement([0, 1, 2]),
            'left_med' => $this->faker->randomElement([0, 1, 2]),
            'take_all_med' => $this->faker->randomElement([0, 1, 2]),
            'stop_med' => $this->faker->randomElement([0, 1, 2]),
            'annoyed_by_med' => $this->faker->randomElement([0, 1, 2]),
            'diff_rem_med' => $this->faker->randomElement(["1.00", "0.75", "0.50", "0.25", "0.00", "-1.00"]),

            // Pain
            'pain_semester' => $this->faker->randomElement([0, 1, 2, 3, 4, 5]),
            'other_pain_area' => $this->faker->word,
            'pain_level' => $this->faker->randomElement(['1', '2', '3', '4', '5', '6', '7', '8', '9', '10']),
            'pain_level_text' => $this->faker->word,

            // Fall History and Hospitalization
            'have_fallen' => $this->faker->word,
            'adm_admitted' => $this->faker->word,
            // 'hosp_month' => $this->faker->word,
            // 'hosp_year' => $this->faker->word,
            // 'hosp_hosp' => $this->faker->randomElement([1, 2, 3, 4, 5, 6, 7, 8, 9, 10]),
            // 'hosp_hosp_other' => $this->faker->word,
            // 'hosp_way' => $this->faker->randomElement([1, 2, 3, 4]),
            // 'hosp_home' => $this->faker->randomElement([1, 2, 3, 4, 5]),
            // 'hosp_home_else' => $this->faker->word,
            // 'hosp_reason' => $this->faker->randomElement([1, 2, 3, 4, 5]),

            // Intervention Effectiveness Evaluation
            'ife_action' => $this->faker->randomElement([1, 2, 3, 4, 5, 6]),
            'ife_self_care' => $this->faker->randomElement([1, 2, 3, 4, 5, 6]),
            'ife_usual_act' => $this->faker->randomElement([1, 2, 3, 4, 5, 6]),
            'ife_discomfort' => $this->faker->randomElement([1, 2, 3, 4, 5, 6]),
            'ife_anxiety' => $this->faker->randomElement([1, 2, 3, 4, 5, 6]),
            'health_scales' => $this->faker->word,
            'health_scale_other' => $this->faker->word,

            // Qualtrics Form Physiological Measurement
            'rest15' => $this->faker->randomElement([1, 2]),
            'eathour' => $this->faker->randomElement([1, 2, 3]),
            // 'body_temperature1' => $this->faker->randomFloat(2, 0, 100),
            // 'body_temperature2' => $this->faker->randomFloat(2, 0, 100),
            // 'sit_upward1' => $this->faker->randomFloat(2, 0, 100),
            // 'sit_upward2' => $this->faker->randomFloat(2, 0, 100),
            // 'sit_depression1' => $this->faker->randomFloat(2, 0, 100),
            // 'sit_depression2' => $this->faker->randomFloat(2, 0, 100),
            // 'sta_upward1' => $this->faker->randomFloat(2, 0, 100),
            // 'sta_upward2' => $this->faker->randomFloat(2, 0, 100),
            // 'sta_depression1' => $this->faker->randomFloat(2, 0, 100),
            // 'sta_depression2' => $this->faker->randomFloat(2, 0, 100),
            // 'blood_ox1' => $this->faker->randomFloat(2, 0, 100),
            // 'blood_ox2' => $this->faker->randomFloat(2, 0, 100),
            // 'heartbeat1' => $this->faker->randomFloat(2, 0, 100),
            // 'heartbeat2' => $this->faker->randomFloat(2, 0, 100),
            // 'blood_glucose1' => $this->faker->randomFloat(2, 0, 100),
            // 'blood_glucose2' => $this->faker->randomFloat(2, 0, 100),
            // 'phy_kardia' => $this->faker->randomElement([1, 2, 3, 4, 5]),
            // 'phy_waist' => $this->faker->randomFloat(2, 0, 100),
            // 'phy_weight' => $this->faker->randomFloat(2, 0, 100),
            // 'phy_height' => $this->faker->randomFloat(2, 0, 100),

            // Physiological Measurement
            'temperature' => $this->faker->randomFloat(2,26,47),
            'sitting_sbp' => $this->faker->numberBetween(10,20),
            'sitting_dbp' => $this->faker->numberBetween(10,20),
            'standing_sbp' => $this->faker->numberBetween(10,20),
            'standing_dbp' => $this->faker->numberBetween(10,20),
            'blood_oxygen' => $this->faker->numberBetween(1,2),
            'heart_rate' => $this->faker->numberBetween(1,2),
            'heart_rythm' => $this->faker->numberBetween(1,2),
            'kardia' => $this->faker->numberBetween(1,2),
            'blood_sugar' => $this->faker->randomFloat(2,10,20), //decimal
            'blood_sugar_time' => $this->faker->numberBetween(1,2),
            'waistline' => $this->faker->randomFloat(2,20,40),
            'weight' => $this->faker->randomFloat(2,20,30),
            'height' => $this->faker->randomFloat(2,2,3), //decimal
            'respiratory_rate' => $this->faker->numberBetween(1, 2),
            'abnormality' => $this->faker->numberBetween(1, 2),
            'other_abnormality' => $this->faker->word,
            'blood_options' => $this->faker->numberBetween(1,2),
            'blood_text' => $this->faker->word,
            'meal_text' => $this->faker->word,

            // Re Physiological Measurement
            're_temperature' => $this->faker->randomFloat(2,26,47),
            're_sitting_sbp' => $this->faker->numberBetween(10,20),
            're_sitting_dbp' => $this->faker->numberBetween(10,20),
            're_standing_sbp' => $this->faker->numberBetween(10,20),
            're_standing_dbp' => $this->faker->numberBetween(10,20),
            're_blood_oxygen' => $this->faker->numberBetween(1,2),
            're_heart_rate' => $this->faker->numberBetween(1,2),
            're_heart_rythm' => $this->faker->numberBetween(1,2),
            're_kardia' => $this->faker->numberBetween(1,2),
            're_blood_sugar' => $this->faker->randomFloat(2,10,20), //decimal
            're_blood_sugar_time' => $this->faker->numberBetween(1,2),
            're_waistline' => $this->faker->randomFloat(2,20,40),
            're_weight' => $this->faker->randomFloat(2,20,30),
            're_height' => $this->faker->randomFloat(2,2,3), //decimal
            're_respiratory_rate' => $this->faker->numberBetween(1, 2),
            're_blood_options' => $this->faker->numberBetween(1,2),
            're_blood_text' => $this->faker->word,
            're_meal_text' => $this->faker->word,

            // Fall Risk
            'timedup_test' => $this->faker->randomElement([1, 2]),
            'timedup_test_skip' => $this->faker->word,
            'timeup_device' => $this->faker->randomElement([1, 2, 3, 4]),
            'timedup_other' => $this->faker->word,
            'timedup_sec' => $this->faker->numberBetween(1, 10),
            // 'timedup_remark' => $this->faker->randomElement([1, 2, 3, 4]),
            'timedup_sec_desc' => $this->faker->word,
            'tr_none' => $this->faker->boolean,
            'tr_stopped' => $this->faker->boolean,
            'tr_impaired' => $this->faker->boolean,
            'tr_others' => $this->faker->boolean,
            'timeup_remark_others' => $this->faker->word,
            'singlestart_sts' => $this->faker->randomElement([1, 2]),
            'singlestart_skip' => $this->faker->word,
            'left_sts' => $this->faker->randomElement([1, 2, 3]),
            'right_sts' => $this->faker->randomElement([1, 2, 3]),

            // Qualtrics Remarks
            'qualtrics_remarks' => $this->faker->word,

            // Free Text
            'fallrisk_fa' => $this->faker->word,
            'fallrisk_rs' => $this->faker->word,
            'hosp_fa' => $this->faker->word,
            'hosp_rs' => $this->faker->word,
            'remark_fa' => $this->faker->word,
            'remark_rs' => $this->faker->word
        ];
    }
}
