<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @OA\Schema(
 *     schema="GeriatricDepressionScaleForm",
 *     type="object",
 * 
 *     @OA\Property(
 *          property="elderly_central_ref_number",
 *          type="string",
 *          example="WTO1000"
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
 *          property="assessor_name",
 *          type="string",
 *          example="John Doe"
 *     ),
 * 
 *     @OA\Property(
 *          property="assessment_kind",
 *          type="integer",
 *          example="1"
 *     ),
 * 
 *     @OA\Property(
 *          property="is_satisfied",
 *          type="integer",
 *          example="1"
 *     ),
 * 
 *     @OA\Property(
 *          property="is_given_up",
 *          type="integer",
 *          example="1"
 *     ),
 *
 *      @OA\Property(
 *          property="is_feel_empty",
 *          type="integer",
 *          example="1"
 *     ),
 * 
 *     @OA\Property(
 *          property="is_often_bored",
 *          type="integer",
 *          example="1"
 *     ),
 * 
 *     @OA\Property(
 *          property="is_happy_a_lot",
 *          type="integer",
 *          example="1"
 *     ),
 * 
 *     @OA\Property(
 *          property="is_affraid",
 *          type="integer",
 *          example="1"
 *     ),
 * 
 *     @OA\Property(
 *          property="is_happy_all_day",
 *          type="integer",
 *          example="1"
 *     ),
 * 
 *     @OA\Property(
 *          property="is_feel_helpless",
 *          type="integer",
 *          example="1"
 *     ),
 * 
 *     @OA\Property(
 *          property="is_prefer_stay",
 *          type="integer",
 *          example="1"
 *     ),
 * 
 *     @OA\Property(
 *          property="is_memory_problem",
 *          type="integer",
 *          example="1"
 *     ),
 * 
 *     @OA\Property(
 *          property="is_good_to_alive",
 *          type="integer",
 *          example="1"
 *     ),
 * 
 *     @OA\Property(
 *          property="is_feel_useless",
 *          type="integer",
 *          example="1"
 *     ),
 * 
 *     @OA\Property(
 *          property="is_feel_energic",
 *          type="integer",
 *          example="1"
 *     ),
 * 
 *     @OA\Property(
 *          property="is_hopeless",
 *          type="integer",
 *          example="1"
 *     ),
 * 
 *     @OA\Property(
 *          property="is_people_better",
 *          type="integer",
 *          example="1"
 *     ),
 * 
 *     @OA\Property(
 *          property="gds15_score",
 *          type="integer",
 *          example="1"
 *     ),
 * 
 * )
 */

class GeriatricDepressionScaleForm extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];
    protected $hidden = ['deleted_at'];
    protected $casts = [
        'assessment_case_id' => 'integer',
        'assessment_kind' => 'integer',
        'is_satisfied' => 'integer',
        'is_given_up' => 'integer',
        'is_feel_empty' => 'integer',
        'is_often_bored' => 'integer',
        'is_happy_a_lot' => 'integer',
        'is_affraid' => 'integer',
        'is_happy_all_day' => 'integer',
        'is_feel_helpless' => 'integer',
        'is_prefer_stay' => 'integer',
        'is_memory_problem' => 'integer',
        'is_good_to_alive' => 'integer',
        'is_feel_useless' => 'integer',
        'is_feel_energic' => 'integer',
        'is_hopeless' => 'integer',
        'is_people_better' => 'integer',
        'gds15_score' => 'integer',
    ];

    public function assessmentCase()
    {
        return $this->belongsTo(AssessmentCase::class, 'assessment_case_id');
    }
}
