<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @OA\Schema(
 *     schema="CaseManager",
 *     type="object",
 * 
 *     @OA\Property(
 *          property="care_plan_id",
 *          description="existing id of care plan",
 *          type="integer",
 *          example=1
 *     ),
 * 
 *     @OA\Property(
 *          property="manager_id",
 *          description="existing id of user",
 *          type="integer",
 *          example=1
 *     ), 
 * 
 *     @OA\Property(
 *          property="manager_name",
 *          description="name of user",
 *          type="string",
 *          example="system"
 *     )
 * )
 */

class CaseManager extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];
    protected $hidden = ['deleted_at'];

    protected $casts = [
        'care_plan_id' => 'integer',
        'manager_id' => 'integer'
    ];

    public function carePlan()
    {
        return $this->belongsTo(CarePlan::class, 'care_plan_id');
    }
}
