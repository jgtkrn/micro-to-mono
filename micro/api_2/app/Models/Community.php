<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     schema="Community",
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
 *          type="string",
 *          example="Google Maps"
 *     ),
 *
 *     @OA\Property(
 *          property="url",
 *          type="string",
 *          example="https://www.google.com/maps"
 *     ),
 * )
 */

class Community extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'url'
    ];
}
