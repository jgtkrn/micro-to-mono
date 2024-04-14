<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @OA\Schema(
 *     schema="BznCareTarget",
 *     type="object",
 * 
 *     @OA\Property(
 *          property="id",
 *          type="integer",
 *          example="1"
 *     ),
 * 
 *     @OA\Property(
 *          property="care_plan_id",
 *          type="integer",
 *          example="1"
 *     ),
 *
 *     @OA\Property(
 *          property="intervention",
 *          type="string",
 *          example="Text of intervention"
 *     ),
 * 
 *     @OA\Property(
 *          property="target_type",
 *          type="integer",
 *          example="1"
 *     ),
 * 
 *     @OA\Property(
 *          property="plan",
 *          type="string",
 *          example="Text of plan"
 *     ),
 * 
 *     @OA\Property(
 *          property="ct_domain",
 *          type="integer",
 *          example=1
 *     ),
 * 
 *     @OA\Property(
 *          property="ct_urgency",
 *          type="integer",
 *          example=1
 *     ),
 *  
 *     @OA\Property(
 *          property="ct_category",
 *          type="integer",
 *          example=1
 *     ),
 *   
 *     @OA\Property(
 *          property="ct_priority",
 *          type="integer",
 *          example=1
 *     ),
 *   
 *     @OA\Property(
 *          property="ct_modifier",
 *          type="integer",
 *          example=1
 *     ),
 * 
 *     @OA\Property(
 *          property="ct_knowledge",
 *          type="integer",
 *          example=1
 *     ),
 * 
 *     @OA\Property(
 *          property="ct_behaviour",
 *          type="integer",
 *          example=1
 *     ),
 * 
 *     @OA\Property(
 *          property="ct_status",
 *          type="integer",
 *          example=1
 *     ),
 * 
 *     @OA\Property(
 *          property="ct_area",
 *          type="string",
 *          example="Text of intervention"
 *     ),
 * 
 *     @OA\Property(
 *          property="ct_target",
 *          type="string",
 *          example="Text of intervention"
 *     ),
 * 
 *     @OA\Property(
 *          property="ct_ssa",
 *          type="string",
 *          example="Text of intervention"
 *     ),
 * 
 *     @OA\Property(
 *          property="updated_at",
 *          type="string",
 *          format="date-time",
 *          example="2022-05-13T00:00:00Z"
 *     ),
 * )
 */
class BznCareTarget extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];
    protected $casts = [
        'target_type' => 'integer',
        'ct_domain' => 'integer',
        'ct_urgency' => 'integer',
        'ct_category' => 'integer',
        'ct_priority' => 'integer',
        'ct_modifier' => 'integer',
        'ct_knowledge' => 'integer',
        'ct_behaviour' => 'integer',
        'ct_status' => 'integer'
    ];
    public $with = ['bznConsultationNotes'];

    public function carePlan()
    {
        return $this->belongsTo(CarePlan::class, 'care_plan_id');
    }

    public function bznConsultationNotes()
    {
        return $this->hasMany(BznConsultationNotes::class, 'bzn_target_id')->with(['bznConsultationSign', 'bznConsultationAttachment'])->orderBy('assessment_date', 'desc')->orderBy('assessment_time', 'desc');
    }
}
