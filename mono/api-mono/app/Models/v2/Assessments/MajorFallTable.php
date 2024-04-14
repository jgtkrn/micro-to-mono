<?php

namespace App\Models\v2\Assessments;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MajorFallTable extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];
    protected $hidden = ['deleted_at'];

    protected $casts = [
        'function_mobility_form_id' => 'integer',
        'location' => 'integer',
        'injury_sustained' => 'integer',
        'fracture' => 'boolean',
    ];

    public function functionAndMobility()
    {
        return $this->belongsTo(FunctionMobilityForm::class, 'function_mobility_form_id');
    }
}
