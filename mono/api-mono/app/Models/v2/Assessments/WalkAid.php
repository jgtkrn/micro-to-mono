<?php

namespace App\Models\v2\Assessments;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WalkAid extends Model
{
    use HasFactory, SoftDeletes;
    protected $guarded = ['id'];
    protected $hidden = ['deleted_at'];
    protected $casts = [
        'walk_aid' => 'integer',
    ];

    public function qualtricsForm()
    {
        return $this->belongsTo(QualtricsForm::class, 'qualtrics_form_id');
    }
}
