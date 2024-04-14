<?php

namespace App\Models\v2\Assessments;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CgaCareTarget extends Model
{
    use HasFactory, SoftDeletes;

    public $with = ['cgaConsultationNotes'];

    protected $guarded = ['id'];
    protected $casts = [
        'motivation' => 'integer',
        'early_change_stage' => 'integer',
        'later_change_stage' => 'integer',
    ];

    public function carePlan()
    {
        return $this->belongsTo(CarePlan::class, 'care_plan_id');
    }

    public function cgaConsultationNotes()
    {
        return $this->hasMany(CgaConsultationNotes::class, 'cga_target_id')->with(['cgaConsultationSign', 'cgaConsultationAttachment'])->orderBy('assessment_date', 'desc')->orderBy('assessment_time', 'desc');
    }
}
