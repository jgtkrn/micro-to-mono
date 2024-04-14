<?php

namespace App\Models\v2\Assessments;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MedicalConditionForm extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];
    protected $hidden = ['deleted_at'];

    protected $casts = [
        'has_medical_history' => 'boolean',
        'has_food_allergy' => 'boolean',
        'has_drug_allergy' => 'boolean',
        'has_medication' => 'boolean',
        'premorbid' => 'integer',
    ];

    protected $with = [
        'chiefComplaint',
        'medicalHistory',
    ];

    public function assessmentCase()
    {
        return $this->belongsTo(AssessmentCase::class, 'assessment_case_id');
    }

    public function chiefComplaint()
    {
        return $this->hasMany(ChiefComplaintTable::class);
    }

    public function medicalHistory()
    {
        return $this->hasMany(MedicalHistoryTable::class);
    }
}
