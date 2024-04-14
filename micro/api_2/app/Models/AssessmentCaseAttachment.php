<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     schema="AssessmentCaseAttachment",
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
 *          example="image.jpg"
 *     ),
 * 
 * )
 */
class AssessmentCaseAttachment extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    protected $hidden = ['deleted_at', 'file_path'];
    protected $casts = [
        'assessment_case_id' => 'integer',
    ];

    public function assessmentCase()
    {
        return $this->belongsTo(AssessmentCase::class, 'assessment_case_id');
    }
}
