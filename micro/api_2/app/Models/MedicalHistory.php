<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MedicalHistory extends Model
{
    /**
     * @OA\Schema(
     *     schema="MedicalHistory",
     *     type="object",
     *
     *     @OA\Property(
     *          property="id",
     *          type="integer",
     *          example="77"
     *     ),
     *
     *     @OA\Property(
     *          property="case_id",
     *          type="integer",
     *          example="1"
     *     ),
     * 
     *     @OA\Property(
     *          property="medical_category_name",
     *          type="string",
     *          example="Medical Category Name"
     *     ),
     * 
     *     @OA\Property(
     *          property="medical_diagnosis_name",
     *          type="string",
     *          example="Medical Diagnosis Name"
     *     ),
     * 
     *     @OA\Property(
     *          property="created_at",
     *          type="date",
     *          example="2022-11-15T00:00:00Z"
     *     ),
     * 
     *     @OA\Property(
     *          property="updated_at",
     *          type="date",
     *          example="2022-11-15T00:00:00Z"
     *     ),
     * 
     *     @OA\Property(
     *          property="deleted_at",
     *          type="date",
     *          example="null"
     *     ),
     * )
     */

    use HasFactory, SoftDeletes;
    protected $guarded = ['id'];
}
