<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @OA\Schema(
 *     schema="LivingStatusTable",
 *     type="object",
 * 
 *     @OA\Property(
 *          property="ls_options",
 *          type="integer",
 *          example=1
 *     ),
 * )
 */
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
