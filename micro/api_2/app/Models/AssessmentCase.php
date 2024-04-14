<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @OA\Schema(
 *     schema="AssessmentCase",
 *     type="object",
 * 
 *     @OA\Property(
 *          property="id",
 *          type="integer",
 *          example="1"
 *     ),
 * 
 *     @OA\Property(
 *          property="case_id",
 *          type="integer",
 *          example=1
 *     ),
 * 
 *     @OA\Property(
 *          property="case_type",
 *          type="string",
 *          example="CGA"
 *     ),
 *
 *     @OA\Property(
 *          property="first_assessor",
 *          type="integer",
 *          example=1
 *     ),
 * 
 *     @OA\Property(
 *          property="second_assessor",
 *          type="integer",
 *          example=2
 *     ),
 *
 *     @OA\Property(
 *          property="assessment_date",
 *          type="string",
 *          format="date",
 *          example="2022-05-13"
 *     ),
 * 
 *     @OA\Property(
 *          property="start_time",
 *          type="string",
 *          format="date-time",
 *          example="2022-05-13T00:00:00Z"
 *     ),
 * 
 *     @OA\Property(
 *          property="end_time",
 *          type="string",
 *          format="date-time",
 *          example="2022-05-13T00:00:00Z"
 *     ),
 * 
 *     @OA\Property(
 *          property="status",
 *          type="string",
 *          example="submitted"
 *     ),
 * 
 *     @OA\Property(
 *          property="priority_level",
 *          type="integer",
 *          example=1
 *     ),
 * 
 *     @OA\Property(
 *          property="updated_at",
 *          type="string",
 *          format="date-time",
 *          example="2022-05-13T00:00:00Z"
 *     ),
 * 
 *     @OA\Property(property="forms_submitted", type="array", description="List of form status",
 *         @OA\Items(type="object",
 *             @OA\Property(property="name", type="string", description="Form name", example="physiological_measurement"),
 *             @OA\Property(property="submit", type="boolean", description="Form status", example="false")
 *         )    
 *     )
 * )
 */
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
