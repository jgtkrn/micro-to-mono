<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @OA\Schema(
 *     schema="CarePlan",
 *     type="object",
 * 
 *     @OA\Property(
 *          property="id",
 *          type="integer",
 *          example="1"
 *     ),
 * 
 *     @OA\Property(
 *          property="case_id",
 *          type="integer",
 *          example="1"
 *     ),
 *
 *     @OA\Property(
 *          property="case_type",
 *          type="enum",
 *          format="BZN, CGA",
 *          example="BZN"
 *     ),
 * 
 *     @OA\Property(
 *          property="case_manager",
 *          type="string",
 *          example="John Doe"
 *     ),
 * 
 *     @OA\Property(
 *          property="handler",
 *          type="string",
 *          example="Jane Doe"
 *     ),
 *      
 *     @OA\Property(
 *          property="manager_id",
 *          type="integer",
 *          example=12
 *     ),
 * 
 *     @OA\Property(
 *          property="handler_id",
 *          type="integer",
 *          example=12
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
class CarePlan extends Model
{
    use HasFactory, SoftDeletes;

    public $guarded = ['id'];
    public $with = ['coachingPam', 'bznNotes', 'cgaNotes'];

    public function coachingPam(){
        return $this->hasOne(CoachingPam::class);
    }

    public function preCoachingPam(){
        return $this->hasOne(PreCoachingPam::class);
    }

    public function bznCareTarget()
    {
        return $this->hasMany(BznCareTarget::class);
    }

    public function caseManagers()
    {
        return $this->hasMany(CaseManager::class);
    }

    public function cgaCareTarget()
    {
        return $this->hasMany(CgaCareTarget::class);
    }

    public function bznNotes()
    {
        return $this->hasManyThrough(
            BznConsultationNotes::class, 
            BznCareTarget::class,
            'care_plan_id',
            'bzn_target_id',
            'id',
            'id'
        )->orderBy('assessment_date', 'desc');
    }

    public function cgaNotes()
    {
        return $this->hasManyThrough(
            CgaConsultationNotes::class, 
            CgaCareTarget::class,
            'care_plan_id',
            'cga_target_id',
            'id',
            'id'
        )->orderBy('assessment_date', 'desc');
    }

    public function delete()
    {
        CoachingPam::where('care_plan_id', $this->id)->delete();
        PreCoachingPam::where('care_plan_id', $this->id)->delete();
        CaseManager::where('care_plan_id', $this->id)->delete();
        return parent::delete();
    }
}
