<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @OA\Schema(
 *     schema="CgaCareTarget",
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
 *          property="target",
 *          type="string",
 *          example="yes"
 *     ),
 * 
 *     @OA\Property(
 *          property="health_vision",
 *          type="string",
 *          example="yes"
 *     ),
 * 
 *     @OA\Property(
 *          property="long_term_goal",
 *          type="string",
 *          example="yes"
 *     ),
 *      
 *     @OA\Property(
 *          property="short_term_goal",
 *          type="string",
 *          example="yes"
 *     ),
 *     
 *     @OA\Property(
 *          property="motivation",
 *          type="integer",
 *          example="1"
 *     ),
 *     
 *     @OA\Property(
 *          property="early_change_stage",
 *          type="integer",
 *          example="1"
 *     ),
 *     
 *     @OA\Property(
 *          property="later_change_stage",
 *          type="integer",
 *          example="1"
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
class CgaCareTarget extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];
    protected $casts = [
        'motivation' => 'integer',
        'early_change_stage' => 'integer',
        'later_change_stage' => 'integer',
    ];

    public $with = ['cgaConsultationNotes'];

    public function carePlan()
    {
        return $this->belongsTo(CarePlan::class, 'care_plan_id');
    }

    public function cgaConsultationNotes()
    {
        return $this->hasMany(CgaConsultationNotes::class, 'cga_target_id')->with(['cgaConsultationSign', 'cgaConsultationAttachment'])->orderBy('assessment_date', 'desc')->orderBy('assessment_time', 'desc');
    }

}
