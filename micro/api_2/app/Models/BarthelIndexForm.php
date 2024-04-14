<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


/**
 * @OA\Schema(
 *     schema="BarthelIndexForm",
 *     type="object",
 * 
 *     @OA\Property(
 *          property="bowels",
 *          type="integer",
 *          example="1"
 *     ),
 * 
 *     @OA\Property(
 *          property="bladder",
 *          type="integer",
 *          example="1"
 *     ),
 *     @OA\Property(
 *          property="grooming",
 *          type="integer",
 *          example="1"
 *     ),
 *     @OA\Property(
 *          property="toilet_use",
 *          type="integer",
 *          example="1"
 *     ),
 *     @OA\Property(
 *          property="feeding",
 *          type="integer",
 *          example="1"
 *     ),
 *     @OA\Property(
 *          property="transfer",
 *          type="integer",
 *          example="1"
 *     ),
 *     @OA\Property(
 *          property="mobility",
 *          type="integer",
 *          example="1"
 *     ),
 *     @OA\Property(
 *          property="dressing",
 *          type="integer",
 *          example="1"
 *     ),
 *     @OA\Property(
 *          property="stairs",
 *          type="integer",
 *          example="1"
 *     ),
 *     @OA\Property(
 *          property="bathing",
 *          type="integer",
 *          example="1"
 *     ),
 *     @OA\Property(
 *          property="barthel_total_score",
 *          type="integer",
 *          example="1"
 *     ),   
 * 
 * )
 */
class BarthelIndexForm extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];
    protected $hidden = ['deleted_at'];

    protected $casts = [
        'assessment_case_id' => 'integer',
        'bowels' => 'integer',
        'bladder' => 'integer',
        'grooming' => 'integer',
        'toilet_use' => 'integer',
        'feeding' => 'integer',
        'transfer' => 'integer',
        'mobility' => 'integer',
        'dressing' => 'integer',
        'stairs' => 'integer',
        'bathing' => 'integer',
        'barthel_total_score' => 'integer'
    ];

    public function assessmentCase()
    {
        return $this->belongsTo(AssessmentCase::class, 'assessment_case_id');
    }
}
