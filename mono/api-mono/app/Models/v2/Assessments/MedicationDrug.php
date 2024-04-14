<?php

namespace App\Models\v2\Assessments;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MedicationDrug extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];

    // One level child
    public function child()
    {
        return $this->hasMany(MedicationDrug::class, 'parent_id');
    }

    // Recursive children
    public function children()
    {
        return $this->hasMany(MedicationDrug::class, 'parent_id')
            ->with('children');
    }

    // One level parent
    public function parent()
    {
        return $this->belongsTo(MedicationDrug::class, 'parent_id');
    }

    // Recursive parents
    public function parents()
    {
        return $this->belongsTo(MedicationDrug::class, 'parent_id')
            ->with('parent');
    }
}
