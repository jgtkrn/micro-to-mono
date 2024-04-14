<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GenogramForm extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    protected $hidden = ['deleted_at', 'file_path'];
    protected $casts = [
        'assessment_case_id' => 'integer',
    ];

    public function assessmentCase()
    {
        return $this->belongsTo(AssessmentCase::class, 'assessment_case_id');
    }
}
