<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OpenApi\Annotations as OA;

class MedicationDrug extends Model
{
    /**
     * @OA\Schema(
     *     schema="MedicationDrug",
     *     type="object",
     *
     *     @OA\Property(
     *          property="id",
     *          type="integer",
     *          example="507"
     *     ),
     *
     *     @OA\Property(
     *          property="parent_id",
     *          type="integer",
     *          example="86"
     *     ),
     *
     *     @OA\Property(
     *          property="name",
     *          type="string",
     *          example="Test"
     *     )
     * )
     */

    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];
    
    // One level child
    public function child()
    {
        return $this->hasMany(MedicationDrug::class, 'parent_id');
    }

    // Recursive children
    public function children()
    {
        return $this->hasMany(MedicationDrug::class, 'parent_id')
            ->with('children');
    }

    // One level parent
    public function parent()
    {
        return $this->belongsTo(MedicationDrug::class, 'parent_id');
    }

    // Recursive parents
    public function parents() {
        return $this->belongsTo(MedicationDrug::class, 'parent_id')
            ->with('parent');
    }
}
