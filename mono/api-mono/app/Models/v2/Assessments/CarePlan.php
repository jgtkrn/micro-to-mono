<?php

namespace App\Models\v2\Assessments;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CarePlan extends Model
{
    use HasFactory, SoftDeletes;

    public $guarded = ['id'];
    public $with = ['coachingPam', 'bznNotes', 'cgaNotes'];

    public function coachingPam()
    {
        return $this->hasOne(CoachingPam::class);
    }

    public function preCoachingPam()
    {
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
