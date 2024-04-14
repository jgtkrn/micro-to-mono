<?php

namespace App\Models\v2\Assessments;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SocialBackgroundForm extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];
    protected $hidden = ['deleted_at'];

    protected $casts = [
        'assessment_case_id' => 'integer',
        'marital_status' => 'integer',
        'safety_alarm' => 'boolean',
        'has_carer' => 'boolean',
        'carer_option' => 'integer',
        'employment_status' => 'integer',
        'has_community_resource' => 'boolean',
        'education_level' => 'integer',
        'financial_state' => 'integer',
        'smoking_option' => 'integer',
        'smoking' => 'integer',
        'drinking_option' => 'integer',
        'drinking' => 'integer',
        'has_religion' => 'boolean',
        'has_social_activity' => 'boolean',
        'lubben_total_score' => 'integer',
    ];

    protected $with = ['livingStatusTable', 'communityResourceTable', 'financialStateTable'];

    public function assessmentCase()
    {
        return $this->belongsTo(AssessmentCase::class, 'assessment_case_id');
    }

    public function livingStatusTable()
    {
        return $this->hasMany(LivingStatusTable::class);
    }

    public function communityResourceTable()
    {
        return $this->hasMany(CommunityResourceTable::class);
    }

    public function financialStateTable()
    {
        return $this->hasMany(FinancialStateTable::class);
    }

    public function delete()
    {
        LivingStatusTable::where('social_background_form_id', $this->id)->delete();
        CommunityResourceTable::where('social_background_form_id', $this->id)->delete();
        FinancialStateTable::where('social_background_form_id', $this->id)->delete();

        return parent::delete();
    }
}
