<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @OA\Schema(
 *     schema="PhysiologicalMeasurementForm",
 *     type="object",
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
 * )
 */
class PhysiologicalMeasurementForm extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];
    protected $hidden = ['deleted_at'];

    protected $casts = [
        'assessment_case_id' => 'integer',
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
    ];

    public function assessmentCase()
    {
        return $this->belongsTo(AssessmentCase::class, 'assessment_case_id');
    }
}
