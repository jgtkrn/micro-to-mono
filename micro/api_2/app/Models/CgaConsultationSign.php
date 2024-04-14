<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CgaConsultationSign extends Model
{
    use HasFactory, SoftDeletes;
    protected $guarded = ['id'];
    protected $hidden = ['deleted_at', 'file_path'];
    protected $casts = [
        'cga_consultation_notes_id' => 'integer',
    ];

    public function cgaConsultationNotes()
    {
        return $this->belongsTo(CgaConsultationNotes::class, 'cga_consultation_notes_id');
    }
}
