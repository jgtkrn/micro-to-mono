<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @OA\Schema(
 *     schema="CommunityResourceTable",
 *     type="object",
 * 
 *     @OA\Property(
 *          property="community_resource",
 *          type="string",
 *          example="Text of community resource"
 *     ),
 * )
 */
class CommunityResourceTable extends Model
{
    use HasFactory, SoftDeletes;
    protected $guarded = ['id'];
    protected $hidden = ['deleted_at'];

    protected $casts = [
            'social_background_form_id' => 'integer',
    ];

    public function socialBackground()
    {
        return $this->belongsTo(SocialBackgroundForm::class, 'social_background_form_id');
    }
}
