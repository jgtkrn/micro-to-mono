<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @OA\Schema(
 *     schema="CgaConsultationNotes",
 *     type="object",
 * 
 *     @OA\Property(
 *          property="id",
 *          type="integer",
 *          example=1
 *     ),
 * 
 *     @OA\Property(
 *          property="cga_target_id",
 *          type="integer",
 *          example=1
 *     ),
 *
 *     @OA\Property(
 *          property="assessor_1",
 *          type="string",
 *          example="yes"
 *     ),
 * 
 *     @OA\Property(
 *          property="assessor_2",
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
 *          property="purpose",
 *          type="integer",
 *          example=1
 *     ),
 * 
 *     @OA\Property(
 *          property="content",
 *          type="string",
 *          example="yes"
 *     ),
 * 
 *     @OA\Property(
 *          property="progress",
 *          type="string",
 *          example="yes"
 *     ),
 * 
 *     @OA\Property(
 *          property="case_summary",
 *          type="string",
 *          example="yes"
 *     ),
 * 
 *     @OA\Property(
 *          property="followup_options",
 *          type="integer",
 *          example=1
 *     ),
 * 
 *     @OA\Property(
 *          property="followup",
 *          type="string",
 *          example="yes"
 *     ),
 * 
 *     @OA\Property(
 *          property="personal_insight",
 *          type="string",
 *          example="yes"
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
class CgaConsultationNotes extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];
    protected $casts = [
            'cga_target_id' => 'integer',
            'sbp' => 'integer',
            'dbp' => 'integer',
            'pulse' => 'integer',
            'pao' => 'integer',
            'hstix' => 'decimal:2',
            'body_weight' => 'integer',
            'waist' => 'integer',
            // 'circumference' => 'integer',

            // Log
            'followup_options' => 'integer',

            // Case Status
            'case_status' => 'integer',
    ];

    public function cgaCareTarget()
    {
        return $this->belongsTo(CgaCareTarget::class, 'cga_target_id');
    }

    public function carePlan()
    {
        return $this->belongsTo(CgaCareTarget::class, 'cga_target_id')->with('carePlan');
    }

    public function cgaConsultationSign()
    {
        return $this->hasOne(CgaConsultationSign::class);
    }

    public function cgaConsultationAttachment()
    {
        return $this->hasMany(CgaConsultationAttachment::class);
    }

    public function delete()
    {
        CgaConsultationSign::where('cga_consultation_notes_id', $this->id)->delete();
        CgaConsultationAttachment::where('cga_consultation_notes_id', $this->id)->delete();
        return parent::delete();
    }
}
