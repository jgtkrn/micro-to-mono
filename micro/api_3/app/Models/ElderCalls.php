<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ElderCalls extends Model
{

    /**
     * @OA\Schema(
     *     schema="Calls",
     *     type="object",
     *
     *     @OA\Property(
     *          property="id",
     *          type="integer",
     *          example="1"
     *     ),
     *
     *     @OA\Property(
     *          property="caller_id",
     *          type="integer",
     *          example="1"
     *     ),
     *
     *     @OA\Property(
     *          property="call_status",
     *          type="string",
     *          example="Pending"
     *     ),
     *     @OA\Property(
     *          property="call_start",
     *          type="datetime",
     *          example="2022-06-08 17:00"
     *     ),
     *     @OA\Property(
     *          property="call_end",
     *          type="datetime",
     *          example="2022-06-08 17:20"
     *     ),
     *     @OA\Property(
     *          property="remarks",
     *          type="string",
     *          example="no answer from elder"
     *     ),
     *     @OA\Property(
     *          property="cases_id",
     *          type="integer",
     *          example="1"
     *
     *     ),
     *
     *     @OA\Property(
     *          property="created_by",
     *          type="longtext",
     *          example="Nurse A"
     *
     *     ),
     *
     *      @OA\Property(
     *          property="updated_by",
     *          type="longtext",
     *          example="Admin"
     *
     *     ),
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
     *
     *
     *
     *
     * )
     */

    use HasFactory;

    protected $fillable = [
        'caller_id',
        'cases_id',
        'call_date',
        'call_status',
        'remark',
        'created_by',
        'updated_by',
        'created_by_name',
        'updated_by_name',
        'created_at',
        'updated_at',
    ];

    protected $guarded = [
        'id',
        'deleted_at'
    ];

    protected $hidden = [
        'deleted_at',
        'updated_at',
        'created_at',
    ];

    public function case()
    {
        return $this->belongsTo(Cases::class, 'cases_id');
    }
}
