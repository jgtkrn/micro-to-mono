<?php

namespace App\Models\v2\Assessments;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SatisfactionEvaluationForm extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];
    protected $hidden = ['deleted_at'];

    protected $casts = [
        'clear_plan' => 'integer',
        'enough_discuss_time' => 'integer',
        'appropriate_plan' => 'integer',
        'has_discussion_team' => 'integer',
        'own_involved' => 'integer',
        'enough_opportunities' => 'integer',
        'enough_information' => 'integer',
        'selfcare_improved' => 'integer',
        'confidence_team' => 'integer',
        'feel_respected' => 'integer',
        'performance_rate' => 'integer',
        'service_scale' => 'integer',
        'recommend_service' => 'integer',
        'case_id' => 'integer',
    ];

}
