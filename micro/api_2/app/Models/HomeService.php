<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HomeService extends Model
{
    use HasFactory, SoftDeletes;
    protected $guarded = ['id'];
    protected $hidden = ['deleted_at'];

    protected $casts = [
            'social_worker_form_id' => 'integer',
            'home_service' => 'integer',
    ];

    public function SocialWorker()
    {
        return $this->belongsTo(SocialWorkerForm::class, 'social_worker_form_id');
    }
}
