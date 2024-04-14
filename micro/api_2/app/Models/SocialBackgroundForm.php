<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @OA\Schema(
 *     schema="SocialBackgroundForm",
 *     type="object",
 * 
 *     @OA\Property(
 *          property="marital_status",
 *          type="integer",
 *          example="1"
 *     ),
 * 
 *     @OA\Property(
 *          property="living_status",
 *          type="integer",
 *          example="1"
 *     ),
 *
 *     @OA\Property(
 *          property="safety_alarm",
 *          type="boolean",
 *          example="false"
 *     ),
 * 
 *     @OA\Property(
 *          property="has_carer",
 *          type="boolean",
 *          example="false"
 *     ),
 *     @OA\Property(
 *          property="carer_option",
 *          type="integer",
 *          example="1"
 *     ),
 * 
 *     @OA\Property(
 *          property="carer",
 *          type="string",
 *          example="Text of carer"
 *     ),
 * 
 *     @OA\Property(
 *          property="employment_status",
 *          type="integer",
 *          example="1"
 *     ),
 * 
 *     @OA\Property(
 *          property="has_community_resource",
 *          type="boolean",
 *          example="false"
 *     ),
 * 
 *     @OA\Property(
 *          property="community_resource",
 *          type="string",
 *          example="Text of community resource"
 *     ),
 * 
 *     @OA\Property(
 *          property="education_level",
 *          type="integer",
 *          example="1"
 *     ),
 * 
 *     @OA\Property(
 *          property="financial_state",
 *          type="integer",
 *          example="1"
 *     ),
 * 
 *     @OA\Property(
 *          property="smoking_option",
 *          type="integer",
 *          example="1"
 *     ),
 * 
 *     @OA\Property(
 *          property="smoking",
 *          type="integer",
 *          example="1"
 *     ),
 * 
 *     @OA\Property(
 *          property="drinking_option",
 *          type="integer",
 *          example="1"
 *     ),
 * 
 *     @OA\Property(
 *          property="drinking",
 *          type="integer",
 *          example="1"
 *     ),
 * 
 *     @OA\Property(
 *          property="has_religion",
 *          type="boolean",
 *          example="false"
 *     ),
 * 
 *     @OA\Property(
 *          property="religion",
 *          type="string",
 *          example="Text of religion"
 *     ),
 * 
 *     @OA\Property(
 *          property="has_social_activity",
 *          type="boolean",
 *          example="false"
 *     ),
 * 
 *     @OA\Property(
 *          property="social_activity",
 *          type="string",
 *          example="Text of social activity"
 *     ),
 * 
 *     @OA\Property(
 *          property="lubben_total_score",
 *          type="integer",
 *          example="1"
 *     ),
 * 
 * )
 */
class SocialBackgroundForm extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];
    protected $hidden = ['deleted_at'];

    protected $casts = [
        'assessment_case_id' => 'integer',
        'marital_status' => 'integer',
        'safety_alarm' => 'boolean',
        'has_carer' => 'boolean',
        'carer_option' => 'integer',
        'employment_status' => 'integer',
        'has_community_resource' => 'boolean',
        'education_level' => 'integer',
        'financial_state' => 'integer',
        'smoking_option' => 'integer',
        'smoking' => 'integer',
        'drinking_option' => 'integer',
        'drinking' => 'integer',
        'has_religion' => 'boolean',
        'has_social_activity' => 'boolean',
        'lubben_total_score' => 'integer',
    ];

    protected $with = ['livingStatusTable', 'communityResourceTable', 'financialStateTable'];

    public function assessmentCase()
    {
        return $this->belongsTo(AssessmentCase::class, 'assessment_case_id');
    }

    public function livingStatusTable()
    {
        return $this->hasMany(LivingStatusTable::class);
    }

    public function communityResourceTable()
    {
        return $this->hasMany(CommunityResourceTable::class);
    }

    public function financialStateTable()
    {
        return $this->hasMany(FinancialStateTable::class);
    }
    
    public function delete()
    {
        LivingStatusTable::where('social_background_form_id', $this->id)->delete();
        CommunityResourceTable::where('social_background_form_id', $this->id)->delete();
        FinancialStateTable::where('social_background_form_id', $this->id)->delete();
        return parent::delete();
    }
}
