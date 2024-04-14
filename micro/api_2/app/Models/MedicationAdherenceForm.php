<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     schema="MedicationAdherenceForm",
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
 *          property="is_forget_sometimes",
 *          type="boolean",
 *          example="false"
 *     ),
 * 
 *     @OA\Property(
 *          property="is_missed_meds",
 *          type="boolean",
 *          example="false"
 *     ),
 * 
 *     @OA\Property(
 *          property="is_reduce_meds",
 *          type="boolean",
 *          example="false"
 *     ),
 * 
 *     @OA\Property(
 *          property="is_forget_when_travel",
 *          type="boolean",
 *          example="false"
 *     ),
 * 
 *     @OA\Property(
 *          property="is_meds_yesterday",
 *          type="boolean",
 *          example="false"
 *     ),
 * 
 *     @OA\Property(
 *          property="is_stop_when_better",
 *          type="boolean",
 *          example="false"
 *     ),
 * 
 *     @OA\Property(
 *          property="is_annoyed",
 *          type="boolean",
 *          example="false"
 *     ),
 *
 *     @OA\Property(
 *          property="forget_sometimes",
 *          type="string",
 *          example="Text of medical history"
 *     ),
 * 
 *     @OA\Property(
 *          property="missed_meds",
 *          type="string",
 *          example="Text of missed meds"
 *     ),
 * 
 *     @OA\Property(
 *          property="reduce_meds",
 *          type="string",
 *          example="Text of reduce meds"
 *     ),
 * 
 *     @OA\Property(
 *          property="forget_when_travel",
 *          type="string",
 *          example="Text of forget when travel"
 *     ),
 * 
 *     @OA\Property(
 *          property="meds_yesterday",
 *          type="string",
 *          example="Text of meds yesterday"
 *     ),
 * 
 *     @OA\Property(
 *          property="stop_when_better",
 *          type="string",
 *          example="Text of stop when better"
 *     ),
 * 
 *     @OA\Property(
 *          property="annoyed",
 *          type="string",
 *          example="Text of annoyed"
 *     ),
 * 
 *     @OA\Property(
 *          property="forget_frequency",
 *          type="integer",
 *          example="1"
 *     ),
 * )
 */
class MedicationAdherenceForm extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    protected $hidden = ['deleted_at'];

    protected $casts = [
        'assessment_case_id' => 'integer',
        'assessment_kind' => 'integer',
        'is_forget_sometimes' => 'boolean',
        'is_missed_meds' => 'boolean',
        'is_reduce_meds' => 'boolean',
        'is_forget_when_travel' => 'boolean',
        'is_meds_yesterday' => 'boolean',
        'is_stop_when_better' => 'boolean',
        'is_annoyed' => 'boolean',
        'forget_frequency' => 'integer',
        'total_mmas_score' => 'decimal:2'
    ];

    public function assessmentCase()
    {
        return $this->belongsTo(AssessmentCase::class, 'assessment_case_id');
    }
}
