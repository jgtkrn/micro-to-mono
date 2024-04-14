<?php

namespace App\Models\v2\Assessments;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

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
