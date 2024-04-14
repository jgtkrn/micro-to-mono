<?php

namespace App\Models\v2\Assessments;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PhysiologicalMeasurementForm extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];
    protected $hidden = ['deleted_at'];

    protected $casts = [
        'assessment_case_id' => 'integer',
        'temperature' => 'decimal:2',
        'sitting_sbp' => 'integer',
        'sitting_dbp' => 'integer',
        'standing_sbp' => 'integer',
        'standing_dbp' => 'integer',
        'blood_oxygen' => 'integer',
        'heart_rate' => 'integer',
        'heart_rythm' => 'integer',
        'kardia' => 'integer',
        'blood_sugar' => 'decimal:2',
        'blood_sugar_time' => 'integer',
        'waistline' => 'decimal:2',
        'weight' => 'decimal:2',
        'height' => 'decimal:2',
        'respiratory_rate' => 'integer',
        'blood_options' => 'integer',
    ];

    public function assessmentCase()
    {
        return $this->belongsTo(AssessmentCase::class, 'assessment_case_id');
    }
}
