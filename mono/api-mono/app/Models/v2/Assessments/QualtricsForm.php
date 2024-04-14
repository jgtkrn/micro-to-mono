<?php

namespace App\Models\v2\Assessments;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

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
