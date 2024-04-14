<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @OA\Schema(
 *     schema="FunctionMobilityForm",
 *     type="object",
 * 
 *     @OA\Property(
 *          property="iadl",
 *          type="integer",
 *          example="1"
 *     ),
 * 
 *     @OA\Property(
 *          property="total_iadl_score",
 *          type="integer",
 *          example="1"
 *     ),
 *
 *     @OA\Property(
 *          property="mobility",
 *          type="integer",
 *          example="1"
 *     ),
 * 
 *     @OA\Property(
 *          property="walk_with_assistance",
 *          type="integer",
 *          example="1"
 *     ),
 *     @OA\Property(
 *          property="mobility_tug",
 *          type="string",
 *          example="yes"
 *     ),
 * 
 *     @OA\Property(
 *          property="left_single_leg",
 *          type="boolean",
 *          example="true"
 *     ),
 * 
 *     @OA\Property(
 *          property="right_single_leg",
 *          type="boolean",
 *          example="true"
 *     ),
 * 
 *     @OA\Property(
 *          property="range_of_motion",
 *          type="integer",
 *          example="1"
 *     ),
 *  
 *     @OA\Property(
 *          property="upper_limb_left",
 *          type="integer",
 *          example="1"
 *     ),
 *  
 *     @OA\Property(
 *          property="upper_limb_right",
 *          type="integer",
 *          example="1"
 *     ),
 *  
 *     @OA\Property(
 *          property="lower_limb_left",
 *          type="integer",
 *          example="1"
 *     ),
 *  
 *     @OA\Property(
 *          property="lower_limb_right",
 *          type="integer",
 *          example="1"
 *     ),
 * 
 *     @OA\Property(
 *          property="fall_history",
 *          type="boolean",
 *          example="true"
 *     ),
 * 
 *     @OA\Property(
 *          property="number_of_major_fall",
 *          type="integer",
 *          example="1"
 *     ),
 * 
 * )
 */

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
        MajorFallTable::where("function_mobility_form_id", $this->id)->delete();
        return parent::delete();
    }
}
