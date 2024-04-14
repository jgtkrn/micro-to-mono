<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @OA\Schema(
 *     schema="SatisfactionEvaluationForm",
 *     type="object",
 *   
 *     @OA\Property(
 *          property="assessor_name",
 *          type="string",
 *          example="John Doe"
 *     ),
 * 
 *     @OA\Property(
 *          property="elder_reference_number",
 *          type="string",
 *          example="WP20001"
 *     ),
 * 
 *     @OA\Property(
 *          property="evaluation_date",
 *          type="date",
 *          example="2022-11-15"
 *     ),
 * 
 * 
 *     @OA\Property(
 *          property="case_id",
 *          type="integer",
 *          example=1
 *     ),
 * 
 *     @OA\Property(
 *          property="clear_plan",
 *          type="integer",
 *          example=1
 *     ),
 * 
 *     @OA\Property(
 *          property="enough_discuss_time",
 *          type="integer",
 *          example=1
 *     ),
 * 
 *     @OA\Property(
 *          property="appropriate_plan",
 *          type="integer",
 *          example=1
 *     ),
 * 
 *     @OA\Property(
 *          property="has_discussion_team",
 *          type="integer",
 *          example=1
 *     ),
 * 
 *     @OA\Property(
 *          property="own_involved",
 *          type="integer",
 *          example=1
 *     ),
 * 
 *     @OA\Property(
 *          property="enough_opportunities",
 *          type="integer",
 *          example=1
 *     ),
 * 
 *     @OA\Property(
 *          property="enough_information",
 *          type="integer",
 *          example=1
 *     ),
 * 
 *     @OA\Property(
 *          property="selfcare_improved",
 *          type="integer",
 *          example=1
 *     ),
 * 
 *     @OA\Property(
 *          property="confidence_team",
 *          type="integer",
 *          example=1
 *     ),
 * 
 *     @OA\Property(
 *          property="feel_respected",
 *          type="integer",
 *          example=1
 *     ),
 * 
 *     @OA\Property(
 *          property="performance_rate",
 *          type="integer",
 *          example=1
 *     ),
 * 
 *     @OA\Property(
 *          property="service_scale",
 *          type="integer",
 *          example=1
 *     ),
 * 
 *     @OA\Property(
 *          property="recommend_service",
 *          type="integer",
 *          example=1
 *     ),
 * )
 */

class SatisfactionEvaluationForm extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];
    protected $hidden = ['deleted_at'];

    protected $casts = [
        'clear_plan' => 'integer',
        'enough_discuss_time' => 'integer',
        'appropriate_plan' => 'integer',
        'has_discussion_team' => 'integer',
        'own_involved' => 'integer',
        'enough_opportunities' => 'integer',
        'enough_information' => 'integer',
        'selfcare_improved' => 'integer',
        'confidence_team' => 'integer',
        'feel_respected' => 'integer',
        'performance_rate' => 'integer',
        'service_scale' => 'integer',
        'recommend_service' => 'integer',
        'case_id' => 'integer'
    ];

}
