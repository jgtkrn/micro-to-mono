<?php

namespace App\Models\v2\Assessments;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AssessmentCase extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];

    public function physiologicalMeasurementForm()
    {
        return $this->hasOne(PhysiologicalMeasurementForm::class);
    }

    public function rePhysiologicalMeasurementForm()
    {
        return $this->hasOne(RePhysiologicalMeasurementForm::class);
    }

    public function physicalConditionForm()
    {
        return $this->hasOne(PhysicalConditionForm::class);
    }

    public function medicalConditionForm()
    {
        return $this->hasOne(MedicalConditionForm::class);
    }

    public function lubbenSocialNetworkScaleForm()
    {
        return $this->hasOne(LubbenSocialNetworkScaleForm::class);
    }

    public function socialBackgroundForm()
    {
        return $this->hasOne(SocialBackgroundForm::class);
    }

    public function medicationAdherenceForm()
    {
        return $this->hasOne(MedicationAdherenceForm::class);
    }

    public function functionMobilityForm()
    {
        return $this->hasOne(FunctionMobilityForm::class);
    }

    public function barthelIndexForm()
    {
        return $this->hasOne(BarthelIndexForm::class);
    }

    public function geriatricDepressionScaleForm()
    {
        return $this->hasOne(GeriatricDepressionScaleForm::class);
    }

    public function iadlForm()
    {
        return $this->hasOne(IadlForm::class);
    }

    public function genogramForm()
    {
        return $this->hasOne(GenogramForm::class);
    }

    public function montrealCognitiveAssessmentForm()
    {
        return $this->hasOne(MontrealCognitiveAssessmentForm::class);
    }

    public function assessmentCaseStatus()
    {
        return $this->hasOne(AssessmentCaseStatus::class);
    }

    public function assessmentCaseAttachment()
    {
        return $this->hasMany(AssessmentCaseAttachment::class);
    }

    public function assessmentCaseSignature()
    {
        return $this->hasOne(AssessmentCaseSignature::class);
    }

    public function qualtricsForm()
    {
        return $this->hasOne(QualtricsForm::class);
    }

    public function socialWorkerForm()
    {
        return $this->hasOne(SocialWorkerForm::class);
    }
}
