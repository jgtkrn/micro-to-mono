<?php

namespace App\Models\v2\Assessments;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PhysicalConditionForm extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];
    protected $hidden = ['deleted_at'];
    protected $casts = [

        // General Condition
        'general_condition' => 'integer',
        'eye_opening_response' => 'integer',
        'verbal_response' => 'integer',
        'motor_response' => 'integer',
        'glasgow_score' => 'integer',

        // Mental State
        'mental_state' => 'integer',
        'edu_percentile' => 'integer',
        'moca_score' => 'integer',

        // Emotional State
        'emotional_state' => 'integer',
        'geriatric_score' => 'integer',

        // Sensory
        'is_good' => 'boolean',
        'is_deaf' => 'boolean',
        'dumb_left' => 'boolean',
        'dumb_right' => 'boolean',
        'non_verbal' => 'boolean',
        'is_visual_impaired' => 'boolean',
        'blind_left' => 'boolean',
        'blind_right' => 'boolean',
        'no_vision' => 'boolean',
        'is_assistive_devices' => 'boolean',
        'denture' => 'boolean',
        'hearing_aid' => 'boolean',
        'glasses' => 'boolean',

        // Nutrition
        'dat_special_diet' => 'integer',
        'is_special_feeding' => 'integer',
        'special_feeding' => 'integer',
        'feeding_person' => 'integer',
        'feeding_technique' => 'integer',

        // Skin Condition
        'intact_abnormal' => 'integer',
        'is_napkin_associated' => 'boolean',
        'is_dry' => 'boolean',
        'is_cellulitis' => 'boolean',
        'is_eczema' => 'boolean',
        'is_scalp' => 'boolean',
        'is_itchy' => 'boolean',
        'is_wound' => 'boolean',
        'wound_size' => 'decimal:2',
        'wound_bed' => 'decimal:2',
        'granulating_tissue' => 'decimal:2',
        'necrotic_tissue' => 'decimal:2',
        'sloughy_tissue' => 'decimal:2',
        'other_tissue' => 'decimal:2',
        'exudate_amount' => 'integer',
        'exudate_type' => 'integer',
        'surrounding_skin' => 'integer',
        'odor' => 'integer',
        'pain' => 'integer',

        // Elimination
        'bowel_habit' => 'integer',
        'abnormal_option' => 'integer',
        'fi_bowel' => 'integer',
        'urinary_habit' => 'integer',
        'fi_urine' => 'integer',
        'urine_device' => 'integer',
        'catheter_type' => 'integer',
        'catheter_size_fr' => 'integer',

        // Pain
        'is_pain' => 'integer',

        'deaf_right' => 'boolean',
        'deaf_left' => 'boolean',
        'skin_rash' => 'boolean',
        'visual_impaired_left' => 'boolean',
        'visual_impaired_right' => 'boolean',
        'visual_impaired_both' => 'boolean',
    ];

    protected $with = ['pains'];

    public function pains()
    {
        return $this->hasMany(PainSiteTable::class);
    }

    public function assessmentCase()
    {
        return $this->belongsTo(AssessmentCase::class, 'assessment_case_id');
    }
}
