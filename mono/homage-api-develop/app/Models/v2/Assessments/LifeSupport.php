<?php

namespace App\Models\v2\Assessments;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LifeSupport extends Model
{
    use HasFactory, SoftDeletes;
    protected $guarded = ['id'];
    protected $hidden = ['deleted_at'];

    protected $casts = [
        'social_worker_form_id' => 'integer',
        'life_support' => 'integer',
    ];

    public function socialWorker()
    {
        return $this->belongsTo(SocialWorkerForm::class, 'social_worker_form_id');
    }
}
