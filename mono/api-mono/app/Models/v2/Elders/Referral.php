<?php

namespace App\Models\v2\Elders;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Referral extends Model
{
    use HasFactory;

    protected $fillable = [
        'label',
        'code',
        'bzn_code',
        'cga_code',
        'created_by',
        'updated_by',
    ];
}
