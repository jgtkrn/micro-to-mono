<?php

namespace App\Models\v2\Assessments;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FunctionMobilityForm extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];
    protected $hidden = ['deleted_at'];

    protected $casts = [
        'iadl' => 'integer',
        'total_iadl_score' => 'integer',
        'mobility' => 'integer',
        'walk_with_assistance' => 'integer',
        'mobility_tug' => 'string',
        'left_single_leg' => 'boolean',
        'right_single_leg' => 'boolean',
        'range_of_motion' => 'integer',
        'upper_limb_left' => 'integer',
        'upper_limb_right' => 'integer',
        'lower_limb_left' => 'integer',
        'lower_limb_right' => 'integer',
        'fall_history' => 'boolean',
        'number_of_major_fall' => 'integer',
        'mi_independent' => 'boolean',
        'mi_walk_assisst' => 'boolean',
        'mi_wheelchair_bound' => 'boolean',
        'mi_bed_bound' => 'boolean',
        'mo_independent' => 'boolean',
        'mo_walk_assisst' => 'boolean',
        'mo_wheelchair_bound' => 'boolean',
        'mo_bed_bound' => 'boolean',
    ];

    protected $with = ['majorFallTable'];

    public function assessmentCase()
    {
        return $this->belongsTo(AssessmentCase::class, 'assessment_case_id');
    }

    public function majorFallTable()
    {
        return $this->hasMany(MajorFallTable::class);
    }

    public function delete()
    {
        MajorFallTable::where('function_mobility_form_id', $this->id)->delete();

        return parent::delete();
    }
}
