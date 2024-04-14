<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RouteLogger extends Model
{
    /**
     * @OA\Schema(
     *     schema="RouteLogger",
     *     type="object",
     *
     *     @OA\Property(
     *          property="id",
     *          type="integer",
     *          example="1"
     *     ),
     *     @OA\Property(
     *          property="method",
     *          type="string",
     *          example="GET"
     *     ),
     *     @OA\Property(
     *          property="url",
     *          type="string",
     *          example="/data"
     *     ),
     *     @OA\Property(
     *          property="time",
     *          type="integer",
     *          example="2500"
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
     public $guarded = ['id'];
     public $casts = ['time' => 'integer'];
}
