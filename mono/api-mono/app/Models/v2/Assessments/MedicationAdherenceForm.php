<?php

namespace App\Models\v2\Assessments;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicationAdherenceForm extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    protected $hidden = ['deleted_at'];

    protected $casts = [
        'assessment_case_id' => 'integer',
        'assessment_kind' => 'integer',
        'is_forget_sometimes' => 'boolean',
        'is_missed_meds' => 'boolean',
        'is_reduce_meds' => 'boolean',
        'is_forget_when_travel' => 'boolean',
        'is_meds_yesterday' => 'boolean',
        'is_stop_when_better' => 'boolean',
        'is_annoyed' => 'boolean',
        'forget_frequency' => 'integer',
        'total_mmas_score' => 'decimal:2',
    ];

    public function assessmentCase()
    {
        return $this->belongsTo(AssessmentCase::class, 'assessment_case_id');
    }
}
