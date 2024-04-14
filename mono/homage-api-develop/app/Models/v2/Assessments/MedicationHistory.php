<?php

namespace App\Models\v2\Assessments;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MedicationHistory extends Model
{
    use HasFactory, SoftDeletes;

    protected $casts = [
        'frequency' => 'array',
        'gp' => 'boolean',
        'epr' => 'boolean',
        'sign_off' => 'boolean',
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
        'updated_by_name',
    ];
}
