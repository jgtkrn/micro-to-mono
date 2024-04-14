<?php

namespace App\Models\v2\Assessments;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BznConsultationNotes extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];
    protected $casts = [
        'sbp' => 'integer',
        'dbp' => 'integer',
        'pulse' => 'integer',
        'pao' => 'integer',
        'hstix' => 'decimal:2',
        'body_weight' => 'integer',
        'waist' => 'integer',
        // 'circumference' => 'integer',

        // Intervention Target 1
        'domain' => 'integer',
        'urgency' => 'integer',
        'category' => 'integer',
        'priority' => 'integer',
        'modifier' => 'integer',
        'knowledge' => 'integer',
        'behaviour' => 'integer',
        'status' => 'integer',

        // Case Status
        'case_status' => 'integer',
    ];

    public function bznCareTarget()
    {
        return $this->belongsTo(BznCareTarget::class, 'bzn_target_id')->with('carePlan');
    }

    public function bznConsultationSign()
    {
        return $this->hasOne(BznConsultationSign::class);
    }

    public function bznConsultationAttachment()
    {
        return $this->hasMany(BznConsultationAttachment::class);
    }

    public function carePlan()
    {
        return $this->belongsTo(BznCareTarget::class, 'bzn_target_id')->with('carePlan');
    }

    public function delete()
    {
        BznConsultationSign::where('bzn_consultation_notes_id', $this->id)->delete();
        BznConsultationAttachment::where('bzn_consultation_notes_id', $this->id)->delete();

        return parent::delete();
    }
}
