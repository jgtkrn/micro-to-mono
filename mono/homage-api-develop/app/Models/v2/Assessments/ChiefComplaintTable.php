<?php

namespace App\Models\v2\Assessments;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChiefComplaintTable extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];
    protected $hidden = ['deleted_at'];
    protected $casts = [
        'medical_condition_form_id' => 'integer',
    ];

    public function medicalCondition()
    {
        return $this->belongsTo(MedicalConditionForm::class, 'medical_condition_form_id');
    }
}
