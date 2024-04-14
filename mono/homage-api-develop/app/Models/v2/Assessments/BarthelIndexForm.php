<?php

namespace App\Models\v2\Assessments;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BarthelIndexForm extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];
    protected $hidden = ['deleted_at'];

    protected $casts = [
        'assessment_case_id' => 'integer',
        'bowels' => 'integer',
        'bladder' => 'integer',
        'grooming' => 'integer',
        'toilet_use' => 'integer',
        'feeding' => 'integer',
        'transfer' => 'integer',
        'mobility' => 'integer',
        'dressing' => 'integer',
        'stairs' => 'integer',
        'bathing' => 'integer',
        'barthel_total_score' => 'integer',
    ];

    public function assessmentCase()
    {
        return $this->belongsTo(AssessmentCase::class, 'assessment_case_id');
    }
}
