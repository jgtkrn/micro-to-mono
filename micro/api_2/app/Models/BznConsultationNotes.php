<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @OA\Schema(
 *     schema="BznConsultationNotes",
 *     type="object",
 * 
 *     @OA\Property(
 *          property="id",
 *          type="integer",
 *          example=1
 *     ),
 * 
 *     @OA\Property(
 *          property="bzn_target_id",
 *          type="integer",
 *          example=1
 *     ),
 *
 *     @OA\Property(
 *          property="assessor",
 *          type="string",
 *          example="yes"
 *     ),
 * 
 *     @OA\Property(
 *          property="meeting",
 *          type="string",
 *          example="yes"
 *     ),
 * 
 *     @OA\Property(
 *          property="visit_type",
 *          type="string",
 *          example="yes"
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
 *          property="assessment_time",
 *          type="string",
 *          example="00:00:00"
 *     ),
 * 
 *     @OA\Property(
 *          property="sbp",
 *          type="integer",
 *          example=1
 *     ),
 * 
 *     @OA\Property(
 *          property="dbp",
 *          type="integer",
 *          example=1
 *     ),
 * 
 *     @OA\Property(
 *          property="pulse",
 *          type="integer",
 *          example=1
 *     ),
 * 
 *     @OA\Property(
 *          property="pao",
 *          type="integer",
 *          example=1
 *     ),
 * 
 *     @OA\Property(
 *          property="hstix",
 *          type="integer",
 *          example=1
 *     ),
 * 
 *     @OA\Property(
 *          property="body_weight",
 *          type="integer",
 *          example=1
 *     ),
 * 
 *     @OA\Property(
 *          property="waist",
 *          type="integer",
 *          example=1
 *     ),
 * 
 *     @OA\Property(
 *          property="circumference",
 *          type="integer",
 *          example=1
 *     ),
 * 
 *     @OA\Property(
 *          property="domain",
 *          type="integer",
 *          example=1
 *     ),
 * 
 *     @OA\Property(
 *          property="urgency",
 *          type="integer",
 *          example=1
 *     ),
 * 
 *     @OA\Property(
 *          property="category",
 *          type="integer",
 *          example=1
 *     ),
 * 
 *     @OA\Property(
 *          property="intervention_remark",
 *          type="string",
 *          example="yes"
 *     ),
 * 
 *     @OA\Property(
 *          property="consultation_remark",
 *          type="string",
 *          example="yes"
 *     ),
 * 
 *     @OA\Property(
 *          property="area",
 *          type="string",
 *          example="yes"
 *     ),
 * 
 *     @OA\Property(
 *          property="priority",
 *          type="integer",
 *          example=1
 *     ),
 * 
 *     @OA\Property(
 *          property="target",
 *          type="string",
 *          example="yes"
 *     ),
 * 
 *     @OA\Property(
 *          property="modifier",
 *          type="integer",
 *          example=1
 *     ),
 * 
 *     @OA\Property(
 *          property="ssa",
 *          type="string",
 *          example="yes"
 *     ),
 * 
 *     @OA\Property(
 *          property="knowledge",
 *          type="integer",
 *          example=1
 *     ),
 * 
 *     @OA\Property(
 *          property="behaviour",
 *          type="integer",
 *          example=1
 *     ),
 * 
 *     @OA\Property(
 *          property="status",
 *          type="integer",
 *          example=1
 *     ),
 * 
 *     @OA\Property(
 *          property="case_status",
 *          type="integer",
 *          example=1
 *     ),
 * 
 *     @OA\Property(
 *          property="case_remark",
 *          type="string",
 *          example="yes"
 *     ),
 * 
 * )
 */

class BznConsultationNotes extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];
    protected $casts = [
            'sbp' => 'integer',
            'dbp' => 'integer',
            'pulse' => 'integer',
            'pao' => 'integer',
            'hstix' => 'decimal:2',
            'body_weight' => 'integer',
            'waist' => 'integer',
            // 'circumference' => 'integer',

            // Intervention Target 1
            'domain' => 'integer',
            'urgency' => 'integer',
            'category' => 'integer',
            'priority' => 'integer',
            'modifier' => 'integer',
            'knowledge' => 'integer',
            'behaviour' => 'integer',
            'status' => 'integer',

            // Case Status
            'case_status' => 'integer',
    ];

    public function bznCareTarget()
    {
        return $this->belongsTo(BznCareTarget::class, 'bzn_target_id')->with('carePlan');
    }

    public function bznConsultationSign()
    {
        return $this->hasOne(BznConsultationSign::class);
    }

    public function bznConsultationAttachment()
    {
        return $this->hasMany(BznConsultationAttachment::class);
    }
    
    public function delete()
    {
        BznConsultationSign::where('bzn_consultation_notes_id', $this->id)->delete();
        BznConsultationAttachment::where('bzn_consultation_notes_id', $this->id)->delete();
        return parent::delete();
    }

}
