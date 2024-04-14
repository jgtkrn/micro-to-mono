<?php

namespace App\Models\v2\Elders;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class District extends Model
{
    use HasFactory;

    protected $guarded = ['id', 'deleted_at'];
    protected $hidden = ['deleted_at'];

    public function elders()
    {

        return $this->hasMany(Elder::class);
    }
}
