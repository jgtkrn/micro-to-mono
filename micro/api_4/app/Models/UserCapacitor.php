<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
/**
 * @OA\Schema(
 *     schema="UserCapacitor",
 *     type="object",
 *
 *     @OA\Property(
 *          property="id",
 *          type="integer",
 *          example=1
 *     ),
 *     
 *     @OA\Property(
 *          property="user_id",
 *          type="integer",
 *          example=1
 *     ),
 *
 *     @OA\Property(
 *          property="user_token",
 *          type="string",
 *          example="example"
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
class UserCapacitor extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'user_token',
    ];
    protected $casts = [
        'user_id' => 'integer'
    ];
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
