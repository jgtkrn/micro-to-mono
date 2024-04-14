<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     schema="Zone",
 *     type="object",
 *
 *     @OA\Property(
 *          property="id",
 *          type="integer",
 *          example="1"
 *     ),
 *
 *     @OA\Property(
 *          property="name",
 *          type="String",
 *          example="Exclusion Zone"
 *     ),
 *
 *     @OA\Property(
 *          property="code",
 *          type="String",
 *          example="exclusion_zone"
 *     ),
 *
 *     @OA\Property(
 *          property="created_by",
 *          type="object",
 *          @OA\Property(
 *              property="id",
 *              type="integer",
 *              example="1"
 *          ),
 *          @OA\Property(
 *              property="name",
 *              type="string",
 *              example="John snow"
 *          )
 *     ),
 *
 *     @OA\Property(
 *          property="updated_by",
 *          type="object",
 *          @OA\Property(
 *              property="id",
 *              type="integer",
 *              example="1"
 *          ),
 *          @OA\Property(
 *              property="name",
 *              type="string",
 *              example="John snow"
 *          )
 *     ),
 *
 *     @OA\Property(
 *          property="created_at",
 *          type="string",
 *          format="date-time",
 *          example="2022-05-13T00:00:00Z"
 *     ),
 *
 *     @OA\Property(
 *          property="updated_at",
 *          type="string",
 *          format="date-time",
 *          example="2022-05-13T00:00:00Z"
 *     )
 * )
 */
class Zone extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'created_by',
        'updated_by',
    ];
}
