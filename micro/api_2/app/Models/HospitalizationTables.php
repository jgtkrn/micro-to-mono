<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
/**
 * @OA\Schema(
 *     schema="HospitalizationTables",
 *     type="object",
 *   
 *     @OA\Property(
 *          property="hosp_month",
 *          type="string",
 *          example="yes"
 *     ),
 *   
 *     @OA\Property(
 *          property="hosp_year",
 *          type="string",
 *          example="yes"
 *     ),
 * 
 *     @OA\Property(
 *          property="hosp_hosp",
 *          type="enum",
 *          format="1, 2, 3, 4, 5, 6, 7, 8, 9, 10",
 *          example="1"
 *     ),
 *  
 *     @OA\Property(
 *          property="hosp_hosp_other",
 *          type="string",
 *          example="yes"
 *     ),
 * 
 *     @OA\Property(
 *          property="hosp_way",
 *          type="enum",
 *          format="1, 2, 3, 4",
 *          example="1"
 *     ),
 *  
 *     @OA\Property(
 *          property="hosp_home",
 *          type="enum",
 *          format="1, 2, 3, 4, 5",
 *          example="1"
 *     ),
 * 
 *     @OA\Property(
 *          property="hosp_home_else",
 *          type="string",
 *          example="yes"
 *     ),
 * 
 *     @OA\Property(
 *          property="hosp_reason",
 *          type="enum",
 *          format="1, 2, 3, 4, 5, 6",
 *          example="1"
 *     ),
 * )
 */
class HospitalizationTables extends Model
{
    use HasFactory, SoftDeletes;
    protected $guarded = ['id'];
    protected $hidden = ['deleted_at'];
    protected $casts = [
        'qualtrics_form_id' => 'integer',
        'hosp_hosp' => 'integer',
        'hosp_way' => 'integer',
        'hosp_home' => 'integer',
        'hosp_reason' => 'integer',
    ];

    public function qualtricsForm()
    {
        return $this->belongsTo(QualtricsForm::class, 'qualtrics_form_id');
    }
}
