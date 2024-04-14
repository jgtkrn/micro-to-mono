<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @OA\Schema(
 *     schema="CoachingPam",
 *     type="object",
 * 
 *     @OA\Property(
 *          property="care_plan_id",
 *          description="existing id of care plan",
 *          type="integer",
 *          example=1
 *     ),
 * 
 *     @OA\Property(
 *          property="section",
 *          description="PAM top section",
 *          type="integer",
 *          example=1
 *     ),
 * 
 *     @OA\Property(
 *          property="intervention_group",
 *          description="PAM top intervention group",
 *          type="integer",
 *          example=1
 *     ),
 * 
 *     @OA\Property(
 *          property="gender",
 *          description="PAM top gender",
 *          type="integer",
 *          example=1
 *     ),
 * 
 *     @OA\Property(
 *          property="health_manage",
 *          description="PAM number 1",
 *          type="integer",
 *          example=1
 *     ),
 * 
 *     @OA\Property(
 *          property="active_role",
 *          description="PAM number 2",
 *          type="integer",
 *          example=1
 *     ),
 * 
 *     @OA\Property(
 *          property="self_confidence",
 *          description="PAM number 3",
 *          type="integer",
 *          example=1
 *     ),
 * 
 *     @OA\Property(
 *          property="drug_knowledge",
 *          description="PAM number 4",
 *          type="integer",
 *          example=1
 *     ),
 *
 *     @OA\Property(
 *          property="self_understanding",
 *          description="PAM number 5",
 *          type="integer",
 *          example=1
 *     ),
 * 
 *     @OA\Property(
 *          property="self_health",
 *          description="PAM number 6",
 *          type="integer",
 *          example=1
 *     ),
 * 
 *     @OA\Property(
 *          property="self_discipline",
 *          description="PAM number 7",
 *          type="integer",
 *          example=1
 *     ),
 * 
 *     @OA\Property(
 *          property="issue_knowledge",
 *          description="PAM number 8",
 *          type="integer",
 *          example=1
 *     ),
 * 
 *     @OA\Property(
 *          property="other_treatment",
 *          description="PAM number 9",
 *          type="integer",
 *          example=1
 *     ),
 * 
 *     @OA\Property(
 *          property="change_treatment",
 *          description="PAM number 10",
 *          type="integer",
 *          example=1
 *     ),
 * 
 *     @OA\Property(
 *          property="issue_prevention",
 *          description="PAM number 11",
 *          type="integer",
 *          example=1
 *     ),
 * 
 *     @OA\Property(
 *          property="find_solutions",
 *          description="PAM number 12",
 *          type="integer",
 *          example=1
 *     ),
 * 
 *     @OA\Property(
 *          property="able_maintain",
 *          description="PAM number 13",
 *          type="integer",
 *          example=1
 *     ),
 * 
 *     @OA\Property(
 *          property="remarks",
 *          description="PAM remarks",
 *          type="string",
 *          example="yes"
 *     ),
 * 
 * )
 */

class CoachingPam extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];
    protected $hidden = ['deleted_at'];

    protected $casts = [
        'care_plan_id' => 'integer',
        'section' => 'integer',
        'intervention_group' => 'integer',
        'gender' => 'integer',
        'health_manage' => 'integer',
        'active_role' => 'integer',
        'self_confidence' => 'integer',
        'drug_knowledge' => 'integer',
        'self_understanding' => 'integer',
        'self_health' => 'integer',
        'self_discipline' => 'integer',
        'issue_knowledge' => 'integer',
        'other_treatment' => 'integer',
        'change_treatment' => 'integer',
        'issue_prevention' => 'integer',
        'find_solutions' => 'integer',
        'able_maintain' => 'integer',
    ];

    public function carePlan()
    {
        return $this->belongsTo(CarePlan::class, 'care_plan_id');
    }

}
