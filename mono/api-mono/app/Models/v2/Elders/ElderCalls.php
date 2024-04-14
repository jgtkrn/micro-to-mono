<?php

namespace App\Models\v2\Elders;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ElderCalls extends Model
{
    use HasFactory;

    protected $fillable = [
        'caller_id',
        'cases_id',
        'call_date',
        'call_status',
        'remark',
        'created_by',
        'updated_by',
        'created_by_name',
        'updated_by_name',
        'created_at',
        'updated_at',
    ];

    protected $guarded = [
        'id',
        'deleted_at',
    ];

    protected $hidden = [
        'deleted_at',
        'updated_at',
        'created_at',
    ];

    public function case()
    {
        return $this->belongsTo(Cases::class, 'cases_id');
    }
}
