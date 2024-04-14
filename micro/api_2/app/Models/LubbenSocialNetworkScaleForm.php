<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @OA\Schema(
 *     schema="LubbenSocialNetworkScaleForm",
 *     type="object",
 * 
 *     @OA\Property(
 *          property="elderly_central_ref_number",
 *          type="string",
 *          example="WTO1000"
 *     ),
 * 
 *     @OA\Property(
 *          property="assessment_date",
 *          type="string",
 *          format="date",
 *          example="2022-05-13"
 *     ),
 *
 *     @OA\Property(
 *          property="assessor_name",
 *          type="string",
 *          example="John Doe"
 *     ),
 * 
 *     @OA\Property(
 *          property="assessment_kind",
 *          type="integer",
 *          example="1"
 *     ),
 * 
 *     @OA\Property(
 *          property="relatives_sum",
 *          type="integer",
 *          example="1"
 *     ),
 * 
 *     @OA\Property(
 *          property="relatives_to_talk",
 *          type="integer",
 *          example="1"
 *     ),
 *     @OA\Property(
 *          property="relatives_to_help",
 *          type="integer",
 *          example="1"
 *     ),
 *     @OA\Property(
 *          property="friends_sum",
 *          type="integer",
 *          example="1"
 *     ),
 *     @OA\Property(
 *          property="friends_to_talk",
 *          type="integer",
 *          example="1"
 *     ),
 *     @OA\Property(
 *          property="friends_to_help",
 *          type="integer",
 *          example="1"
 *     ),
 *     @OA\Property(
 *          property="lubben_total_score",
 *          type="integer",
 *          example="1"
 *     ),
 * )
 */

class LubbenSocialNetworkScaleForm extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];
    protected $hidden = ['deleted_at'];
    protected $casts = [
        'assessment_case_id' => 'integer',
        'assessment_kind' => 'integer',
        'relatives_sum' => 'integer',
        'relatives_to_talk' => 'integer',
        'relatives_to_help' => 'integer',
        'friends_sum' => 'integer',
        'friends_to_talk' => 'integer',
        'friends_to_help' => 'integer',
        'lubben_total_score' => 'integer',
    ];

    public function assessmentCase()
    {
        return $this->belongsTo(AssessmentCase::class, 'assessment_case_id');
    }
}
