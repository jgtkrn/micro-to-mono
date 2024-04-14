<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @OA\Schema(
 *     schema="QualtricsForm",
 *     type="object",
 * 
 *     @OA\Property(
 *          property="assessor_1",
 *          type="string",
 *          example="John Doe"
 *     ),
 * 
 *     @OA\Property(
 *          property="assessor_2",
 *          type="string",
 *          example="John Cena"
 *     ),
 * 
 *     @OA\Property(
 *          property="no_chronic",
 *          type="boolean",
 *          example="false"
 *     ),
 * 
 *     @OA\Property(
 *          property="is_hypertension",
 *          type="boolean",
 *          example="true"
 *     ),
 * 
 *     @OA\Property(
 *          property="is_heart_disease",
 *          type="boolean",
 *          example="true"
 *     ),
 * 
 *     @OA\Property(
 *          property="is_diabetes",
 *          type="boolean",
 *          example="true"
 *     ),
 *  
 *     @OA\Property(
 *          property="is_high_cholesterol",
 *          type="boolean",
 *          example="true"
 *     ),
 *  
 *     @OA\Property(
 *          property="is_copd",
 *          type="boolean",
 *          example="true"
 *     ),
 *  
 *     @OA\Property(
 *          property="is_stroke",
 *          type="boolean",
 *          example="true"
 *     ),
 *  
 *     @OA\Property(
 *          property="is_dementia",
 *          type="boolean",
 *          example="true"
 *     ),
 *  
 *     @OA\Property(
 *          property="is_cancer",
 *          type="boolean",
 *          example="true"
 *     ),
 *  
 *     @OA\Property(
 *          property="is_rheumatoid",
 *          type="boolean",
 *          example="true"
 *     ),
 *  
 *     @OA\Property(
 *          property="is_osteoporosis",
 *          type="boolean",
 *          example="true"
 *     ),
 *  
 *     @OA\Property(
 *          property="is_gout",
 *          type="boolean",
 *          example="true"
 *     ),
 *  
 *     @OA\Property(
 *          property="is_depression",
 *          type="boolean",
 *          example="true"
 *     ),
 *   
 *     @OA\Property(
 *          property="is_schizophrenia",
 *          type="boolean",
 *          example="true"
 *     ),
 *   
 *     @OA\Property(
 *          property="is_enlarged_prostate",
 *          type="boolean",
 *          example="true"
 *     ),
 *   
 *     @OA\Property(
 *          property="is_parkinson",
 *          type="boolean",
 *          example="true"
 *     ),
 *    
 *     @OA\Property(
 *          property="is_other_disease",
 *          type="boolean",
 *          example="true"
 *     ),
 * 
 *     @OA\Property(
 *          property="other_disease",
 *          type="string",
 *          example="yes"
 *     ),
 * 
 *     @OA\Property(
 *          property="no_followup",
 *          type="boolean",
 *          example="true"
 *     ),
 *    
 *     @OA\Property(
 *          property="is_general_clinic",
 *          type="boolean",
 *          example="true"
 *     ),
 *     
 *     @OA\Property(
 *          property="is_internal_medicine",
 *          type="boolean",
 *          example="true"
 *     ),
 *     
 *     @OA\Property(
 *          property="is_cardiology",
 *          type="boolean",
 *          example="true"
 *     ),
 *     
 *     @OA\Property(
 *          property="is_geriatric",
 *          type="boolean",
 *          example="true"
 *     ),
 *     
 *     @OA\Property(
 *          property="is_endocrinology",
 *          type="boolean",
 *          example="true"
 *     ),
 *
 *     @OA\Property(
 *          property="is_gastroenterology",
 *          type="boolean",
 *          example="true"
 *     ),
 *
 *     @OA\Property(
 *          property="is_nephrology",
 *          type="boolean",
 *          example="true"
 *     ),
 *   
 *     @OA\Property(
 *          property="is_dep_respiratory",
 *          type="boolean",
 *          example="true"
 *     ),
 *   
 *     @OA\Property(
 *          property="is_surgical",
 *          type="boolean",
 *          example="true"
 *     ),
 *   
 *     @OA\Property(
 *          property="is_psychiatry",
 *          type="boolean",
 *          example="true"
 *     ),
 *  
 *     @OA\Property(
 *          property="is_private_doctor",
 *          type="boolean",
 *          example="true"
 *     ),
 *   
 *     @OA\Property(
 *          property="is_oncology",
 *          type="boolean",
 *          example="true"
 *     ),
 *   
 *     @OA\Property(
 *          property="is_orthopedics",
 *          type="boolean",
 *          example="true"
 *     ),
 *   
 *     @OA\Property(
 *          property="is_urology",
 *          type="boolean",
 *          example="true"
 *     ),
 *   
 *     @OA\Property(
 *          property="is_opthalmology",
 *          type="boolean",
 *          example="true"
 *     ),
 *   
 *     @OA\Property(
 *          property="is_ent",
 *          type="boolean",
 *          example="true"
 *     ),
 * 
 *     @OA\Property(
 *          property="is_other_followup",
 *          type="boolean",
 *          example="true"
 *     ),
 *  
 *     @OA\Property(
 *          property="other_followup",
 *          type="string",
 *          example="yes"
 *     ),
 * 
 *     @OA\Property(
 *          property="never_surgery",
 *          type="boolean",
 *          example="true"
 *     ),
 * 
 *     @OA\Property(
 *          property="is_aj_replace",
 *          type="boolean",
 *          example="true"
 *     ),
 * 
 *     @OA\Property(
 *          property="is_cataract",
 *          type="boolean",
 *          example="true"
 *     ),
 * 
 *     @OA\Property(
 *          property="is_cholecystectomy",
 *          type="boolean",
 *          example="true"
 *     ),
 * 
 *     @OA\Property(
 *          property="is_malignant",
 *          type="boolean",
 *          example="true"
 *     ),
 * 
 *     @OA\Property(
 *          property="is_colectomy",
 *          type="boolean",
 *          example="true"
 *     ),
 * 
 *     @OA\Property(
 *          property="is_thyroidectomy",
 *          type="boolean",
 *          example="true"
 *     ),
 * 
 *     @OA\Property(
 *          property="is_hysterectomy",
 *          type="boolean",
 *          example="true"
 *     ),
 * 
 *     @OA\Property(
 *          property="is_thongbo",
 *          type="boolean",
 *          example="true"
 *     ),
 * 
 *     @OA\Property(
 *          property="is_pacemaker",
 *          type="boolean",
 *          example="true"
 *     ),
 * 
 *     @OA\Property(
 *          property="is_prostatectomy",
 *          type="boolean",
 *          example="true"
 *     ),
 * 
 *     @OA\Property(
 *          property="is_other_surgery",
 *          type="boolean",
 *          example="true"
 *     ),
 * 
 *     @OA\Property(
 *          property="other_surgery",
 *          type="string",
 *          example="yes"
 *     ),
 *     
 *     @OA\Property(
 *          property="left_ear",
 *          type="enum",
 *          format="0, 1, 2",
 *          example="2"
 *     ),
 *     
 *     @OA\Property(
 *          property="right_ear",
 *          type="enum",
 *          format="0, 1, 2",
 *          example="2"
 *     ),
 *     
 *     @OA\Property(
 *          property="left_eye",
 *          type="enum",
 *          format="0, 1, 2",
 *          example="2"
 *     ),
 *     
 *     @OA\Property(
 *          property="right_eye",
 *          type="enum",
 *          format="0, 1, 2",
 *          example="2"
 *     ),
 *     
 *     @OA\Property(
 *          property="hearing_aid",
 *          type="enum",
 *          format="0, 1",
 *          example="1"
 *     ),
 *     
 *     @OA\Property(
 *          property="walk_aid",
 *          type="enum",
 *          format="1, 2, 3, 4, 5, 6",
 *          example="2"
 *     ),
 *     
 *     @OA\Property(
 *          property="other_walk_aid",
 *          type="string",
 *          example="yes"
 *     ),
 *     
 *     @OA\Property(
 *          property="amsler_grid",
 *          type="enum",
 *          format="1, 2, 3, 4, 5",
 *          example="2"
 *     ),
 * 
 *     @OA\Property(
 *          property="om_regular_desc",
 *          type="string",
 *          example="yes"
 *     ),
 * 
 *     @OA\Property(
 *          property="om_needed_desc",
 *          type="string",
 *          example="yes"
 *     ),
 * 
 *     @OA\Property(
 *          property="tm_regular_desc",
 *          type="string",
 *          example="yes"
 *     ),
 * 
 *     @OA\Property(
 *          property="tm_needed_desc",
 *          type="string",
 *          example="yes"
 *     ),
 *     
 *     @OA\Property(
 *          property="not_prescribed_med",
 *          type="string",
 *          example="yes"
 *     ),
 * 
 *     @OA\Property(
 *          property="forget_med",
 *          type="enum",
 *          format="0, 1, 2",
 *          example="1"
 *     ),
 *    
 *     @OA\Property(
 *          property="missing_med",
 *          type="enum",
 *          format="0, 1, 2",
 *          example="1"
 *     ),
 *    
 *     @OA\Property(
 *          property="reduce_med",
 *          type="enum",
 *          format="0, 1, 2",
 *          example="1"
 *     ),
 *    
 *     @OA\Property(
 *          property="left_med",
 *          type="enum",
 *          format="0, 1, 2",
 *          example="1"
 *     ),
 *   
 *     @OA\Property(
 *          property="take_all_med",
 *          type="enum",
 *          format="0, 1, 2",
 *          example="1"
 *     ),
 *   
 *     @OA\Property(
 *          property="stop_med",
 *          type="enum",
 *          format="0, 1, 2",
 *          example="1"
 *     ),
 *   
 *     @OA\Property(
 *          property="annoyed_by_med",
 *          type="enum",
 *          format="0, 1, 2",
 *          example="1"
 *     ),
 * 
 *     @OA\Property(
 *          property="diff_rem_med",
 *          type="string",
 *          example="0.75"
 *     ),
 *   
 *     @OA\Property(
 *          property="pain_semester",
 *          type="enum",
 *          format="0, 1, 2, 3, 4, 5",
 *          example="1"
 *     ),
 *   
 *     @OA\Property(
 *          property="other_pain_area",
 *          type="string",
 *          example="yes"
 *     ),
 * 
 *     @OA\Property(
 *          property="pain_level",
 *          type="enum",
 *          format="1, 2, 3, 4, 5, 6, 7, 8, 9, 10",
 *          example="1"
 *     ),
 *    
 *     @OA\Property(
 *          property="pain_level_text",
 *          type="string",
 *          example="yes"
 *     ),
 * 
 *     @OA\Property(
 *          property="have_fallen",
 *          type="string",
 *          example="yes"
 *     ),
 *   
 *     @OA\Property(
 *          property="adm_admitted",
 *          type="string",
 *          example="yes"
 *     ),
 * 
 *     @OA\Property(
 *          property="ife_action",
 *          type="enum",
 *          format="1, 2, 3, 4, 5, 6",
 *          example="1"
 *     ),
 * 
 *     @OA\Property(
 *          property="ife_self_care",
 *          type="enum",
 *          format="1, 2, 3, 4, 5, 6",
 *          example="1"
 *     ),
 * 
 *     @OA\Property(
 *          property="ife_usual_act",
 *          type="enum",
 *          format="1, 2, 3, 4, 5, 6",
 *          example="1"
 *     ),
 * 
 *     @OA\Property(
 *          property="ife_discomfort",
 *          type="enum",
 *          format="1, 2, 3, 4, 5, 6",
 *          example="1"
 *     ),
 * 
 *     @OA\Property(
 *          property="ife_anxiety",
 *          type="enum",
 *          format="1, 2, 3, 4, 5, 6",
 *          example="1"
 *     ),
 * 
 *     @OA\Property(
 *          property="health_scales",
 *          type="string",
 *          example="90"
 *     ),
 * 
 *     @OA\Property(
 *          property="health_scale_other",
 *          type="string",
 *          example="yes"
 *     ),
 * 
 *     @OA\Property(
 *          property="rest15",
 *          type="enum",
 *          format="1, 2",
 *          example=1
 *     ),
 *  
 *     @OA\Property(
 *          property="eathour",
 *          type="enum",
 *          format="1, 2, 3",
 *          example=1
 *     ),
 * 
 *     @OA\Property(
 *          property="temperature",
 *          type="integer",
 *          example="1"
 *     ),
 * 
 *     @OA\Property(
 *          property="sitting_sbp",
 *          type="integer",
 *          example="1"
 *     ),
 *
 *     @OA\Property(
 *          property="sitting_dbp",
 *          type="integer",
 *          example="1"
 *     ),
 * 
 *     @OA\Property(
 *          property="standing_sbp",
 *          type="integer",
 *          example="1"
 *     ),
 * 
 *     @OA\Property(
 *          property="standing_dbp",
 *          type="integer",
 *          example="1"
 *     ),
 * 
 *     @OA\Property(
 *          property="blood_oxygen",
 *          type="integer",
 *          example="1"
 *     ),
 * 
 *     @OA\Property(
 *          property="heart_rate",
 *          type="integer",
 *          example="1"
 *     ),
 * 
 *     @OA\Property(
 *          property="heart_rythm",
 *          type="integer",
 *          example="1"
 *     ),
 * 
 *     @OA\Property(
 *          property="kardia",
 *          type="integer",
 *          example="1"
 *     ),
 * 
 *     @OA\Property(
 *          property="blood_sugar",
 *          type="number",
 *          format="float",
 *          example="1.80"
 *     ),
 * 
 *     @OA\Property(
 *          property="blood_sugar_time",
 *          type="integer",
 *          example="1"
 *     ),
 * 
 *     @OA\Property(
 *          property="waistline",
 *          type="integer",
 *          example="1"
 *     ),
 * 
 *     @OA\Property(
 *          property="weight",
 *          type="integer",
 *          example="1"
 *     ),
 * 
 *     @OA\Property(
 *          property="height",
 *          type="number",
 *          format="float",
 *          example="1.80"
 *     ),
 *  
 *     @OA\Property(
 *          property="respiratory_rate",
 *          type="integer",
 *          example=1
 *     ),
 * 
 *     @OA\Property(
 *          property="re_temperature",
 *          type="integer",
 *          example="1"
 *     ),
 * 
 *     @OA\Property(
 *          property="re_sitting_sbp",
 *          type="integer",
 *          example="1"
 *     ),
 *
 *     @OA\Property(
 *          property="re_sitting_dbp",
 *          type="integer",
 *          example="1"
 *     ),
 * 
 *     @OA\Property(
 *          property="re_standing_sbp",
 *          type="integer",
 *          example="1"
 *     ),
 * 
 *     @OA\Property(
 *          property="re_standing_dbp",
 *          type="integer",
 *          example="1"
 *     ),
 * 
 *     @OA\Property(
 *          property="re_blood_oxygen",
 *          type="integer",
 *          example="1"
 *     ),
 * 
 *     @OA\Property(
 *          property="re_heart_rate",
 *          type="integer",
 *          example="1"
 *     ),
 * 
 *     @OA\Property(
 *          property="re_heart_rythm",
 *          type="integer",
 *          example="1"
 *     ),
 * 
 *     @OA\Property(
 *          property="re_kardia",
 *          type="integer",
 *          example="1"
 *     ),
 * 
 *     @OA\Property(
 *          property="re_blood_sugar",
 *          type="number",
 *          format="float",
 *          example="1.80"
 *     ),
 * 
 *     @OA\Property(
 *          property="re_blood_sugar_time",
 *          type="integer",
 *          example="1"
 *     ),
 * 
 *     @OA\Property(
 *          property="re_waistline",
 *          type="integer",
 *          example="1"
 *     ),
 * 
 *     @OA\Property(
 *          property="re_weight",
 *          type="integer",
 *          example="1"
 *     ),
 * 
 *     @OA\Property(
 *          property="re_height",
 *          type="number",
 *          format="float",
 *          example="1.80"
 *     ),
 *  
 *     @OA\Property(
 *          property="re_respiratory_rate",
 *          type="integer",
 *          example="1"
 *     ),
 * 
 *     @OA\Property(
 *          property="timedup_test",
 *          type="enum",
 *          format="1, 2",
 *          example="1"
 *     ),
 * 
 *     @OA\Property(
 *          property="timedup_test_skip",
 *          type="string",
 *          example="yes"
 *     ),
 * 
 *     @OA\Property(
 *          property="timeup_device",
 *          type="enum",
 *          format="1, 2, 3, 4",
 *          example="1"
 *     ),
 * 
 *     @OA\Property(
 *          property="timedup_other",
 *          type="string",
 *          example="yes"
 *     ),
 * 
 *     @OA\Property(
 *          property="timedup_sec",
 *          type="integer",
 *          example="8"
 *     ),
 *
 *     @OA\Property(
 *          property="timedup_sec_desc",
 *          type="string",
 *          example="yes"
 *     ),
 *  
 *     @OA\Property(
 *          property="tr_none",
 *          type="boolean",
 *          example="false"
 *     ),
 * 
 *     @OA\Property(
 *          property="tr_stopped",
 *          type="boolean",
 *          example="false"
 *     ),
 * 
 *     @OA\Property(
 *          property="tr_impaired",
 *          type="boolean",
 *          example="false"
 *     ),
 * 
 *     @OA\Property(
 *          property="tr_others",
 *          type="boolean",
 *          example="false"
 *     ),
 * 
 *     @OA\Property(
 *          property="timeup_remark_others",
 *          type="string",
 *          example="yes"
 *     ),
 * 
 *     @OA\Property(
 *          property="singlestart_sts",
 *          type="enum",
 *          format="1, 2",
 *          example="1"
 *     ),
 * 
 *     @OA\Property(
 *          property="singlestart_skip",
 *          type="string",
 *          example="yes"
 *     ),
 * 
 *     @OA\Property(
 *          property="left_sts",
 *          type="enum",
 *          format="1, 2, 3",
 *          example="1"
 *     ),
 * 
 *     @OA\Property(
 *          property="right_sts",
 *          type="enum",
 *          format="1, 2, 3",
 *          example="1"
 *     ),
 * 
 *     @OA\Property(
 *          property="qualtrics_remarks",
 *          type="string",
 *          example="yes"
 *     ),
 * 
 * )
 */

class QualtricsForm extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];
    protected $hidden = ['deleted_at'];

    protected $casts = [
            'assessment_case_id' => 'integer',

            // Health Professional
            // Chronic Disease History
            'no_chronic' => 'boolean',
            'is_hypertension' => 'boolean',
            'is_heart_disease' => 'boolean',
            'is_diabetes' => 'boolean',
            'is_high_cholesterol' => 'boolean',
            'is_copd' => 'boolean',
            'is_stroke' => 'boolean',
            'is_dementia' => 'boolean',
            'is_cancer' => 'boolean',
            'is_rheumatoid' => 'boolean',
            'is_osteoporosis' => 'boolean',
            'is_gout' => 'boolean',
            'is_depression' => 'boolean',
            'is_schizophrenia' => 'boolean',
            'is_enlarged_prostate' => 'boolean',
            'is_parkinson' => 'boolean',
            'is_other_disease' => 'boolean',
            'no_followup' => 'boolean',
            'is_general_clinic' => 'boolean',
            'is_internal_medicine' => 'boolean',
            'is_cardiology' => 'boolean',
            'is_geriatric' => 'boolean',
            'is_endocrinology' => 'boolean',
            'is_gastroenterology' => 'boolean',
            'is_nephrology' => 'boolean',
            'is_dep_respiratory' => 'boolean',
            'is_surgical' => 'boolean',
            'is_psychiatry' => 'boolean',
            'is_private_doctor' => 'boolean',
            'is_oncology' => 'boolean',
            'is_orthopedics' => 'boolean',
            'is_urology' => 'boolean',
            'is_opthalmology' => 'boolean',
            'is_ent' => 'boolean',
            'is_other_followup' => 'boolean',
            'never_surgery' => 'boolean',
            'is_aj_replace' => 'boolean',
            'is_cataract' => 'boolean',
            'is_cholecystectomy' => 'boolean',
            'is_malignant' => 'boolean',
            'is_colectomy' => 'boolean',
            'is_thyroidectomy' => 'boolean',
            'is_hysterectomy' => 'boolean',
            'is_thongbo' => 'boolean',
            'is_pacemaker' => 'boolean',
            'is_prostatectomy' => 'boolean',
            'is_other_surgery' => 'boolean',
            'left_ear' => 'integer',
            'right_ear' => 'integer',
            'left_eye' => 'integer',
            'right_eye' => 'integer',
            'hearing_aid' => 'integer',
            // 'walk_aid' => 'integer',
            'amsler_grid' => 'integer',

            // Medication
            'om_regular' => 'boolean',
            'om_needed' => 'boolean',
            'tm_regular' => 'boolean',
            'tm_needed' => 'boolean',
            'forget_med' => 'integer',
            'missing_med' => 'integer',
            'reduce_med' => 'integer',
            'left_med' => 'integer',
            'take_all_med' => 'integer',
            'stop_med' => 'integer',
            'annoyed_by_med' => 'integer',

            // Pain
            'forget_med_p' => 'integer',
            'missing_med_p' => 'integer',
            'reduce_med_p' => 'integer',
            'left_med_p' => 'integer',
            'take_all_med_p' => 'integer',
            'stop_med_p' => 'integer',
            'annoyed_by_med_p' => 'integer',
            'pain_semester' => 'integer',
            'abnormality' => 'integer', 

            // Fall History and Hospitalization
            // 'hosp_hosp' => 'integer',
            // 'hosp_way' => 'integer',
            // 'hosp_home' => 'integer',
            // 'hosp_reason' => 'integer',

            // Intervention Effectiveness Evaluation
            'ife_action' => 'integer',
            'ife_self_care' => 'integer',
            'ife_usual_act' => 'integer',
            'ife_discomfort' => 'integer',
            'ife_anxiety' => 'integer',

            // Qualtrics Form Physiological Measurement
            'rest15' => 'integer',
            'eathour' => 'integer',
            // 'body_temperature1' => 'decimal:2',
            // 'body_temperature2' => 'decimal:2',
            // 'sit_upward1' => 'decimal:2',
            // 'sit_upward2' => 'decimal:2',
            // 'sit_depression1' => 'decimal:2',
            // 'sit_depression2' => 'decimal:2',
            // 'sta_upward1' => 'decimal:2',
            // 'sta_upward2' => 'decimal:2',
            // 'sta_depression1' => 'decimal:2',
            // 'sta_depression2' => 'decimal:2',
            // 'blood_ox1' => 'decimal:2',
            // 'blood_ox2' => 'decimal:2',
            // 'heartbeat1' => 'decimal:2',
            // 'heartbeat2' => 'decimal:2',
            // 'blood_glucose1' => 'decimal:2',
            // 'blood_glucose2' => 'decimal:2',
            // 'phy_kardia' => 'integer',
            // 'phy_waist' => 'decimal:2',
            // 'phy_weight' => 'decimal:2',
            // 'phy_height' => 'decimal:2',

            // Physiological Measurement
            'temperature' => 'decimal:2',
            'sitting_sbp' => 'integer',
            'sitting_dbp' => 'integer',
            'standing_sbp' => 'integer',
            'standing_dbp' => 'integer',
            'blood_oxygen' => 'integer',
            'heart_rate' => 'integer',
            'heart_rythm' => 'integer',
            'kardia' => 'integer',
            'blood_sugar' => 'decimal:2',
            'blood_sugar_time' => 'integer',
            'waistline' => 'decimal:2',
            'weight' => 'decimal:2',
            'height' => 'decimal:2',
            'respiratory_rate' => 'integer',
            'blood_options' => 'integer',

            // Re Physiological Measurement
            're_temperature' => 'decimal:2',
            're_sitting_sbp' => 'integer',
            're_sitting_dbp' => 'integer',
            're_standing_sbp' => 'integer',
            're_standing_dbp' => 'integer',
            're_blood_oxygen' => 'integer',
            're_heart_rate' => 'integer',
            're_heart_rythm' => 'integer',
            're_kardia' => 'integer',
            're_blood_sugar' => 'decimal:2',
            're_blood_sugar_time' => 'integer',
            're_waistline' => 'decimal:2',
            're_weight' => 'decimal:2',
            're_height' => 'decimal:2',
            're_respiratory_rate' => 'integer',
            're_blood_options' => 'integer',

            // Fall Risk
            'timedup_test' => 'integer',
            'timeup_device' => 'integer',
            'timedup_sec' => 'integer',
            'tr_none' => 'boolean',
            'tr_stopped' => 'boolean',
            'tr_impaired' => 'boolean',
            'tr_others' => 'boolean',
            // 'timedup_remark' => 'integer',
            'singlestart_sts' => 'integer',
            'left_sts' => 'integer',
            'right_sts' => 'integer',

            // Qualtrics Remarks
            // string only
    ];

    protected $with = ['hospitalizationTables', 'walkAids'];
    
    public function hospitalizationTables()
    {
        return $this->hasMany(HospitalizationTables::class);
    }

    public function walkAids()
    {
        return $this->hasMany(WalkAid::class);
    }

    public function assessmentCase()
    {
        return $this->belongsTo(AssessmentCase::class, 'assessment_case_id');
    }
}

            
