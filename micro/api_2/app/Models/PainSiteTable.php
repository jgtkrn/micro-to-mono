<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @OA\Schema(
 *     schema="PainSiteTable",
 *     type="object",
 *    
 *     @OA\Property(
 *          property="is_pain",
 *          type="integer",
 *          example=1
 *     ),
 *    
 *     @OA\Property(
 *          property="provoking_factor",
 *          type="string",
 *          example="too much eating"
 *     ),
 *     
 *     @OA\Property(
 *          property="pain_location1",
 *          type="string",
 *          example="stomach"
 *     ),
 *     
 *     @OA\Property(
 *          property="is_dull",
 *          type="boolean",
 *          example="true"
 *     ),
 *     
 *     @OA\Property(
 *          property="is_achy",
 *          type="boolean",
 *          example="true"
 *     ),
 *     
 *     @OA\Property(
 *          property="is_sharp",
 *          type="boolean",
 *          example="true"
 *     ),
 *     
 *     @OA\Property(
 *          property="is_stabbing",
 *          type="boolean",
 *          example="true"
 *     ),
 *     
 *     @OA\Property(
 *          property="stabbing_option",
 *          type="enum",
 *          format="constant, intermittent",
 *          example="constant"
 *     ),
 *      
 *     @OA\Property(
 *          property="pain_location2",
 *          type="string",
 *          example="stomach"
 *     ),
 *      
 *     @OA\Property(
 *          property="is_relief",
 *          type="boolean",
 *          example="true"
 *     ),
 *      
 *     @OA\Property(
 *          property="what_relief",
 *          type="string",
 *          example="sleep"
 *     ),
 *      
 *     @OA\Property(
 *          property="have_relief_method",
 *          type="integer",
 *          example=1
 *     ),
 *      
 *     @OA\Property(
 *          property="relief_method",
 *          type="integer",
 *          example="1"
 *     ),
 *      
 *     @OA\Property(
 *          property="other_relief_method",
 *          type="string",
 *          example="fishing"
 *     ),
 *      
 *     @OA\Property(
 *          property="pain_scale",
 *          type="integer",
 *          example="1"
 *     ),
 *      
 *     @OA\Property(
 *          property="when_pain",
 *          type="string",
 *          example="20 Dec 2021"
 *     ),
 *      
 *     @OA\Property(
 *          property="affect_adl",
 *          type="integer",
 *          example=1
 *     ),
 *      
 *     @OA\Property(
 *          property="adl_info",
 *          type="string",
 *          example="yes"
 *     ),
 *        
 *     @OA\Property(
 *          property="pain_remark",
 *          type="string",
 *          example="yes"
 *     ),
 *     
 *     @OA\Property(
 *          property="is_radiation",
 *          type="string",
 *          example="1"
 *     ),
 *     
 * )
 */
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
