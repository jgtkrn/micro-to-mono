<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @OA\Schema(
 *     schema="MajorFallTable",
 *     type="object",
 * 
 *     @OA\Property(
 *          property="assessment_kind",
 *          type="integer",
 *          example="1"
 *     ),
 * 
 *     @OA\Property(
 *          property="relatives_sum",
 *          type="integer",
 *          example="1"
 *     ),
 * )
 */
class MajorFallTable extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];
    protected $hidden = ['deleted_at'];

    protected $casts = [
            'function_mobility_form_id' => 'integer',
            'location' => 'integer',
            'injury_sustained' => 'integer',
            'fracture' => 'boolean',
    ];

    public function functionAndMobility()
    {
        return $this->belongsTo(FunctionMobilityForm::class, 'function_mobility_form_id');
    }
}
