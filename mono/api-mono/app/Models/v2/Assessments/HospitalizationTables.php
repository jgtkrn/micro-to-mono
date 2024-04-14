<?php

namespace App\Models\v2\Assessments;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HospitalizationTables extends Model
{
    use HasFactory, SoftDeletes;
    protected $guarded = ['id'];
    protected $hidden = ['deleted_at'];
    protected $casts = [
        'qualtrics_form_id' => 'integer',
        'hosp_hosp' => 'integer',
        'hosp_way' => 'integer',
        'hosp_home' => 'integer',
        'hosp_reason' => 'integer',
    ];

    public function qualtricsForm()
    {
        return $this->belongsTo(QualtricsForm::class, 'qualtrics_form_id');
    }
}
