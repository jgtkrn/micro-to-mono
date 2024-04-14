<?php

namespace App\Models\v2\Users;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccessRole extends Model
{
    use HasFactory;

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
