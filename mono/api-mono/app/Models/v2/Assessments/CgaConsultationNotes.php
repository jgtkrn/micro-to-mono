<?php

namespace App\Models\v2\Assessments;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CgaConsultationNotes extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];
    protected $casts = [
        'cga_target_id' => 'integer',
        'sbp' => 'integer',
        'dbp' => 'integer',
        'pulse' => 'integer',
        'pao' => 'integer',
        'hstix' => 'decimal:2',
        'body_weight' => 'integer',
        'waist' => 'integer',
        // 'circumference' => 'integer',

        // Log
        'followup_options' => 'integer',

        // Case Status
        'case_status' => 'integer',
    ];

    public function cgaCareTarget()
    {
        return $this->belongsTo(CgaCareTarget::class, 'cga_target_id');
    }

    public function carePlan()
    {
        return $this->belongsTo(CgaCareTarget::class, 'cga_target_id')->with('carePlan');
    }

    public function cgaConsultationSign()
    {
        return $this->hasOne(CgaConsultationSign::class);
    }

    public function cgaConsultationAttachment()
    {
        return $this->hasMany(CgaConsultationAttachment::class);
    }

    public function delete()
    {
        CgaConsultationSign::where('cga_consultation_notes_id', $this->id)->delete();
        CgaConsultationAttachment::where('cga_consultation_notes_id', $this->id)->delete();

        return parent::delete();
    }
}
