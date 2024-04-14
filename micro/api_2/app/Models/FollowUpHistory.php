<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OpenApi\Annotations as OA;

class FollowUpHistory extends Model
{
    /**
     * @OA\Schema(
     *     schema="FollowUpHistory",
     *     type="object",
     *
     *     @OA\Property(
     *          property="id",
     *          type="integer",
     *          example="1"
     *     ),
     *     @OA\Property(
     *          property="case_id",
     *          type="integer",
     *          example="1"
     *     ),
     *     @OA\Property(
     *          property="date",
     *          type="date",
     *          example="2022-10-30"
     *     ),
     *     @OA\Property(
     *          property="time",
     *          type="datetime",
     *          example="2022-10-30 18:37:00"
     *     ),
     *     @OA\Property(
     *          property="appointment_id",
     *          type="integer",
     *          example="1"
     *     ),
     *     @OA\Property(
     *          property="type",
     *          type="string",
     *          example="yes"
     *     ),
     *     @OA\Property(
     *          property="created_at",
     *          type="datetime",
     *          example="2022-11-15T00:00:00Z"
     *     ),
     *     @OA\Property(
     *          property="updated_at",
     *          type="datetime",
     *          example="2022-11-15T00:00:00Z"
     *     ),
     *     @OA\Property(
     *          property="deleted_at",
     *          type="datetime",
     *          example="null"
     *     ),
     * )
     */

    use HasFactory, SoftDeletes;
    protected $guarded = ['id'];

}
