<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @OA\Schema(
 *     schema="MedicalConditionForm",
 *     type="object",
 * 
 *     @OA\Property(
 *          property="complaint",
 *          type="string",
 *          example="Text of complaint"
 *     ),
 * 
 *     @OA\Property(
 *          property="has_medical_history",
 *          type="boolean",
 *          example="false"
 *     ),
 *
 *     @OA\Property(
 *          property="medical_history",
 *          type="string",
 *          example="Text of medical history"
 *     ),
 * 
 *     @OA\Property(
 *          property="premorbid",
 *          type="integer",
 *          example=1
 *     ),
 * 
 *     @OA\Property(
 *          property="followup_appointment",
 *          type="string",
 *          example=" Text of follow up appointment"
 *     ),
 * 
 *     @OA\Property(
 *          property="has_allergy",
 *          type="boolean",
 *          example="false"
 *     ),
 * 
 *     @OA\Property(
 *          property="allergy_description",
 *          type="string",
 *          example="Text of allergy description"
 *     ),
 * 
 *     @OA\Property(
 *          property="has_medication",
 *          type="boolean",
 *          example="false"
 *     ),
 * 
 *     @OA\Property(
 *          property="medication_description",
 *          type="string",
 *          example="Text of medication description"
 *     ),
 * 
 * )
 */
class MedicalConditionForm extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];
    protected $hidden = ['deleted_at'];

    protected $casts = [
        'has_medical_history' => 'boolean',
        'has_food_allergy' => 'boolean',
        'has_drug_allergy' => 'boolean',
        'has_medication' => 'boolean',
        'premorbid' => 'integer'
    ];

    protected $with = [
        'chiefComplaint',
        'medicalHistory'
    ];

    public function assessmentCase()
    {
        return $this->belongsTo(AssessmentCase::class, 'assessment_case_id');
    }

    public function chiefComplaint()
    {
        return $this->hasMany(ChiefComplaintTable::class);
    }

    public function medicalHistory()
    {
        return $this->hasMany(MedicalHistoryTable::class);
    }
}
