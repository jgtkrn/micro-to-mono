<?php

namespace App\Models\v2\Assessments;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BznCareTarget extends Model
{
    use HasFactory, SoftDeletes;

    public $with = ['bznConsultationNotes'];

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
        'ct_status' => 'integer',
    ];

    public function carePlan()
    {
        return $this->belongsTo(CarePlan::class, 'care_plan_id');
    }

    public function bznConsultationNotes()
    {
        return $this->hasMany(BznConsultationNotes::class, 'bzn_target_id')->with(['bznConsultationSign', 'bznConsultationAttachment'])->orderBy('assessment_date', 'desc')->orderBy('assessment_time', 'desc');
    }
}
