<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
/**
 * @OA\Schema(
 *     schema="CrossDisciplinary",
 *     type="object",
 *
 *     @OA\Property(
 *          property="case_id",
 *          type="integer",
 *          example=1
 *     ),
 *
 *     @OA\Property(
 *          property="role",
 *          type="string",
 *          example="yes"
 *     ),
 *
 *     @OA\Property(
 *          property="comments",
 *          type="string",
 *          example="yes"
 *     ),
 * )
 */
class CrossDisciplinary extends Model
{
    use HasFactory, SoftDeletes;
    protected $guarded = ['id'];
    protected $hidden = ['deleted_at'];

    protected $casts = [
            'case_id' => 'integer',
            'date' => 'date'
    ];
}
