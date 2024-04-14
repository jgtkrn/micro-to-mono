<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FinancialStateTable extends Model
{
    use HasFactory, SoftDeletes;
    protected $guarded = ['id'];
    protected $hidden = ['deleted_at'];

    protected $casts = [
            'social_background_form_id' => 'integer',
            'financial_state' => 'integer',
    ];

    public function socialBackgroundForm()
    {
        return $this->belongsTo(SocialBackgroundForm::class, 'social_background_form_id');
    }
}
