<?php

namespace App\Models\v2\Assessments;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MontrealCognitiveAssessmentForm extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];
    protected $hidden = ['deleted_at'];
    protected $casts = [
        'assessment_case_id' => 'integer',
        'assessment_kind' => 'integer',
        'memory_c11' => 'boolean',
        'memory_c12' => 'boolean',
        'memory_c13' => 'boolean',
        'memory_c14' => 'boolean',
        'memory_c15' => 'boolean',
        'memory_c21' => 'boolean',
        'memory_c22' => 'boolean',
        'memory_c23' => 'boolean',
        'memory_c24' => 'boolean',
        'memory_c25' => 'boolean',
        'memory_score' => 'decimal:2',
        'all_words' => 'decimal:2',
        'repeat_words' => 'decimal:2',
        'non_animal_words' => 'decimal:2',
        'language_fluency_score' => 'decimal:2',
        'orientation_day' => 'integer',
        'orientation_month' => 'integer',
        'orientation_year' => 'integer',
        'orientation_week' => 'integer',
        'orientation_score' => 'decimal:2',
        'face_word' => 'integer',
        'velvet_word' => 'integer',
        'church_word' => 'integer',
        'daisy_word' => 'integer',
        'red_word' => 'integer',
        'delayed_memory_score' => 'decimal:2',
        'category_percentile' => 'integer',
        'total_moca_score' => 'decimal:2',
        'education_level' => 'string',
    ];

    public function assessmentCase()
    {
        return $this->belongsTo(AssessmentCase::class, 'assessment_case_id');
    }
}
