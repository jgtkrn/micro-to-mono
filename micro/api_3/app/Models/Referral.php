<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     schema="Referral",
 *     type="object",
 *
 *     @OA\Property(
 *          property="id",
 *          type="integer",
 *          example="1"
 *     ),
 *
 *     @OA\Property(
 *          property="label",
 *          type="String",
 *          example="Bubu Pharmacy"
 *     ),
 *
 *     @OA\Property(
 *          property="code",
 *          type="String",
 *          example="bubu_pharmacy"
 *     ),
 *
 *     @OA\Property(
 *          property="bzn_code",
 *          type="String",
 *          example="bbf"
 *     ),
 *
 *     @OA\Property(
 *          property="cga_code",
 *          type="String",
 *          example="cbf"
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
class Referral extends Model
{
    use HasFactory;

    protected $fillable = [
        'label',
        'code',
        'bzn_code',
        'cga_code',
        'created_by',
        'updated_by',
    ];
}
