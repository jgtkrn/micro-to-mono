<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class District extends Model
{

    /**
     * @OA\Schema(
     *     schema="district",
     *     type="object",
     *
     *     @OA\Property(
     *          property="id",
     *          type="integer",
     *          example="1"
     *     ),
     *
     *     @OA\Property(
     *          property="district_name",
     *          type="string",
     *          example="Thuen muen"
     *     ),
     *     @OA\Property(
     *          property="bzn_code",
     *          type="string",
     *          example="NAAC"
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

    use HasFactory;

    protected $guarded = ['id', 'deleted_at'];
    protected $hidden = ['deleted_at'];

    public function elders()
    {

        return $this->hasMany(Elder::class);
    }
}
