<?php

namespace App\Models\v2\Assessments;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GeriatricDepressionScaleForm extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];
    protected $hidden = ['deleted_at'];
    protected $casts = [
        'assessment_case_id' => 'integer',
        'assessment_kind' => 'integer',
        'is_satisfied' => 'integer',
        'is_given_up' => 'integer',
        'is_feel_empty' => 'integer',
        'is_often_bored' => 'integer',
        'is_happy_a_lot' => 'integer',
        'is_affraid' => 'integer',
        'is_happy_all_day' => 'integer',
        'is_feel_helpless' => 'integer',
        'is_prefer_stay' => 'integer',
        'is_memory_problem' => 'integer',
        'is_good_to_alive' => 'integer',
        'is_feel_useless' => 'integer',
        'is_feel_energic' => 'integer',
        'is_hopeless' => 'integer',
        'is_people_better' => 'integer',
        'gds15_score' => 'integer',
    ];

    public function assessmentCase()
    {
        return $this->belongsTo(AssessmentCase::class, 'assessment_case_id');
    }
}
