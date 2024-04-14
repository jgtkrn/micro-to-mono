<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OpenApi\Annotations as OA;

class MedicationHistory extends Model
{
    /**
     * @OA\Schema(
     *     schema="MedicationHistory",
     *     type="object",
     *
     *     @OA\Property(
     *          property="id",
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
     *          property="medication_category",
     *          type="string",
     *          example="hyspepsia and gastro-oesophaeal reflux disease"
     *     ),
     * 
     *     @OA\Property(
     *          property="medication_name",
     *          type="string",
     *          example="Mylanta"
     *     ),
     * 
     *     @OA\Property(
     *          property="dosage",
     *          type="string",
     *          example="400mg"
     *     ),
     * 
     *     @OA\Property(
     *          property="number_of_intake",
     *          type="string",
     *          example="1 tab"
     *     ),
     * 
     *     @OA\Property(
     *          property="frequency",
     *          type="array",
     *          @OA\Items(
     *              type="string",
     *              example="[""Daily"", ""BD"", ""TDS"", ""QID"", ""Q_H, ""Nocte"", ""prn"", ""Others""]"
     *          ),
     *     ),
     * 
     *     @OA\Property(
     *          property="route",
     *          type="string",
     *          example="PO/SL/LA/PUFF/SC/PR/Other"
     *     ),
     * 
     *     @OA\Property(
     *          property="remarks",
     *          type="string",
     *          example="remarks example"
     *     ),
     * 
     *     @OA\Property(
     *          property="gp",
     *          type="boolean",
     *          example="false"
     *     ),
     * 
     *     @OA\Property(
     *          property="epr",
     *          type="boolean",
     *          example="false"
     *     ),
     *
     *     @OA\Property(
     *          property="sign_off",
     *          type="boolean",
     *          example="false"
     *     ),
     * 
     *     @OA\Property(
     *          property="created_at",
     *          type="string",
     *          format="date-time",
     *          example="2022-10-17T00:00:00Z"
     *     ),
     *
     *     @OA\Property(
     *          property="updated_at",
     *          type="string",
     *          format="date-time",
     *          example="2022-10-17T00:00:00Z"
     *     ),
     * 
     *     @OA\Property(
     *          property="deleted_at",
     *          type="string",
     *          format="date-time",
     *          example="2022-10-17T00:00:00Z"
     *     )
     * )
     * 
     */
    use HasFactory, SoftDeletes;

    protected $casts = [
        'frequency' => 'array',
        'gp' => 'boolean',
        'epr' => 'boolean',
        'sign_off' => 'boolean'
    ];

    protected $fillable = [
        'case_id',
        'medication_category',
        'medication_name',
        'dosage',
        'number_of_intake',
        'frequency',
        'route',
        'remarks',
        'gp',
        'epr',
        'sign_off',
        'qi_data',
        'frequency_other',
        'routes_other',
        'created_by',
        'updated_by',
        'created_by_name',
        'updated_by_name'
    ];
}
