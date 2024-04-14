<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BznConsultationSign extends Model
{
    use HasFactory, SoftDeletes;
    protected $guarded = ['id'];
    protected $hidden = ['deleted_at', 'file_path'];
    protected $casts = [
        'bzn_consultation_notes_id' => 'integer',
    ];

    public function bznConsultationNotes()
    {
        return $this->belongsTo(BznConsultationNotes::class, 'bzn_consultation_notes_id');
    }
}
