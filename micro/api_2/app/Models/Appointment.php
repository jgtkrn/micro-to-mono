<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OpenApi\Annotations as OA;

class Appointment extends Model
{
    /**
     * @OA\Schema(
     *     schema="Appointment",
     *     type="object",
     *
     *     @OA\Property(
     *          property="id",
     *          type="integer",
     *          example="98"
     *     ),
     *
     *     @OA\Property(
     *          property="cluster",
     *          type="string",
     *          example="cluster"
     *     ),
     * 
     *     @OA\Property(
     *          property="type",
     *          type="string",
     *          example="type"
     *     ),
     * 
     *     @OA\Property(
     *          property="appointment_other_text",
     *          type="string",
     *          example="other"
     *     ),
     * 
     *     @OA\Property(
     *          property="name_en",
     *          type="string",
     *          example="name_en"
     *     ),
     * 
     *     @OA\Property(
     *          property="name_sc",
     *          type="string",
     *          example="name_sc"
     *     ),
     * )
     */

    use HasFactory, SoftDeletes;
    protected $guarded = ['id'];

}
