<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @OA\Schema(
 *     schema="PhysicalConditionForm",
 *     type="object",
 * 
 *     @OA\Property(
 *          property="general_condition",
 *          type="integer",
 *          example="1"
 *     ),
 * 
 *     @OA\Property(
 *          property="eye_opening_response",
 *          type="integer",
 *          example="1"
 *     ),
 * 
 *     @OA\Property(
 *          property="verbal_response",
 *          type="integer",
 *          example="1"
 *     ),
 * 
 *     @OA\Property(
 *          property="motor_response",
 *          type="integer",
 *          example="1"
 *     ), 
 * 
 *     @OA\Property(
 *          property="glasgow_score",
 *          type="integer",
 *          example="1"
 *     ),
 * 
 *     @OA\Property(
 *          property="mental_state",
 *          type="integer",
 *          example="1"
 *     ),
 * 
 *     @OA\Property(
 *          property="percentile",
 *          type="integer",
 *          example="1"
 *     ),
 * 
 *     @OA\Property(
 *          property="moca_score",
 *          type="integer",
 *          example="1"
 *     ),
 * 
 *     @OA\Property(
 *          property="emotional_state",
 *          type="integer",
 *          example="1"
 *     ),
 * 
 *     @OA\Property(
 *          property="geriatric_score",
 *          type="integer",
 *          example="1"
 *     ),
 * 
 *     @OA\Property(
 *          property="is_good",
 *          type="boolean",
 *          example="false"
 *     ),
 *  
 *     @OA\Property(
 *          property="is_deaf",
 *          type="boolean",
 *          example="true"
 *     ),
 *  
 *     @OA\Property(
 *          property="dumb_left",
 *          type="boolean",
 *          example="true"
 *     ),
 *  
 *     @OA\Property(
 *          property="dumb_right",
 *          type="boolean",
 *          example="true"
 *     ),
 *  
 *     @OA\Property(
 *          property="is_visual_impaired",
 *          type="boolean",
 *          example="true"
 *     ),
 *   
 *     @OA\Property(
 *          property="blind_left",
 *          type="boolean",
 *          example="true"
 *     ),
 *   
 *     @OA\Property(
 *          property="blind_right",
 *          type="boolean",
 *          example="true"
 *     ),
 *   
 *     @OA\Property(
 *          property="no_vision",
 *          type="boolean",
 *          example="true"
 *     ),
 *      
 *     @OA\Property(
 *          property="is_assistive_devices",
 *          type="boolean",
 *          example="true"
 *     ),
 *   
 *     @OA\Property(
 *          property="denture",
 *          type="boolean",
 *          example="true"
 *     ),
 *   
 *     @OA\Property(
 *          property="hearing_aid",
 *          type="boolean",
 *          example="true"
 *     ),
 *   
 *     @OA\Property(
 *          property="glasses",
 *          type="boolean",
 *          example="true"
 *     ),
 *    
 *     @OA\Property(
 *          property="is_pain",
 *          type="boolean",
 *          example="true"
 *     ),
 *    
 *     @OA\Property(
 *          property="provoking_factor",
 *          type="string",
 *          example="too much eating"
 *     ),
 *     
 *     @OA\Property(
 *          property="pain_location1",
 *          type="string",
 *          example="stomach"
 *     ),
 *     
 *     @OA\Property(
 *          property="is_dull",
 *          type="boolean",
 *          example="true"
 *     ),
 *     
 *     @OA\Property(
 *          property="is_achy",
 *          type="boolean",
 *          example="true"
 *     ),
 *     
 *     @OA\Property(
 *          property="is_sharp",
 *          type="boolean",
 *          example="true"
 *     ),
 *     
 *     @OA\Property(
 *          property="is_stabbing",
 *          type="boolean",
 *          example="true"
 *     ),
 *     
 *     @OA\Property(
 *          property="stabbing_option",
 *          type="enum",
 *          format="constant, intermittent",
 *          example="constant"
 *     ),
 *      
 *     @OA\Property(
 *          property="pain_location2",
 *          type="string",
 *          example="stomach"
 *     ),
 *      
 *     @OA\Property(
 *          property="is_relief",
 *          type="boolean",
 *          example="true"
 *     ),
 *      
 *     @OA\Property(
 *          property="what_relief",
 *          type="string",
 *          example="sleep"
 *     ),
 *      
 *     @OA\Property(
 *          property="have_relief_method",
 *          type="boolean",
 *          example="true"
 *     ),
 *      
 *     @OA\Property(
 *          property="relief_method",
 *          type="integer",
 *          example="1"
 *     ),
 *      
 *     @OA\Property(
 *          property="other_relief_method",
 *          type="string",
 *          example="fishing"
 *     ),
 *      
 *     @OA\Property(
 *          property="pain_scale",
 *          type="integer",
 *          example="1"
 *     ),
 *      
 *     @OA\Property(
 *          property="when_pain",
 *          type="string",
 *          example="20 Dec 2021"
 *     ),
 *      
 *     @OA\Property(
 *          property="affect_adl",
 *          type="boolean",
 *          example="true"
 *     ),
 *      
 *     @OA\Property(
 *          property="adl_info",
 *          type="string",
 *          example="yes"
 *     ),
 *        
 *     @OA\Property(
 *          property="pain_remark",
 *          type="string",
 *          example="yes"
 *     ),
 * 
 *     @OA\Property(
 *          property="dat_special_diet",
 *          type="integer",
 *          example=1
 *     ),
 *        
 *     @OA\Property(
 *          property="special_diet",
 *          type="string",
 *          example="yes"
 *     ),
 *       
 *     @OA\Property(
 *          property="is_special_feeding",
 *          type="integer",
 *          example=1
 *     ),
 *         
 *     @OA\Property(
 *          property="special_feeding",
 *          type="integer",
 *          example=1
 *     ),
 *       
 *     @OA\Property(
 *          property="thickener_formula",
 *          type="string",
 *          example="yes"
 *     ),
 *        
 *     @OA\Property(
 *          property="fluid_restriction",
 *          type="string",
 *          example="yes"
 *     ),
 *        
 *     @OA\Property(
 *          property="tube_next_change",
 *          type="string",
 *          example="yes"
 *     ),
 *        
 *     @OA\Property(
 *          property="ng_tube",
 *          type="string",
 *          example="yes"
 *     ),
 *        
 *     @OA\Property(
 *          property="milk_formula",
 *          type="string",
 *          example="yes"
 *     ),
 *        
 *     @OA\Property(
 *          property="milk_regime",
 *          type="string",
 *          example="yes"
 *     ),
 *          
 *     @OA\Property(
 *          property="feeding_person",
 *          type="integer",
 *          example="1"
 *     ),
 *          
 *     @OA\Property(
 *          property="feeding_person_text",
 *          type="string",
 *          example="yes"
 *     ),
 *          
 *     @OA\Property(
 *          property="feeding_technique",
 *          type="integer",
 *          example="1"
 *     ),
 *        
 *     @OA\Property(
 *          property="intact_abnormal",
 *          type="integer",
 *          example=1
 *     ),
 *        
 *     @OA\Property(
 *          property="is_napkin_associated",
 *          type="boolean",
 *          example="true"
 *     ),
 *        
 *     @OA\Property(
 *          property="is_dry",
 *          type="boolean",
 *          example="true"
 *     ),
 *        
 *     @OA\Property(
 *          property="is_cellulitis",
 *          type="boolean",
 *          example="true"
 *     ),
 *         
 *     @OA\Property(
 *          property="cellulitis_desc",
 *          type="string",
 *          example="yes"
 *     ),
 *        
 *     @OA\Property(
 *          property="is_eczema",
 *          type="boolean",
 *          example="true"
 *     ),
 *         
 *     @OA\Property(
 *          property="eczema_desc",
 *          type="string",
 *          example="yes"
 *     ),
 *         
 *     @OA\Property(
 *          property="is_scalp",
 *          type="boolean",
 *          example="true"
 *     ),
 *         
 *     @OA\Property(
 *          property="scalp_desc",
 *          type="string",
 *          example="yes"
 *     ),
 *          
 *     @OA\Property(
 *          property="is_itchy",
 *          type="boolean",
 *          example="true"
 *     ),
 *         
 *     @OA\Property(
 *          property="itchy_desc",
 *          type="string",
 *          example="yes"
 *     ),
 *          
 *     @OA\Property(
 *          property="is_wound",
 *          type="boolean",
 *          example="true"
 *     ),
 *         
 *     @OA\Property(
 *          property="wound_desc",
 *          type="string",
 *          example="yes"
 *     ),
 *          
 *     @OA\Property(
 *          property="wound_size",
 *          type="float",
 *          example="3.4"
 *     ),
 * 
 *     @OA\Property(
 *          property="tunneling_time",
 *          type="string",
 *          example="02:30"
 *     ),
 *           
 *     @OA\Property(
 *          property="wound_bed",
 *          type="float",
 *          example="3.4"
 *     ),
 *           
 *     @OA\Property(
 *          property="granulating_tissue",
 *          type="float",
 *          example="3.4"
 *     ),
 *           
 *     @OA\Property(
 *          property="necrotic_tissue",
 *          type="float",
 *          example="3.4"
 *     ),
 *           
 *     @OA\Property(
 *          property="sloughy_tissue",
 *          type="float",
 *          example="3.4"
 *     ),
 *           
 *     @OA\Property(
 *          property="other_tissue",
 *          type="float",
 *          example="3.4"
 *     ),
 *           
 *     @OA\Property(
 *          property="exudate_amount",
 *          type="integer",
 *          example="1"
 *     ),
 *    
 *     @OA\Property(
 *          property="exudate_type",
 *          type="integer",
 *          example="1"
 *     ),
 *   
 *     @OA\Property(
 *          property="other_exudate",
 *          type="string",
 *          example="yes"
 *     ),
 *  
 *     @OA\Property(
 *          property="surrounding_skin",
 *          type="integer",
 *          example="1"
 *     ),
 *   
 *     @OA\Property(
 *          property="other_surrounding",
 *          type="string",
 *          example="yes"
 *     ),
 *         
 *     @OA\Property(
 *          property="odor",
 *          type="integer",
 *          example=1
 *     ),
 *          
 *     @OA\Property(
 *          property="pain",
 *          type="integer",
 *          example=1
 *     ),
 *           
 *     @OA\Property(
 *          property="bowel_habit",
 *          type="integer",
 *          example=1
 *     ),
 * 
 *     @OA\Property(
 *          property="abnormal_option",
 *          type="integer",
 *          example="1"
 *     ),
 *  
 *     @OA\Property(
 *          property="fi_bowel",
 *          type="integer",
 *          example="1"
 *     ),
 *          
 *     @OA\Property(
 *          property="urinary_habit",
 *          type="integer",
 *          example=1
 *     ),
 * 
 *     @OA\Property(
 *          property="fi_urine",
 *          type="integer",
 *          example="1"
 *     ),
 *   
 *     @OA\Property(
 *          property="urine_device",
 *          type="integer",
 *          example=1
 *     ),
 * 
 *     @OA\Property(
 *          property="catheter_type",
 *          type="integer",
 *          example="1"
 *     ),
 *     @OA\Property(
 *          property="catheter_next_change",
 *          type="string",
 *          example="yes"
 *     ),
 *     @OA\Property(
 *          property="catheter_size_fr",
 *          type="integer",
 *          example="1"
 *     ),
 *     
 * )
 */

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
