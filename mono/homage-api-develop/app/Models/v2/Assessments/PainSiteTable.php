<?php

namespace App\Models\v2\Assessments;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PainSiteTable extends Model
{
    use HasFactory, SoftDeletes;
    protected $guarded = ['id'];
    protected $hidden = ['deleted_at'];
    protected $casts = [
        'physical_condition_form_id' => 'integer',
        // Pain
        'is_dull' => 'boolean',
        'is_achy' => 'boolean',
        'is_sharp' => 'boolean',
        'is_stabbing' => 'boolean',
        'is_relief' => 'boolean',
        'have_relief_method' => 'integer',
        'relief_method' => 'integer',
        'pain_scale' => 'integer',
        'affect_adl' => 'integer',
    ];

    public function physicalConditionForm()
    {
        return $this->belongsTo(PhysicalConditionForm::class, 'physical_condition_form_id');
    }
}
