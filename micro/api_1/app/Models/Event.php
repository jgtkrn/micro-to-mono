<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     schema="Event",
 *     type="object",
 *
 *     @OA\Property(
 *          property="id",
 *          type="integer",
 *          example="1"
 *     ),
 *
 *     @OA\Property(
 *          property="title",
 *          type="string",
 *          example="Title"
 *     ),
 * 
 *     @OA\Property(
 *          property="start",
 *          type="string",
 *          format="date-time",
 *          example="2022-05-13T00:00:00Z"
 *     ),
 * 
 *     @OA\Property(
 *          property="end",
 *          type="string",
 *          format="date-time",
 *          example="2022-05-13T00:00:00Z"
 *     ),
 * 
 *     @OA\Property(
 *          property="elder_id",
 *          type="integer",
 *          example="1"
 *     ),
 * 
 *     @OA\Property(
 *          property="case_id",
 *          type="integer",
 *          example="1"
 *     ),
 * 
 *     @OA\Property(
 *          property="remark",
 *          type="string",
 *          example="Remarks"
 *     ),
 * 
 *     @OA\Property(
 *          property="category_id",
 *          type="integer",
 *          example="1"
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

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        "title",
        "start",
        "end",
        "elder_id",
        "case_id",
        "remark",
        "address",
        "category_id",
        "created_by",
        "updated_by",
        "created_by_name",
        "updated_by_name"
    ];

    //protected $hidden = ["elder_id"];

    public function user()
    {
        return $this->hasMany(UserEvent::class);
    }

    public function file()
    {
        return $this->hasMany(File::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class, "category_id");
    }
}
