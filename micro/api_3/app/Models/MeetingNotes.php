<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
     * @OA\Schema(
     *     schema="MeetingNotes",
     *     type="object",
     *
     *     @OA\Property(
     *          property="id",
     *          type="integer",
     *          example="1"
     *     ),
     *
     *     @OA\Property(
     *          property="cases_id",
     *          type="integer",
     *          example="1"
     *     ),
     * 
     *     @OA\Property(
     *          property="notes",
     *          type="string",
     *          example="yes"
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
     *
* )
*/
class MeetingNotes extends Model
{
    use HasFactory, SoftDeletes;
    protected $guarded = ['id'];
    protected $casts = [
        'cases_id' => 'integer',
        'created_by' => 'integer',
        'updated_by' => 'integer'
    ];

    public function cases()
    {
        return $this->belongsTo(Cases::class, 'cases_id');
    }

}
