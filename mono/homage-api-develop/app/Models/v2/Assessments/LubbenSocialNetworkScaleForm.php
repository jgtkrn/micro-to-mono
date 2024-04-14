<?php

namespace App\Models\v2\Assessments;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LubbenSocialNetworkScaleForm extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];
    protected $hidden = ['deleted_at'];
    protected $casts = [
        'assessment_case_id' => 'integer',
        'assessment_kind' => 'integer',
        'relatives_sum' => 'integer',
        'relatives_to_talk' => 'integer',
        'relatives_to_help' => 'integer',
        'friends_sum' => 'integer',
        'friends_to_talk' => 'integer',
        'friends_to_help' => 'integer',
        'lubben_total_score' => 'integer',
    ];

    public function assessmentCase()
    {
        return $this->belongsTo(AssessmentCase::class, 'assessment_case_id');
    }
}
