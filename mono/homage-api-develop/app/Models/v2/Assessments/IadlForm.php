<?php

namespace App\Models\v2\Assessments;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class IadlForm extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];
    protected $hidden = ['deleted_at'];
    protected $casts = [
        'assessment_case_id' => 'integer',
        'assessment_kind' => 'integer',
        'can_use_phone' => 'integer',
        'can_take_ride' => 'integer',
        'can_buy_food' => 'integer',
        'can_cook' => 'integer',
        'can_do_housework' => 'integer',
        'can_do_repairment' => 'integer',
        'can_do_laundry' => 'integer',
        'can_take_medicine' => 'integer',
        'can_handle_finances' => 'integer',
        'iadl_total_score' => 'integer',
    ];

    public function assessmentCase()
    {
        return $this->belongsTo(AssessmentCase::class, 'assessment_case_id');
    }
}
