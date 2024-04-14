<?php

namespace App\Models\v2\Users;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserCapacitor extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'user_token',
    ];
    protected $casts = [
        'user_id' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
