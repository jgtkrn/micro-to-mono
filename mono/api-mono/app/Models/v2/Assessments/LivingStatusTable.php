<?php

namespace App\Models\v2\Assessments;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LivingStatusTable extends Model
{
    use HasFactory, SoftDeletes;
    protected $guarded = ['id'];
    protected $hidden = ['deleted_at'];

    protected $casts = [
        'social_background_form_id' => 'integer',
        'ls_options' => 'integer',
    ];

    public function functionAndMobility()
    {
        return $this->belongsTo(SocialBackgroundForm::class, 'social_background_form_id');
    }
}
