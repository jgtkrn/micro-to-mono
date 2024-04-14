<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @OA\Schema(
 *     schema="IadlForm",
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
 *          property="can_use_phone",
 *          type="integer",
 *          example="1"
 *     ),
 *     
 *     @OA\Property(
 *          property="text_use_phone",
 *          type="string",
 *          example="yes"
 *     ),
 * 
 *     @OA\Property(
 *          property="can_take_ride",
 *          type="integer",
 *          example="1"
 *     ),
 *  
 *     @OA\Property(
 *          property="text_take_ride",
 *          type="string",
 *          example="yes"
 *     ),
 *
 *      @OA\Property(
 *          property="can_buy_food",
 *          type="integer",
 *          example="1"
 *     ),
 *  
 *     @OA\Property(
 *          property="text_buy_food",
 *          type="string",
 *          example="yes"
 *     ),
 * 
 *     @OA\Property(
 *          property="can_cook",
 *          type="integer",
 *          example="1"
 *     ),
 *  
 *     @OA\Property(
 *          property="text_cook",
 *          type="string",
 *          example="yes"
 *     ),
 * 
 *     @OA\Property(
 *          property="can_do_housework",
 *          type="integer",
 *          example="1"
 *     ),
 *  
 *     @OA\Property(
 *          property="text_do_housework",
 *          type="string",
 *          example="yes"
 *     ),
 * 
 *     @OA\Property(
 *          property="can_do_repairment",
 *          type="integer",
 *          example="1"
 *     ),
 *  
 *     @OA\Property(
 *          property="text_repairment",
 *          type="string",
 *          example="yes"
 *     ),
 * 
 *     @OA\Property(
 *          property="can_do_laundry",
 *          type="integer",
 *          example="1"
 *     ),
 *
 *     @OA\Property(
 *          property="text_do_laundry",
 *          type="string",
 *          example="yes"
 *     ),
 * 
 *     @OA\Property(
 *          property="can_take_medicine",
 *          type="integer",
 *          example="1"
 *     ),
 * 
 *     @OA\Property(
 *          property="text_take_medicine",
 *          type="string",
 *          example="yes"
 *     ),
 * 
 *     @OA\Property(
 *          property="can_handle_finances",
 *          type="integer",
 *          example="1"
 *     ),
 * 
 *     @OA\Property(
 *          property="text_handle_finances",
 *          type="string",
 *          example="yes"
 *     ),
 * 
 *     @OA\Property(
 *          property="iadl_total_score",
 *          type="integer",
 *          example="1"
 *     ),
 * 
 * )
 */

class IadlForm extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];
    protected $hidden = ['deleted_at'];
    protected $casts = [
        'assessment_case_id' => 'integer',
        'assessment_kind' => 'integer',
        'can_use_phone' => 'integer',
        'can_take_ride' => 'integer',
        'can_buy_food' => 'integer',
        'can_cook' => 'integer',
        'can_do_housework' => 'integer',
        'can_do_repairment' => 'integer',
        'can_do_laundry' => 'integer',
        'can_take_medicine' => 'integer',
        'can_handle_finances' => 'integer',
        'iadl_total_score' => 'integer',
    ];

    public function assessmentCase()
    {
        return $this->belongsTo(AssessmentCase::class, 'assessment_case_id');
    }
}
