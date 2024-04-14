<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     schema="File",
 *     type="object",
 *
 *     @OA\Property(
 *          property="id",
 *          type="integer",
 *          example="1"
 *     ),
 *
 *     @OA\Property(
 *          property="file_name",
 *          type="string",
 *          example="file.jpeg"
 *     ),
 * )
 */

class File extends Model
{
    use HasFactory;

    protected $fillable = ["file_name", "disk_name", "user_id", "event_id"];
    protected $hidden = ["event_id"];

    public function event()
    {
        return $this->belongsTo(Event::class, "event_id");
    }
}
