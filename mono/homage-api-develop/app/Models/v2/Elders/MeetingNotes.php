<?php

namespace App\Models\v2\Elders;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MeetingNotes extends Model
{
    use HasFactory, SoftDeletes;
    protected $guarded = ['id'];
    protected $casts = [
        'cases_id' => 'integer',
        'created_by' => 'integer',
        'updated_by' => 'integer',
    ];

    public function cases()
    {
        return $this->belongsTo(Cases::class, 'cases_id');
    }
}
