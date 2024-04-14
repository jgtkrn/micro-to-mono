<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @OA\Schema(
 *     schema="RePhysiologicalMeasurementForm",
 *     type="object",
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
 * )
 */

class RePhysiologicalMeasurementForm extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];
    protected $hidden = ['deleted_at'];

    protected $casts = [
        're_assessment_case_id' => 'integer',
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
    ];

    public function assessmentCase()
    {
        return $this->belongsTo(AssessmentCase::class, 'assessment_case_id');
    }
}
